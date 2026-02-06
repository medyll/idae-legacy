# Idae-Legacy AI Instructions (PHP 8.2 Migration)

**Context**: Migrating a legacy CMS from PHP 5.6/MongoV1 to PHP 8.2/MongoV1.8.
**Status**: Active Migration. See `MIGRATION.md` and `MONGOCOMPAT.md`.

## Key References
- **[MIGRATION.md](../MIGRATION.md)**: Current phase, blockers, and migration checklist.
- **[MONGOCOMPAT.md](../MONGOCOMPAT.md)**: API wrapper usages (`MongoCompat::toObjectId`, etc).
- **[AGENTS.md](../AGENTS.md)**: Coding standards and header preservation rules.

## Essentials
1.  **Architecture**: Schema-driven MVC. No SQL schema; definitions in generic `appscheme` collection.
    -   **ORM**: `App` class (handles DB) -> `AppSite` -> `ClassIdae`, `ClassAct`.
    -   **Data**: Dynamic fields `codeAppscheme_field` + table suffix (e.g., `nom` -> `nomProduit`).
2.  **Environment**: Docker (PHP 8.2 Container) <-> Host MongoDB (`host.docker.internal`).
    -   **Auth**: Requires `MDB_USER`/`MDB_PASSWORD` env vars for MongoDB.
3.  **Real-Time**: Node.js 18 + Socket.io. Server: `idae/web/app_node/idae_server.js`.
4.  **Permissions**: 3-tier (Agent -> Group -> Table Rights). Check `droit_table($idagent, 'R', 'table')`.

## Code Patterns
-   **Mongo Compatibility** (Critical):
    -   X `new MongoId($id)` -> V `MongoCompat::toObjectId($id)`
    -   X `new MongoRegex("/^t/i")` -> V `MongoCompat::toRegex("^t", "i")`
    -   X `new MongoDate()` -> V `MongoCompat::toDate()`
-   **File Headers**: Preserve original `Date:`/`Time:` headers. Add `Modified:` line for changes.
-   **Queries**: Use `class App` methods (`find`, `findOne`, `create_update`) which wrap the modern driver.

## Workflows
-   **Build**: `docker-compose up` (PHP container only).
-   **Logs**: `/var/log/apache2/php-error.log` inside container.
-   **API**: `POST /services/json_data.php` (Core data endpoint).
