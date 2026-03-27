# Sprint 05 – Modules, Security, Hardening & Regression

**Duration:** 2026-04-27 → 2026-05-08
**Capacity:** 15 dev-days (3 devs)
**Planned:** 15 points

## Sprint Goal
All `mdl/*` modules use full `<?php` open tags (PHP 8.2 compatible). CSRF and input validation are in place for `actions.php`/`postAction.php`. Full regression test suite passes. Project is shippable on the `migration` branch.

---

## Stories

| ID | Epic | Title | Points | Priority |
|---|---|---|---|---|
| S5-01 | Modules | Audit `mdl/*` for legacy patterns — produce fix list | 2 | Must |
| S5-02 | Modules | Fix 374 short open tags `<?` → `<?php` across `mdl/` | 3 | Must |
| S5-03 | Security | Add CSRF token generation + validation to `actions.php` and `postAction.php` | 3 | Must |
| S5-04 | Security | Remove `extract($_POST)` from `postAction.php` + input validation on key endpoints | 2 | Should |
| S5-05 | Security | Composer dependency audit (`composer audit`) + update critical packages | 1 | Should |
| S5-06 | BugFix | BUG-02 `CleanStr` broader testing + fix if still failing | 1 | Low |
| S5-07 | QA | Full regression: `composer test` suite pass + browser smoke test (login → grid load → CRUD) | 3 | Must |

**Total:** 15 points — full capacity sprint.

### Audit Findings (S5-01 completed 2026-03-15)
- **MongoId/MongoRegex/MongoDate in mdl/**: 0 occurrences — already migrated
- **Short open tags `<?` in mdl/**: 374 files — critical PHP 8.2 blocker
- **`array()` old syntax in mdl/**: 1,280 occurrences — style only, low priority
- **Error suppression `@` in mdl/**: 0 occurrences — clean
- **CSRF tokens**: missing from actions.php and postAction.php
- **`extract($_POST)`**: present in postAction.php line 24 — variable injection risk
- **CleanStr validation**: only trims whitespace and detects date patterns, no escaping

---

## Dependencies
- S4-01 → S4-05 done (ClassApp + services + socket fix)
- All prior PHPUnit tests must be green before S5-07

---

## Definition of Done
- [ ] `grep -rn '<?' idae/web/mdl/ | grep -v '<?php'` → 0 results
- [ ] CSRF tokens validated on all form/AJAX actions
- [ ] `extract($_POST)` removed from postAction.php
- [ ] `composer audit` — 0 critical vulnerabilities
- [ ] `composer test` — all Unit + Integration tests green
- [ ] Browser smoke test: login → load grid → create/edit/delete a record — no JS console errors

## Risks
- Bulk short-tag replacement may break files with `<?xml` declarations or inline `<?=` echo tags — need to preserve `<?=` (valid in PHP 8.2) and only replace bare `<?` followed by whitespace/newline.
- CSRF on legacy AJAX calls needs careful testing — some calls may use non-standard headers.
