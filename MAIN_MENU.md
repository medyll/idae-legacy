# Main Menu Architecture — IDAE Application

## Overview

The main menu system is a multi-panel, modular PHP/JavaScript interface built on a custom module loading framework (`skelMdl::cf_module`). It is composed of a root container (`app_gui_main`) that orchestrates several sub-modules: a task bar, a start menu overlay, a collapsible side menu, a desktop area, and a right-side panel. All modules live under:

```
idae/web/mdl/app/app_gui/
```

---

## Module Hierarchy (Tree)

```
app_gui_main.php                          ← Root container
│
├── TaskBar (inline, line 27-82)
│   ├── Toggle menu visibility (caret-left/right)
│   ├── Waffle button → opens gui_pane (start menu overlay)
│   ├── Email count badge
│   ├── Agent name + context menu (reload, quit)
│   ├── Cache toggle (DEV only)
│   └── Socket status indicators
│
├── gui_pane (deferred load, line 83-84)
│   └── app_gui_start.php                 ← Start menu overlay (waffle)
│       ├── Search form + BuildSearch instance
│       │   └── app_gui_start_search_last.php   ← Recent/frequent searches
│       │
│       ├── Left pane (50%) — app_gui_start_menu.php
│       │   ├── Loops appscheme_type → appscheme collections
│       │   ├── Each item → app_gui_start_menu_launch.php (sub-menu)
│       │   ├── "All types" button → app_gui_start_menu_launch_all.php
│       │   ├── Production link → app_gui_production.php
│       │   ├── Administration link (ADMIN only)
│       │   └── app_gui_tile_user.php (settings gear icon)
│       │
│       └── Right pane (50%) — app_gui_today.php
│           ├── Planning taches (calendar link)
│           ├── app_gui_today_create.php    ← Quick-create buttons
│           ├── app_gui_today_link.php      ← Quick-access list links
│           ├── Mails link (webmail)
│           └── app_gui_today_echeancier.php ← Schedule/timeline
│
├── gui_menu (collapsible left sidebar, line 87-99)
│   ├── Search form + BuildSearch instance
│   └── app_gui_menu.php
│       ├── Loops appscheme_type → appscheme (tree structure)
│       ├── Each item expandable (auto_tree)
│       │   ├── "Espace" link → app_explorer
│       │   ├── "Créer" link → app_create
│       │   └── "Explorer" link → app_prod
│       └── app_gui_tile_user.php (settings gear)
│       └── app_gui_today_create.php (deferred, bottom)
│
├── for_patolon_bis (search results popup, line 100-108)
│   └── BuildSearch "patolaon_bis" instance
│
├── desktop (main content area, line 110-114)
│   └── app_gui_desktop.php
│       ├── zone_agent_table (resizable)
│       ├── zone_agent_tuile (resizable)
│       ├── note_panel (resizable)
│       ├── app_gui_calendar (deferred)
│       ├── image_dyn (agent avatar)
│       └── app_gui_panel_list.php (right panel tree)
│           └── app_gui_panel.php (per-collection recent history)
│
└── app_user_pref/app_user_pref_css.php (custom CSS injection)
```

---

## Detailed Module Descriptions

### 1. `app_gui_main.php` — Root Container

**Path:** `idae/web/mdl/app/app_gui/app_gui_main.php`

**Role:** The top-level layout that wraps the entire application GUI. It initializes the task bar, the start menu overlay, the collapsible left side menu, and the desktop area.

**Key PHP logic (lines 1-25):**
- Fetches the current agent from `sitebase_base.agent`
- Reads user settings: `gui_menu_visible`, `cache_mode`
- Determines visibility states for the side menu and cache toggle

**HTML structure:**
| Element | ID | Purpose |
|---------|-----|---------|
| TaskBar | `taskBar` | Top bar with controls, email badge, agent name |
| Start pane | `gui_pane` | Overlay start menu (hidden by default) |
| Side menu | `gui_menu` | Collapsible left sidebar |
| Search popup | `for_patolon_bis` | Search results container |
| Desktop | `desktop` | Main workspace area |
| Main app | `mainApp` | Dynamic app container (JS-controlled) |

**JavaScript (lines 150-198):**
- `window.JSGUI = new appGui($('mainApp'))` — initializes the main app controller
- `session_verif()` — periodic session validation (every 60s), calls `ajaxValidation('quitter')` if session expired
- Cookie checks for `PHPSESSID`, `SESSID`, `idagent`

**CSS classes used:** `flex_v`, `flex_h`, `taskBar`, `gradb`, `frmCol1`, `blanc`, `shadowbox`, `animated`, `slideInDown`

---

### 2. `app_gui_start.php` — Start Menu Overlay

**Path:** `idae/web/mdl/app/app_gui/app_gui_start.php`

**Role:** The "waffle" menu overlay that appears when clicking the waffle icon in the task bar. Split into two 50% panes: left for navigation/search, right for "Today" dashboard.

**Left pane (lines 17-66):**
- **Search form** (lines 25-37): Submits to `main_item_search.load_data()`, toggles `main_item_search_zone`
  - On submit, shows `app_gui_start_search_last.php` in a dropdown
  - `BuildSearch` instance targets `patolaon` div
- **Search results zone** (`main_item_search_zone`, lines 42-61): Black overlay with search results
- **Start menu** (line 63): `app_gui_start_menu.php` loaded with `scope='app_menu_start'`

**Right pane (lines 70-85):**
- Header with "Aujourd'hui" (Today) title + settings gear
- `app_gui_today.php` loaded in `loader_gui_pane` (cached)

**Footer bar (lines 87-105):**
- Reload button → `reloadModule('app/app_gui/app_gui_main','*')`
- Personalize button → `app_user_pref/app_user_pref_style`
- Quit button → `ajaxValidation('quitter')`

**Loading zone** (line 106-107): `loader_progress_pane` for deferred content

---

### 3. `app_gui_start_menu.php` — Start Menu Left Pane

**Path:** `idae/web/mdl/app/app_gui/app_gui_start_menu.php`

**Role:** The main navigation tree in the start menu. Loops over `appscheme_type` and `appscheme` collections to build a categorized menu.

**PHP data flow (lines 1-30):**

```
1. App('appscheme') → all schemes
2. App('agent_groupe_droit') → user's read permissions (R=true)
3. Distinct appscheme_type IDs from permitted schemes
4. App('appscheme_type') → types sorted by name
5. App('agent_pref') → user preferences matching 'app_menu_start_*'
6. Filter schemes by user preferences + permissions
```

**Key queries:**
| Variable | Collection | Filter | Purpose |
|----------|------------|--------|---------|
| `$arr_table_R` | `agent_groupe_droit` | `R=true, idagent_groupe=X` | User's readable collections |
| `$ARR_TYPE` | `appscheme` | `codeAppscheme IN arr_table_R` | Distinct type IDs |
| `$RSNOSCHEME` | `appscheme` | `idappscheme_type IN [null,0,'']` | Schemes without a type |
| `$arr_pref` | `agent_pref` | `codeAgent_pref LIKE 'app_menu_start_%', valeurAgent_pref=true` | User's visible menu items |
| `$arr_sch` | `agent_groupe_droit` | `codeAppscheme IN DIST_TBL_PREF, R=true` | Filtered permitted schemes |
| `$RS_TY` | `appscheme_type` | `idappscheme_type IN arr_sch_type` | Types to display |

**HTML structure (lines 69-156):**
- Outer: `applink flex_h fond_noir color_fond_noir` (black background)
- Inner: `toggler flex_v` with `panel_entete` scrollable area
- **Type groups** (lines 72-98): Each `appscheme_type` renders:
  - Type icon button → loads `app_gui_start_menu_launch_all.php` into `loader_gui_pane`
  - List of schemes → each loads `app_gui_start_menu_launch.php` into `loader_gui_pane`
- **No-type schemes** (lines 99-123): Schemes with null type, filtered by user prefs
- **Bottom bar** (lines 143-155):
  - Production link → `app_gui_production.php`
  - Administration link (ADMIN only) → `app/app_admin/app_admin`
  - Settings gear → `app_gui_tile_user.php` with `code='app_menu_start'`

**CSS classes:** `toggler`, `applink`, `applinkblock`, `panel_entete`, `autoToggle`, `flex_h`, `flex_v`, `fond_noir`, `color_fond_noir`, `transpnoir`

---

### 4. `app_gui_start_menu_launch.php` — Sub-Menu for Single Collection

**Path:** `idae/web/mdl/app/app_gui/app_gui_start_menu_launch.php`

**Role:** When a user clicks a collection in the start menu, this module loads into `loader_gui_pane` showing collection-specific actions.

**PHP logic (lines 1-14):**
- Receives `table` from POST
- Creates `App($table)` instance
- Extracts variables via `$APP_TMP->extract_vars()` (sets `$NAME_APP`, `$ARR_GROUP_FIELD`, `$APP_TABLE`, `$GRILLE_FK`, `$R_FK`, `$HTTP_VARS`)
- Computes RGBA gradient from collection color

**Action buttons (permission-gated):**

| Permission | Action | Target Module | Description |
|------------|--------|---------------|-------------|
| `C` (Create) | "Créer {table}" | `app_create` | Create new record |
| `L` (List) | "Espace {table}" | `app/app/app_explorer` | Home/explorer view |
| `R` (Read) | "Recherche rapide" | `app/app_prod/app_prod_search` | Quick search |
| `L` (List) | "Parcourir" | `app/app_prod/app_prod` | Browse/production view |
| `R` (Read) | "Comparer" | `app/app/app_compare` | Comparison view |
| `ADMIN` | "Trier" | `app/app/app_dispatch` | Sort/dispatch view |
| `L` + has `dateDebut` + hasStatutScheme | "console {table}" | `app_console` | Console/dashboard |
| Any | "images {table}" | `app/app_img/app_img` | Image management |

**Status breakdown (DEV only, lines 88-113):**
- If collection has `hasStatutScheme`, loops `{$table}_statut` collection
- Each status shows count and links to `app_liste` filtered by status

**Bottom section (lines 116-118):**
- Deferred load of `app_gui_panel.php` with `table` and `idagent` vars

**Visual:** Gradient background from white to collection color at 50% opacity. Metro-style tiles (`appmetro` class).

---

### 5. `app_gui_start_menu_launch_all.php` — All Collections for a Type

**Path:** `idae/web/mdl/app/app_gui/app_gui_start_menu_launch_all.php`

**Role:** When clicking a type icon in the start menu, shows all collections of that type in a flat list with quick action icons.

**PHP logic:**
- Receives `idappscheme_type` from POST
- Fetches type info from `appscheme`
- Gets all schemes for that type via `$APP_SCH->get_schemes()`

**Per-collection actions (lines 22-50):**

| Permission | Action | Icon |
|------------|--------|------|
| `L` (List) | "Espace {table}" | Collection icon (colored) |
| `C` (Create) | Create new | `fa-save` |
| `L` (List) | Home | `fa-home` |
| `L` (List) | Production | `fa-folder-open` |
| `R` (Read) | Quick search | `fa-search` |
| Has `dateDebut` + hasStatutScheme | Console | `fa-dashboard` |

---

### 6. `app_gui_menu.php` — Collapsible Side Menu

**Path:** `idae/web/mdl/app/app_gui/app_gui_menu.php`

**Role:** The persistent left sidebar menu. Uses a tree structure (`auto_tree`) with expandable type groups.

**PHP data flow (lines 1-30):**

```
1. App('agent') → current agent info
2. App->get_schemes() → all schemes (line 12)
3. App('agent_pref') → preferences matching 'app_menu_*'
4. Filter by user prefs + agent_groupe_droit permissions
5. App('appscheme_type') → types for permitted schemes
```

**Key difference from `app_gui_start_menu.php`:**
- Uses `app_menu_*` preferences instead of `app_menu_start_*`
- Renders as an expandable tree (`auto_tree` / `auto_tree_click`)
- Each scheme has a sub-panel with Espace/Créer/Explorer links

**HTML structure (lines 32-87):**
- Outer: `#app_menu_dyn` with `main_auto_tree` attribute
- Scrollable content area (`overflow:auto`)
- **Type groups** (lines 34-79):
  - Type header (clickable, expands/collapses)
  - Scheme items (clickable, expands sub-panel)
  - Sub-panel (dark_3): Espace, Créer, Explorer links
- Bottom: `app_gui_tile_user.php` with `code='app_menu'`
- Deferred: `app_gui_today_create.php` at bottom

**CSS:** `dark_1`, `dark_2`, `dark_3` for nested levels, `auto_tree`, `auto_tree_click`, `retrait` for indentation

---

### 7. `app_gui_start_search_last.php` — Recent/Frequent Searches

**Path:** `idae/web/mdl/app/app_gui/app_gui_start_search_last.php`

**Role:** Shows the user's recent or frequent searches in the start menu search dropdown.

**PHP logic:**
- Queries `agent_recherche` collection for current agent
- Sorts by `dateCreationAgent_recherche` (desc) for "last" mode
- Sorts by `valeurAgent_recherche` (desc) for "more" (frequent) mode
- 30-day lookback window
- Pagination support (`page`, `nbRows`)

**UI features:**
- Toggle between "dernières" (recent) and "fréquentes" (frequent)
- Each search item shows the record name via `$APP_TMP->draw_field()`
- Clicking a search loads it into `BuildSearch`
- Delete button (minus icon) removes from history
- Shows search count (`quantiteAgent_recherche`)

---

### 8. `app_gui_today.php` — Today Dashboard

**Path:** `idae/web/mdl/app/app_gui/app_gui_today.php`

**Role:** Right pane of the start menu. Shows today's overview: planning, quick-create, quick-access lists, email, and schedule.

**Components:**

| Component | Module | Description |
|-----------|--------|-------------|
| Planning | Inline link → `app_planning` | Calendar/task planning |
| Quick-create | `app_gui_today_create.php` | Create buttons per collection |
| Quick-access lists | `app_gui_today_link.php` | Links to user's lists with counts |
| Mails | External link | Webmail at `DOCUMENTDOMAIN:8080` |
| Schedule | `app_gui_today_echeancier.php` | Timeline of start/end dates |
| Stats (hidden) | `app_stat_draw_mini` | Mini stat charts (DEV) |

---

### 9. `app_gui_today_create.php` — Quick-Create Buttons

**Path:** `idae/web/mdl/app/app_gui/app_gui_today_create.php`

**Role:** Grid of "create new" buttons for each collection the user has permission to create in.

**PHP logic:**
- Fetches all schemes via `App('appscheme')->find()->sort(['nomAppscheme' => 1])`
- Filters by:
  - User preference: `app_menu_create_{table} == 'true'`
  - Permission: `droit_table(agent, 'C', table)`
- Renders each as a clickable row with icon + name
- Click triggers `fonctionsJs::app_create(table, {idagent})`

**Visual:** Two-column grid (`demi flex_main`), color-coded icons, ellipsis for long names.

---

### 10. `app_gui_today_link.php` — Quick-Access List Links

**Path:** `idae/web/mdl/app/app_gui/app_gui_today_link.php`

**Role:** Metro-style tiles showing collections the user owns records in, with record counts.

**PHP logic:**
- Loops all schemes
- Filters by:
  - Permission: `droit_table(agent, 'L', table)`
  - Has agent field: `$APP_TMP->has_agent()`
  - Has records for user: `$APP_TMP->find(['idagent' => X])->count() > 0`
- Shows collection icon, name, and count
- Click opens `app_liste` filtered by user's agent

---

### 11. `app_gui_today_echeancier.php` — Schedule/Timeline

**Path:** `idae/web/mdl/app/app_gui/app_gui_today_echeancier.php`

**Role:** Shows a day-by-day timeline of records starting or ending within a date range.

**PHP logic:**
- Computes 4 date periods:
  - `semaine`: Monday this week → Sunday this week
  - `semaine_prochaine`: Monday next week → Sunday next week
  - `mois_fin`: Monday next week → Last day of current month
  - `mois_prochain`: First day of next month → Last day of next month
- Default period: "semaine"
- Loops all schemes with dates (`get_schemes()`)
- For each day in period, queries `dateDebut{Table}` and `dateFin{Table}`
- Shows "+" (green) for starts, "-" (red) for ends
- Empty days are hidden via inline CSS

**UI features:**
- Period selector dropdown (context menu)
- Expandable tree per day → per collection → per record
- Links open `app_fiche` on click

---

### 12. `app_gui_desktop.php` — Desktop/Workspace

**Path:** `idae/web/mdl/app/app_gui/app_gui_desktop.php`

**Role:** The main workspace area behind the overlays. Shows agent-specific widgets and the right panel.

**Components:**

| Zone | ID | Module/Config | Description |
|------|-----|---------------|-------------|
| Agent table | `zone_agent_table` | `agent_table` → `app_fiche_icone` | Resizable agent table widget |
| Agent tiles | `zone_agent_tuile` | `agent_tuile` → `app_fiche_mini` | Resizable agent tile widget |
| Notes | `note_panel` | `agent_note` → `app_fiche` | Resizable notes panel (max 700px) |
| Calendar | N/A | `app_gui_calendar` (deferred) | Mini calendar |
| Avatar | N/A | `image_dyn` (agent square image) | Clickable → opens agent fiche |
| Right panel | N/A | `app_gui_panel_list.php` | Collapsible tree of recent items |

**Right panel (lines 43-50):**
- 210px wide, scrollable, `gradb` background
- Contains `app_gui_panel_list.php` which loads `app_gui_panel.php` per collection
- Settings gear at bottom: `app_gui_tile_user.php` with `code='app_panel'`

---

### 13. `app_gui_panel.php` — Recent History Panel

**Path:** `idae/web/mdl/app/app_gui/app_gui_panel.php`

**Role:** Shows the user's recent history for a specific collection (from `agent_history`).

**PHP logic:**
- Queries `agent_history` where `codeAgent_history = table`
- Sorted by `{sortBy}Agent_history` desc, then date/time desc
- 30-day lookback, limit 15 rows
- Validates each record still exists; removes orphaned history entries
- Uses `$APP_TMP->draw_field()` to render the record name

**UI:** Expandable tree (`auto_tree`), click opens `app_fiche` in chrome GUI.

---

### 14. `app_gui_panel_list.php` — Panel Collection List

**Path:** `idae/web/mdl/app/app_gui/app_gui_panel_list.php`

**Role:** Loops all schemes and defers loading of `app_gui_panel.php` for each collection the user has enabled in their `app_panel_*` preferences.

**Filter:** `app_panel_{table} == 'true'` AND `droit_table(agent, 'R', table)`

---

### 15. `app_gui_production.php` — Production Menu

**Path:** `idae/web/mdl/app/app_gui/app_gui_production.php`

**Role:** Alternative production-focused menu view. Shows all collections grouped by type in an accordion tree.

**PHP logic:**
- Similar to `app_gui_start_menu.php` but includes ALL permitted schemes (not filtered by `app_menu_start_*` prefs)
- Queries `agent_groupe_droit` for all `R=true` permissions
- Types with schemes → expandable groups
- Each scheme links to `app_gui_start_menu_launch.php`

**UI:** `main_auto_tree` with `auto_tree_accordeon="true"`, white background, border-separated groups.

---

### 16. `app_gui_tile_user.php` — Settings Tile

**Path:** `idae/web/mdl/app/app_gui/app_gui_tile_user.php`

**Role:** Reusable settings gear icon that appears in multiple panels. Shows an orange warning icon if no preferences are configured for the given `code`.

**Parameters:**
| Param | Description |
|-------|-------------|
| `code` | Preference scope (e.g., `app_menu`, `app_menu_start`, `app_menu_create`, `app_panel`, `app_search`) |
| `text` | Optional label text |
| `css` | Optional CSS classes |
| `moduleTag` | HTML tag wrapper (default: `div`) |

**Logic:** Queries `agent_pref` for preferences matching the code. If none found, shows orange exclamation icon.

**Click action:** Opens `app/app_user_pref/app_user_pref` with `mdl=app/app_user_pref/app_user_pref_scheme&code={code}`

---

### 17. `app_gui_start_search_admin.php` — Admin Search (referenced but not detailed)

**Path:** `idae/web/mdl/app/app_gui/app_gui_start_search_admin.php`

**Role:** Admin-specific search panel, loaded into `search_admin` div (line 68 of `app_gui_start.php`) when admin mode is active.

---

## Data Flow Summary

### Permission Chain

```
1. $_SESSION['idagent'] → current user
2. $_SESSION['idagent_groupe'] → user's group
3. agent_groupe_droit → R/C/U/L permissions per codeAppscheme
4. agent_pref → visibility preferences (app_menu_*, app_panel_*, etc.)
5. Intersection of (3) AND (4) → final visible collections
```

### Preference Keys

| Prefix | Used In | Purpose |
|--------|---------|---------|
| `app_menu_{table}` | `app_gui_menu.php` | Show in side menu |
| `app_menu_start_{table}` | `app_gui_start_menu.php` | Show in start menu |
| `app_menu_create_{table}` | `app_gui_today_create.php` | Show in quick-create |
| `app_panel_{table}` | `app_gui_panel_list.php` | Show in right panel |
| `gui_menu_visible` | `app_gui_main.php` | Side menu visibility |
| `cache_mode` | `app_gui_main.php` | Module caching on/off |

### MongoDB Collections Used

| Collection | Purpose |
|------------|---------|
| `sitebase_base.agent` | User info |
| `sitebase_base.agent_groupe_droit` | Permissions |
| `sitebase_base.agent_history` | Recent activity |
| `sitebase_base.agent_recherche` | Search history |
| `sitebase_pref.agent_pref` | User preferences |
| `appscheme` | Collection definitions |
| `appscheme_type` | Collection type categories |
| `appscheme_base` | Base scheme definitions |
| `{table}_statut` | Per-collection status definitions |

---

## JavaScript Integration

### BuildSearch

Two instances are created:
- `main_item_search` → targets `patolaon` div (in start menu overlay)
- `main_item_search_gui` → targets `patolaon_bis` div (in side menu search popup)

### Auto-Toggle System

The `autoToggle` class + `act_target` / `mdl` / `vars` attributes drive dynamic content loading:
- `act_target` → target DOM element ID
- `mdl` → module path to load
- `vars` → POST variables for the module

### Tree System

`auto_tree` / `auto_tree_click` / `main_auto_tree` attributes drive expandable tree navigation:
- Clicking a tree header toggles visibility of the next sibling
- `auto_tree_accordeon="true"` enables accordion mode (close others on open)

### Session Verification

`session_verif()` runs every 60 seconds:
1. Calls `json_ssid` endpoint
2. Checks cookies for `PHPSESSID`, `SESSID`, `idagent`
3. If any missing → triggers `ajaxValidation('quitter')`

---

## CSS Class Glossary

| Class | Purpose |
|-------|---------|
| `flex_v` | Vertical flexbox container |
| `flex_h` | Horizontal flexbox container |
| `flex_main` | Flex item that grows to fill space |
| `flex_align_middle` | Vertical centering |
| `flex_align_bottom` | Bottom alignment |
| `flex_wrap` | Flex wrap enabled |
| `applink` | Clickable area container |
| `applinkblock` | Block-level link container |
| `autoToggle` | AJAX toggle trigger |
| `toggler` | Toggle visibility container |
| `toggler_visible` | Visible toggle state |
| `hide_gui_pane` | Clicking hides the GUI pane |
| `panel_entete` | Panel header styling |
| `appmetro` | Metro-style tile |
| `fond_noir` | Black background |
| `color_fond_noir` | Light text on black |
| `transpnoir` | Semi-transparent black |
| `transpblanc` | Semi-transparent white |
| `ededed` | Light gray background (#ededed) |
| `blanc` | White background |
| `gradb` | Gradient background |
| `shadowbox` | Box shadow |
| `boxshadowb` | Bottom box shadow |
| `borderb` / `bordert` / `borderr` / `border4` | Border on specific sides |
| `padding` / `padding_more` / `paddingmore` | Spacing utilities |
| `margin` / `margin_more` | Margin utilities |
| `retrait` | Indentation |
| `demi` | Half-width (50%) |
| `ellipsis` | Text overflow ellipsis |
| `aligncenter` / `alignright` / `uppercase` | Text utilities |
| `animated` / `fadeIn` / `slideInDown` / `speed` | CSS animations |
| `cursor` | Pointer cursor |
| `sticky` | Sticky positioning |
| `relative` / `absolute` | Position utilities |
| `none` | Display:none utility |

---

## Key Entry Points for Navigation

| User Action | Module Loaded | Target |
|-------------|---------------|--------|
| Click waffle icon | `app_gui_start.php` | `gui_pane` (overlay) |
| Click collection in start menu | `app_gui_start_menu_launch.php` | `loader_gui_pane` |
| Click type icon in start menu | `app_gui_start_menu_launch_all.php` | `loader_gui_pane` |
| Click "Production" | `app_gui_production.php` | `loader_gui_pane` |
| Click "Administration" | `app/app_admin/app_admin` | `loader_gui_pane` |
| Click collection in side menu | `app_gui_start_menu_launch.php` | `loader_gui_pane` |
| Click "Espace {table}" | `app/app/app_explorer` | New tab/onglet |
| Click "Créer {table}" | `app_create` (JS function) | New tab/onglet |
| Click "Recherche rapide" | `app/app_prod/app_prod_search` | New tab/onglet |
| Click "Parcourir" | `app/app_prod/app_prod` | New tab/onglet |
| Click "Comparer" | `app/app/app_compare` | New tab/onglet |
| Click "Trier" | `app/app/app_dispatch` | New tab/onglet |
| Click search result | `BuildSearch.load_data()` | `patolaon` / `patolaon_bis` |
| Click history item | `app_fiche` | Chrome GUI window |
| Click settings gear | `app_user_pref/app_user_pref` | Chrome GUI window |
