# BMAD Dashboard — Idae Legacy Code Modernization

> Updated: 2026-03-11T12:00:00Z | Team: 3 devs | Sprint: 2 weeks | Horizon: ~10 weeks (5 sprints)

---

## Planning Artifacts

| Artifact | Status | Path |
|----------|--------|------|
| Product Brief | ✅ Done | `bmad/artifacts/product-brief.md` |
| PRD | ✅ Done | `bmad/artifacts/prd.md` |
| Architecture | ✅ Done | `bmad/artifacts/architecture.md` |
| Tech Spec | ⏭ Skipped | Covered by Architecture doc |
| Sprint 01–05 | ✅ Done | `bmad/artifacts/sprints/` |

---

## Sprint Overview

| Sprint | Theme | Points | Dates | Status |
|--------|-------|--------|-------|--------|
| S01 | Foundation: Test Infra + SCSS Pipeline | 12 | 2026-03-02 → 03-13 | ✅ **DONE** |
| S02 | SCSS Complete + ClassApp Connection | 13 | 2026-03-16 → 03-27 | **ACTIVE** |
| S03 | ClassApp CRUD + ClassAppFk + Unit Tests | 14 | 2026-03-30 → 04-10 | Upcoming |
| S04 | ClassAppAgg + Integration Tests + Services | 14 | 2026-04-13 → 04-24 | **ACTIVE** |
| S05 | Modules + Security + Regression | 15 | 2026-04-27 → 05-08 | Upcoming |
| **Total** | | **68 pts** | **~10 weeks** | |

---

## Sprint 01 — ✅ COMPLETE (12/12 pts)

| ID | Title | Points | Status |
|----|-------|--------|--------|
| S1-01 | Add `mongo-test` sidecar to docker-compose | 1 | ✅ done |
| S1-02 | `MONGO_ENV` guard in `ClassMongoDb` | 1 | ✅ done |
| S1-03 | `TestCase.php` + `fixtures/seed.php` | 2 | ✅ done |
| S1-04 | PHPUnit config + `composer test` | 1 | ✅ done |
| S1-05 | `backup_mongo.sh` + retention + log | 1 | ✅ done |
| S1-06 | PHPStan pre-commit hook | 2 | ✅ done |
| S1-07 | Audit `.less` files — import tree + mixins | 1 | ✅ done |
| S1-08 | dart-sass setup + `build:css` + `watch:css` | 1 | ✅ done |
| S1-09 | Convert `vars` + `app_fabric` + `style` → SCSS | 2 | ✅ done |

## Sprint 02 — Active Stories

| ID | Title | Points | Status |
|----|-------|--------|--------|
| (stories pending `/dev-story S2-xx`) | | | |

---

## Data Preservation (LOCKED)

| Decision | Value |
|----------|-------|
| Test DB | `mongo-test` sidecar (Docker, port 27018) |
| Guard | `MONGO_ENV=test` → host ≠ `host.docker.internal` |
| Backup | `bin/backup_mongo.sh` — daily cron 02:00, 7-day retention |
| Alert | Log fichier (`mongo_backup/backup.log`) |

---

## Active Bugs

| ID | Severity | Title | Status |
|----|----------|-------|--------|
| BUG-01 | Medium | Node.js socket server refuses connections (port 3005) | Open → Sprint 04 |
| BUG-02 | Low | CleanStr signature mismatch | Patched → Sprint 05 |

---

## Next Step

**Recommended**: run integration tests for `json_data.php` — `bmad-master test integration S4-02 --auto`. If Docker is not available, start Docker with `docker-compose up` then run `composer test` from `idae/web/`.
