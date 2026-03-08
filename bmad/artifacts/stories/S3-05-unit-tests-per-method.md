# S3-05 — Unit tests per method

Estimate: 3 points
Status: ready

Description:
Add unit tests for ClassApp CRUD-related methods (query, findOne, create_update, insert, update, remove). Tests should exercise both normal and error paths and use test fixtures where applicable. Ensure tests are isolated and do not rely on a running Docker stack (use mocks where reasonable) or mark them as integration tests when necessary.

Acceptance criteria:
- Tests located under idae/web/tests/Unit and follow existing test conventions.
- Positive and negative cases covered for each target method.
- Tests pass locally with vendor/bin/phpunit for Unit suite.
