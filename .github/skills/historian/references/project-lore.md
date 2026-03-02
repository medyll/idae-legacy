# IDAE Legacy — Project Lore

Reference for the Historian skill. Read this when writing a new chronicle entry
to ground the narrative in accurate historical context.

---

## Table of Contents

1. [The Eras](#the-eras)
2. [Genesis — The AppScheme Philosophy (2007)](#1-genesis--the-appscheme-philosophy-2007)
3. [The Bag System — A JS Architecture Born of Necessity](#2-the-bag-system--a-js-architecture-born-of-necessity)
4. [The Drift — Technical Debt Accumulates (2015–2024)](#3-the-drift--technical-debt-accumulates-20152024)
5. [The Renaissance — Migration Campaign (2025–2026)](#4-the-renaissance--migration-campaign-20252026)
6. [Key Characters](#key-characters)
7. [Vocabulary & Epithets](#vocabulary--epithets)

---

## The Eras

| Era | Years | Epithet |
|-----|-------|---------|
| **Genesis** | 2007–2014 | *The Architect's Vision* |
| **The Drift** | 2015–2024 | *Frozen in Amber* |
| **The Renaissance** | 2025–2026 | *The Grand Migration* |
| **Phase 2** | 2026– | *The Reformation* |

---

## 1. Genesis — The AppScheme Philosophy (2007)

- **Architect:** Meddy Lebrun.
- **Core Insight:** Eliminate the database schema as a source of truth. Instead, store all entity definitions in MongoDB (`appscheme` collection) and bind fields dynamically at runtime.
- **Field Naming Law:** Every stored field is named `codeAppscheme_field` + `ucfirst(tableName)`. Example: field `nom` on table `produit` → `nomProduit`. This law is immutable and sacred.
- **The ORM:** `ClassApp` — a custom ORM mapping business intents to dynamic MongoDB documents. It handled complex grids and reverse-grids without a single SQL `CREATE TABLE`.
- **Significance:** Radical at the time. Gave the system extraordinary flexibility; also bound it to MongoDB for eternity.

---

## 2. The Bag System — A JS Architecture Born of Necessity

- **Type:** Pre-Webpack Single Page Application (SPA), built c. 2015, long before modern bundlers.
- **Loader:** `bag.js` + `main_bag.js` — a hand-crafted dependency graph resolver that stores scripts as Blobs in IndexedDB and injects them via `eval`. Webpack before Webpack existed.
- **The Dependency Hell Bundle:** PrototypeJS 1.7.3 + Scriptaculous, collectively named `require_hell` in the codebase. They extend native `Array`, `String`, and `Element` — ripping them out would detonate the entire frontend.
- **Caching Layers:**
  - *Layer A (Assets):* Source code cached in IndexedDB. Clear with `app_cache_reset()` in the console.
  - *Layer B (Data):* Application state via `localforage` in `app_cache.js`.
- **UI Rendering:** Fully metadata-driven. `schemeLoad()` calls `json_scheme.php` and populates `window.APP.APPSCHEMES`. The HTML is intentionally empty — the UI is assembled from MongoDB metadata at runtime, every time.

---

## 3. The Drift — Technical Debt Accumulates (2015–2024)

A decade of stasis. The codebase was frozen in PHP 5.6 long after PHP 5.6 itself was frozen in history.

- **The PHP Trap:** Relied on `variable variables` and the deprecated `ext-mongo` C extension. Upgrading PHP meant rewriting the driver layer.
- **The Driver Gap:** The legacy `MongoClient` / `MongoId` / stateful cursor API was binary-incompatible with the modern `mongodb/mongodb` library (`ObjectId`, `BSON`, stateless cursors).
- **The `getNext()` Nemesis:** The old driver's stateful `getNext()` iterator was used throughout ADODB-style loops. The modern library had no equivalent — a cursor must be iterated as a whole. Shimming this was the single biggest blocker.
- **The `MongoId` Sprawl:** Thousands of instantiations of `new MongoId($x)` scattered across the codebase. Each one had to become `MongoCompat::toObjectId($x)`.
- **Stalled Attempts:** The migration was attempted and abandoned at least twice before 2025. Each time, the sheer scope of the cursor and ID problems drove the effort to a halt.

---

## 4. The Renaissance — Migration Campaign (2025–2026)

The campaign that broke the stasis.

- **Objective:** Dockerize the app on PHP 8.2 without rewriting business logic. Backward-compatible modernization.
- **The Facade (`MongoCompat`):**
  - `AppCommon\MongoCompat` intercepts all legacy MongoDB type construction and routes it to modern BSON equivalents.
  - `MongodbCursorWrapper` re-implements the stateful `getNext()` iterator behavior as a compatibility shim — the key that unlocked the cursor battle.
  - `ClassApp` constructor refactored with lazy `__get` loading, eliminating schema-fetch bottlenecks in the new container environment.
- **Infrastructure:** PHP 8.2 Apache container; MongoDB kept on the host at `host.docker.internal:27017`.
- **Phase 1 Victory (2026):** The application booted. The UI was functional. The long siege had produced a beachhead.
- **Phase 2 (ongoing):** Modernization of internals — strict types, Composer autoloading, dependency injection, CSS reformulation, test coverage.

---

## Key Characters

| Name | Role |
|------|------|
| **Meddy Lebrun** | Original architect. Author of the AppScheme vision. The project's founding spirit. |
| **ClassApp** | The ORM. Heart of the system. Still beating after two decades. |
| **MongoCompat** | The great mediator. Born in the Renaissance to bridge two irreconcilable worlds. |
| **MongodbCursorWrapper** | The cursor hero. Tamed the `getNext()` beast so thousands of loops could live. |
| **bag.js** | The ghost in the machine. A pre-modern bundler that still haunts the frontend. |
| **PrototypeJS** | The old guard. Cannot be deposed. Extends everything; tolerates no rivals. |

---

## Vocabulary & Epithets

When writing chronicle entries, use these consistently:

| Technical Fact | Narrative Form |
|----------------|----------------|
| Migration | The Campaign / The Grand Migration |
| A bug fixed | An adversary vanquished |
| A test suite passing | A triumph / a beachhead secured |
| Technical debt | The accumulated weight of the years / the sediment of the Drift |
| The `ext-mongo` driver | The old C-driver / the ancient compact |
| PHP 8.2 upgrade | The Reformation / crossing into the modern era |
| Docker container | The Citadel / the new fortress |
| A refactor | A reformation / a restructuring of the ranks |
| An ongoing blocker | A contested front / an unresolved siege |
| MongoDB `ObjectId` | The new seal / the BSON covenant |
