# MongoDB Migration — Progress Briefing

**Date**: 2026-02-06  
**Status**: Phase 2 still in progress (connection layer stable, CRUD + services pending)

---

## Highlights since the last update
- Docker stack simplified: only the PHP (Apache) container is built, and it now connects to the host MongoDB instance via `host.docker.internal`. The MySQL service was removed entirely.
- `MongoCompat` enhancements landed (GridFS helper stabilized, duplicate `getGridFS` definition removed, cursor double-wrapping avoided in `ClassApp::find`).
- `conf.lan.inc.php` syntax issues fixed so the configuration loads again under PHP 8.2.
- Startup now fails loudly instead of silently: session garbage collection tries to delete records in MongoDB without credentials, surfacing the missing-auth problem immediately.

---

## Current position
- **Driver & helper layer**: `MongoCompat`, `MongodbCursorWrapper`, and the partially refactored `ClassApp` constructor/`plug()` continue to work when invoked in isolation.
- **Environment**: No embedded Mongo/MySQL containers remain; all data services must be reachable on the host. Credentials must be injected via environment variables.
- **Blocking issue**: The PHP container lacks `MDB_USER` / `MDB_PASSWORD` (or equivalent), so any request that touches MongoDB fails with `BulkWriteException: command delete requires authentication`.
- **Migration phase**: CRUD helpers (`insert`, `update`, `remove`, `create_update`) and the JSON services (`services/json_*.php`) still rely on legacy semantics and have not been validated on PHP 8.2.

---

## Next actions
1. **Provide Mongo credentials** so the container can authenticate (env vars or config entries).
2. **Finish refactoring the CRUD helpers** inside `ClassApp.php` to call `insertOne`, `updateOne/Many`, and `deleteOne/Many`.
3. **Smoke-test the services** (`test_migration.php`, `services/json_data.php`) once the stack boots without auth errors.
4. **Update validation docs** after the above succeeds; until then all “final” or “complete” reports remain inaccurate.

---

## Risks & watchpoints
- **Authentication**: Nothing else matters until Mongo credentials are wired correctly.
- **External dependencies**: Any path still assuming the removed MySQL container exists must be pointed to a host DSN.
- **Legacy calls**: Numerous `MongoId`/`MongoRegex` usages still depend on the compat layer; these must be audited after CRUD helpers are modernized.

---

**Takeaway**: Infrastructure groundwork is done, but functional validation is blocked by missing MongoDB authentication and unfinished CRUD refactors. Restore access to the database first, then continue with the remaining migration phases.
