Title: Integration S4-02 — JsonData Regression
Created: 2026-03-11T12:00:00Z
Participants: bmad-master, dev-team, tester

Summary:
- `bmad-master next --auto` initiated the integration test task S4-02.
- Goal: verify `services/json_data.php` JSON shapes for `produit`, `agent`, `appscheme` against seeded fixtures.

Action Items:
- Tester: run `docker-compose up` (app + mongo-test) and `composer test` in `idae/web/`.
- Dev: ensure `services/json_data.php` includes are compatible with `tests/Integration/conf_test.php` stubs.
- Dev: if tests fail due to missing migrations, open PR and reference this thread.

Status: open
