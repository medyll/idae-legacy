# IDAE Legacy JavaScript Architecture Analysis

**Created**: 2026-02-06
**Context**: Deep technical analysis of the 2015-2016 era Single Page Application (SPA) architecture used in IDAE.

## Executive Summary

The client-side architecture is a sophisticated, pre-Webpack **Single Page Application** constructed manually. It features an aggressive custom asset loader ("The Bag"), a dual-layer caching strategy involving IndexedDB, and a metadata-driven UI powered by **PrototypeJS 1.7.3**.

While highly performant on low-bandwidth networks (due to caching), it represents significant technical debt due to its reliance on `eval`/`Blob` injection for scripts, making modern debugging and migration challenging.

---

## 1. The "Bag" Loading System (Asset Bootstrapping)

Instead of a standard `<script src="...">` or a bundled application, IDAE uses a client-side dynamic loader.

### Entry Point
-   **`index.php`**: Loads the minimal bootloader `javascript/vendor/bag.js`.
-   **`javascript/main_bag.js`**: Defines the dependency graph and starts the loading process.

### Dependency Graph (`require_trame`)
Dependencies are defined manually in `main_bag.js` within the `require_trame` object. This acts as a manual graph definition that a bundler like Webpack would normally generate.

```javascript
var require_trame = {
    require_boot:       [...], // Core bootstrapping libs (localforage, basket)
    require_hell:       [...], // PrototypeJS, Scriptaculous (Heavy legacy libs)
    require_to_log:     [...], // Logging and utility modules
    require_scripts:    [...], // Vendor plugins (Chartist, TinyMCE)
    require_app:        [...]  // The actual application logic (Controllers/Views)
}
```

### The Recursive Loader (`dyn_require`)
The function `dyn_require()` in `main_bag.js` implements the loading sequence:
1.  Iterates through the `require_queue`.
2.  Calls `bag.require()` for each block.
3.  Upon promise resolution, recursively calls itself (`dyn_require()`) to load the next block.

**Key Observation**: The naming of **`require_hell`** for the PrototypeJS/Scriptaculous bundle suggests the original developer was aware of the weight and complexity these legacy libraries imposed on the system.

---

## 2. Dual-Layer Caching Strategy

The application employs two distinct caching layers to minimize network requests.

### Layer A: Asset Caching (`bag.js`)
Crucial for checking if a new version of the code exists vs loading from local storage.
-   **Mechanism**: Uses `bag.js` (likely a fork or implementation similar to `basket.js`) to intercept script requests.
-   **Storage**: Scripts source code is stored as text blobs in **IndexedDB** (with LocalStorage fallback).
-   **Execution**:
    -   If cached: Source is read from DB and injected via `eval()` or `<script>` blob injection. **No HTTP request is made.**
    -   If stale/missing: HTTP request fetches file, stores in DB, then executes.
-   **Risk**: If the PHP server doesn't send correct ETag/Last-Modified headers, the client may never update files.

### Layer B: Data & State Caching (`app_cache.js`)
Handles application data persistence.
-   **Library**: `localforage` (wraps IndexedDB/WebSQL).
-   **Implementation**: `window.app_cache` instance.
-   **Key Generation**: `build_cache_key(file, vars)` generates MD5-like keys to store API responses or UI states.
-   **Maintenance**:
    -   Persistence is extremely high (survives browser restarts).
    -   **`app_cache_reset()`**: A critical utility function that nukes storage (`localforage.clear()`). Without calling this, clients may run against a new backend schema with cached, obsolete frontend definitions, causing invisible crashes.

---

## 3. Metadata-Driven "Scheme" Architecture

The UI is not hardcoded; it is generated at runtime based on database metadata, mirroring the PHP `AppScheme` architecture.

### The `schemeLoad()` Process
Located in `javascript/app/app_bootstrap.js`:
1.  **Trigger**: Called by `dyn_require` just before the app enters the "Ready" state.
2.  **Fetch**: Calls `get_data('json_scheme')` (maps to PHP service `json_data.php?piece=scheme`).
3.  **Store**: The huge JSON response containing all table definitions, field types, and validation rules is loaded into global memory:
    -   `window.APP.APPSCHEMES`
    -   `window.APP.APPFIELDS`
4.  **Render**: The application views rely on `APP.APPSCHEMES` to know how to render a form or a grid. If a field `nom` exists in the `client` table schema, the JS automatically renders a text input with the correct validation logic.

---

## 4. The PrototypeJS Facade

The codebase relies heavily on **PrototypeJS 1.7.3**, a predecessor to jQuery/Modern JS.

-   **Global Pollution**: PrototypeJS extends native objects (`Array.prototype`, `String.prototype`, `HTMLElement.prototype`).
-   **Syntax Patterns**:
    -   `$A(iterable)`: Converts array-likes to Arrays (used extensively for DOM lists).
    -   `Class.create()`: Legacy OOP inheritance model.
    -   `$('id')`: The original DOM selector (before jQuery co-opted the `$` symbol).
    -   `Element.update()` / `Element.insert()`: DOM manipulation methods.

**Migration Warning**: You cannot simply "drop in" standard modern JavaScript. `Array.forEach` might work, but specialized Prototype methods like `Array.pluck` or `String.evalScripts` are unique dependencies.

---

## 5. Initialization Flow Summary

1.  **HTML Load**: `index.php` serves barebones HTML + `bag.js`.
2.  **Manifest Calculation**: `main_bag.js` runs, calculates total assets size for the loading bar (`require_progress`).
3.  **Asset Hydration**:
    -   `bag.js` checks IndexedDB.
    -   Misses are fetched via XHR.
    -   Code is evaluated.
4.  **Boot Phase**: `require_boot` loads `localforage` and polyfills.
5.  **Core Load**: `require_hell` (Prototype) and `require_app` (MVC logic) load.
6.  **Bootstrap**: `dyn_require` finishes queue -> Calls `schemeLoad()`.
7.  **Data Sync**: `schemeLoad` retrieves DB structure from PHP.
8.  **App Ready**: `idae_log()` runs, wallpaper loads, UI renders.

---

## 6. Migration & Debugging Guidelines

### Debugging "The Bag"
Because scripts are injected via `eval` or Blobs:
-   **Chrome DevTools**: You may not see file names in the Sources tab. Look for `VMxxxx` scripts or source maps if available (unlikely).
-   **Breakpoints**: `debugger;` statements in code are more reliable than clicking line numbers in DevTools.

### PHP 8.2 Compatibility Requirements
The PHP migration **must not break** the asset serving mechanism.
-   Ensure Apache/PHP returns correct `Content-Type` headers for `.js` files if served via PHP wrappers.
-   Ensure `Last-Modified` headers are preserved. If `bag.js` thinks a file hasn't changed (due to missing/bad headers), it will serve the old cached version forever.

### Refactoring Strategy
**Do not rewrite the loader in Phase 1.**
The interdependence between `main_bag.js`, `bag.js`, and the globally polluted PrototypeJS namespace is fragile.
-   **Keep**: The `bag.js` loader.
-   **Keep**: PrototypeJS (it is too deeply ingrained to replace).
-   **Focus**: Ensuring the *data* fed to `schemeLoad()` (via `json_data.php`) remains structurally identical to the legacy output.
