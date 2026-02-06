# MongoDB Migration — Status Brief (February 2026)

> **Important:** The migration is **not** complete. The stack does not boot successfully yet because the PHP container cannot authenticate to the host MongoDB instance. This document summarizes the real state of work so far and the gaps that remain before any "final" report can be produced.

---

## Current Status
- **Environment**: Docker now builds only the PHP 8.2/Apache container. MongoDB and MySQL are expected to run on the host; `host.docker.internal` is used for Mongo, and the MySQL service was removed.
- **Driver Layer**: `mongodb/mongodb` (1.8.0) is installed alongside the `MongoCompat` helper and the cursor wrapper. The connection layer inside `ClassApp` was partially refactored (constructor + `plug()`), but CRUD helpers still call legacy-style methods.
- **Runtime**: Every web request aborts at startup. PHP session garbage collection issues a `delete` command against MongoDB but no credentials are provided, resulting in `MongoDB\Driver\Exception\BulkWriteException: command delete requires authentication`.
- **Validation**: No integration or functional tests have passed since the migration branch started; the application has never completed a successful boot on this stack.

---

## Work Completed
1. **Infrastructure clean-up**
   - Docker Compose simplified to a single `app` service; Mongo/MySQL containers removed.
   - PHP image upgraded to 8.2 with the MongoDB extension installed via PECL.
2. **Compatibility Helpers**
   - `MongoCompat.php` implements ObjectId/Regex/Date conversions plus GridFS helpers.
   - `MongodbCursorWrapper.php` added to emulate the legacy `getNext()` cursor API.
3. **ClassApp groundwork**
   - Constructor now uses `MongoDB\Client` with a type-map forcing array returns.
   - `plug()` / `plug_base()` obtain collections/databases with the modern driver.
4. **Bug fixes**
   - Duplicate `getGridFS` method removed from `MongoCompat`.
   - Syntax error in `conf.lan.inc.php` fixed (duplicate `if (isset($hostConf['mdb']))`).

---

## Outstanding Work
| Area | Status | Notes |
|------|--------|-------|
| Mongo authentication | ❌ Failing | Need valid `MDB_USER` / `MDB_PASSWORD` exposed to the container (or disable auth on host) before any further testing. |
| ClassApp CRUD helpers (`insert`, `update`, `remove`, `create_update`) | ⏳ Legacy code | Still use v1 semantics and must be rewritten to call `insertOne`, `updateOne/Many`, `deleteOne/Many`, etc. |
| JSON services (`services/json_*.php`) | ⏳ Untested | Blocked until the stack can reach MongoDB with credentials. |
| Validation scripts (`test_integration.php`, `check_mongo.php`) | ⏳ Stale | Written for the old Docker layout; need adjustments once the app can boot. |
| Documentation | ⚠️ Outdated | Previous reports claimed completion; they have been replaced with this in-progress status. |

---

## Next Steps
1. **Provide Mongo credentials**: Set `MDB_USER`, `MDB_PASSWORD`, and (if needed) `MDB_PREFIX` either in `.env` or directly in `docker-compose.yml` so the PHP session handler can authenticate.
2. **Finish Phase 2 refactor**: Update all CRUD helpers inside `ClassApp` to use the modern driver methods and ensure they still respect legacy calling conventions.
3. **Smoke-test services**: Once the stack boots, run `test_migration.php` plus basic `services/json_data.php` calls to confirm responses match expectations.
4. **Rebuild validation plan**: Only after CRUD + services pass can we talk about a final validation report. Until then, treat this document as a living WIP summary.

---

If you need a "final" migration report for release notes, postpone it until the application starts successfully, CRUD paths are validated, and authentication is wired correctly. For now, this file intentionally documents that the migration is **incomplete**.
