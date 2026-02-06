# MongoDB Migration — Validation Report (On Hold)

**Date**: 2026-02-06  
**Status**: ⚠️ **NOT VALIDATED**

---

## Why validation is blocked
- The PHP container now connects to the host MongoDB instance, but no credentials are provided. Every request fails with `MongoDB\Driver\Exception\BulkWriteException: command delete requires authentication` when the session handler tries to purge expired sessions.
- Because the application cannot complete its bootstrap sequence, none of the planned HTTP or CLI validation scripts (`test_integration.php`, `check_mongo.php`, etc.) can run.
- CRUD helper methods inside `ClassApp.php` still use legacy driver semantics. Until they are refactored to `insertOne`, `updateOne`, `deleteOne`, etc., even a successful authentication would only move the failure point deeper into the stack.

---

## Completed checks
| Check | Result | Notes |
|-------|--------|-------|
| MongoDB client installation | ✅ Pass | `mongodb/mongodb` library installed; `MongoCompat` helper loads. |
| ClassApp constructor + `plug()` | ✅ Pass | Collections resolved via modern driver when invoked in isolation. |
| Application bootstrap | ❌ Fail | Aborts immediately due to missing Mongo credentials (session GC delete). |
| CRUD operations (`insert`, `update`, `remove`) | ❌ Not tested | Code still calls legacy driver APIs. |
| JSON service responses | ❌ Not tested | Blocked until the application boots. |

---

## What is needed before validation
1. **Configure Mongo credentials** for the container (environment variables or config files) so the session handler can authenticate.
2. **Finish the CRUD refactor** inside `ClassApp.php` to ensure all database operations use the modern driver correctly.
3. **Re-run smoke tests** (`test_migration.php`, cURL against `services/json_data.php`, browser login) once the stack can stay up.
4. **Update the validation scripts** (paths, assumptions) to match the new Docker layout without the MySQL/Mongo containers.

Only after these prerequisites are met can we produce a genuine validation report with pass/fail metrics.
