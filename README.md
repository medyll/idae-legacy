# Idae Legacy — Migration Project

A concise reference for the Idae-Legacy project: a migration of a legacy PHP 5.6 / Node.js / MongoDB CMS to modern tooling (PHP 8.2, modern MongoDB driver, Dockerized environment).

## Project Status
- Migration branch: `migration` (work in progress)
- Default branch: `main`
- Current scope: application boots, UI functional (Phase 1). Phase 2 modernization in progress.

See the migration planning and status files for details: [MIGRATION_PHASE_2.md](MIGRATION_PHASE_2.md), [MIGRATION_STATUS.md](MIGRATION_STATUS.md), [MIGRATION.md](MIGRATION.md).

## Goals
- Upgrade to PHP 8.2 and modern MongoDB driver
- Preserve backward compatibility with legacy UI while modernizing internals
- Add Docker-based development environment and repeatable tests

## Quick Start (development)
Prerequisites: Docker Desktop (Windows), Git.

1. Start the stack (from repository root):

```powershell
docker-compose up --build
```

2. Open the web container endpoints as configured in `docker-compose.yml` or check `idae/web/README.md` for web-specific notes.

## Running Tests
After the stack is up and services are available, run PHP test scripts located in `idae/web/`:

```powershell
php idae/web/test_migration.php
php idae/web/test_integration.php
```

## Important Conventions and Compatibility Notes
- Always use the compatibility wrapper in `MONGOCOMPAT.md` and helpers under `appclasses`/`appconf` for MongoDB types. Use `AppCommon\\MongoCompat::toObjectId()` and related helpers rather than legacy Mongo extension classes.
- Preserve original file headers (Date/Time) when modifying legacy files; add a `Modified: YYYY-MM-DD` line for major changes. See `AGENTS.md` for header rules.
- Use `error_log()` for server-side debugging — never echo debug output to the client (breaks AJAX responses).

## Useful Files and Locations
- App/web root: `idae/web/` — primary PHP application and tests.
- Node server: `idae/web/app_node/` — Socket.io and real-time services.
- Migration docs: [MIGRATION.md](MIGRATION.md), [MONGOCOMPAT.md](MONGOCOMPAT.md), [MIGRATION_PHASE_2.md](MIGRATION_PHASE_2.md).
- Docker scripts: `docker-restart.ps1`, `docker-health.ps1`, `docker-logs.ps1`, `docker-emergency.ps1`.

## Development Guidelines
- Prefer small, focused changes. Keep legacy behaviour intact unless explicitly modernizing.
- Add type hints and `declare(strict_types=1);` on new/updated PHP files where feasible.
- Use composer for PHP dependencies placed under `idae/web/composer.json` when applicable.

## Contributing
- Create feature branches from `migration` when working on modernization tasks.
- Open a PR targeting `migration` for review; include tests or a migration checklist when relevant.

## Troubleshooting
- Check PHP logs in the container (Apache/PHP error log) and Node logs under `idae/web/app_node/logs/`.
- If Mongo issues appear, review host connectivity — containers assume Mongo is reachable at `host.docker.internal` (see `DOCKER_SCRIPTS.md`).

## References
- [AGENTS.md](AGENTS.md) — agent and workspace rules
- [MONGOCOMPAT.md](MONGOCOMPAT.md) — Mongo compatibility helpers
- [DOCKER_SCRIPTS.md](DOCKER_SCRIPTS.md) — helper scripts and diagnostics

## Contact
If you need clarification about migration decisions, update the relevant MIGRATION docs or open an issue on the repository.

---
_This README is intended as a concise developer reference. For full details, consult the linked documentation files in the repository._
