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
