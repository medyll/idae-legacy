# PRD – Idae Legacy Code Modernization

**Date**: 2026-03-02
**Author**: PM Agent (BMAD)
**Status**: Draft v1.0

---

## Overview

The Idae CMS infrastructure is operational on PHP 8.2 + Docker. This PRD covers the
**code modernization phase**: replacing legacy MongoDB v1.x driver patterns, migrating
LESS to SCSS, introducing a rigorous test suite, enforcing strong PHP typing, factorizing
the codebase, and adding English inline documentation throughout.

The MongoDB instance is **external to Docker and contains production data**. All development
and testing work must be isolated from it via a dedicated MongoDB sidecar container, with
daily automated backups of the production database.

---

## Goals & Success Metrics

| Goal | Metric | Target |
|---|---|---|
| Test coverage | PHPUnit line coverage | ≥ 70% on `appclasses/`, `services/`, `appfunc/` |
| Zero Mongo v1.x references | `grep MongoClient\|MongoId\|MongoRegex\|MongoDate` | 0 occurrences |
| CSS build pipeline | SCSS compilation works in Docker | 0 errors on `docker-compose up` |
| Type safety | `grep -L declare(strict_types=1)` on modified files | 0 files without strict_types |
| Production data integrity | mongodump before any risky op | 0 data loss incidents |
| English comments | French comments in modified files | 0 (new code must be English) |

---

## User Personas

### Persona 1 – Developer (internal team)
- Role: Writes and maintains PHP/JS code on the migration branch.
- Needs: Clear test feedback, safe DB isolation, consistent code style.
- Pain points: Fear of breaking production data, opaque legacy code with no tests.

### Persona 2 – End User (CMS operator)
- Role: Uses Idae daily for cruise/blog/commercial data management.
- Needs: Application stays functional, no data loss, no regression in UI behavior.
- Pain points: Downtime, broken AJAX responses, lost records.

---

## Use Cases

### UC-01 – Run the test suite safely
**Actor:** Developer
**Trigger:** Before any commit / PR to `migration` branch
**Flow:**
1. Developer runs `docker-compose up` — app container + MongoDB sidecar start.
2. Sidecar is seeded with anonymized fixture data.
3. Developer runs `composer test` (PHPUnit).
4. Tests run against sidecar DB — production DB untouched.
5. Results printed, CI-ready exit code.
**Expected outcome:** All tests green, no side effects on production data.
**Edge cases:** Seed fails → test run aborts with clear error, no partial writes.

### UC-02 – Modify a PHP class (ClassApp, services, mdl/*)
**Actor:** Developer
**Trigger:** A method uses `MongoId`, `MongoRegex`, or ADODB-style cursor.
**Flow:**
1. Developer opens the file, replaces legacy patterns with `MongoCompat` equivalents.
2. Adds `declare(strict_types=1)` and type hints.
3. Adds PHPDoc in English.
4. Runs unit test for the modified method.
5. Runs integration test to verify JSON output is structurally identical.
**Expected outcome:** Method passes unit + integration tests, no PHP warnings.

### UC-03 – Build SCSS (was LESS)
**Actor:** Developer / CI
**Trigger:** CSS source file edited.
**Flow:**
1. Developer edits a `.scss` file under `idae/web/appcss/`.
2. Runs `npm run build:css` (or triggered automatically by Docker watch).
3. Compiled `.css` is output to the correct public path.
4. Browser loads the updated stylesheet.
**Expected outcome:** No LESS compiler involved; SCSS compiles cleanly.

### UC-04 – Daily production backup
**Actor:** Automated cron (host or Docker)
**Trigger:** Scheduled daily (e.g., 02:00)
**Flow:**
1. Cron job executes `mongodump` against `host.docker.internal:27017`.
2. Dump saved to `./mongo_backup/YYYY-MM-DD/` (gitignored).
3. Retention: last 7 days kept, older dumps pruned.
**Expected outcome:** Backup exists and is restorable. Any data loss incident can be recovered.

---

## Functional Requirements

| ID | Requirement | Priority | Notes |
|---|---|---|---|
| FR-01 | Add MongoDB sidecar service to `docker-compose.yml` for tests | Must | Isolated from prod. Uses mongo:7. |
| FR-02 | Create PHP fixture seeder for sidecar (per-test suite reset) | Must | `idae/web/test/fixtures/seed.php` |
| FR-03 | Replace all `MongoClient`/`MongoId`/`MongoRegex`/`MongoDate` with `MongoCompat` equivalents | Must | Scope: `ClassApp`, all `services/`, `appfunc/`, `mdl/` |
| FR-04 | Audit all `.less` files across the repo before migration (`find . -name "*.less"`) | Must | Unknown scope outside `appcss/` — audit first, migrate second |
| FR-05 | Migrate LESS → SCSS: convert all discovered `.less` files | Must | Keep same compiled output; do not change CSS class names |
| FR-05b | Add `npm run build:css` script using Sass (dart-sass) | Must | Replaces broken `less.inc.php` / `lessc.inc.php` PHP boot-time compilers |
| FR-06 | PHPUnit test suite: unit tests per `ClassApp` method | Must | Covers query, findOne, insert, update, remove, distinct, FK methods |
| FR-07 | PHPUnit integration tests: `json_data.php` response shape | Must | Assert JSON keys identical to legacy output |
| FR-08 | Add `declare(strict_types=1)` to all modified PHP files | Must | New files must always include it |
| FR-09 | Add typed parameters and return types to all modified methods | Must | `mixed` allowed only when truly necessary |
| FR-10 | Refactor `ClassApp.php` methods into focused private helpers | Should | Max ~50 lines per public method |
| FR-11 | Convert inline English comments in all modified files | Should | Remove/translate French comments; original Date/Time headers preserved |
| FR-12 | Daily `mongodump` cron job script (`bin/backup_mongo.sh`) | Must | Retention 7 days. Writes to `./mongo_backup/`. |
| FR-13 | `MONGO_ENV` env var guard: prevent test code connecting to prod | Should | Checked in `ClassMongoDb.php` constructor |
| FR-14 | Composer `test` script wired to PHPUnit | Must | `composer test` runs the full suite |
| FR-15 | PHPUnit config (`phpunit.xml`) scoped to `idae/web/test/` | Must | Separate test DB DSN via env |
| FR-16 | Reorganize `ClassApp.php`: extract FK logic to `ClassAppFk.php` | Could | Reduces file from 2072 to ~600 lines |

---

## Non-Functional Requirements

| Category | Requirement | Acceptance Criteria |
|---|---|---|
| Data Safety | Production MongoDB never touched by tests | `MONGO_TEST_HOST` env always points to sidecar in CI |
| Data Safety | Daily backup of prod DB | `mongo_backup/YYYY-MM-DD/` exists and is non-empty |
| Performance | Test suite completes in < 60s | PHPUnit output shows timing ≤ 60s |
| Correctness | JSON API output structurally identical | Integration test diffs: 0 missing keys |
| Compatibility | SCSS output identical to LESS output | Visual regression or byte-level CSS diff |
| Maintainability | No file > 600 lines (post-refactor) | `wc -l` check on modified files |
| Typing | No untyped public method signatures in modified files | Static analysis (PHPStan **level 6**) — 0 errors |

---

## Data Preservation Strategy

**Chosen approach: MongoDB Sidecar + Daily Cron Backup**

### Development / Test Isolation
- `docker-compose.yml` adds a `mongo-test` service (image: `mongo:7`, port 27018).
- Environment variable `MONGO_TEST_HOST=mongo-test` (in Docker Compose).
- PHPUnit bootstrap connects to `MONGO_TEST_HOST`, never to `host.docker.internal`.
- `ClassMongoDb.php` constructor asserts: if `MONGO_ENV=test`, host must equal `MONGO_TEST_HOST`.

### Seed & Reset
- `idae/web/test/fixtures/seed.php`: drops and recreates test collections with minimal anonymized data.
- PHPUnit `TestCase` base class calls seeder in `setUpBeforeClass()`.

### Production Backup
- Script: `idae/web/bin/backup_mongo.sh`
- Runs `mongodump --host host.docker.internal --out ./mongo_backup/$(date +%Y-%m-%d)/`
- Retention: prune directories older than 7 days.
- Scheduled via Windows Task Scheduler (host) or cron (Linux) at 02:00 daily.
- `./mongo_backup/` added to `.gitignore`.

---

## Out of Scope

- Rewriting the PrototypeJS SPA frontend (bag.js loader stays as-is).
- Changing MongoDB collection schemas or document structure.
- MySQL/SQL layer (ADODB `adodb.inc.php` for SQL — not the main target).
- UX redesign or new features.
- Production deployment pipeline / CI/CD server setup.

---

## Dependencies

| Dependency | Type | Notes |
|---|---|---|
| `mongodb/mongodb ^2.0` | PHP library | Already in composer.json |
| `phpunit/phpunit ^11` | Dev dependency | Add to composer.json |
| `sass` (dart-sass) | npm dev dep | Replaces less.inc.php |
| Docker Desktop | Infra | Required for sidecar |
| MongoDB 7 (sidecar) | Docker service | Test-only |

---

## Open Questions

- [x] Are there `.less` files outside `idae/web/appcss/`? → **Unknown** — audit required at start of SCSS sprint (glob all `.less` files before touching anything).
- [x] Does `less.inc.php` compile at request time or build time? → **Boot-time in header, currently broken.** The LESS pipeline is already non-functional. SCSS migration must produce working CSS where LESS currently fails. This is a fix, not just a migration.
- [x] PHPStan target level? → **Level 6** (full return types including generics).
- [x] Backup failure notification? → **Log file only** (`./mongo_backup/backup.log`). No email.

---

## Revision History

| Date | Author | Change |
|---|---|---|
| 2026-03-02 | PM Agent (BMAD) | Initial draft — data strategy (Option B + daily cron) integrated |
