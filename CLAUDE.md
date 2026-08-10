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

### Never browse the app on `localhost` (Windows + WSL2)

Use **`http://127.0.0.1:8080`** — or a hosts-file name such as
`http://tactac.idae.preprod.lan:8080` / `http://maw.idae.preprod.lan:8080`.
Never `http://localhost:8080`. This applies to the browser, to Playwright, and
to any manual `curl`.

Why: Windows resolves `localhost` to `::1` before `127.0.0.1`, and since the
move to WSL2 nothing answers there. Measured 2026-08-10:
`curl --ipv6 http://localhost:3005/health` → **21.05s then code 000**;
`--ipv4` → **200 in 3ms**. The browser hides this for plain HTTP (Happy
Eyeballs falls back in ~250ms), but not for socket.io: the connection dies,
reconnects with a new sid every second, and the server answers each in-flight
ack to a connection that no longer exists.

`*.lan` names are immune because the Windows hosts file is IPv4-only by
construction — which is why this never showed up back when the app was browsed
on `idaenext.idae.lan` and appeared the day someone typed `localhost`.

Not fixable from the app: the socket.io host is derived from `document.domain`,
which is correct. Rewriting the socket host without rewriting the page would
split the cookie jar (`localhost` and `127.0.0.1` are different hosts),
`PHPSESSID` would not follow, and `json_ssid` would report a mismatch on every
boot — a login loop instead of a socket loop.

`docker-compose.yml` publishes both `0.0.0.0` and `[::]` for ports 8080/3005.
The `[::]` half is **inert under Docker Desktop + WSL2 mirrored networking**
(`docker port` reports it, the host has no listener, `curl --ipv6` still times
out). It is kept because it is correct on a Linux host. It is not the fix here.

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

Helpers in `appfunc/function.php`:
- `droit_table_enforce($code, $table)` — **call this from server-side entry points.** Wraps `droit_table()` with the no-session / ADMIN-DEV / unconfigured-table rules.
- `droit_table($idagent, $code, $table)` — checks single operation (`C`/`R`/`U`/`D`/`L`/`CONF`) for an agent on a table.
- `droit_table_multi($idagent, $code)` — returns list of permitted tables.
- `droit($code)` — checks app-level flag (`ADMIN`/`DEV`/`CONF`) on the agent record.

Write paths (`ClassAction` CRUD, `services/json_action.php`) are gated and CSRF-protected.
**Read paths are not** — `services/json_data*.php` and `json_scheme.php` still serve any table
to any authenticated agent. Open TODO, details and fix recipe in `SCHEMA-AUTH.md`.
Mutating endpoints must carry `_csrf` (`window.APP.CSRF_TOKEN`, or the `X-CSRF-Token` header).

Data model: `agent` → `agent_groupe` → `agent_groupe_droit` (per-table boolean flags).

### Frontend SPA (`idae/web/javascript/`)

A 2015-era SPA built without bundlers:

- **`javascript/vendor/bag.js`** — custom asset loader; caches scripts as blobs in IndexedDB.
- **`javascript/main_bag.js`** — defines the dependency graph (`require_trame`) and drives sequential loading via `dyn_require()`.
- **`javascript/app/app_bootstrap.js`** — calls `schemeLoad()` to fetch schema JSON from PHP, populates `window.APP.APPSCHEMES` / `window.APP.APPFIELDS`.
- **`require_hell` bundle** — as of `feat/idae-be-migration` Phase 4 (`41d3985`), this is `@medyll/idae-be` (`javascript/vendor/idae-be/idae-be.iife.js`) plus 7 compatibility shims (`javascript/vendor/idae-be-shim/shim-*.js`), **not PrototypeJS/Scriptaculous** — those were swapped out and removed. The shims exist to keep the ~1,667 `$()` / ~2,074 `Element.*` / `Class.create` / `Ajax.*` / `Effect.*` call sites working unmodified (patterns like `$A()`, `Class.create()`, `$('id')` are still ubiquitous in app code — only their implementation changed). Full plan, phase checklist, and what's still native-Prototype-shaped vs. migrated: `BE_PLAN.md` at the repo root. Do not reintroduce `vendor/prototype/` or `vendor/scriptaculous/` — they were deleted in Phase 3 as dead weight once the shim covered their surface.
- **`app_cache.js`** — data/state cache via `localforage`. Call `app_cache_reset()` after schema changes to avoid stale client state.

The loader (`bag.js`/`main_bag.js`) itself is stable and should not be rewritten — only its `require_hell` payload changed. The JSON shape returned by `json_data.php` / `json_scheme.php` must remain structurally identical to legacy output.

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
