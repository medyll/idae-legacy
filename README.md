# Idae Legacy — Migration Project

A concise reference for the Idae-Legacy project: a migration of a legacy PHP 5.6 / Node.js / MongoDB CMS to modern tooling (PHP 8.2, modern MongoDB driver, Dockerized environment).

## Project Status
- All work lands on `main`; feature branches (e.g. `migration-sprint-6`, `s3-04/refactor-classapp-crud`) are merged via PR and deleted afterward.
- Current scope: application boots, UI functional (Phase 1). Phase 2 modernization in progress — core CRUD (`ClassApp`, `ClassAppAgg`), session handling, and Mongo driver migration are underway across several sprints.

See the migration planning and status files for details: [MIGRATION_PHASE_2.md](MIGRATION_PHASE_2.md), [MIGRATION_STATUS.md](MIGRATION_STATUS.md).

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
PHPUnit is the primary suite (config in `idae/web/phpunit.xml`, tests under `idae/web/tests/`):

```powershell
cd idae/web
composer install
composer test
```

There are also standalone ad-hoc PHP scripts and a Playwright suite for UI/CRUD smoke checks:

```powershell
php idae/web/test_migration.php
php idae/web/test_integration.php

cd playwright
npm install
npx playwright test
```

## Important Conventions and Compatibility Notes
- Always use the compatibility wrapper in `MONGOCOMPAT.md` and helpers under `appclasses`/`appconf` for MongoDB types. Use `AppCommon\\MongoCompat::toObjectId()` and related helpers rather than legacy Mongo extension classes.
- Preserve original file headers (Date/Time) when modifying legacy files; add a `Modified: YYYY-MM-DD` line for major changes. See `AGENTS.md` for header rules.
- Use `error_log()` for server-side debugging — never echo debug output to the client (breaks AJAX responses).

## Useful Files and Locations
- App/web root: `idae/web/` — primary PHP application, `composer.json`, and tests.
- Node server: `idae/web/app_node/` — Socket.io and real-time services.
- Playwright suite: `playwright/` — smoke, CRUD, and UI/UX browser tests.
- Migration docs: [MONGOCOMPAT.md](MONGOCOMPAT.md), [MIGRATION_PHASE_2.md](MIGRATION_PHASE_2.md), [MIGRATION_STATUS.md](MIGRATION_STATUS.md).
- Docker scripts: `docker-restart.ps1`, `docker-health.ps1`, `docker-logs.ps1`, `docker-emergency.ps1`.

## Development Guidelines
- Prefer small, focused changes. Keep legacy behaviour intact unless explicitly modernizing.
- Add type hints and `declare(strict_types=1);` on new/updated PHP files where feasible.
- Use composer for PHP dependencies placed under `idae/web/composer.json` when applicable.

## Contributing
- Create feature branches from `main` when working on modernization tasks.
- Open a PR targeting `main` for review; include tests or a migration checklist when relevant.

## Troubleshooting
- Check PHP logs in the container (Apache/PHP error log) and Node logs under `idae/web/app_node/logs/`.
- If Mongo issues appear, review host connectivity — containers assume Mongo is reachable at `host.docker.internal`, and the PHPUnit suite uses the isolated `mongo-test` service instead.

## References
- [AGENTS.md](AGENTS.md) — agent and workspace rules
- [MONGOCOMPAT.md](MONGOCOMPAT.md) — Mongo compatibility helpers
- `docker-restart.ps1`, `docker-health.ps1`, `docker-logs.ps1`, `docker-emergency.ps1` — helper scripts and diagnostics

## Contact
If you need clarification about migration decisions, update the relevant MIGRATION docs or open an issue on the repository.

---
_This README is intended as a concise developer reference. For full details, consult the linked documentation files in the repository._
