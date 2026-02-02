# Idae-Legacy AI Agent Instructions

## Project Overview

**Idae** is a legacy PHP 5.6 + Node.js CMS/CRM system with MongoDB backend. It implements an ORM-based GUI framework for dynamic data management with real-time socket.io synchronization. Built for complex business logic (CRM, invoicing, scheduling) with multi-tenant support and event-driven architecture.

- **Backend**: PHP 5.6 with ADODB ORM, MongoDB data persistence, MySQL 5.7 for legacy data
- **Real-time**: Node.js 12 socket.io server for live updates across clients and persistent connections
- **Architecture**: Schema-driven MVC with dynamic field binding, no explicit database schemas
- **Deployment**: Docker (Apache + PHP + MySQL 5.7 + Node.js 12)
- **Status**: Legacy maintenance mode; focus on stability over modernization

## Migration Guidance

When asked to perform a migration, always follow MIGRATION.md and MONGOCOMPAT.md. Ensure the project includes and adheres to those documents.

## Code History & Authorship

**Preserve existing authorship comments** in legacy code, especially **Date and Time** information:
```php
/**
 * Created by PhpStorm.
 * User: Mydde
 * Date: 23/05/14      <-- PRESERVE THIS
 * Time: 20:26         <-- PRESERVE THIS
 */
class ClassName { ... }
```

**English only**: All code comments, modification notes, and documentation must be in English.

These timestamps track code origin and evolution in a legacy system without formal Git history. When making significant changes (migrations, major refactoring), **add a new comment line** documenting the change:
```php
/**
 * Created by PhpStorm.
 * User: Mydde
 * Date: 23/05/14
 * Time: 20:26
 * 
 * Modified: 2026-02-02 - Migrated to MongoDB v2.0 driver (Agent)
 */
```

**Never remove** the original Date/Time markers — they preserve code provenance in a legacy system without formal Git history. Only add modification notes, never alter original timestamps.

---

## Key Architecture Patterns

### 1. **Schema-Driven Application Model** (`AppScheme`)
- **No explicit DB schemas**: All entity definitions stored in `appscheme` MongoDB collection
- **Self-describing data**: Fields, validation rules, relationships, UI hints all in one document
- **Dynamic field naming**: Field codes become database column names (e.g., `codeAppscheme_field` → `code` + table name as suffix)
- **Example appscheme record**:
  ```php
  [
    '_id' => MongoId(...),
    'nomAppscheme' => 'produit',
    'codeAppscheme' => 'produit',
    'grilleFk' => [['table_fk' => 'marque', 'fk_field' => 'idmarque']],
    'appscheme_field' => [
      ['codeAppscheme_field' => 'nom', 'typeField' => 'text', 'estObligatoireAppscheme_field' => 1],
      ['codeAppscheme_field' => 'prix', 'typeField' => 'number', 'min' => 0]
    ]
  ]
  ```
- **Key classes**: `App` (base, handles MongoDB queries), `ClassIdae.php` (business logic), `AppSite` (website-specific filtering)
- **Reference**: [appclasses/](idae/web/appclasses/), [conf_init.php](idae/web/appconf/conf_init.php)

### 2. **Hierarchical ORM System**

**Class Hierarchy**: `App` (base) → `AppSite` (web-specific) → `Idae`, `Act`, `Artis` (domain models)

**Core Query Methods**:
```php
// QUERY EXECUTION
$app = new App('produit');
$rs = $app->query(['estActif' => 1, 'prixProduit' => ['$gte' => 100]]);
$rs->sort(['nomProduit' => 1])->limit(50)->skip(0);

// SINGLE RECORD
$record = $app->findOne(['idproduit' => 123]);  // array or false

// CREATE/UPDATE (UPSERT)
$id = $app->create_update(
  ['idproduit' => 123],  // search filter
  ['nomProduit' => 'Widget', 'prixProduit' => 99.99],  // values
  ['upsert' => true, 'safe' => 1]
);

// DELETE
$app->remove(['idproduit' => 123]);

// BULK UPDATE
$app->update(['categoryId' => 5], ['$set' => ['estActif' => 0]], ['multi' => true]);
```

**Foreign Key Relationships**:
- `grille_fk` (forward): child→parent (produit→marque)
- `reverse_grille_fk`: parent→children (marque→all products)
- **Pattern**: `$app->get_grille_fk($table)` returns FK metadata; `$app->get_reverse_grille_fk($table, $id)` fetches related records

### 3. **JSON API Request/Response Pattern**

**Standard Data Endpoint** [services/json_data.php](idae/web/services/json_data.php):
```javascript
// CLIENT REQUEST
POST /services/json_data.php
{
  table: "produit",
  vars: { estActifProduit: 1, prixProduit: { "$gte": 100 } },
  page: 1,
  nbRows: 50,
  sortBy: "nomProduit",
  sortDir: 1,
  piece: "data"  // or "scheme" for metadata
}

// SERVER RESPONSE
{
  appscheme_field: [...field definitions...],
  grilleFk: [...FK relationships...],
  grilleFK: [...FK arrays for each record...],
  records: [...data rows...],
  groupBy: {...aggregation if requested...}
}
```

**Other Critical Endpoints**:
- [services/json_scheme.php](idae/web/services/json_scheme.php) - fetch schema metadata
- [services/json_exec.php](idae/web/services/json_exec.php) - execute server actions
- [services/json_data_event.php](idae/web/services/json_data_event.php) - triggers socket.io broadcasts

### 4. **Socket.io Real-Time Sync** (`idae_server.js`)

**Server Structure** [app_node/idae_server.js](idae/web/app_node/idae_server.js):
- **Port**: Default 3005 (configurable via `SOCKET_PORT`)
- **Session Management**: `sessionMgm` Map maintains user sessions with heartbeat tracking
- **MongoDB Connection**: Direct MongoDB driver (v2.2.10) for shared state
- **Event Routing**: HTTP endpoints (`/postScope`, `/run`, `/runModule`) trigger socket broadcasts

**Real-Time Flow**:
```javascript
// CLIENT: Subscribe to updates
socket.on('connect', () => {
  socket.emit('subscribe', { table: 'produit', scope: 'list_produit' });
});

// SERVER: Broadcast when data changes
io.to('room_produit_list').emit('update', { 
  action: 'refresh',
  scope: 'list_produit',
  data: {...changed records...}
});

// CLIENT: Refresh UI on update
socket.on('update', (data) => {
  reloadModule(data.scope);  // Re-fetch from json_data.php
});
```

**HTTP Endpoints**:
```javascript
POST /postScope?scope=list_produit&value=refreshData  // Broadcast to all
POST /run?mdl=module/action&PHPSESSID=xyz              // Execute PHP module
POST /runModule?mdl=module/action&PHPSESSID=xyz        // Execute + broadcast
```

---

## Configuration Management

**Dynamic Host Detection** [conf.inc.php#L24-L42](idae/web/conf.inc.php#L24-L42):
```php
// Host-based environment routing
if ('lan' === end($host_parts) || $host === 'localhost') {
  include_once('conf.lan.inc.php');  // Local dev
} elseif (strpos($_SERVER['HTTP_HOST'], 'preprod') === false) {
  DEFINE('ENVIRONEMENT', 'PROD');    // Production
} else {
  DEFINE('ENVIRONEMENT', 'PREPROD'); // Staging
}
```

**Environment Defines**:
- Paths: `APPPATH`, `SITEPATH`, `APPCONFDIR`, `APPCLASSES`, `ADODBDIR`
- Connections: `SOCKETIO_PORT`, `SQL_HOST`, `MDB_HOST`, `MONGO_USER`, `MONGO_PASS`
- Tenant: `BUSINESS`, `APPNAME`, `CUSTOMERNAME`, `DOCUMENTDOMAIN`
- Session: `SESSION_PATH`, `COOKIE_PATH`
- Mode: `ENVIRONEMENT` (PROD|PREPROD|LAN) controls error display

**Module Bootstrap** [conf_init.php](idae/web/appconf/conf_init.php):
- Auto-loads from [conf_modules/](idae/web/appconf/conf_modules/)
- Initializes schemes via `App::init_scheme()`
- Pattern: `$APP->init_scheme('database', 'table', ['fields' => [...], 'grilleFK' => [...]])`

---

## Application Bootstrap Process

### **Server-Side Bootstrap** (PHP/Node.js)

#### 1. **Initial Request Flow**
```
Browser Request → reindex.php
  ↓
Sets $_SESSION['reindex'] = date()
  ↓
Redirects to index.php
  ↓
```

#### 2. **index.php Initialization** [index.php](idae/web/index.php)
```php
<?php
// Step 1: Include configuration (environment detection, path setup, database connection)
include_once($_SERVER['CONF_INC']);

// Step 2: Validate session - if not set, redirect to reindex.php for cleanup
if (empty($_SESSION['reindex'])) header("Location: reindex.php");

// Step 3: Load all module schemes - registers data entities, validates DB collections
include_once(APPCONFDIR . "conf_init.php");
?>
<html>
  <head>
    <!-- Load CSS dynamically via lessc.inc.php (LESS compilation on-the-fly) -->
    <? include_once('lessc.inc.php'); ?>
    <!-- Load bag.js for async resource loading -->
    <script src="javascript/vendor/bag.js"></script>
  </head>
  <body>
    <div id="inBody"></div>
    <!-- Load main bootstrap JavaScript -->
    <script src="javascript/main_bag.js"></script>
  </body>
</html>
```

#### 3. **conf.inc.php & conf.lan.inc.php** [conf.inc.php](idae/web/conf.inc.php)
```php
// Environment detection based on hostname
if ('lan' === end($host_parts) || $host === 'localhost') {
  include_once('conf.lan.inc.php');  // Dev: debug enabled, direct paths
} else {
  DEFINE('ENVIRONEMENT', 'PROD|PREPROD');  // Production: strict error handling
}

// Load configuration from JSON (hosts, databases, API keys)
$configFile = __DIR__ . '/../config/' . (ENVIRONEMENT === 'PREPROD' ? 'lan-hosts.json' : 'prod-hosts.json');
$hostConf = json_decode(file_get_contents($configFile), true)['hosts'][$host];

// Define all system constants
DEFINE('APPPATH', $projectRoot);
DEFINE('SITEPATH', $webDir . '/');
DEFINE('SOCKETIO_PORT', $hostConf['socketio_port'] ?? 3005);
DEFINE('MONGO_HOST', $hostConf['mongo_host'] ?? 'localhost');
```

#### 4. **conf_init.php Scheme Registration** [conf_init.php](idae/web/appconf/conf_init.php)
```php
// Initialize core App instance for schema management
$APP = new App('appscheme');

// Check if database is initialized
if(empty($APP->app_table_one)) {
  echo HTTPAPP."conf_install.php";  // First-time setup redirect
  exit;
}

// Auto-discover and load all module configurations
$conf_module_dir = APPCONFDIR . 'conf_modules/';
foreach (scandir($conf_module_dir) as $file) {
  if ($file == '.' || $file == '..') continue;
  include_once($conf_module_dir . $file);  // Each file calls App::init_scheme()
}

// Example: Register agent entity with FK relationships
$APP->init_scheme('sitebase_pref', 'agent', [
  'fields' => ['nom', 'code', 'password', 'login', 'mailPassword'],
  'grilleFK' => ['agent_groupe_droit', 'agent_type']
]);
```

#### 5. **Node.js Socket Server Bootstrap** [idae_server.js](idae/web/app_node/idae_server.js)
```javascript
// 1. Server startup
const port = process.env.PORT || process.env.SOCKET_PORT || 3005;
const app = http.createServer(http_handler);
const io = socketio(app);

// 2. MongoDB connection for persistent state
const mongoHost = process.env.MONGO_HOST || 'localhost';
let socket_db;
mongoClient.connect(`mongodb://${mongoUser}:${mongoPass}@${mongoHost}:27017/...`, ...);

// 3. Session management
const sessionMgm = (() => {
  const sessions = new Map();
  return {
    add: (sess) => sessions.set(sess.sessionId, sess),
    getBySession: (sessionId) => sessions.get(sessionId),
    removeBySession: (sessionId) => sessions.delete(sessionId)
  };
})();

// 4. HTTP endpoint handlers for PHP → Node communication
// /postScope - broadcast to all clients
// /run - execute PHP module on domain
// /runModule - execute module + broadcast result

// 5. Socket.io event listeners
io.on('connection', (socket) => {
  socket.on('subscribe', (data) => {
    socket.join(`table_${data.table}`);  // Join room for table updates
  });
  
  socket.on('grantIn', (data) => {
    sessionMgm.add({...});  // Add user session to session map
  });
});

app.listen(port, () => console.log(`Socket.io server running on port ${port}`));
```

---

### **Client-Side Bootstrap** (JavaScript)

#### 1. **index.html Load Flow**
```
1. index.php renders HTML + CSS + bag.js
2. bag.js initialized (IndexedDB cache layer)
3. main_bag.js loaded (resource configuration)
4. Resource cascade: require_boot → require_polyfill → require_hell → require_to_log → ...
```

#### 2. **main_bag.js** [main_bag.js](idae/web/javascript/main_bag.js) - Resource Configuration
```javascript
// Define resource loading sequence using bag.js (cached module loader)
var require_trame = {
  require_boot: [
    'javascript/vendor/localforage.js',      // IndexedDB wrapper
    'javascript/vendor/basket.js'            // bag.js cache manager
  ],
  
  require_polyfill: [
    'javascript/vendor/polyfill/rsvp.js',    // Promise polyfill (for older browsers)
    'javascript/vendor/polyfill/EventSource.js',
    'javascript/vendor/moment.js'            // Date manipulation
  ],
  
  require_hell: [
    'javascript/vendor/prototype/prototype-1.7.3.js',  // Prototype.js framework
    'javascript/vendor/scriptaculous/scriptaculous.js',  // Effects/animations
    'javascript/vendor/scriptaculous/effects.js',
    'javascript/vendor/scriptaculous/dragdrop.js'       // Drag-drop support
  ],
  
  require_to_log: [
    'javascript/engine/module.js',        // Module system - socketModule()
    'javascript/engine/methods.js',       // Common DOM methods
    'javascript/app/app_php.js',         // PHP XHR communication layer
    'javascript/app/app_functions.js',   // Utility functions
    'javascript/app/app_bootstrap_init.js',  // Session/auth initialization
    'javascript/engine/initApp.js'       // App core initialization
  ],
  
  require_scripts: [
    'javascript/vendor/chartist.js',      // Charts, UI components
    'javascript/vendor/chart.bundle.min.js',
    'javascript/vendor/tinymce/tinymce.min.js',  // Rich text editor
    'javascript/vendor/pikaday.js'               // Date picker
  ],
  
  require_boostrap: [
    'javascript/app/app_bootstrap.js'     // Host detection, schema loading
  ],
  
  require_app: [
    'javascript/app/app_cache.js',        // Local cache management
    'javascript/app/app_socket.js',       // Socket.io integration
    'javascript/app/app_live_data.js',    // Real-time data sync
    'javascript/app/app_datatable.js',    // Data grid display
    'javascript/app/app_window.js',       // Window/modal management
    'javascript/engine/engine.js',        // Core event engine
    'javascript/engine/afterAjaxCall.js'  // Post-AJAX hooks
  ]
};

// Load cascade using promises
bag.require(require_trame.require_boot)
  .then(() => bag.require(require_trame.require_polyfill))
  .then(() => bag.require(require_trame.require_hell))
  .then(() => bag.require(require_trame.require_to_log))
  .then(() => bag.require(require_trame.require_scripts))
  .then(() => bag.require(require_trame.require_boostrap))
  .then(() => bag.require(require_trame.require_app))
  .then(() => {
    console.log('All resources loaded, starting bootstrap');
    idae_log();  // Begin session/auth flow
  });
```

#### 3. **app_bootstrap.js** [app_bootstrap.js](idae/web/javascript/app/app_bootstrap.js) - Host & Schema Loading
```javascript
// Determine base URLs based on hostname (support localhost + production)
switch (window.document.location.hostname) {
  case 'localhost':
  case '127.0.0.1':
    var HTTPJAVASCRIPT = window.document.location.href.replace(location.hash, "") + '/javascript/';
    var HTTPCSS = window.document.location.href.replace(location.hash, "") + '/css/';
    break;
  default:
    var HTTPJAVASCRIPT = document.location.protocol + '//' + window.document.location.host + '/javascript/';
    var HTTPCSS = document.location.protocol + '//' + window.document.location.host + '/css/';
}

// Load entity schemas from server (executed once at startup)
schemeLoad = function() {
  if (!window.APP) window.APP = [];
  if (!window.APP.APPSCHEMES) window.APP.APPSCHEMES = [];
  if (!window.APP.APPFIELDS) window.APP.APPFIELDS = [];
  
  return new RSVP.Promise((resolve, reject) => {
    // Fetch entity schemas, field definitions, and relationships from server
    app_init_template().then(function(edd){
      console.log('Entity schemes loaded into APP.APPSCHEMES');
      resolve('ok');
    });
  });
};
```

#### 4. **app_bootstrap_init.js** [app_bootstrap_init.js](idae/web/javascript/app/app_bootstrap_init.js) - Session & Socket Auth
```javascript
function idae_log() {
  // Step 1: Check for existing PHP session cookie
  if (Cookies.get('PHPSESSID')) {
    
    // Step 2: Validate session with server via XHR
    get_data('json_ssid', {}).then(function(res) {
      res = JSON.parse(res);
      
      if (res.PHPSESSID == Cookies.get('PHPSESSID')) {
        // Session cookie valid
        if (!res.SESSID || res.SESSID == 0) {
          // Session expired on server
          request_login();
        } else {
          // Step 3: Authenticate with Node.js socket server
          socket.emit('grantIn', {
            DOCUMENTDOMAIN: window.document.location.hostname,
            IDAGENT: Cookies.get('idagent'),
            SESSID: Cookies.get('idagent'),
            PHPSESSID: Cookies.get('PHPSESSID')
          }, function(data) {
            console.log('Socket.io authentication granted');
            // Step 4: Load main app GUI
            $('inBody').socketModule('app/app_gui/app_gui_main', '');
            hide_login();
          });
        }
      } else {
        // Session mismatch - refresh session
        request_login();
      }
    });
  } else {
    // No session - show login
    console.log('No PHPSESSID, showing login');
    request_login();
  }
}

function request_login() {
  // Create login modal and load via socket.io communication
  var div_login = document.createElement("div");
  $(div_login).setStyle({position: 'absolute', width: '100%', height: '100%'});
  $(div_login).id = 'div_login';
  $(div_login).socketModule('app/app_login/app_login', '', {cache: true});
  document.body.appendChild(div_login);
}

function hide_login() {
  if (!$('div_login')) return;
  $('div_login').fade();  // Fade out login overlay
}
```

#### 5. **Complete Client Bootstrap Sequence**
```
index.php rendered (HTML + PHP output)
   ↓
bag.js loaded (IndexedDB cache layer initialized)
   ↓
main_bag.js executed (defines resource_trame cascade)
   ↓
Load require_boot (localforage, basket.js)
   ✓ Cascade continues when resolved
   ↓
Load require_polyfill (rsvp, EventSource, moment)
   ✓ Cascade continues
   ↓
Load require_hell (Prototype.js, scriptaculous)
   ✓ Cascade continues (heavy framework)
   ↓
Load require_to_log (module.js, app_bootstrap_init.js, initApp.js)
   ✓ Cascade continues
   ↓
Load require_scripts (vendor libs: chartist, tinymce, pikaday)
   ✓ Cascade continues (plugins, not core)
   ↓
Load require_boostrap (app_bootstrap.js)
   ✓ Executes: schemeLoad()
   ✓ Fetches entity definitions from server
   ✓ Populates APP.APPSCHEMES
   ↓
Load require_app (app_socket.js, app_live_data.js, engine.js)
   ✓ Cascade continues (feature modules)
   ↓
ALL RESOURCES LOADED
   ↓
Call idae_log() - START SESSION AUTHENTICATION
   ├─→ Check PHPSESSID cookie
   ├─→ Validate with server (json_ssid.php)
   ├─→ If valid: socket.emit('grantIn') → Node.js authentication
   ├─→ If valid: Load main GUI ($('inBody').socketModule('app_gui_main'))
   └─→ If invalid: request_login() → Show login modal
   ↓
APP READY FOR USER INTERACTION
```

**Key Bootstrap Concepts**:
- **Lazy Loading**: Resources loaded in cascade (bag.js caches in IndexedDB)
- **Session Bridge**: PHP session validated on both PHP + Node.js sides
- **Socket.io Handshake**: Client authenticates to socket server after session validation
- **Schema Preload**: All entity schemas fetched once at startup (APP.APPSCHEMES global)
- **Module System**: `socketModule()` enables dynamic UI component loading via socket.io

---

## Permission Model (Role-Based Access Control)

### **Architecture Overview**

**Three-tier permission system**:
1. **Agent** - Individual user account (login, password, group assignment)
2. **Agent Group** - Role/department (ADMIN, USER, MANAGER, etc.)
3. **Agent Group Rights** - Permission grants (table-level CRUD access per group)

**Permission Levels**:
- **App-level** (`droit_app`): Global feature access (ADMIN, DEV, CONF, etc.)
- **Table-level** (`agent_groupe_droit`): CRUD per entity (C=Create, R=Read, U=Update, D=Delete, L=List, CONF=Configure)

### **Database Schema**

**agent** table:
```php
[
  'idagent' => 1,
  'nomAgent' => 'John Doe',
  'loginAgent' => 'jdoe',
  'passwordAgent' => 'hashed_password',
  'estActifAgent' => 1,
  'idagent_groupe' => 1,              // FK to agent_groupe
  'droit_app' => [                    // App-level permissions (embedded)
    'ADMIN' => 1,
    'DEV' => 1,
    'CONF' => 1
  ]
]
```

**agent_groupe** table:
```php
[
  'idagent_groupe' => 1,
  'nomAgent_groupe' => 'Administrateur',
  'codeAgent_groupe' => 'ADMIN',
  'descriptionAgent_groupe' => 'Full system access'
]
```

**agent_groupe_droit** table (grid/join table):
```php
[
  'idagent_groupe_droit' => 1,
  'idagent_groupe' => 1,              // FK to agent_groupe
  'codeAppscheme' => 'produit',       // Entity code (table name)
  'C' => 1,                           // Create permission
  'R' => 1,                           // Read permission (detail view)
  'U' => 1,                           // Update permission
  'D' => 1,                           // Delete permission
  'L' => 1,                           // List permission
  'CONF' => 1                         // Configure permission (schema editing)
]
```

### **Permission Functions**

**Check app-level permission** [appfunc/function.php](idae/web/appfunc/function.php):
```php
function droit($code) {
  // Checks if current agent has app-level permission (ADMIN, DEV, CONF, etc.)
  $APP = new App('agent');
  $arr = $APP->findOne([
    'idagent' => (int)$_SESSION['idagent'],
    'droit_app.' . $code => 1           // Query embedded droit_app field
  ]);
  
  if (empty($arr['idagent'])) {
    return false;  // Permission denied
  }
  
  return true;
}

// Usage:
if (droit('ADMIN')) {
  // Execute admin-only action
}
if (droit('DEV')) {
  // Show developer options
}
```

**Check table-level permission**:
```php
function droit_table($idagent, $code, $table) {
  // Check CRUD permission for specific table/entity
  // $code: 'C'=Create, 'R'=Read, 'U'=Update, 'D'=Delete, 'L'=List, 'CONF'=Configure
  
  $APP    = new App('agent');
  $APP_GD = new App('agent_groupe_droit');
  
  // 1. Get agent's group assignment
  $arr_ag = $APP->findOne(['idagent' => (int)$idagent]);
  
  // 2. Check if agent's group has permission on table
  $count = $APP_GD->find([
    'idagent_groupe' => (int)$arr_ag['idagent_groupe'],
    'codeAppscheme' => $table,          // Filter by entity
    $code => true                       // Check specific permission code
  ])->count();
  
  if ($count == 0) {
    return false;  // No permission
  }
  
  return true;
}

// Usage:
if (!droit_table($_SESSION['idagent'], 'R', 'produit')) {
  http_response_code(403);
  exit;  // Cannot read produit table
}

if (droit_table($_SESSION['idagent'], 'C', 'produit')) {
  // Show create button
}
```

**Get all tables where agent has permission**:
```php
function droit_table_multi($idagent, $code, $table = null) {
  // Returns: specific table (if $table provided) or all tables with permission
  $APP    = new App('agent');
  $APP_GD = new App('agent_groupe_droit');
  
  $arr_ag = $APP->findOne(['idagent' => (int)$idagent]);
  
  if (!empty($table)) {
    // Check specific table
    $count = $APP_GD->find([
      'idagent_groupe' => (int)$arr_ag['idagent_groupe'],
      'codeAppscheme' => $table,
      $code => true
    ])->count();
    
    return $count > 0 ? $table : false;
  } else {
    // Get all accessible tables
    $dist = $APP_GD->distinct_all('codeAppscheme', [
      'idagent_groupe' => (int)$arr_ag['idagent_groupe'],
      $code => true
    ]);
    
    return sizeof($dist) > 0 ? $dist : false;
  }
}

// Usage:
$read_tables = droit_table_multi($_SESSION['idagent'], 'R');  // Get all readable tables
// Returns: ['produit', 'client', 'commande', ...]

$can_list = droit_table_multi($_SESSION['idagent'], 'L', 'produit');
// Returns: 'produit' if allowed, false otherwise
```

### **Permission Checks in API Endpoints**

**json_data_table.php** - Data fetch with permission validation:
```php
if (!droit_table($_SESSION['idagent'], 'CONF', $table) && $APP->has_agent()) {
  // User not allowed to configure this table
  http_response_code(403);
  echo json_encode(['error' => 'Permission denied']);
  exit;
}

if (!droit_table($_SESSION['idagent'], 'R', $table)) {
  // User cannot read detail records
  return [];
}

if (!droit_table($_SESSION['idagent'], 'L', $table)) {
  // User cannot list this table
  http_response_code(403);
  exit;
}
```

**json_data_search.php** - Search filtering by permissions:
```php
// Filter search results: only return tables user can read AND list
$rs = $APP_SCHEME->find(['codeAppscheme_type' => 'entity']);

foreach ($rs as $row) {
  $table = $row['codeAppscheme'];
  
  // Skip tables without list permission
  if (!droit_table($_SESSION['idagent'], 'L', $table)) continue;
  
  // Skip tables without read permission
  if (!droit_table($_SESSION['idagent'], 'R', $table)) continue;
  
  // Add to search results
  $results[] = $table;
}
```

### **Setting Up Permissions**

**During installation** [conf_install_go.php](idae/web/appconf/conf_install_go.php):
```php
// 1. Create agent group (ADMIN)
$APP_AGENT_GR = new App('agent_groupe');
$idagent_groupe = $APP_AGENT_GR->create_update(
  ['codeAgent_groupe' => 'ADMIN'],
  ['nomAgent_groupe' => 'Administrateur']
);

// 2. Create default admin user
$APP_AGENT = new App('agent');
$idagent = $APP_AGENT->create_update(
  ['agent_auto' => 1],
  [
    'nomAgent' => 'Mydde',
    'loginAgent' => 'Mydde',
    'passwordAgent' => 'malaterre',
    'idagent_groupe' => $idagent_groupe,
    'estActifAgent' => 1,
    'droit_app' => ['ADMIN' => 1, 'DEV' => 1]  // App-level permissions
  ]
);

// 3. Grant table permissions via agent_groupe_droit
$APP_GD = new App('agent_groupe_droit');
foreach (['produit', 'client', 'commande', 'facture'] as $table) {
  $APP_GD->create_update(
    ['idagent_groupe' => $idagent_groupe, 'codeAppscheme' => $table],
    [
      'C' => 1,        // Create
      'R' => 1,        // Read
      'U' => 1,        // Update
      'D' => 1,        // Delete
      'L' => 1,        // List
      'CONF' => 1      // Configure
    ]
  );
}
```

**Programmatic permission grant**:
```php
// Grant USER group read-only access to produit table
$APP_GD = new App('agent_groupe_droit');
$APP_GD->create_update(
  [
    'idagent_groupe' => 2,  // USER group ID
    'codeAppscheme' => 'produit'
  ],
  [
    'C' => 0,        // Cannot create
    'R' => 1,        // Can read detail
    'U' => 0,        // Cannot update
    'D' => 0,        // Cannot delete
    'L' => 1,        // Can list
    'CONF' => 0      // Cannot configure
  ]
);

// Revoke all permissions for a table from group
$APP_GD = new App('agent_groupe_droit');
$APP_GD->remove([
  'idagent_groupe' => 2,
  'codeAppscheme' => 'sensitive_table'
]);
```

### **Common Permission Gotchas**

1. **CRUD codes must be boolean true, not 1**:
   ```php
   // ❌ WRONG - MongoDB won't match integers
   $APP_GD->find(['C' => 1]);
   
   // ✅ CORRECT
   $APP_GD->find(['C' => true]);
   ```

2. **Session-based vs group-based checks**:
   ```php
   // ❌ WRONG - relies on old session variable
   if ($_SESSION['droit_produit'] == 1) { ... }
   
   // ✅ CORRECT - always query current agent's group
   if (droit_table($_SESSION['idagent'], 'R', 'produit')) { ... }
   ```

3. **Permission inheritance is group-based, not hierarchical**:
   ```php
   // User inherits permissions from group only
   // Changing group instantly changes all user permissions
   $APP_AGENT->update(
     ['idagent' => 123],
     ['$set' => ['idagent_groupe' => 2]],  // Switch to different group
     ['safe' => 1]
   );
   // User now has all permissions of group 2
   ```

4. **List vs Read permissions**:
   - **'L'** (List): Can user see the table in list/search?
   - **'R'** (Read): Can user open detail/edit records?
   - User can have L=1, R=0 (see table name but can't open records)

5. **CONF permission gates schema editing**:
   ```php
   // Only allow schema configuration if CONF=1
   if (!droit_table($_SESSION['idagent'], 'CONF', 'produit')) {
     // Hide field/entity configuration UI
   }
   ```

---

## Common Developer Workflows

### Add a New Entity
```php
// 1. Create appscheme record
$app_schema = new App('appscheme');
$app_schema->create_update(
  ['codeAppscheme' => 'mon_entite'],
  [
    'nomAppscheme' => 'Mon Entité',
    'grilleFk' => [['table_fk' => 'marque', 'fk_field' => 'idmarque']],
    'appscheme_field' => [
      ['codeAppscheme_field' => 'nom', 'typeField' => 'text'],
      ['codeAppscheme_field' => 'actif', 'typeField' => 'boolean']
    ]
  ]
);

// 2. Optional: Create handler class (appclasses/ClassMyEntity.php)
class MyEntity extends App {
  function __construct() { parent::__construct('mon_entite'); }
  function getActive() { return $this->find(['estActif' => 1]); }
}

// 3. Access via API: POST /services/json_data.php?table=mon_entite
```

### Query with Caching (AppSite Pattern)
```php
$key = http_build_query($vars) . $sort;
if (!empty($this->query_cache[$key])) {
  return $this->query_cache[$key];
}
$results = $app->find($vars)->sort(['nomProduit' => 1]);
$this->query_cache[$key] = $results;  // Cache result
return $results;
```

### Create Migration Script
```php
<?php
include_once('conf.inc.php');
$APP = new App();
set_time_limit(0);

// Create indexes for performance
$app = $APP->plug('sitebase_base', 'produit');
$app->createIndex(['nomProduit' => 1]);
$app->createIndex(['estActifProduit' => 1, 'prixProduit' => -1]);

// Data transformation
$rs = $app->find(['estActif' => 1]);
while ($row = $rs->fetchRow()) {
  $app->update(
    ['idproduit' => $row['idproduit']], 
    ['$set' => ['prixProduit' => (float)$row['prixProduit']]],
    ['safe' => 1]
  );
}
?>
```

### Trigger Real-Time Refresh
```php
// After updating data in PHP:
file_put_contents(ACTIVEMODULEFILE, 'reloadModule("scope_name")');

// Socket server polls json_data_event.txt and broadcasts to clients
```

---

## Common Bugs & Gotchas

### 1. **MongoId Casting Issues**
```php
// ❌ WRONG - String compared to MongoId
$app->findOne(['_id' => $_POST['id']]);

// ✅ CORRECT
$app->findOne(['_id' => new MongoId($_POST['id'])]);
```

### 2. **Field Naming Mismatch**
```php
// Field: codeAppscheme_field = 'nom', table = 'produit'
// Stored as: nomProduit in MongoDB

// ❌ WRONG
$app->findOne(['nom' => 'Widget']);  // Returns nothing

// ✅ CORRECT
$app->findOne(['nomProduit' => 'Widget']);
```

### 3. **ADODB Result Set Exhaustion**
```php
$rs = $app->query(['estActif' => 1]);
while ($row = $rs->fetchRow()) { ... }
while ($row = $rs->fetchRow()) { ... }  // ❌ EMPTY - cursor exhausted

// ✅ CORRECT - Query again if needed
$rs = $app->query(['estActif' => 1]);
while ($row = $rs->fetchRow()) { ... }
$rs = $app->query(['estActif' => 1]);
while ($row = $rs->fetchRow()) { ... }
```

### 4. **Socket.io Session Mismatch**
```javascript
// Socket server has limited PHP session tracking
// Solutions:
// 1. Write session state to MongoDB
// 2. Pass PHPSESSID explicitly in socket events
// 3. Verify SOCKET_PORT=3005 connectivity in Docker
// 4. Check browser console for WebSocket errors
```

### 5. **Type Coercion in Queries**
```php
// ❌ WRONG - ID as string
$app->find(['idproduit' => '123']);

// ✅ CORRECT - Force integer
$app->find(['idproduit' => (int)$_POST['idproduit']]);
```

### 6. **Display Errors in Production**
```php
// Credentials/paths exposed when errors shown
if (ENVIRONEMENT !== 'PROD') {
  ini_set('display_errors', 55);
}

// Always catch errors in production:
try {
  $app->create_update(...);
} catch (Exception $e) {
  error_log($e->getMessage());  // Log, don't expose
  echo json_encode(['error' => 'Database error']);
}
```

### 7. **Regex with Special Characters**
```php
// ❌ WRONG - User input not escaped
$search = $_POST['search'];  // "test.com" matches "testXcom"
new MongoRegex("/$search/i");

// ✅ CORRECT
$search = preg_quote($_POST['search']);
new MongoRegex("/$search/i");
```

### 8. **Array Field Filtering Issues**
```php
// ❌ WRONG
$status = '1,2,3';  // String
$app->find(['status' => ['$in' => $status]]);

// ✅ CORRECT
$status = explode(',', $_GET['status']);
$status = array_map('intval', $status);
$app->find(['status' => ['$in' => $status]]);
```

### 9. **Performance: Missing Indexes**
```php
// Large queries without indexes timeout/hang
$app->createIndex(['nomProduit' => 1]);
$app->createIndex(['estActifProduit' => 1, 'prixProduit' => -1]);
$app->createIndex(['dateCreation' => -1]);
```

### 10. **Null/Empty Field Confusion**
```php
// MongoDB distinguishes between:
// 1. Missing field: {nomProduit: <not in doc>}
// 2. Null: {nomProduit: null}
// 3. Empty string: {nomProduit: ''}

$app->find(['nomProduit' => ['$exists' => false]]);  // Missing
$app->find(['nomProduit' => null]);                  // Explicit null
$app->find(['nomProduit' => '']);                    // Empty string
```

---

## Performance Considerations

- **Pagination**: Always use `page`/`nbRows` in `json_data.php` to avoid loading entire collections
- **Indexes**: Create on frequently filtered/sorted fields in migration scripts
- **Query caching**: `AppSite` caches results by parameters - reuse instance if possible
- **Bulk operations**: Use `multi` flag and `$set` operator, not looping individual updates
- **Cursors**: ADODB cursors one-way; don't expect to iterate twice without re-querying
- **Socket.io**: Broadcast only to specific rooms, not globally, to avoid client overload

---

## File Organization

| Directory | Purpose |
|-----------|---------|
| `appclasses/` | Core ORM classes; extend App for entities |
| `appconf/` | Bootstrap & module loading; model configs |
| `bin/` | Routes, templates, cron jobs |
| `services/` | HTTP endpoints (json_*) returning JSON |
| `appcomponents/` | Riot.js components (deprecated) |
| `app_node/` | Node.js socket server & daemons |
| `appfunc/` | Utility functions for data conversion |
| `tpl/` | Template files for rendering |

---

## Cross-Component Integration

1. **PHP → Node.js**: HTTP POST to `/postScope`, `/run`, `/runModule`
2. **PHP → MongoDB**: Direct via `Mongo` extension (driver v1.x, deprecated)
3. **Client → PHP**: XHR to `json_data*.php` + `services/*`
4. **Client → Node.js**: WebSocket via socket.io, room-based subscriptions
5. **MongoDB ↔ All**: Central state store; eventual consistency via polling

---

## Environment & Debugging

- **Docker**: `docker-compose up` (PHP 5.6 on :8080, Node on :3005)
- **Local dev**: `php -S localhost:8000 -t ./web/` + `node idae_server.js`
- **Logs**: PHP → `/var/log/apache2/php-error.log`; Node → stdout
- **Errors**: PROD disables display_errors; PREPROD/LAN show errors
- **Helper**: `Helper::dump($var)` respects environment
- **Socket debugging**: Check browser console for WebSocket errors; verify port 3005 open

---

## Important Legacy Patterns

- **Session**: `session_start()` in index.php; custom path via `SESSION_PATH`
- **PHP 5.6 short tags**: Enabled in Dockerfile; `<?=` works directly
- **ADODB**: Some methods return cursors, others arrays; check documentation
- **MongoId**: Object type, not string; must cast for `_id` queries
- **Function naming**: `get_` (retrieve), `find_` (query), `set_` (configure)
- **Error suppression**: `@` operator used extensively; catch with `error_get_last()`

---

## Testing & Validation

- No formal test suite; manual QA + PREPROD staging environment
- Validation rules embedded in `appscheme_field` collection
- Use PREPROD before pushing to PROD

---

**Last Updated**: 2026-02-02 | **Framework**: PHP 5.6 / Node.js 12 (legacy) | **MongoDB**: v1.x (Mongo ext)
