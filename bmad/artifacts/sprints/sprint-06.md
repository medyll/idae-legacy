# Sprint 06 — CSRF, Input Validation & Bug Fixes

**Duration:** 2026-05-11 → 2026-05-22
**Capacity:** 12 dev-days (3 devs)
**Planned:** 12 points

## Sprint Goal
Implement CSRF protection on critical endpoints, complete input validation, and fix remaining bugs. Project is production-ready.

---

## Stories

| ID | Epic | Title | Points | Priority |
|---|---|---|---|---|
| S6-01 | Security | Implement CSRF token generation/validation on `actions.php` and `postAction.php` | 3 | Must |
| S6-02 | Security | Add input validation helpers with sanitization for common patterns (email, URL, int, bool) | 2 | Must |
| S6-03 | Security | Apply input validation to critical endpoints (login, user create, data export) | 2 | Should |
| S6-04 | BugFix | Fix BUG-02: CleanStr signature mismatch with broader testing | 2 | Should |
| S6-05 | QA | Browser smoke test: login → grid → CRUD (manual validation) | 2 | Must |
| S6-06 | Docs | Update README.md with security features and CSRF usage | 1 | Low |

**Total:** 12 points

---

## Dependencies
- S5-05 done (security dependencies updated)
- S5-07 done (all tests passing)

---

## Definition of Done
- [ ] CSRF tokens generated per session and validated on POST/PUT/DELETE
- [ ] Input validation helpers available in `AppCommon\InputValidator`
- [ ] Critical endpoints (login, user management) protected
- [ ] BUG-02 fixed with unit tests
- [ ] Browser smoke test passes (no console errors)
- [ ] README.md updated with security documentation

---

## Risks
- CSRF on legacy AJAX calls may break existing functionality — need graceful degradation
- CleanStr fix may have wide impact — test thoroughly before deploying
