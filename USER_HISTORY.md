# User History System — IDAE

## Overview

5 collections track user activity across the app:

| Collection | DB | Purpose | Lifespan |
|------------|-----|---------|----------|
| `agent_history` | `sitebase_pref` | Recently viewed items (per record) | Persistent |
| `activity` | `sitebase_base` | Global action log (CRUD, views) | Persistent |
| `activity_code` | `sitebase_base` | Dictionary of valid activity codes | Static reference |
| `activity_expl` | `sitebase_base` | Explorer navigation history | Persistent |
| `data_activity` | `sitebase_sockets` | Real-time SSE event bus | ~30 seconds |
| `agent_recherche` | `sitebase_pref` | Search query history | Persistent |

---

## Data Flow

```
User action (view/create/update/delete/form/search)
    │
    ├─ set_log() ──────────→ agent_history (upsert, $inc quantite)
    │                    └─→ activity (upsert, 5-min bucket, $inc nb)
    │
    ├─ postAction.php ─────→ activity (global hook, any F_action)
    │
    ├─ set_hist() ─────────→ activity_expl (UID-deduped navigation)
    │
    ├─ json_data_search ───→ agent_recherche (search query + count)
    │
    └─ skelMdl::send_cmd() → data_activity (ephemeral SSE push)
```

---

## 1. `agent_history` — Recently Viewed

**Schema:**
```
{
  idagent_history: int,          // PK auto-increment
  idagent: int,
  codeAgent_history: string,     // collection code (e.g. 'client')
  valeurAgent_history: int,      // record ID viewed
  nomAgent_history: string,      // record name (denormalized, lowercased)
  quantiteAgent_history: int,    // view count ($inc on each view)
  timeAgent_history: int,        // last view timestamp
  dateAgent_history: string,     // 'YYYY-MM-DD'
  heureAgent_history: string     // 'HH:MM:SS'
}
```

**Write:** `ClassApp::set_log(idagent, table, table_value, log_type)`
- Called from 15+ modules: fiche views, create, update, delete
- Upserts on `{idagent, codeAgent_history, valeurAgent_history}`
- Increments `quantiteAgent_history`
- Reloads right panel: `skelMdl::reloadModule('app/app_gui/app_gui_panel', table)`

**Read:**
- `app_gui_panel.php` — right sidebar, sorted by `quantiteAgent_history` desc (15 rows)
- `app_explorer_home_entete_last.php` — 5 most recent (date/time desc)
- `app_explorer_home_entete.php` — 5 most consulted (quantite desc)
- Orphan cleanup: deletes entries when referenced record no longer exists

---

## 2. `activity` — Global Action Log

**Schema:**
```
{
  codeActivite: string,     // 'CLIENT_FICHE', 'ADD_DOC', 'QUITTER', etc.
  timeActivite: int,        // rounded to 5-min bucket
  dateActivite: string,     // 'YYYY-MM-DD'
  heureActivite: string,    // 'HH:MM:SS'
  idagent: int,
  table: string,            // target collection
  table_value: int,         // target record ID
  nb: int,                  // action count in this time bucket
  // optional context fields from POST:
  iddevis, idclient, idproduit, idfournisseur
}
```

**Write path A — `set_log()`:**
```php
$upd['codeActivite'] = strtoupper($table) . '_' . strtoupper($log_type);
// CLIENT_FICHE, CLIENT_CREATE, CLIENT_UPDATE, CLIENT_DELETE
```

**Write path B — `postAction.php` (global hook):**
```php
$upd['codeActivite'] = strtoupper($F_action);  // ADD_DOC, SETWALLPAPER, etc.
// Excludes 'POLL' actions (noise reduction)
```

**5-minute bucketing:**
```php
$rounded_time = (int)(round(time() / 300) * 300);
// Actions within same 5-min window merge into one doc with $inc nb
```

**Read:** `app_gui_activity.php` — top 15 most-acted records per table (30-day window)

---

## 3. `activity_code` — Code Dictionary

**Schema:** `{ _id: ObjectId, codeActivite: string }`

Static reference listing all valid activity codes. Not read by PHP code — serves as documentation/validation.

**Observed codes:**
| Pattern | Examples |
|---------|----------|
| `OUVERTURE_{ENTITY}_FICHE` | `OUVERTURE_CLIENT_FICHE`, `OUVERTURE_DEVIS_FICHE` |
| `{ENTITY}_UPDATE` | `DEVIS_UPDATE`, `PRODUIT_UPDATE` |
| `CREATION_{ENTITY}` | `CREATION_CLIENT` |
| `SUPPRESSION_{ENTITY}` / `DELETE{ENTITY}` | `SUPPRESSION_DEVIS`, `DELETENOTE` |
| `ENVOI_{ACTION}` / `SEND{ENTITY}` | `ENVOI_MAIL_DEVIS`, `SENDDEVIS` |
| `ADD{ACTION}` | `ADDDOC`, `CREATEIMAGE` |
| `SET{ACTION}` | `SETWALLPAPER` |
| Auth | `IDENTIFICATIONAGENT`, `QUITTER` |

---

## 4. `activity_expl` — Explorer Navigation

**Schema:**
```
{
  idactivity_expl: int,
  uid: string,                    // unique ID for dedup
  idagent: int,
  nomActivity_expl: string,       // 'client statut' (table + groupBy)
  vars: object,                   // full navigation context
  dateCreationActivity_expl: string,
  heureCreationActivity_expl: string,
  timeCreationActivity_expl: int
}
```

**Write:** `ClassApp::set_hist(idagent, vars)` — upserts by `uid`
**Read:** `app_gui_activity_expl.php` — last 1000 navigation events (15-day window)

---

## 5. `data_activity` — Real-Time SSE Bus

**Schema:**
```
{
  timeData_activity: int,         // event timestamp
  codeData_activity: string,      // event category
  eventData_activity: string,     // SSE event name
  varsData_activity: object,      // navigation context
  dataData_activity: mixed,       // payload
  table: string,
  table_value: int
}
```

**Server:** `services/json_data_event.php` — long-polling SSE endpoint
- Purges events older than 30 seconds
- Sends `act_upd_data` (update) and `act_add_data` (create) events
- Currently disabled (`die()` on line 5)

**Client:** `javascript/app/app_sse.js` — `EventSource` listener
- `act_upd_data` → calls `act_upd_data(new_data)` to refresh UI
- `act_add_data` → calls `act_add_data(new_data)` to insert records

**Connection:** `ClassApp::$APPCACHE` points to this collection — any code can write events.

---

## 6. `agent_recherche` — Search History

**Schema:**
```
{
  idagent_recherche: int,
  idagent: int,
  codeAgent_recherche: string,    // search query text
  nomAgent_recherche: string,     // duplicate of query
  valeurAgent_recherche: int,     // incremented on each search
  quantiteAgent_recherche: int,   // result count
  dateCreationAgent_recherche: string,
  heureCreationAgent_recherche: string,
  timeAgent_recherche: int
}
```

**Write:** `json_data_search.php` — creates entry per search with result count
**Read:** `app_gui_start_search_last.php` — two modes:
- `last` — sorted by date desc (most recent)
- `more` — sorted by `valeurAgent_recherche` desc (most frequent)
- 30-day lookback, paginated, with delete button

---

## Key Patterns

### Time Strategies
| System | Precision | Dedup |
|--------|-----------|-------|
| `agent_history` | Exact timestamp | Per (agent, table, record) |
| `activity` | 5-min bucket | Per (action, time bucket, agent) |
| `activity_expl` | Exact timestamp | Per `uid` |
| `data_activity` | Exact timestamp | None (ephemeral) |
| `agent_recherche` | Exact timestamp | Per search query |

### Excluded Tables
Internal tables skip logging: `agent_tuile`, `agent_activite`, `agent_history`, `agent_table`, `agent_recherche`, `agent_liste`

### Known Issues
1. **No purging** — all persistent collections grow unbounded
2. **Lazy orphan cleanup** — only runs when panel renders
3. **Stale names** — `nomAgent_history` captured at write time, not updated on record rename
4. **Sort field mismatch** — `activity_expl` sorts by `heureActivite` but field is `heureCreationActivity_expl`
5. **Legacy `update()`** — `postAction.php` uses old driver method vs `updateOne()` elsewhere
6. **Redundant fields** — `agent_recherche` stores query text in both `codeAgent_recherche` and `nomAgent_recherche`
