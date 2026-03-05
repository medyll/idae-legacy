# Sprint 03 – ClassApp CRUD + ClassAppFk + Unit Tests

**Duration:** 2026-03-30 → 2026-04-10
**Capacity:** 15 dev-days (3 devs)
**Planned:** 14 points

## Sprint Goal
All ClassApp CRUD methods are migrated to the modern driver, `ClassAppFk` is extracted, and each method has a passing unit test running against the sidecar.

---

## Stories

| ID | Epic | Title | Points | Priority |
|---|---|---|---|---|
| S3-01 | ClassApp | Migrate `query()` + `findOne()` — strict types, MongoCompat filters, unit tests | 3 | Must |
| S3-02 | ClassApp | Migrate `insert()` + `update()` + `remove()` — strict types, MongoCompat filters, unit tests | 4 | Must |
| S3-03 | ClassApp | Migrate `distinct()` + aggregation pipeline helpers — strict types, unit tests | 3 | Must |
| S3-04 | ClassApp | Extract `ClassAppFk.php` (extends `ClassApp`): `get_grille_fk()`, `get_reverse_grille_fk()` — strict types, unit tests | 4 | Must |

**Total:** 14 points — 1 point buffer.

---

## Dependencies
- S2-05 done (`ClassApp.__construct` stable)
- S1-03 done (`TestCase` + sidecar seed operational)
- `MongoCompat` fully implemented

---

## Definition of Done
- [ ] `composer test` — all Unit/ClassAppTest.php tests green
- [ ] `composer test` — all Unit/ClassAppFkTest.php tests green
- [ ] `declare(strict_types=1)` + full type hints on all modified methods
- [ ] English PHPDoc on all public methods
- [ ] PHPStan level 6 — 0 violations on modified files
- [ ] No `MongoClient` / `MongoId` / `MongoRegex` / `MongoDate` in any modified file

## Risks
- `get_reverse_grille_fk()` queries multiple collections dynamically — may uncover untested `appscheme` metadata edge cases. Timebox to 1.5 days.
- Some `query()` call sites use `while ($row = $cursor->fetchRow())` pattern — ensure `typeMap` arrays make this safe before changing call sites.
