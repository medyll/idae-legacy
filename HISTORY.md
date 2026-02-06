# HISTORY of IDAE Legacy

> *A chronicle of a visionary framework, its rise, its drift, and its renaissance.*

## Genesis (2007): The Vision

In 2007, the web was a text-heavy, reload-driven ecosystem. PHP 5.2 was the standard, smartphones were barely a concept (the iPhone launched that June), and relational databases ruled supreme. "NoSQL" was a whisper in the back alleys of data engineering. It was in this era that **Meddy Lebrun** architected **IDAE**.

The goal was audacious: to create a web application framework that defied the rigidity of SQL schemas. While the world was busy managing migrations and writing `ALTER TABLE` statements, IDAE was built on a fluid, metadata-driven philosophy. It was a CMS/CRM hybrid that prioritized **plasticity** above all else.

### The "No-Schema" Architecture
At its core lay the **`AppScheme`**. Instead of hardcoding database columns, IDAE stored entity definitions as data within MongoDB itself.
-   **Metadata Collections**: The system relied on `appscheme`, `appscheme_field`, and `appscheme_field_type` to define the structure of data.
-   **Dynamic Field Binding**: Fields were not fixed columns but dynamic bindings generated at runtime (e.g., `codeAppscheme_field` + `tableName`), creating a flexible data layer that could swallow any business requirement without schema migrations.
-   **The ORM**: The `App` class (and its descendants `AppSite`, `ClassIdae`, `ClassAct`) served as a powerful Object-Relational Mapper. Unlike Doctrine or Eloquent, it didn't map classes to tables; it mapped abstract business intents to dynamic MongoDB documents, handling complex relationships (Grids/Reverse Grids) automatically.

## The Vanguard Era (2008 - 2012)

By 2008, IDAE was technologically years ahead of its mainstream counterparts.

### Early Real-Time Adoption
Long before WebSockets (RFC 6455) were standardized in 2011, IDAE was experimenting with bidirectional communication.
-   **The Node.js Bridge**: It integrated a **Node.js + Socket.io** layer (`idae_server.js`) running alongside the PHP core.
-   **Architecture**: PHP handled business logic and persistence, while Node.js broadcasted state changes to connected clients via shared Redis/Mongo channels. This allowed for room-based subscriptions and "magical" live updates where a change by one user was instantly reflected on another's screen—a feature that wouldn't become common in frameworks until the late 2010s.

## The Drift (2015 - 2024)

As the years passed, the web matured. PHP evolved from 5.x to 7 and then 8, introducing strict typing and killing unsafe features. MongoDB, the engine powering IDAE's flexibility, underwent a radical transformation.

### The Technical Debt Trap
The very technologies that made IDAE cutting-edge became its anchors.
1.  **PHP 5.6**: The codebase relied on flexible typing, variable variables, and reference behaviors that were deprecated or removed in PHP 7/8 (e.g., `continue` inside `switch`, undefined constants).
2.  **The MongoDB Paradigm Shift**: The most critical blow was MongoDB's driver rewrite. The legacy `ext-mongo` extension (built on C, utilizing stateful, persistent connections resource types like `MongoId`) was abandoned for `mongodb/mongodb` (a high-level PHP library atop the low-level `ext-mongodb` driver).
    -   **Binary Incompatibility**: `MongoId` → `MongoDB\BSON\ObjectId`.
    -   **Regex Changes**: `MongoRegex` → `MongoDB\BSON\Regex`.
    -   **Cursor Behavior**: The old stateful cursors (`getNext()`, `timeout()`) were replaced by stateless `Traversable` iterators.

The cost of migration was catastrophic. It required rewriting the entire `ClassApp` ORM—the heart of the system—and touching thousands of files where `new MongoId()` was hardcoded. The application was frozen in time, requiring legacy Docker containers just to breathe.

## The Renaissance (2026): The Migration

In early 2026, the decision was made: **Standardize or perish.**  
The mission: migrate the 2007 codebase to a modern **PHP 8.2** environment, containerized via Docker, without rewriting the business logic that had served faithfully for nearly two decades.

### The Strategy: `MongoCompat` & The Facade Pattern
Rather than rewriting thousands of lines of business logic, the migration team (led by AI assistance) engineered a compatibility bridge.

1.  **Driver Shimming**:
    -   A new `AppCommon\MongoCompat` class acts as a translator. It intercepts legacy calls (`toObjectId`, `toRegex`) and converts them to modern BSON equivalents.
    -   `LegacyMongoDB` and `LegacyMongoCollection` classes serve as proxies, implementing the old API surface (`insert`, `update`, `findOne`) but internally routing commands to the modern `MongoDB\Client`.

2.  **Cursor Emulation**:
    -   The `MongodbCursorWrapper` was created to wrap the modern `Iterator` cursor. It manually implements the legacy `getNext()` method by managing an internal `IteratorIterator`, allowing old ADODB-style loops (`while ($row = $cursor->getNext())`) to function on PHP 8.2.

3.  **Lazy-Loading Refactor**:
    -   The `ClassApp` constructor was refactored to lazily load metadata. Previously, instantiating `App` triggered heavy synchronous Mongo lookups. The new design uses PHP magic methods (`__get`) to fetch schema definitions only when accessed, significantly improving boot time in the containerized environment.

### The Great Blockers
The path was not easy.
-   **The Session Paradox**: The PHP session handler, attempting garbage collection during startup, would crash the application because it couldn't authenticate with the new Dockerized MongoDB instance via `host.docker.internal`.
-   **Authentication Complexity**: Modern MongoDB requires explicit authentication databases (often `admin`), whereas the legacy code assumed implicit access. This required injecting `MDB_USER`/`MDB_PASSWORD` securely via environment variables and rewriting connection strings.

## February 2026: The Hybrid Pivot

As the migration deepened, the team realized that fully containerizing a 15-year-old stateful monolith was a bridge too far. The decision was made to pivot.

### The "Hybrid" Architecture
Instead of isolating the database inside a container—risking data loss and complicating backups—the architecture was inverted.
-   **PHP 8.2** remains containerized, providing a clean, modern runtime.
-   **Data Services (MongoDB, MySQL)** remain on the **Host Machine**.
-   **The Bridge**: Docker's `host.docker.internal` became the lifeline, connecting the encapsulated logic to the persistent data.

### The Great Cleanup
Simultaneously, a massive reconnaissance mission mapped the "Legacy JS" territories.
-   **Node.js Simplification**: Obsolete Node subsystems were purged, leaving only the essential socket server.
-   **Documentation as Code**: The creation of `JS_STRUCTURE_LEGACY.md` and `MIGRATION_STATUS.md` transformed oral history into written canon.

### The Battle of Authentication
Progress was fierce but halted by the **"Auth Wall"**. As the PHP container tried to speak to the Host Mongo, strict modern authentication rules triggered a wave of `BulkWriteException`s. The legacy "implicit trust" model clashed with modern security standards, leaving the application in a state of "unauthenticated suspension" while the team forged new keys.

## The Future

Today, IDAE stands at the threshold of a new era. It is a testament to the longevity of well-conceived software. By wrapping its legacy core in modern infrastructure (Docker, PHP 8.2, adapters), it preserves the genius of Meddy Lebrun's original vision while shedding the shackles of obsolete infrastructure.

It remains a fascinating case study in software evolution: how a "No-Schema" pioneer survived the SQL wars, the death of PHP 5, and the reinvention of MongoDB, to run again on the modern web.

---

## February 6, 2026: First Boot — The Application Lives Again

After weeks of migration work, **IDAE successfully booted for the first time on PHP 8.2 with the modern MongoDB driver**. The login screen appeared. The GUI loaded. The socket bridge responded. For the first time since the migration began, the full application stack was alive:

**PHP 8.2 (Docker)** ↔ **Node.js 18 + Socket.IO 4** ↔ **MongoDB (Host)** ↔ **Legacy Browser Client**

### The Final Chain of Blockers

The last stretch required solving eight interlocking issues, each one invisible until the previous was fixed:

1.  **CORS Policy** — Socket.IO server used `origin: "*"` but the legacy client connected with `withCredentials: true`. Modern browsers reject wildcard origins when credentials are included. Fixed by specifying explicit origins (`http://localhost:8080`) and enabling `credentials: true` in the Socket.IO server configuration.

2.  **Corrupted DOCUMENTDOMAIN** — The client-side JavaScript built `DOCUMENTDOMAIN` using `window.document.location.href` instead of `window.document.location.host`. On localhost, this produced `localhost:8080/index.php?retry=2` instead of `localhost:8080`, causing every proxied URL to be malformed (e.g., `http://localhost:8080/index.php?retry=2/services/json_ssid.php`). Fixed in `app.js`, `app_bootstrap.js`, and `methods.js`.

3.  **Trailing Slash Sanitization** — Even after the href fix, some edge cases produced `localhost:8080/` with a trailing slash, causing double-slash URLs (`http://localhost:8080//services/...`). Added a `cleanDomain()` sanitizer in the Node.js socket handlers to strip trailing slashes and any leaked path components.

4.  **Missing `socketModule` DOM Handler** — The browser-side `app_socket.js` was missing the `socket.on('socketModule', ...)` listener responsible for injecting PHP-rendered HTML into the DOM. The server responded correctly, but the client silently discarded the responses. Added the handler to update `$(element)` with the response body and fire `content:loaded`.

5.  **Axios JSON Auto-Parse** — The Node.js `phpBridge` used Axios, which automatically parses JSON responses into JavaScript objects. But the legacy client expected raw JSON **strings** (to call `JSON.parse()` manually). Receiving `[object Object]` caused silent parse failures. Fixed by re-stringifying object responses before sending them back through the socket.

6.  **PHP Redirect Loop** — When the Node server proxied requests to PHP, Axios followed HTTP redirects (302 to `reindex.php`, then back to `index.php`). After 3 retries, PHP returned an HTML error page ("Session Error") instead of JSON. Fixed by setting `maxRedirects: 0` in the PHP bridge, letting the Node server detect and handle redirects gracefully.

7.  **MongoDB Cursor Rewind** — The `MongodbCursorWrapper::rewind()` method attempted to call `rewind()` on a MongoDB cursor that had already been partially iterated, triggering a `LogicException`. Fixed by converting the cursor to an array on rewind, since MongoDB cursors are forward-only.

8.  **PHP Error Display Pollution** — With `display_errors = 1`, PHP warnings (`Undefined array key`, etc.) were injected directly into JSON and HTML responses. The client JavaScript couldn't parse the corrupted output. Disabled `display_errors` in `conf.lan.inc.php` — errors are still logged to Apache's error log inside the container.

### The Lesson

Each fix was trivial in isolation. But like dominoes, they formed a chain where one masked the next. The CORS fix revealed the URL corruption. The URL fix revealed the missing handler. The handler fix revealed the JSON parsing bug. The parsing fix revealed the redirect loop. The redirect fix revealed the cursor crash. And the cursor fix revealed the error display pollution.

The application's original architecture — a PHP backend, a Node.js real-time layer, and a Prototype.js browser client communicating via Socket.IO — was designed in 2007 and remains fundamentally sound. It just needed its plumbing reconnected after 18 years of infrastructure evolution.

### The Strict Typing Era (Feb 6, 2026)

With the infrastructure stabilized, the focus shifted to code execution within the PHP 8.2 container. The strictness of modern PHP exposed latent bugs in the logic that PHP 5.6 had previously tolerated.

9. **The Login Null-Pointer**: Authentication via `actions.php` was failing with a 500 error due to `sizeof(null)` usage. In PHP 8+, `count()` and `sizeof()` throw fatal errors on non-countable types. Fixed by adding null coalescence checks.
10. **The Socket Recursion Loop**: A critical flaw in `skelMdl::doSocket` was discovered. When the Node.js socket server was offline (e.g., during development), the connection failure triggered a system notification. This notification _itself_ attempted to use the socket to broadcast the error, creating an infinite recursion loop that crashed the stack. Added a `try-catch` block to break the cycle and fallback to log-only error reporting.
11. **Static vs Dynamic Methods**: Legacy code frequently called methods statically (`fonctionsProduction::isTrueFloat`) that were defined as instance methods. While PHP 5 warns, PHP 8 throws fatal errors. Converted utility methods to `static` where appropriate.
12. **GridFS Property Access**: The `ClassAct::imgSrc` method failed when trying to access properties (`$file['file']`) on `MongoGridFSFile` objects returned by the compatibility layer. The modern driver returns objects, not arrays. Updated `MongoCompat` to implement the magic `__get` method, restoring array-like property access for legacy code.
13. **String Search Type Safety**: The `str_find` helper failed when processing array keys (integers) because `strpos` in PHP 8 no longer accepts integers as the haystack. Added strict type casting to ensure string operations receive strings.
14. **Socket Host Parsing (fsockopen)**: The PHP backend's socket client (`skelMdl.php`) was failing with a 30s timeout during login. The cause was `fsockopen` receiving `localhost:8080` as the hostname (extracted from `$_SERVER['HTTP_HOST']`), where the port should only be passed as the second argument. Fixed by stripping the port from the host string, restoring instant backend-to-socket-server communication.
15. **Frontend Cache Busting**: The browser persisted in serving stale versions of `bag.js` and other core libraries despite server changes. Implemented a "Nuclear Option" in `main_bag.js`: a global `cache_buster` variable (timestamp) is now appended to every script and CSS file requested by the loader (`?v=...`), ensuring the development cycle isn't hindered by aggressive caching.
16. **Login Logic Updates**: Updated `idae/web/mdl/app/app_login/actions.php` to handle PHP 8.2 deprecations, specifically replacing the ternary shorthand `?:` with the null coalescing operator `??` to prevent warnings during authentication checks.
