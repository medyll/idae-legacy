# S3-06 — json_data.php regression test

Estimate: 3 points
Status: ready

Description:
Add an automated regression test for key JSON endpoints (services/json_data.php) to validate read/write flows used by the SPA. The test should perform a login, fetch a sample grid, perform a create/update/delete cycle on a safe test collection, and validate responses and schema.

Acceptance criteria:
- Regression test script available (php or PHPUnit integration) under idae/web/tests/Integration or idae/web/tests/Regression.
- Test can be run against the Docker stack and documents any required env variables.
- No production data modified; use test prefix/fixture collection.
