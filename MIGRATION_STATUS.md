# Migration Status — 2026-02-06

## Overview
- **Environment**: Docker image now runs PHP 8.2 and connects to the host MongoDB instance via `host.docker.internal`. The MySQL container has been removed from `docker-compose.yml`.
- **Driver layer**: `MongoCompat` wrapper and the ClassApp connection layer remain in place; recent fixes removed duplicate GridFS declarations and a syntax error in `conf.lan.inc.php`.
- **Current blocker**: Application dies during PHP session garbage collection because the MongoDB delete command is executed without valid credentials (`BulkWriteException: command delete requires authentication`).

## Completed Since Last Checkpoint
- Docker stack simplified (only the PHP app container is built; all data services now expected to run on the host).
- `conf.lan.inc.php` braces fixed (duplicate `if (isset($hostConf['mdb']))` removed) to restore configuration loading.
- `MongoCompat` cleanup: removed duplicate `getGridFS()` declaration and added `__call()` forwarding for legacy names.
- `ClassApp::find()` patched to avoid double-wrapping cursors already returned by `MongodbCursorWrapper`.

## In Progress
- Wiring environment variables (`MDB_USER`, `MDB_PASSWORD`, `MDB_PREFIX`) so that the PHP container can authenticate to the host MongoDB instance.
- Continuing Phase 2 of the migration: CRUD helpers inside `ClassApp` (insert/update/remove/create_update) still rely on legacy driver semantics and need to be ported to the modern API.
- JSON service endpoints (`json_data.php`, `json_scheme.php`, `json_data_table.php`, etc.) have not been validated since the environment switch; testing is blocked by the authentication failure above.

## Blockers & Risks
1. **Mongo credentials missing** — no authenticated connection means every request aborts; fix is required before further migration work can be validated.
2. **Legacy CRUD paths** — until `insert/update/remove` are migrated, service endpoints may revert to deprecated behavior even after auth is fixed.
3. **No MySQL container** — any code paths that still expect the local `idae-mysql` service must be pointed to a host database manually.

## Next Steps
1. Provide valid MongoDB credentials to the container via `.env` or compose overrides and re-test startup to clear the `BulkWriteException`.
2. Resume Phase 2 migrations (CRUD helpers, cursor wrappers, FK helpers) once the stack can execute requests again.
3. Re-run `test_migration.php` and smoke-test `services/json_data.php` after CRUD helpers are updated.

## Status
**Overall**: _Blocked by Mongo authentication_. Migration work can continue once the container can write to MongoDB again.
