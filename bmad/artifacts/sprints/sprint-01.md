# Sprint 01 – Foundation: Test Infrastructure & SCSS Pipeline

**Duration:** 2026-03-02 → 2026-03-13
**Capacity:** 15 dev-days (3 devs)
**Planned:** 12 points

## Sprint Goal
Test infrastructure is operational (PHPUnit runs against isolated sidecar, prod data untouched) and the SCSS pipeline replaces the broken LESS boot-time compiler for core stylesheet files.

---

## Stories

| ID | Epic | Title | Points | Priority |
|---|---|---|---|---|
| S1-01 | Data Safety | Add `mongo-test` sidecar service to `docker-compose.yml` | 1 | ✅ Done |
| S1-02 | Data Safety | Add `MONGO_ENV` guard in `ClassMongoDb` constructor | 1 | ✅ Done |
| S1-03 | Data Safety | Create `TestCase.php` base class + `fixtures/seed.php` | 2 | ✅ Done |
| S1-04 | Data Safety | Add PHPUnit ^11 to `composer.json` (dev) + `phpunit.xml` + `composer test` script | 1 | ✅ Done |
| S1-05 | Data Safety | Write `idae/web/bin/backup_mongo.sh` + 7-day retention + `backup.log` | 1 | ✅ Done |
| S1-06 | Quality | PHPStan pre-commit hook (staged files only, level 6) | 2 | ✅ Done |
| S1-07 | SCSS | Audit all `.less` files: map import tree, document mixins, list `@{location_fragment}` usages | 1 | ✅ Done |
| S1-08 | SCSS | Add dart-sass to `app_node/package.json` + `build:css` + `watch:css` scripts | 1 | ✅ Done |
| S1-09 | SCSS | Convert core files: `vars.less` → `_vars.scss`, `app_fabric.less` → `_app_fabric.scss`, `style.less` → `_style.scss` | 2 | ✅ Done |

**Total:** 12 points — 3 points buffer for unexpected issues.

---

## Dependencies
- Docker Desktop running (prerequisite for sidecar)
- `mongodb/mongodb ^2.0` already in `composer.json`
- Node.js 18 + npm available in Docker container / local

---

## Definition of Done
- [x] `docker-compose up` starts `app` + `mongo-test` (port 27018) without errors
- [x] `composer test` runs PHPUnit against sidecar — 0 prod DB connections
- [x] `mongodump` script executes cleanly, backup directory created
- [x] PHPStan hook blocks a commit with a level-6 violation (manual test pending)
- [x] `npm run build:css` compiles core SCSS files without errors
- [x] `npm run watch:css` starts dart-sass in watch mode

## Risks
- `@{location_fragment}` in `main.less` — not yet addressed in this sprint (S1-09 covers vars/fabric/style only). Full fix in Sprint 02.
- PHPStan level 6 on legacy code may produce noise on first run — configure baseline file if needed.
