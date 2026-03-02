# Architecture – Idae Legacy Code Modernization

**Date**: 2026-03-02
**Author**: Architect Agent (BMAD)
**Status**: Accepted

---

## System Context Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│  Browser (PrototypeJS SPA / bag.js)                             │
└───────────────────────────┬─────────────────────────────────────┘
                            │ HTTP / AJAX / Socket.IO
┌───────────────────────────▼─────────────────────────────────────┐
│  Docker Container: idae-legacy (port 8080)                       │
│                                                                  │
│  Apache 2.4 + PHP 8.2                                           │
│  ├── index.php / actions.php / postAction.php                   │
│  ├── services/json_*.php          ← JSON API (read by SPA)      │
│  ├── appclasses/                  ← ORM layer (ClassApp…)       │
│  ├── appfunc/                     ← helpers (droit_table…)      │
│  └── mdl/                         ← module fragments            │
│                                                                  │
│  Node.js 18 (app_node/src/)       ← Socket.IO / real-time       │
└──────┬───────────────────────────────────────────┬──────────────┘
       │ write/read (prod)                          │ write/read (tests)
       ▼                                            ▼
┌─────────────┐                         ┌──────────────────────┐
│  MongoDB     │                         │  MongoDB sidecar     │
│  (host)      │  ← PRODUCTION DATA →   │  (Docker, port 27018)│
│  host.docker │      READ ONLY          │  mongo:7             │
│  .internal   │      from app code      │  test fixtures only  │
│  :27017      │                         └──────────────────────┘
└─────────────┘
       │
       │  daily cron backup (02:00)
       ▼
┌─────────────────────────┐
│  ./mongo_backup/         │
│  YYYY-MM-DD/             │
│  (7-day retention)       │
└─────────────────────────┘
```

---

## Architecture Decisions (ADR)

### ADR-01 – MongoDB Test Isolation via Docker Sidecar
- **Status:** Accepted
- **Context:** Production MongoDB is external to Docker and holds live data. Tests must never connect to it.
- **Decision:** Add a `mongo-test` service (mongo:7, port 27018) to `docker-compose.yml`. PHPUnit connects exclusively to this sidecar via `MONGO_TEST_HOST` env var. A guard in `ClassMongoDb` asserts that `MONGO_ENV=test` implies host ≠ `host.docker.internal`.
- **Consequences:** Tests are fully isolated. Fixture seed/reset is required before each test suite. Slight complexity in docker-compose setup.

### ADR-02 – LESS → SCSS Migration Strategy: Direct Port with Sass Module System
- **Status:** Accepted
- **Context:** 29 `.less` files, all in `appcss/less/`. The pipeline is broken (boot-time compile fails). `main.less` uses `@{location_fragment}` — LESS-only variable interpolation in `@import` paths, unsupported in SCSS.
- **Decision:** Direct port with targeted fixes:
  1. Rename `.less` → `.scss`, move to `appcss/scss/`.
  2. Replace `@{location_fragment}` dynamic imports with explicit `@use`/`@forward` or hardcoded `@import` paths.
  3. Convert LESS mixins (`.boxshadow`, `.padding_more`, etc.) to SCSS `@mixin` / `@include`.
  4. Replace LESS variable syntax (`@var`) with SCSS syntax (`$var`).
  5. Add `npm run build:css` (dart-sass) in `idae/web/app_node/` or project root.
  6. Output compiled CSS to `appcss/generated/` — same path as before (no change to PHP asset URLs).
- **Consequences:** Legacy CSS class names and generated file paths unchanged — SPA unaffected. The `less.inc.php` / `lessc.inc.php` PHP compilers are removed from the boot path.

### ADR-03 – ClassApp Refactoring: Modular Extract
- **Status:** Accepted
- **Context:** `ClassApp.php` is 2072 lines mixing connection, query, write, aggregation, FK resolution, and schema metadata logic.
- **Decision:** Extract into focused classes:
  - `ClassApp.php` (~500 lines) — connection singleton, collection resolution, core CRUD (`query`, `findOne`, `insert`, `update`, `remove`, `count`).
  - `ClassAppFk.php` (~300 lines) — `get_grille_fk()`, `get_reverse_grille_fk()`, FK metadata.
  - `ClassAppAgg.php` (~200 lines) — `distinct()`, aggregation pipeline helpers.
  - All three share the same connection singleton via a protected `getCollection(string $name)` method on `ClassApp`.
- **Consequences:** Easier unit testing per class. Files stay under 600 lines (PRD NFR). Backward-compatible: callers instantiate `App` (alias for `ClassApp`) as before; FK/Agg methods are inherited.

### ADR-04 – PHPUnit Wiring and Test Structure
- **Status:** Accepted
- **Context:** No test infrastructure exists yet. Fixtures must seed the sidecar before tests run.
- **Decision:**
  - Add `phpunit/phpunit ^11` to `composer.json` (dev).
  - `phpunit.xml` at `idae/web/` root, testsuite pointing to `idae/web/test/`.
  - Base class `test/TestCase.php` extends `PHPUnit\Framework\TestCase`:
    - `setUpBeforeClass()`: seeds the sidecar via `test/fixtures/seed.php`.
    - `tearDownAfterClass()`: drops test collections.
  - Separate unit (`test/Unit/`) and integration (`test/Integration/`) directories.
  - `composer test` runs `vendor/bin/phpunit`.
- **Consequences:** PHPUnit isolation per test class. Integration tests hit sidecar only. Environment variable `MONGO_TEST_DSN` is injected by docker-compose.

### ADR-05 – PHP Strict Typing Rollout
- **Status:** Accepted
- **Context:** Legacy code has no `declare(strict_types=1)` and minimal type hints. PHPStan level 6 target.
- **Decision:** Incremental rollout — add `declare(strict_types=1)` and full type hints **only to files touched in each sprint**. No big-bang full-repo pass. PHPStan runs as part of `composer lint` against the `migration` branch diff only.
- **Consequences:** No disruption to untouched legacy files. Coverage grows sprint-by-sprint.

### ADR-06 – CSS Build Pipeline Location
- **Status:** Accepted
- **Context:** The existing Node.js package lives in `idae/web/app_node/`. A separate `package.json` exists at repo root (only `express`).
- **Decision:** Add dart-sass as a dev dependency to `idae/web/app_node/package.json`. Add `build:css` script there. SCSS source: `idae/web/appcss/scss/`. Output: `idae/web/appcss/generated/`. This keeps CSS tooling co-located with the existing Node toolchain.
- **Consequences:** Two scripts in `app_node/package.json`: `build:css` (one-shot) and `watch:css` (dart-sass `--watch`, dev only). Docker watch can trigger recompile on `.scss` change during development.

### ADR-08 – PHPStan as Pre-commit Git Hook
- **Status:** Accepted
- **Context:** PHPStan level 6 is the quality gate. Running it only manually risks forgotten checks before commits.
- **Decision:** Install a git pre-commit hook (via `captainhook/captainhook` or a simple shell script in `.git/hooks/pre-commit`) that runs `vendor/bin/phpstan analyse --level=6` scoped to staged PHP files only. Fails the commit if violations are found.
- **Consequences:** Zero-friction on clean commits. Developers must fix PHPStan errors before committing. Scope limited to staged files to avoid full-repo overhead on legacy untouched code.

### ADR-07 – MongoCompat as the Single Type Conversion Gateway
- **Status:** Accepted (pre-existing, reinforced)
- **Context:** `MongoCompat` already exists. Some files still use raw `MongoId`/`MongoRegex`.
- **Decision:** `MongoCompat` is the only permitted instantiation point for MongoDB BSON types. Any direct `new \MongoDB\BSON\ObjectId()` in application code (outside `MongoCompat` itself) is a violation. PHPStan custom rule or `grep` CI check enforces this.
- **Consequences:** Single place to update if driver behavior changes again.

---

## Components

### ClassApp (refactored)
- **Responsibility:** MongoDB connection singleton, collection resolution, core CRUD methods.
- **Technology:** PHP 8.2, `mongodb/mongodb ^2.0`, `AppCommon\MongoCompat`.
- **Interfaces:** Instantiated with optional `$table` name. Exposes `query()`, `findOne()`, `insert()`, `update()`, `remove()`, `count()`.
- **File:** `idae/web/appclasses/appcommon/ClassApp.php` (~500 lines post-refactor).

### ClassAppFk
- **Responsibility:** Forward and reverse FK grid resolution (reads `appscheme` metadata, queries related collections).
- **Technology:** PHP 8.2. Extends `ClassApp` or uses it via composition.
- **Interfaces:** `get_grille_fk(string $table): array`, `get_reverse_grille_fk(string $table, int $id): array`.
- **File:** `idae/web/appclasses/appcommon/ClassAppFk.php` (new).

### ClassAppAgg
- **Responsibility:** Aggregation pipeline helpers (`distinct`, `group`, `count`).
- **Technology:** PHP 8.2, MongoDB aggregation pipeline.
- **File:** `idae/web/appclasses/appcommon/ClassAppAgg.php` (new).

### MongoCompat
- **Responsibility:** Single conversion point for all MongoDB BSON types and cursor normalization.
- **File:** `idae/web/appclasses/appcommon/MongoCompat.php` (existing, completed).

### ClassMongoDb
- **Responsibility:** Low-level MongoDB connection manager. Holds `MongoDB\Client` singleton. Guards `MONGO_ENV` / host mismatch.
- **File:** `idae/web/appclasses/ClassMongoDb.php`.

### PHPUnit Test Suite
- **Responsibility:** Unit + integration coverage for all modified PHP classes and services.
- **Structure:**
  ```
  idae/web/test/
  ├── TestCase.php              # base class: seed/teardown
  ├── fixtures/
  │   └── seed.php              # drops + recreates test collections
  ├── Unit/
  │   ├── ClassAppTest.php
  │   ├── ClassAppFkTest.php
  │   ├── ClassAppAggTest.php
  │   └── MongoCompatTest.php
  └── Integration/
      ├── JsonDataTest.php      # json_data.php response shape
      ├── JsonSchemeTest.php
      └── JsonDataTableTest.php
  ```

### SCSS Pipeline
- **Responsibility:** Compile SCSS sources to CSS, replacing broken LESS pipeline.
- **Source:** `idae/web/appcss/scss/` (29 files converted from `.less`).
- **Output:** `idae/web/appcss/generated/` (same paths as before).
- **Entrypoints:** `main.scss`, `appsite.scss`, `interface.scss`, `datatable.scss`, `notifier.scss`, `pikaday.scss`, `sortable.scss`, + skin variants.
- **Script:** `npm run build:css` in `app_node/` → `sass scss/main.scss ../appcss/generated/main.css --style=compressed`.

### MongoDB Sidecar (test only)
- **Responsibility:** Isolated MongoDB instance for PHPUnit. Never contains production data.
- **Technology:** Docker `mongo:7`, port 27018.
- **Lifecycle:** Started with `docker-compose up`. Seeded by PHPUnit bootstrap. Wiped per test suite.

### Backup Cron
- **Responsibility:** Daily `mongodump` of production MongoDB to local directory.
- **Script:** `idae/web/bin/backup_mongo.sh`.
- **Schedule:** 02:00 daily (Windows Task Scheduler or Linux cron on the host).
- **Retention:** 7 days. Log: `./mongo_backup/backup.log`.

---

## Data Flow

### Flow: PHPUnit Test Run
```
1. Developer runs: docker-compose up (app + mongo-test)
2. Developer runs: composer test
3. PHPUnit bootstrap → TestCase::setUpBeforeClass()
4. seed.php connects to mongo-test:27018 (MONGO_TEST_DSN)
5. Drops + recreates test collections with fixture data
6. Test methods run against mongo-test — prod untouched
7. TestCase::tearDownAfterClass() drops test collections
```

### Flow: JSON API Request (production)
```
1. SPA calls: GET /idae/web/services/json_data.php?piece=table&table=produit
2. conf.inc.php → ClassSession → verifies $_SESSION['idagent']
3. droit_table($idagent, 'R', 'produit') → agent_groupe_droit check
4. ClassApp('produit') → MongoDB query on host.docker.internal:27017
5. MongoCompat::cursorToArray() if needed
6. JSON encoded → response
7. SPA populates grid via APP.APPSCHEMES metadata
```

### Flow: CSS Build
```
1. Developer edits idae/web/appcss/scss/main.scss
2. npm run build:css (from app_node/)
3. dart-sass compiles → idae/web/appcss/generated/main.css
4. Browser loads CSS via HTTPCSS constant (unchanged URL)
```

---

## Deployment Architecture

| Environment | Infrastructure | MongoDB | CSS |
|---|---|---|---|
| Dev (Docker) | docker-compose (app + mongo-test) | host.docker.internal:27017 (prod, read-only) + sidecar:27018 (tests) | `npm run build:css` on change |
| Production | Docker (or bare Apache/PHP 8.2) | host.docker.internal:27017 | Pre-compiled CSS committed to `generated/` |

---

## Cross-Cutting Concerns

### Security
- **Auth:** Session-based (`ClassSession`, `$_SESSION['idagent']`). `droit_table()` guards every data endpoint.
- **Data isolation:** `MONGO_ENV` guard prevents test code from connecting to prod.
- **Secrets:** MongoDB credentials in docker-compose env vars or `conf.lan.inc.php` (not committed).
- **Input:** `MongoCompat::toRegex(preg_quote($input))` for all user-supplied search terms.

### Observability
- **PHP logging:** `error_log()` exclusively — never `echo`/`print` in application code.
- **PHP log location:** `/var/log/apache2/error.log` (Docker) — tailed via `docker-logs.ps1`.
- **Node.js logs:** `app_node/logs/`.
- **Backup log:** `./mongo_backup/backup.log`.
- **PHPStan:** `composer lint` — level 6, run on modified files per sprint.

### Resilience
- **MongoDB connection:** Singleton `$PERSIST_CON` (global). No retry logic today; add try/catch with `error_log` on connection failure.
- **Socket.IO:** Already gracefully handled — PHP catches connection refusal, logs, continues without real-time.
- **Backup / DR:** Daily `mongodump`. Manual restore via `mongorestore`. 7-day window.

---

## File Structure Changes (post-modernization)

```
idae/web/
├── appclasses/appcommon/
│   ├── ClassApp.php          (refactored, ~500 lines)
│   ├── ClassAppFk.php        (extracted, NEW)
│   ├── ClassAppAgg.php       (extracted, NEW)
│   └── MongoCompat.php       (existing, complete)
├── appcss/
│   ├── less/                 (kept for reference, not compiled)
│   ├── scss/                 (NEW — 29 converted files)
│   └── generated/            (unchanged output paths)
├── bin/
│   └── backup_mongo.sh       (NEW)
└── test/
    ├── TestCase.php           (NEW)
    ├── fixtures/seed.php      (NEW)
    ├── Unit/                  (NEW)
    └── Integration/           (NEW)
```

---

## Closed Architectural Questions

| Question | Decision | Rationale |
|---|---|---|
| `ClassAppFk` — extend vs composition | **extends ClassApp** | Simpler, native access to `$this->collection`, consistent with existing class hierarchy |
| SCSS watch mode | **`npm run watch:css` added** | dart-sass `--watch` alongside `build:css` for dev convenience |
| PHPStan trigger | **Pre-commit git hook** | Blocks commits with level-6 violations; runs on modified files only to avoid full-repo friction |
