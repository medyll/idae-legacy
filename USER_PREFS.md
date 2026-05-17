# User Preferences System — IDAE Application

## Overview

The user preferences system controls **what each agent sees** across every panel of the application. It is a key-value store backed by the `sitebase_pref.agent_pref` MongoDB collection, accessed through `ClassApp::get_settings()` / `set_settings()` / `del_settings()`, and persisted via `ClassAction::set_settings()` / `init_settings()` / `del_settings()`.

All preference modules live under:
```
idae/web/mdl/app/app_user_pref/
```

---

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│  app_user_pref.php  (pref dialog — 750×450px)              │
│  ┌──────────────────┬──────────────────────────────────┐    │
│  │  Left Nav        │  Content (loader_user_pref)      │    │
│  │  ┌────────────┐  │                                  │    │
│  │  │Menu princ. │──│→ app_user_pref_scheme            │    │
│  │  │Panneau G.  │──│→ app_user_pref_scheme            │    │
│  │  │Panneau D.  │──│→ app_user_pref_scheme            │    │
│  │  │Recherche   │──│→ app_user_pref_scheme            │    │
│  │  │Style       │──│→ app_user_pref_style             │    │
│  │  │            │   │  └→ app_wallpaper               │    │
│  │  │Couleurs    │──│→ app_user_pref_color             │    │
│  │  │Réinitialiser│──│→ app_user_pref_init              │    │
│  │  └────────────┘  │                                  │    │
│  └──────────────────┴──────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘

Data Layer:
  sitebase_pref.agent_pref  ←  key-value store per agent
  sitebase_base.agent       ←  wallpaper, tiles (embedded)
  sitebase_image.wallpaper  ←  GridFS image storage

API Layer:
  ClassApp::get_settings() / set_settings() / del_settings()
  ClassAction::set_settings() / init_settings() / del_settings()
  JS: save_settings() / save_setting_mdl_search() / save_setting_autoNext()
```

---

## 1. Data Layer

### 1.1 Primary Collection: `sitebase_pref.agent_pref`

**Schema (inferred from usage):**

| Field | Type | Purpose |
|-------|------|---------|
| `idagent_pref` | int | Primary key (auto-increment via `getNext('idagent_pref')`) |
| `idagent` | int | Owner agent ID |
| `codeAgent_pref` | string | Preference key (e.g., `app_menu_start_client`) |
| `valeurAgent_pref` | mixed | Stored value (`true`, `'true'`, `'none'`, color hex, module path, etc.) |

**Key naming convention:** `{scope}_{target}`

| Scope | Target examples | Meaning |
|-------|----------------|---------|
| `app_menu_start` | `client`, `tache`, `appscheme_type_id` | Show in start menu |
| `app_menu` | `client`, `tache` | Show in left sidebar |
| `app_menu_create` | `client`, `tache` | Show in quick-create panel |
| `app_panel` | `client`, `tache` | Show in right panel |
| `app_search` | `client`, `tache` | Include in global search |
| `gui_menu_visible` | — | Sidebar visibility (`''` or `'none'`) |
| `cache_mode` | — | Module caching (`'on'` or `'off'`) |
| `app_gui_color` | — | Interface accent color (hex) |
| `app_gui_color_gui` | — | Window accent color (hex) |
| `list_data_button_group_{table}` | field code | Default group-by field |
| `list_data_button_sort_{table}` | field code | Default sort field |
| `list_data_button_dsp_{table}` | display mode | Default display mode |
| `list_data_button_dsp_mdl_{table}` | module path | Display module |
| `list_data_button_nbRows_{table}` | int | Rows per page |
| `list_data_button_className_{table}` | string | CSS class for list items |
| `{table}_panel` | `'none'` or `''` | Panel section visibility |
| `{table}_preview_list` | `'none'` or `''` | Preview list visibility |
| `{table}_preview_grillefk` | `'none'` or `''` | FK grid visibility |
| `{table}_preview_fk` | `'none'` or `''` | FK preview visibility |
| `{table}_search_field` | `'1'` or `''` | Include in search fields |
| `groupBy_{table}` | field code | Persisted group-by |
| `sortBy_{table}` | field code | Persisted sort field |
| `sortDir_{table}` | `'asc'`/`'desc'` | Persisted sort direction |
| `nbRows_{table}` | int | Persisted row count |
| `{setting_home_name}_{table}` | module path | Fiche home module choice |

### 1.2 Secondary Storage: `sitebase_base.agent`

Embedded preferences stored directly on the agent document:

| Field | Type | Purpose |
|-------|------|---------|
| `settings.wallpaper` | string | Filename of selected wallpaper |
| `settings.backgroundcolor` | string | Solid background color (hex) |
| `tile` | array[] | Desktop tile configurations (pushed/pulled) |

### 1.3 Image Storage: `sitebase_image.wallpaper` (GridFS)

| Metadata | Purpose |
|----------|---------|
| `metadata.thumb` | `1` = thumbnail, absent = full image |
| `metadata.idagent` | Owner agent ID (absent = system/shared wallpaper) |
| `metadata.date` | Upload date (sort order) |

---

## 2. PHP API — `ClassApp`

**File:** `idae/web/appclasses/appcommon/ClassApp.php` (lines 564-595)

### 2.1 `get_settings($idagent, $key, $table = '')`

```php
function get_settings($idagent, $key, $table = '') {
    $width_table = empty($table) ? '' : '_' . $table;
    $arr = $this->plug('sitebase_pref', 'agent_pref')
        ->findOne(['idagent' => (int)$idagent, 'codeAgent_pref' => $key . $width_table]);
    return $arr['valeurAgent_pref'];
}
```

- **3-parameter form**: `get_settings(idagent, key, table)` → looks up `key_table`
- **2-parameter form**: `get_settings(idagent, key)` → looks up `key`
- Returns `null` if no record exists (falsy in PHP)
- Used **everywhere** to gate visibility of UI elements

### 2.2 `set_settings($idagent, $vars)`

```php
function set_settings($idagent, $vars) {
    foreach ($vars as $key => $val) {
        $out['valeurAgent_pref'] = $val;
        $out['codeAgent_pref']   = $key;
        $out['idagent']          = (int)$idagent;
        $arr = $this->plug('sitebase_pref', 'agent_pref')
            ->findOne(['idagent' => (int)$idagent, 'codeAgent_pref' => $key]);
        if (empty($arr['idagent_pref'])) {
            $out['idagent_pref'] = (int)$this->getNext('idagent_pref');
        }
        $this->plug('sitebase_pref', 'agent_pref')
            ->updateOne(['idagent' => (int)$idagent, 'codeAgent_pref' => $key],
                        ['$set' => $out], ['upsert' => true]);
    }
}
```

- Accepts an **array** of key-value pairs (bulk write)
- Uses **upsert** — creates if missing, updates if exists
- Auto-generates `idagent_pref` on first write via `getNext()`

### 2.3 `del_settings($idagent, $key, $table = '')`

```php
function del_settings($idagent, $key, $table = '') {
    $width_table = empty($table) ? '' : '_' . $table;
    $del = $this->plug('sitebase_pref', 'agent_pref')
        ->remove(['idagent' => (int)$idagent, 'codeAgent_pref' => $key . $width_table]);
    return $del;
}
```

- Removes the preference document entirely

---

## 3. Server Actions — `ClassAction`

**File:** `idae/web/bin/classes/ClassAction.php` (lines 410-464)

### 3.1 `set_settings($ARGS)`

```php
public function set_settings($ARGS) {
    $APP   = new App();
    $key   = $ARGS['key'];
    $value = $ARGS['value'];
    $APP->set_settings($_SESSION['idagent'], [$key => $value]);
    return $ARGS;
}
```

- Called via `ajaxValidation('set_settings', 'mdl/app/', 'key=X&value=Y')`
- Single key-value write

### 3.2 `init_settings($ARGS)`

```php
public function init_settings($ARGS) {
    $APP = new App();
    $APP->init_scheme('sitebase_pref', 'agent_pref');
    $APP = new App('agent_pref');

    foreach (array_keys($ARGS['vars']['arr_settings']) as $key => $val) {
        // 1. Remove ALL existing prefs matching the scope prefix
        $APP->plug('sitebase_pref', 'agent_pref')
            ->remove(['idagent' => (int)$ARGS['vars']['idagent'],
                      'codeAgent_pref' => MongoCompat::toRegex(preg_quote($val, '/'), 'i')]);

        // 2. Re-populate with defaults based on permissions
        switch ($val) {
            case "app_search":
                foreach (droit_table_multi($ARGS['vars']['idagent'], 'R') as $ks => $table) {
                    // skip _ligne, _type, _statut suffixes
                    $APP->set_settings($_SESSION['idagent'], ["$val" . "_$table" => true]);
                }
                break;
            case "app_menu_start":
                foreach (droit_table_multi($ARGS['vars']['idagent'], 'R') as $ks => $table) {
                    $APP->set_settings($_SESSION['idagent'], ["$val" . "_$table" => true]);
                }
                break;
            case "app_menu_create":
                foreach (droit_table_multi($ARGS['vars']['idagent'], 'C') as $ks => $table) {
                    $APP->set_settings($_SESSION['idagent'], ["$val" . "_$table" => true]);
                }
                break;
        }
    }
    return $ARGS;
}
```

- **Nuclear reset**: wipes all prefs for the selected scopes, then re-enables everything the user has permission for
- Skips internal tables: `_ligne`, `_type`, `_statut`
- `app_search` / `app_menu_start` → uses **Read** permission
- `app_menu_create` → uses **Create** permission

### 3.3 `del_settings($ARGS)`

```php
public function del_settings($ARGS) {
    $APP   = new App();
    $key   = $ARGS['key'];
    $value = $ARGS['value'];
    $test  = $APP->del_settings($_SESSION['idagent'], [$key => $value]);
    return $ARGS;
}
```

---

## 4. JavaScript API

**File:** `idae/web/javascript/app/app_functions.js`

### 4.1 `save_settings(key, value)`

```javascript
function save_settings(key, value) {
    ajaxValidation('set_settings', 'mdl/app/', 'key=' + key + '&value=' + value);
}
```

- Fires immediately, no debounce
- Used for color picks, menu toggles, display mode changes

### 4.2 `save_setting_mdl_search(node, key)`

**Defined inline in each module** that uses it, not in `app_functions.js`.

**`app_user_pref_scheme.php` (lines 106-115):**
```javascript
save_setting_mdl_search = function (node, key) {
    setTimeout(function () {
        dsp = $(node).checked;
        ajaxValidation('set_settings', 'mdl/app/', 'key=' + key + '&value=' + dsp);
        setTimeout(function () {
            reloadScope('<?=$code?>', '*');
        }, 1500)
    }, 100)
}
```

**`app_search_all.php`:** same pattern, no reloadScope call.

- 100ms delay before save, 1500ms delay before reload
- Used in checkbox toggles for scheme visibility and search field toggles
- Reloads the entire scope after save

### 4.3 `save_setting_autoNext(node, key)`

```javascript
function save_setting_autoNext(node, key) {
    clearTimeout(time_set_ayto_next);
    time_set_ayto_next = setTimeout(function () {
        var dsp = node.next().getStyle('display');
        ajaxValidation('set_settings', 'mdl/app/', 'key=' + key + '&value=' + dsp);
    }, 500)
}
```

- Debounced (500ms), saves the **display style** of the next sibling element
- Used for collapsible tree sections (save open/closed state)

### 4.4 `del_settings(key, value)`

```javascript
function del_settings(key, value) {
    setTimeout(function () {
        ajaxValidation('deleteTile', 'mdl/app/app_gui',
                       'table=' + key + '&table_value=' + value);
    }, 500)
}
```

- Removes desktop tiles (not agent_pref entries — uses `$pull` on `agent.tile` array)

---

## 5. UI Modules

### 5.1 `app_user_pref.php` — Preferences Dialog

**Path:** `idae/web/mdl/app/app_user_pref/app_user_pref.php`

**Role:** Master dialog (750×450px) with left navigation and deferred content loader.

**Left navigation items:**

| Label | Module Loaded | Vars |
|-------|---------------|------|
| Menu principal | `app_user_pref_scheme` | `code=app_menu_start` |
| Panneau latéral gauche | `app_user_pref_scheme` | `code=app_menu` |
| Panneau latéral droit | `app_user_pref_scheme` | `code=app_panel` |
| Recherche rapide | `app_user_pref_scheme` | `code=app_search` |
| Style | `app_user_pref_style` | — |
| Couleurs | `app_user_pref_color` | — |
| Réinitialiser | `app_user_pref_init` | — |

**Entry points (how users open it):**
- Start menu → settings gear → `act_chrome_gui('app/app_user_pref/app_user_pref', 'mdl=...&code=...')`
- `app_gui_tile_user.php` gear icon → opens with specific `code`
- Footer "Personnaliser" button → opens with `app_user_pref_style`

### 5.2 `app_user_pref_scheme.php` — Scope Visibility Editor

**Path:** `idae/web/mdl/app/app_user_pref/app_user_pref_scheme.php`

**Role:** The most-used preference module. Shows a tree of all collection types and schemes with checkboxes to toggle visibility per scope.

**Parameters:**
| Param | Values | Description |
|-------|--------|-------------|
| `code` | `app_menu_start`, `app_menu`, `app_panel`, `app_search`, `app_menu_create` | Which scope to edit |

**Data flow:**
1. Fetches all `appscheme_type` records
2. Fetches all `appscheme` records grouped by type
3. Fetches "unclassified" schemes (no type)
4. For each type: checkbox for the type itself (`{code}_{type_code}`)
5. For each scheme: checkbox (`{code}_{table}`)
6. Filters by `droit_table(agent, 'R', table)` (and `'C'` for `app_menu_create`)

**UI structure:**
```
┌──────────────────────────────────────────────┐
│ Préférences : Menu démarrer, accés aux espaces│
├──────────────────────────────────────────────┤
│ [QuickFind search input]                     │
├──────────────────────────────────────────────┤
│ ▼ [icon] Type Name                    [☑]    │  ← type-level toggle
│   ┌─────────┬─────────┬─────────┐            │
│   │ [☑] Client      │ [☑] Prospect   │ [☑] Contact   │  ← 33% width columns
│   └─────────┴─────────┴─────────┘            │
│ ▼ Non classés                                │
│   [☑] Untable1    [☑] Untable2               │
└──────────────────────────────────────────────┘
```

**Save behavior:**
- Checkbox click → `save_setting_mdl_search()` → `ajaxValidation('set_settings')` → `reloadScope(code, '*')` after 1.5s
- Value saved: checkbox `.checked` state (boolean → stored as `'true'` or `'false'`)

**Title mapping (line 20-40):**

| Code | Title |
|------|-------|
| `app_search` | Recherche rapide |
| `app_panel` | Panneau latéral droit |
| `app_menu` | Panneau latéral gauche |
| `app_menu_start` | Menu démarrer, accés aux espaces |
| *(other)* | `{code}` (raw) |

### 5.3 `app_user_pref_style.php` — Style/Wallpaper Panel

**Path:** `idae/web/mdl/app/app_user_pref/app_user_pref_style.php`

**Role:** Thin wrapper that loads `app_wallpaper.php` into a 350px tall container.

### 5.4 `app_wallpaper.php` — Wallpaper Selector

**Path:** `idae/web/mdl/app/app_user_pref/app_wallpaper.php`

**Role:** Two-section wallpaper picker: **Général** (system wallpapers) and **Personnel** (user-uploaded wallpapers).

**Data sources:**
- **System**: `sitebase_image.wallpaper` GridFS where `metadata.idagent` does NOT exist and `metadata.thumb = 1`
- **Personal**: Same collection where `metadata.idagent = $_SESSION['idagent']` and `metadata.thumb = 1`

**Upload forms:**
- `form_system`: uploads to `sitebase_image.wallpaper` without `idagent` (shared)
- `form_perso`: uploads with `vars[idagent]` (personal)
- Both use `myddeAttach` for drag-and-drop with preview in `#pref_preview`

**Save action:**
```javascript
saveimage = function(wall) {
    ajaxValidation('setWallPaper', 'mdl/app/app_user_pref/', 'wallpaper=' + wall);
}
```

**Server-side `setWallPaper` action** (`actions.php` line 22-38):
1. Updates `sitebase_base.agent.settings.wallpaper` with filename
2. Fetches full image from GridFS (non-thumb version)
3. Builds URL: `/images/appimg-{_id}.jpg`
4. Sets `localStorage.setItem('wallpaper', url_w)`
5. Applies `$('body').setStyle({backgroundImage: url_w})`

**Delete action:** `delWallPaper` — removes file from filesystem (legacy, not GridFS)

### 5.5 `app_user_pref_color.php` — Color Picker

**Path:** `idae/web/mdl/app/app_user_pref/app_user_pref_color.php`

**Role:** Two color swatch groups for interface and window theming.

**Color scopes:**

| Key | Label | CSS Impact |
|-----|-------|------------|
| `app_gui_color` | Interface | Active link hover color, sidebar border, taskbar indicator |
| `app_gui_color_gui` | Fenetres | Window handle colors, gradient backgrounds, taskbar text shadow |

**Swatch palette (40+ colors):**
- Row 1: `#313942`, `#89c2f3`, `#ffffff`, `#333333` (neutrals)
- Row 2: Reds, browns, oranges (`#A0151E` → `#CF7806`)
- Row 3: Pinks, magentas (`#FF1629` → `#FFC5F0`)
- Row 4: Greens, blues, purples (`#539A2F` → `#7B7326`)

**Save behavior:**
```javascript
$('color_gui_ch').on('click', '[data-color]', function(event, node) {
    var color = node.readAttribute('data-color');
    code = node.up('[code]').readAttribute('code');
    save_settings(code, color);
    setTimeout(function() {
        reloadModule('app/app_user_pref/app_user_pref_css', '*')
    }, 1250)
});
```

### 5.6 `app_user_pref_css.php` — Dynamic CSS Injector

**Path:** `idae/web/mdl/app/app_user_pref/app_user_pref_css.php`

**Role:** Generates inline `<style>` blocks based on user's color preferences. Loaded in `app_gui_main.php` and `app_gui_desktop.php`.

**Generated CSS for `app_gui_color`:**

```css
/* Hover/active state for all applink elements */
.applink .active, .applink .autoToggle.active,
.applink a:hover, .applink label:hover {
    color: {contrast_color} !important;
    text-shadow: 0 0 4px {contrast_shadow} !important;
    background-color: {app_gui_color} !important;
}

/* Sidebar border */
.frmCol1 { border-color: {app_gui_color} !important; }

/* Active container border */
.containerdisp.active { border: 1px solid {app_gui_color} !important; }

/* Taskbar active tab indicator */
.taskBar .taskBarButton.active::before {
    background-color: {app_gui_color};
}
```

**Contrast calculation** (line 17):
```php
if (0.3*$co[0] + 0.59*$co[1] + 0.11*$co[2] <= 128) {
    $color = "#FFFFFF"; $color_shadow = "#333333";  // dark bg → white text
} else {
    $color = "#333333"; $color_shadow = "#FFFFFF";  // light bg → dark text
}
```

**Generated CSS for `app_gui_color_gui`:**

```css
/* Window handle (active) */
.containerdisp.active .handledisp {
    background-color: rgba({rgb}, 0.6) !important;
    color: {contrast} !important;
    text-shadow: 0 0 2px {contrast_contrast}, 0 0 1px {contrast_contrast};
}

/* Window handle (inactive) */
.containerdisp .handledisp {
    background-color: rgba({rgb}, 0.2) !important;
}

/* Title bar gradient */
.gradb {
    background: linear-gradient(45deg, rgba({rgb}, 0.7), rgba({rgb}, 0.3), rgba({rgb}, 0.4)) !important;
}

/* Taskbar text shadow */
.taskBar .buttonbody { text-shadow: 0 0 2px {app_gui_color_gui}; }
```

**Contrast chain:**
```php
$app_gui_color_gui_contrast = color_contrast($app_gui_color_gui);
$app_gui_color_gui_contrast_contrast = color_contrast($app_gui_color_gui_contrast);
```

### 5.7 `app_user_pref_init.php` — Reset Panel

**Path:** `idae/web/mdl/app/app_user_pref/app_user_pref_init.php`

**Role:** Bulk reset form. Checkboxes for each scope to reset.

**Resettable scopes:**

| Key | Label |
|-----|-------|
| `app_search` | Recherche rapide |
| `app_menu_create` | Création rapide |
| `app_panel` | Panneau latéral droit |
| `app_menu` | Panneau latéral gauche |
| `app_menu_start` | Menu démarrage, accés aux espaces |

**Submit action:** `ajaxValidation('init_settings', 'mdl/app/', ...)` → triggers `ClassAction::init_settings()`

### 5.8 `app_user_pref_reload.php` — Legacy Color Reload

**Path:** `idae/web/mdl/app/app_user_pref/app_user_pref_reload.php`

**Role:** Legacy module that injects CSS for older color preference keys.

**Keys used:**
| Key | CSS Class |
|-----|-----------|
| `appgui_windowcolor` | `.appgui_windowcolor.active` |
| `appgui_guicolor` | `.appgui_guicolor` |
| `appgui_backgroundcolor` | `.appgui_backgroundcolor` |

Note: This uses a different key format than the modern `app_gui_color` / `app_gui_color_gui` system.

---

## 6. Preference Consumption — Where Settings Are Read

### 6.1 Menu Visibility

| Module | Setting Key | Effect |
|--------|-------------|--------|
| `app_gui_start_menu.php` | `app_menu_start_{table}` | Show/hide collection in start menu |
| `app_gui_menu.php` | `app_menu_{table}` | Show/hide collection in left sidebar |
| `app_gui_today_create.php` | `app_menu_create_{table}` | Show/hide in quick-create grid |
| `app_gui_panel_list.php` | `app_panel_{table}` | Show/hide in right panel |
| `json_data_search.php` | `app_search_{table}` | Include/exclude from search |

### 6.2 List/Table Display

| Module | Setting Key | Effect |
|--------|-------------|--------|
| `app_liste.php` | `list_data_button_dsp_{table}` | Display mode (table, thumb, icon, etc.) |
| `app_liste.php` | `list_data_button_dsp_mdl_{table}` | Display module path |
| `app_liste.php` | `list_data_button_group_{table}` | Group-by field |
| `app_liste.php` | `list_data_button_sort_{table}` | Sort field |
| `app_liste.php` | `list_data_button_nbRows_{table}` | Rows per page |
| `app_liste.php` | `list_data_button_className_{table}` | CSS class for items |
| `app_prod_liste.php` | `groupBy_{table}` | Persisted group-by |
| `app_prod_liste.php` | `sortBy_{table}` | Persisted sort field |
| `app_prod_liste.php` | `sortDir_{table}` | Persisted sort direction |
| `app_prod_liste.php` | `nbRows_{table}` | Persisted row count |

### 6.3 Fiche/Preview Display

| Module | Setting Key | Effect |
|--------|-------------|--------|
| `app_fiche_preview.php` | `{table}_preview_list` | Show/hide list section |
| `app_fiche_preview_grillefk` | `{table}_preview_grillefk` | Show/hide FK grid |
| `app_fiche_mini_full.php` | `{table}_preview_fk` | Show/hide FK preview |
| `app_fiche_maxi.php` | `{setting_home_name}_{table}` | Selected home module |
| `app_fiche_rfk_liste.php` | `{vars_rfk[table]}{table}_preview_fk_liste` | RFK list visibility |

### 6.4 Panel State

| Module | Setting Key | Effect |
|--------|-------------|--------|
| `app_gui_panel.php` | `{table}_panel` | Panel section display (`'none'` or `''`) |
| `app_gui_main.php` | `gui_menu_visible` | Sidebar visibility |
| `app_gui_main.php` | `cache_mode` | Module caching on/off |

### 6.5 Theme/Style

| Module | Setting Key | Effect |
|--------|-------------|--------|
| `app_user_pref_css.php` | `app_gui_color` | Interface accent color |
| `app_user_pref_css.php` | `app_gui_color_gui` | Window accent color |
| `app_user_pref_reload.php` | `appgui_windowcolor` | Legacy window color |
| `app_user_pref_reload.php` | `appgui_backgroundcolor` | Legacy background color |

---

## 7. Preference Setting Flow — End to End

### 7.1 Checkbox Toggle (scheme visibility)

```
User clicks checkbox in app_user_pref_scheme.php
    │
    ├─ 100ms setTimeout
    │
    ├─ save_setting_mdl_search(node, 'app_menu_start_client', 'client')
    │   │
    │   ├─ ajaxValidation('set_settings', 'mdl/app/',
    │   │                 'key=app_menu_start_client&value=true')
    │   │
    │   └─ ClassAction::set_settings($ARGS)
    │       └─ ClassApp::set_settings(idagent, {key: value})
    │           └─ agent_pref.updateOne(upsert: true)
    │
    └─ 1500ms setTimeout
        └─ reloadScope('app_menu_start', '*')
            └─ All modules with scope='app_menu_start' reload
```

### 7.2 Color Pick

```
User clicks color swatch in app_user_pref_color.php
    │
    ├─ save_settings('app_gui_color', '#FF505E')
    │   └─ ajaxValidation('set_settings', ...)
    │       └─ ClassAction::set_settings()
    │           └─ agent_pref.updateOne(upsert: true)
    │
    └─ 1250ms setTimeout
        └─ reloadModule('app/app_user_pref/app_user_pref_css', '*')
            └─ New CSS injected with updated color
```

### 7.3 Wallpaper Selection

```
User clicks wallpaper thumbnail in app_wallpaper.php
    │
    ├─ saveimage('my-wallpaper.jpg')
    │   └─ ajaxValidation('setWallPaper', 'mdl/app/app_user_pref/',
    │                     'wallpaper=my-wallpaper.jpg')
    │       └─ actions.php::setWallPaper
    │           ├─ agent.update({settings.wallpaper: filename})
    │           ├─ GridFS lookup for non-thumb version
    │           ├─ localStorage.setItem('wallpaper', url)
    │           └─ $('body').setStyle({backgroundImage: url})
```

### 7.4 Bulk Reset

```
User checks scopes in app_user_pref_init.php and submits
    │
    ├─ ajaxValidation('init_settings', 'mdl/app/',
    │                 'vars[idagent]=X&vars[arr_settings][app_menu_start]=on')
    │
    └─ ClassAction::init_settings($ARGS)
        ├─ agent_pref.remove({codeAgent_pref: /app_menu_start.*/i})
        ├─ droit_table_multi(idagent, 'R') → [client, tache, ...]
        ├─ For each table (skip _ligne, _type, _statut):
        │   └─ set_settings({app_menu_start_client: true, ...})
        └─ skelMdl::send_cmd('act_notify', {msg: scope_name})
```

### 7.5 List Display Mode Change

```
User clicks display mode button in app_liste_menu.php
    │
    ├─ onclick="save_settings('list_data_button_dsp_client', 'thumb')"
    │   └─ ajaxValidation('set_settings', ...)
    │       └─ ClassAction::set_settings()
    │           └─ agent_pref.updateOne({valeurAgent_pref: 'thumb'})
    │
    └─ Next page load: app_liste.php reads setting
        └─ $settings_button_dsp = get_settings(idagent, 'list_data_button_dsp', table)
            └─ Renders with 'thumb' display mode
```

---

## 8. Permission Gating

Preferences are **always** gated by permissions. A checkbox for a collection only appears if the user has the required right:

| Scope | Required Permission | Function |
|-------|---------------------|----------|
| `app_menu_start` | `R` (Read) | `droit_table(agent, 'R', table)` |
| `app_menu` | `R` (Read) | `droit_table(agent, 'R', table)` |
| `app_panel` | `R` (Read) | `droit_table(agent, 'R', table)` |
| `app_search` | `R` (Read) | `droit_table(agent, 'R', table)` |
| `app_menu_create` | `C` (Create) | `droit_table(agent, 'C', table)` |

During **reset** (`init_settings`), the same permission check is applied via `droit_table_multi()`:
- `app_search`, `app_menu_start` → all tables with `R` permission
- `app_menu_create` → all tables with `C` permission

---

## 9. QuickFind Integration

`app_user_pref_scheme.php` includes a QuickFind search input (line 47):

```html
<input data-quickFind=""
       data-quickFind-tag=".autoBlock"
       data-quickFind-parent=".sparent"
       type="text"/>
```

- Filters `.autoBlock` elements (the collection checkbox grids)
- Scoped to `.sparent` (each type group)
- Allows rapid filtering of collections by name within the preference editor

---

## 10. Tree/Accordion State

The preference editor uses `main_auto_tree` (line 50) for expandable type groups:

```html
<div main_auto_tree>
    <div auto_tree>...</div>   ← type header (clickable)
    <div class="autoBlock">...</div>  ← collection checkboxes (toggled)
</div>
```

The `auto_tree` system is separate from preferences — it's a UI pattern for collapsible sections. The `save_setting_autoNext()` JS function can persist the open/closed state of these sections, but this is not used in the preference editor itself.

---

## 11. Known Issues & Technical Debt

### 11.1 Inconsistent Value Types

`valeurAgent_pref` stores different types depending on context:
- **Booleans**: `'true'`, `'1'`, `true` (MongoDB boolean) — sometimes string, sometimes native
- **Display states**: `'none'`, `''` (empty string = visible)
- **Colors**: `'#FF505E'` (hex string)
- **Module paths**: `'app/app/app_fiche_forward'`
- **Numbers**: stored as strings (`'5000'` for nbRows)

The `checked()` helper (used in scheme checkboxes) likely handles the truthy comparison, but the inconsistency makes debugging harder.

### 11.2 Dual Storage for Colors

Two parallel color systems exist:
- **Modern**: `app_gui_color`, `app_gui_color_gui` (used by `app_user_pref_color.php` + `app_user_pref_css.php`)
- **Legacy**: `appgui_windowcolor`, `appgui_guicolor`, `appgui_backgroundcolor` (used by `app_user_pref_reload.php`)

These are never synced. If a user sets colors via the modern picker, the legacy keys remain untouched.

### 11.3 Wallpaper Storage Mismatch

- Wallpaper **selection** is stored in `sitebase_base.agent.settings.wallpaper` (agent document)
- Wallpaper **images** are in `sitebase_image.wallpaper` (GridFS)
- The `delWallPaper` action tries to `unlink()` from filesystem, but images are in GridFS — this action is broken

### 11.4 No Preference Validation

`set_settings` accepts any key/value pair with no validation. Typos in key names create orphaned preference documents that are never cleaned up.

### 11.5 Regex-Based Deletion in Reset

`init_settings` uses `MongoCompat::toRegex(preg_quote($val, '/'), 'i')` to delete prefs by prefix. This is case-insensitive and could accidentally match unintended keys if a scope name is a substring of another.

### 11.6 Hardcoded Scope List in Reset

`app_user_pref_init.php` hardcodes 5 scopes. Adding a new scope (e.g., a new panel) requires updating both the init form AND the `init_settings` switch statement — no central registry exists.

---

## 12. Complete Preference Key Registry

### Menu & Panel Scopes

| Key Pattern | Example | Stored By | Consumed By | Value Type |
|-------------|---------|-----------|-------------|------------|
| `app_menu_start_{table}` | `app_menu_start_client` | scheme editor | `app_gui_start_menu.php` | `'true'` / falsy |
| `app_menu_start_{type_code}` | `app_menu_start_12` | scheme editor | (type-level toggle) | `'true'` / falsy |
| `app_menu_{table}` | `app_menu_client` | scheme editor | `app_gui_menu.php` | `'true'` / falsy |
| `app_menu_create_{table}` | `app_menu_create_client` | scheme editor | `app_gui_today_create.php` | `'true'` / falsy |
| `app_panel_{table}` | `app_panel_client` | scheme editor | `app_gui_panel_list.php` | `'true'` / falsy |
| `app_search_{table}` | `app_search_client` | scheme editor | `json_data_search.php` | `'true'` / falsy |

### GUI State

| Key | Value | Consumed By |
|-----|-------|-------------|
| `gui_menu_visible` | `''` or `'none'` | `app_gui_main.php` |
| `cache_mode` | `'on'` or `'off'` | `app_gui_main.php` |

### Theme

| Key | Value | Consumed By |
|-----|-------|-------------|
| `app_gui_color` | Hex color | `app_user_pref_css.php` |
| `app_gui_color_gui` | Hex color | `app_user_pref_css.php` |
| `appgui_windowcolor` | Hex color | `app_user_pref_reload.php` (legacy) |
| `appgui_guicolor` | Hex color | `app_user_pref_reload.php` (legacy) |
| `appgui_backgroundcolor` | Hex color | `app_user_pref_reload.php` (legacy) |

### List Display (per-table)

| Key Pattern | Value Examples | Consumed By |
|-------------|---------------|-------------|
| `list_data_button_dsp_{table}` | `table`, `thumb`, `icon`, `mdl`, `line_fk`, `flex_line`, `fields`, `image` | `app_liste.php` |
| `list_data_button_dsp_mdl_{table}` | `app/app/app_fiche_forward`, `app/app/app_fiche_icone`, `app/app/app_fiche_entete` | `app_liste.php` |
| `list_data_button_group_{table}` | Field code (e.g., `statut`, `type`) | `app_liste.php`, `app_liste_menu.php` |
| `list_data_button_sort_{table}` | Field code (e.g., `nomClient`, `dateCreation`) | `app_liste.php`, `app_liste_menu.php` |
| `list_data_button_nbRows_{table}` | `5000`, etc. | `app_liste_menu.php` |
| `list_data_button_className_{table}` | `sortable`, etc. | `app_liste.php` |
| `groupBy_{table}` | Field code | `app_prod_liste.php`, `app_liste_table.php` |
| `sortBy_{table}` | Field code | `app_prod_liste.php`, `app_liste_table.php` |
| `sortDir_{table}` | `asc`, `desc` | `app_prod_liste.php`, `app_liste_table.php` |
| `nbRows_{table}` | int | `app_prod_liste.php`, `app_liste_table.php` |

### Fiche/Preview (per-table)

| Key Pattern | Value | Consumed By |
|-------------|-------|-------------|
| `{table}_preview_list` | `'none'` / `''` | `app_fiche_preview.php` |
| `{table}_preview_grillefk` | `'none'` / `''` | `app_fiche_preview_grillefk` |
| `{table}_preview_fk` | `'none'` / `''` | `app_fiche_mini_full.php`, `app_fiche_entete_arbo.php` |
| `{table}_{othertable}_preview_fk_liste` | `'none'` / `''` | `app_fiche_rfk_liste.php` |
| `{setting_home_name}_{table}` | Module path | `app_fiche_maxi.php`, `app_fiche_maxi_home.php`, `app_fiche_maxi_old.php` |

### Panel State (per-table)

| Key Pattern | Value | Consumed By |
|-------------|-------|-------------|
| `{table}_panel` | `'none'` / `''` | `app_gui_panel.php` |

### Search Fields (per-table)

| Key Pattern | Value | Consumed By |
|-------------|-------|-------------|
| `{table}_search_field` | `'1'` / `''` | `app_search_all.php` |

### Embedded in `agent` Document

| Field | Value | Set By |
|-------|-------|--------|
| `settings.wallpaper` | Filename | `setWallPaper` action |
| `settings.backgroundcolor` | Hex color | `setColor` action |
| `tile[]` | Array of tile configs | `createTile` / `deleteTile` actions |
