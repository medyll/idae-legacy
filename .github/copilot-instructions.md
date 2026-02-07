
# Idae-Legacy AI Coding Agent Guide

**Context:** Migrating a legacy PHP 5.6/Node.js/MongoDB CMS to PHP 8.2, modern MongoDB driver, and Dockerized environment.  
**Status:** Phase 1 complete: the application boots and the UI is functional. Phase 2 modernization is in progress. See [MIGRATION.md](../MIGRATION.md), [MIGRATION_PHASE_2.md](../MIGRATION_PHASE_2.md), and [MONGOCOMPAT.md](../MONGOCOMPAT.md) for current blockers and compatibility wrappers.

## Architecture & Data Flow

- **Schema-driven MVC:** No SQL schema; all entity definitions in the `appscheme` MongoDB collection.  
    - **ORM:** `App` (DB access) → `AppSite` → domain models (`ClassIdae`, `ClassAct`, etc.)
    - **Dynamic fields:** Use `codeAppscheme_field` + table suffix (e.g., `nom` → `nomProduit`).
- **Real-time:** Node.js (18+) + Socket.io server (`idae/web/app_node/idae_server.js`) for live updates.
- **Permissions:** 3-tier (Agent → Group → Table). Use `droit_table($idagent, 'R', 'table')` for checks.
- **API:** All core data via `POST /services/json_data.php`.

## Critical Conventions

- **MongoDB Compatibility:**  
    - Always use `AppCommon\MongoCompat` for Mongo types:
        - `MongoCompat::toObjectId($id)` (never `new MongoId($id)`)
        - `MongoCompat::toRegex($pattern, $flags)` (never `new MongoRegex`)
        - `MongoCompat::toDate($value)` (never `new MongoDate`)
    - See [MONGOCOMPAT.md](../MONGOCOMPAT.md) for all helper methods and usage.
- **File Headers:** Preserve original `Date:`/`Time:` headers. Add `Modified: YYYY-MM-DD` for major changes.
- **Debugging:** Never use `echo`/`print` for debugging in client-facing code (breaks AJAX). Use `error_log()` only.
- **Queries:** Use `App` class methods (`find`, `findOne`, `create_update`)—these wrap the modern MongoDB driver.

## Developer Workflows

- **Build/Run:**  
    - `docker-compose up` (PHP 8.2 container, expects MongoDB on `host.docker.internal`)
    - Logs: `/var/log/apache2/php-error.log` (PHP), `app_node/logs/` (Node.js)
- **Restart/Debug:**  
    - Use PowerShell scripts:  
        - `docker-restart.ps1` (Apache/container restart)
        - `docker-health.ps1` (diagnostics)
        - `docker-emergency.ps1` (force reset)
        - `docker-logs.ps1` (log viewing)
    - See [DOCKER_SCRIPTS.md](../DOCKER_SCRIPTS.md) for details.
- **Testing:**  
    - Run `php test_migration.php` and `test_integration.php` after stack boots.
    - Validate API with cURL or browser against `/services/json_data.php`.

## Modernization (Phase 2)

- See [MIGRATION_PHASE_2.md](../MIGRATION_PHASE_2.md) for the full action plan and rationale.
- **Quick wins:**
    - Add `declare(strict_types=1);` and type hints to PHP files
    - Use short array syntax (`[]`)
    - Remove deprecated functions and error suppression
    - Move hardcoded config to files
    - Standardize on `error_log()` for logging
- **Deeper refactors:**
    - Composer autoloading, dependency injection
    - Refactor procedural scripts to class-based controllers
    - Modular CSS, responsive design, accessibility
    - Modernize API endpoints and validation
    - Add tests and CI/CD
- **All changes must:**
    - Use `MongoCompat` for all MongoDB types
    - Preserve file headers and comments (see [AGENTS.md](../AGENTS.md))
    - Maintain legacy compatibility

## Common Pitfalls

- **Field naming:** Always append table name (e.g., `nomProduit` not `nom`).
- **Type safety:** Use `MongoCompat::toIntSafe()` for IDs; avoid type juggling.
- **Session/auth:** Ensure `MDB_USER`/`MDB_PASSWORD` are set for MongoDB access.
- **Legacy code:** Many helpers still use v1 driver semantics—see [MIGRATION.md](../MIGRATION.md) for migration status.

## Key References

- [AGENTS.md](../AGENTS.md): Coding standards, header rules, and safety.
- [MIGRATION.md](../MIGRATION.md): Migration status, blockers, and checklist.
- [MIGRATION_PHASE_2.md](../MIGRATION_PHASE_2.md): Phase 2 modernization plan.
- [MONGOCOMPAT.md](../MONGOCOMPAT.md): MongoDB compatibility helpers.
- [DEBUGGING.md](../DEBUGGING.md): Safe debugging and troubleshooting.
- [idae/web/README.md](../../idae/web/README.md): Full architecture, workflows, and directory map.

---

**If any section is unclear or missing for your workflow, please specify so it can be improved.**
