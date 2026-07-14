# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Context

Migration of a legacy PHP 5.6 / MongoDB v1.x / Node.js CMS ("Idae") to PHP 8.2, `mongodb/mongodb` v2+ library, and a Dockerized environment. The active branch is `migration`. The goal is backward-compatible modernization — preserve the legacy UI and behavior while updating internals.

## Development Environment

**Prerequisites**: Docker Desktop, Git.

Start the full stack (PHP + Apache on port 8080):
```bash
docker-compose up --build
```

Helper scripts (PowerShell):
- `docker-restart.ps1` — rebuild and restart container
- `docker-health.ps1` — check health endpoint
- `docker-logs.ps1` — tail Apache/PHP logs
- `docker-emergency.ps1` — hard reset

MongoDB is expected on the host at `host.docker.internal:27017`. Configure credentials via environment variables `MDB_USER`, `MDB_PASSWORD`, `MDB_PREFIX` or edit `idae/web/conf.lan.inc.php` for local LAN/dev.

## Running Tests

After the stack is running:
```bash
# Integration: tests the full request cycle (login, data endpoints)
php idae/web/test_migration.php
php idae/web/test_integration.php
```

For specific subsystem tests:
```bash
php idae/web/test_quick.php
php idae/web/test_minimal.php
php idae/web/test_mongodb_migration.php
```

## Node.js Socket Server

Located in `idae/web/app_node/`. Modernized from a monolithic script to a modular ESM structure.

```bash
cd idae/web/app_node
npm install
npm run dev    # development (nodemon)
npm start      # production (node src/main.js)
```

Structure: `src/config/`, `src/db/`, `src/services/`, `src/socket/`, `src/web/`.

## Architecture Overview

### PHP Application (`idae/web/`)

| Path | Role |
|------|------|
| `conf.inc.php` | Main bootstrap: environment detection by `HTTP_HOST`, defines all constants (`SITEPATH`, `APPPATH`, `MDB_*`, etc.), loads functions and autoloader. LAN/local config in `conf.lan.inc.php`. |
| `appclasses/` | PHP classes — `ClassApp.php` (core MongoDB ORM), `ClassSession.php`, `ClassAppSite.php`, `ClassMongoDb.php`, etc. |
| `appclasses/appcommon/` | `MongoCompat.php` (compatibility helpers), `ClassApp.php` (post-migration class). |
| `appconf/conf_init.php` | Registers all `appscheme*` collections and application metadata at startup. |
| `appfunc/function.php` | Global helpers including `droit_table()`, `droit_table_multi()`, `droit()` for authorization. |
| `services/json_*.php` | JSON API endpoints consumed by the frontend SPA (e.g. `json_data.php`, `json_scheme.php`, `json_data_table.php`, `json_data_search.php`). |
| `mdl/` | Module files — per-entity UI fragments loaded dynamically. |
| `tpl/` | Smarty/Latte templates. |
| `javascript/` | Frontend SPA assets (see below). |
| `actions.php` / `postAction.php` | AJAX action entry points. |

### Schema-Driven Architecture

The UI is not hardcoded. All entity/field definitions live in MongoDB `appscheme*` collections:

- `appscheme` — entity definitions (tables). Key field: `codeAppscheme` (e.g. `produit`).
- `appscheme_field` — reusable field catalog.
- `appscheme_has_field` — per-entity field binding.
- `appscheme_field_type` — type registry (text, number, date, prix, fk…).
- `appscheme_field_group` — UI grouping (identification, commercial…).
- `appscheme_type` — enumeration values when `hasTypeScheme` is set.

**Field naming rule**: stored field = `codeAppscheme_field` + `ucfirst(codeAppscheme)`.
Example: field `nom` + table `produit` → MongoDB field `nomProduit`.

Use `AppCommon\MongoCompat::toFieldName($code, $table)` to compute field names programmatically.

`services/json_scheme.php` assembles `fieldModel`, `columnModel`, `miniModel`, `defaultModel` JSON consumed by the frontend as `window.APP.APPSCHEMES`.

### Authorization Model

Three helpers in `appfunc/function.php`:
- `droit_table($idagent, $code, $table)` — checks single operation (`C`/`R`/`U`/`D`/`L`/`CONF`) for an agent on a table.
- `droit_table_multi($idagent, $code)` — returns list of permitted tables.
- `droit($code)` — checks app-level flag (`ADMIN`/`DEV`/`CONF`) on the agent record.

Data model: `agent` → `agent_groupe` → `agent_groupe_droit` (per-table boolean flags).

### Frontend SPA (`idae/web/javascript/`)

A 2015-era SPA built without bundlers:

- **`javascript/vendor/bag.js`** — custom asset loader; caches scripts as blobs in IndexedDB.
- **`javascript/main_bag.js`** — defines the dependency graph (`require_trame`) and drives sequential loading via `dyn_require()`.
- **`javascript/app/app_bootstrap.js`** — calls `schemeLoad()` to fetch schema JSON from PHP, populates `window.APP.APPSCHEMES` / `window.APP.APPFIELDS`.
- **PrototypeJS 1.7.3** (`require_hell` bundle) — extends native `Array`, `String`, `Element`. Cannot be replaced; patterns like `$A()`, `Class.create()`, `$('id')` are ubiquitous.
- **`app_cache.js`** — data/state cache via `localforage`. Call `app_cache_reset()` after schema changes to avoid stale client state.

Do not rewrite the loader or remove PrototypeJS. The JSON shape returned by `json_data.php` / `json_scheme.php` must remain structurally identical to legacy output.

## Critical Conventions

### MongoDB Migration

- **Always** use `AppCommon\MongoCompat` for MongoDB types — never instantiate `MongoId`, `MongoRegex`, `MongoDate` directly.
  - `MongoCompat::toObjectId($value)` — convert to `MongoDB\BSON\ObjectId`
  - `MongoCompat::toRegex($pattern, $flags)` — convert to `MongoDB\BSON\Regex`
  - `MongoCompat::toDate($value)` — convert to `\DateTime`
  - `MongoCompat::cursorToArray($cursor)` — normalize cursor to array
- Use `MongoDB\Client` (library) semantics, not `MongoClient` (extension).
- Most Idae tables use integer PKs (`idproduit`), not `_id` (ObjectId). Only convert actual `_id` fields.
- See `MONGOCOMPAT.md` and `idae/web/PHASE2_STRATEGY.md` for the `ClassApp.php` migration plan.

### Debugging

- **Never** echo or print debug output to the client. All HTML insertion is via AJAX — any stray text breaks responses.
- Use `error_log()` exclusively for server-side debug output.
- Check PHP errors: `docker-logs.ps1` or `docker exec idae-legacy tail -f /var/log/apache2/error.log`.

### File Headers

- Preserve original `Date:` / `Time:` comment headers in legacy files.
- Add a `Modified: YYYY-MM-DD` line for significant changes.
- All new comments and documentation must be in English.

### PHP Style (new/modified files)

- Add `declare(strict_types=1);` to new files where feasible.
- Replace `array()` with `[]`.
- Avoid `@` error suppression — use `try/catch`.
- Prefer `error_log()` over any form of display output.

### CSS Policy

- **New styles = native nested CSS**, loaded explicitly (`<link>` in `index.php` or the bag.js manifest). Do not add new partials to `appcss/scss/`.
- The existing SCSS tree (`appcss/scss/`) is legacy-frozen: maintain it, migrate partials to native `.css` opportunistically when touched, never grow it.
- `index.php` only loads `appcss/dist/main.css`. Rebuild with `npm run build:css` in `idae/web/app_node/` after any SCSS change. The build runs `check-scss-wiring.mjs` first and fails if a partial under `scss/` is not reachable from `main.scss` — this prevents partials from silently dropping out of the bundle.
- `css/windowGui/windowGui.css` is an orphan (referenced nowhere); the live source is `appcss/scss/app_window_gui/_app-window-gui.scss`.

## Key Reference Documents

- `MONGOCOMPAT.md` — MongoCompat API reference
- `SCHEMA.md` — schema-driven collections, naming conventions, JSON examples
- `SCHEMA-AUTH.md` — authorization model details
- `JS_STRUCTURE_LEGACY.md` — deep analysis of the frontend SPA architecture
- `MIGRATION_PHASE_2.md` — Phase 2 modernization inventory and plan
- `MIGRATION_STATUS.md` — last known migration status
- `idae/web/PHASE2_STRATEGY.md` — `ClassApp.php` step-by-step migration strategy
