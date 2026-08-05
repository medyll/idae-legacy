# Schema — Authentication & Authorization

This file documents the authorization model used by the Idae system: Agent → Agent Group → Group Rights (per table). It includes the main helper semantics, representative sample documents, and a compact Mermaid diagram.

## OPEN TODO — read paths are not access-controlled

**Status: open as of 2026-08-05. Anyone touching `services/json_data*.php` should read this first.**

Write paths are gated: `app_create` / `app_update` / `app_delete` / `app_multi_delete` in
`idae/web/bin/classes/ClassAction.php` and `services/json_action.php` all call
`droit_table_enforce()`, and both action dispatchers enforce CSRF.

**Read paths are not.** These endpoints return records for any `table` the caller names, with
no rights check at all — an authenticated agent can read every collection regardless of its
`agent_groupe_droit` rows:

| Endpoint | Notes |
|---|---|
| `services/json_data.php` | main read path for the SPA and the WebMCP `idae-query-entity` tool |
| `services/json_data_table.php` | grid data |
| `services/json_data_search.php` | cross-table search |
| `services/json_data_row.php`, `json_data_table_row.php` | single-row fetches |
| `services/json_scheme.php` | exposes the full entity/field catalogue |

The `R` filter applied by the WebMCP bridge (`javascript/app/app_webmcp.js`, fed by
`services/json_droit_table.php`) is **cosmetic** — it only stops the model from proposing calls;
a direct POST still reads anything.

### What the fix looks like

Add at the top of each endpoint, right after `$table` is read:

```php
if (!droit_table_enforce('R', $table)) {
    echo json_encode([]);   // or 403 — pick one shape and apply it everywhere
    return;
}
```

`droit_table_enforce()` already exists in `appfunc/function.php` and handles the
no-session / ADMIN / unconfigured-table cases.

### Why it was not done in the same pass

`json_data.php` is the read path of the entire SPA. Turning rights on there changes what every
existing screen displays for non-admin agents, and no agent group other than the admin one has
been exercised since the PHP 8 migration. It needs its own pass with a non-admin test account
and a screen-by-screen check — not a drive-by edit.

---

## Key runtime helpers

- `droit_table_enforce($code, $table)` — **the one to call from server-side entry points.** Wraps
  `droit_table()` with the rules: no authenticated agent → deny; app-level `ADMIN` or `DEV` → allow;
  table never declared in `agent_groupe_droit` → allow (unconfigured internal tables such as
  `agent_tuile`); otherwise `droit_table()`. Denials are logged via `error_log('[droit] …')`.
- `droit_table($idagent, $code, $table)` — checks whether an agent (by `idagent`) has the operation `code` (single letter: `C`, `R`, `U`, `D`, `L`, `CONF`, ...) on `table`. Implementation: find agent by `idagent` → read `idagent_groupe` → query `agent_groupe_droit` for `idagent_groupe` + `codeAppscheme` == `table` with the requested `code` flag true. (See `idae/web/appfunc/function.php`.)
- `droit_table_multi($idagent, $code, $table)` — returns a single permitted table name or a list of permitted tables for that group, depending on `table` param.
- `droit($code)` — app-level check against `agent.droit_app.<CODE>` on the agent record (ADMIN/DEV/CONF, etc.).

## Data model (conceptual)

- `agent` — user record; key fields:
  - `idagent` (int)
  - `idagent_groupe` (int) — link to group
  - `login`, `prenomAgent`, `nomAgent`, `droit_app` (map of app-level flags)

- `agent_groupe` — group record; key fields:
  - `idagent_groupe`, `codeAgent_groupe`, `nomAgent_groupe`

- `agent_groupe_droit` — group→table rights; each document associates a group with a table and per-operation booleans.
  - Example boolean fields: `C`, `R`, `U`, `D`, `L`, `CONF` (stored as true/false for that row)
  - `codeAppscheme` indicates the target table/collection.

## Representative sample documents

Example `agent` document (representative JSON):

```json
{
  "idagent": 101,
  "loginAgent": "j.doe",
  "prenomAgent": "John",
  "nomAgent": "Doe",
  "idagent_groupe": 1,
  "droit_app": { "ADMIN": 0, "DEV": 0, "CONF": 1 }
}
```

Example `agent_groupe` document:

```json
{
  "idagent_groupe": 1,
  "codeAgent_groupe": "ADMIN",
  "nomAgent_groupe": "Administrators"
}
```

Example `agent_groupe_droit` documents (three sample rows):

```json
{
  "idagent_groupe_droit": 10,
  "idagent_groupe": 1,
  "codeAppscheme": "produit",
  "C": true,
  "R": true,
  "U": true,
  "D": true,
  "L": true,
  "CONF": true
}

{
  "idagent_groupe_droit": 11,
  "idagent_groupe": 2,
  "codeAppscheme": "commande",
  "C": false,
  "R": true,
  "U": false,
  "D": false,
  "L": true,
  "CONF": false
}

{
  "idagent_groupe_droit": 12,
  "idagent_groupe": 3,
  "codeAppscheme": "client",
  "C": true,
  "R": true,
  "U": true,
  "D": false,
  "L": true,
  "CONF": false
}
```

## Typical check flow (conceptual)

1. Agent logs in; server sets session with `$_SESSION['idagent']`.
2. Before acting on a table, code calls `droit_table($_SESSION['idagent'], 'R', 'produit')` (or other code). 
3. `droit_table` resolves agent → group → `agent_groupe_droit` row and returns true/false.

## Where to look in the codebase

- Helper definitions: `idae/web/appfunc/function.php` (`droit_table`, `droit_table_multi`, `droit`).
- Scheme registration: `idae/web/appconf/conf_init.php` registers `agent_groupe_droit` among other schemes.
- Uses: many controllers/templates guard access with `droit_table(...)` — examples: `idae/web/services/json_data_table.php`, `idae/web/services/json_data_search.php`, `idae/web/mdl/*` (menus, create buttons, config).

## Diagram (Agent → Group → Rights → Table)

```mermaid
flowchart LR
  A[Agent record]
  G[Agent Group]
  GR[Agent Group Rights]
  T[App Scheme (table)]

  A -->|belongs to idagent_groupe| G
  G -->|has many| GR
  GR -->|grants operations on| T
```

## Examples: using the helpers

- Check read permission for current session on `produit`:

```php
if (droit_table($_SESSION['idagent'], 'R', 'produit')) {
  // allowed
} else {
  // denied
}
```

- List permitted tables for a group (via `droit_table_multi`):

```php
$allowed = droit_table_multi($_SESSION['idagent'], 'R');
if ($allowed === false) {
  // no access
} else {
  // $allowed is array of table codes allowed for R
}
```

---

File created automatically by the assistant to document auth schema and examples.
