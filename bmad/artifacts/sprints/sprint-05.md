# Sprint 05 – Modules, Security, Hardening & Regression

**Duration:** 2026-04-27 → 2026-05-08
**Capacity:** 15 dev-days (3 devs)
**Planned:** 15 points

## Sprint Goal
All `mdl/*` modules are free of legacy Mongo patterns. CSRF and input validation are in place. Full regression test suite passes. Project is shippable on the `migration` branch.

---

## Stories

| ID | Epic | Title | Points | Priority |
|---|---|---|---|---|
| S5-01 | Modules | Audit `mdl/*`: identify all remaining `MongoId`/`MongoRegex`/`MongoDate`/`fetchRow()` usages — produce fix list | 2 | Must |
| S5-02 | Modules | Apply `MongoCompat` fixes across `mdl/*` + `cursorToArray` where needed | 3 | Must |
| S5-03 | Security | Add CSRF token generation + validation to `actions.php` and `postAction.php` | 3 | Must |
| S5-04 | Security | Input validation audit on key endpoints + fix critical issues | 2 | Should |
| S5-05 | Security | Composer dependency audit (`composer audit`) + update critical packages | 1 | Should |
| S5-06 | BugFix | BUG-02 `CleanStr` broader testing + fix if still failing | 1 | Low |
| S5-07 | QA | Full regression: `composer test` suite pass + browser smoke test (login → grid load → CRUD) | 3 | Must |

**Total:** 15 points — full capacity sprint.

---

## Dependencies
- S4-01 → S4-04 done (ClassApp + services modernized)
- All prior PHPUnit tests must be green before S5-07

---

## Definition of Done
- [ ] `grep -r "MongoId\|MongoRegex\|MongoDate\|MongoClient" idae/web/` → 0 results (outside `less/` folder)
- [ ] CSRF tokens validated on all form/AJAX actions
- [ ] `composer audit` — 0 critical vulnerabilities
- [ ] `composer test` — all Unit + Integration tests green
- [ ] Browser smoke test: login → load grid → create/edit/delete a record — no JS console errors
- [ ] `app_cache_reset()` tested after schema change — SPA reloads correctly
- [ ] PHPStan level 6 — 0 violations on entire `migration` branch diff vs `main`

## Risks
- `mdl/*` audit scope is unknown until S5-01 runs. If > 20 files have legacy patterns, split S5-02 into two stories and push lowest-priority modules to a Sprint 06 if needed.
- CSRF on legacy AJAX calls needs careful testing — some calls may use non-standard headers.
