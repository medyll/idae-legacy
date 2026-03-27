# IDAE Legacy — Project Context

## Project Overview

**IDAE Legacy** is a migration project modernizing a 2007-era PHP 5.6 / MongoDB CMS/CRM platform to run on **PHP 8.2** with the modern MongoDB driver, containerized via Docker. The application is a schema-driven, real-time business management system featuring dynamic UI generation, role-based access control, and WebSocket-based live data synchronization.

### Key Technologies
| Layer | Technology |
|-------|------------|
| **Backend** | PHP 8.2 (migrated from 5.6), Apache |
| **Database** | MongoDB 7 (modern driver v2.0), MySQL 5.7 (legacy) |
| **Real-time** | Node.js 18 + Socket.IO 4 |
| **Frontend** | Prototype.js, jQuery, Chart.js, TinyMCE |
| **ORM** | Custom `App`/`AppSite` class hierarchy with `MongoCompat` bridge |
| **Container** | Docker (PHP 8.2 + Apache + Node.js) |

### Architecture Highlights
- **Schema-driven metadata**: Entity definitions stored in MongoDB (`appscheme*` collections), not static SQL schemas
- **Dynamic field naming**: Fields follow pattern `codeAppscheme_field` + table name (e.g., `nom` → `nomProduit`)
- **Real-time sync**: Socket.IO broadcasts data changes to subscribed clients
- **Hybrid deployment**: PHP containerized, data services on host (accessible via `host.docker.internal`)

---

## Quick Start

### Prerequisites
- Docker Desktop (Windows) or Docker Compose
- Git
- PowerShell (for helper scripts)

### Start the Stack
```powershell
# From project root
docker-compose up --build

# Access application
# http://localhost:8080

# Default credentials
# Username: Mydde
# Password: malaterre
```

### Stop the Stack
```powershell
docker-compose down
```

### Emergency Reset
```powershell
.\docker-emergency.ps1   # Force container reset
```

---

## Development Commands

### Docker Management
| Command | Description |
|---------|-------------|
| `docker-compose up` | Start all services |
| `docker-compose down` | Stop containers |
| `docker-compose build --no-cache` | Rebuild without cache |
| `.\docker-restart.ps1` | Restart Apache/container |
| `.\docker-health.ps1` | Run diagnostics |
| `.\docker-logs.ps1` | View logs |
| `.\docker-logs.ps1 -Follow` | Live tail logs |

### PHP Commands (run from `idae/web/`)
```powershell
composer install                     # Install dependencies
composer test                        # Run PHPUnit tests
composer run test -- --filter=TestName  # Run single test
composer run test -- --testsuite=Unit   # Unit tests
composer run test -- --testsuite=Integration  # Integration tests

vendor/bin/phpunit                   # Direct PHPUnit
vendor/bin/phpunit --filter=testName  # Run test by name
vendor/bin/phpstan analyse           # Static analysis (level 6)
vendor/bin/phpstan analyse --memory-limit=512M  # With more memory
```

### Node.js Commands
```powershell
cd idae/web/app_node
npm install                          # Install dependencies
npm run start                        # Start Socket.IO server (background)
npm run stop                         # Stop server
npm run status                       # Check status
npm run logs                         # View logs
npm run dev                          # Development mode (foreground)
```

### Test Scripts
```powershell
# Migration tests (after stack boots)
php idae/web/test_migration.php
php idae/web/test_integration.php

# MongoDB connectivity
docker exec idae-legacy php /var/www/html/idae/web/check_mongo.php
```

---

## Project Structure

```
idae-legacy/
├── docker-compose.yml          # Docker orchestration
├── Dockerfile                  # PHP 8.2 + Apache image
├── package.json                # Node.js dependencies
├── *.ps1                       # PowerShell helper scripts
├── MIGRATION*.md               # Migration planning & status
├── MONGOCOMPAT.md              # MongoDB compatibility guide
├── AGENTS.md                   # Coding standards & rules
├── DEBUGGING.md                # Debugging best practices
└── idae/
    ├── config/                 # Apache vhosts, host configs
    └── web/                    # Main application
        ├── appclasses/         # Core ORM classes (App, AppSite, etc.)
        │   ├── appcommon/      # Shared utilities (MongoCompat)
        │   ├── ClassApp.php    # Base ORM class
        │   └── ClassSession.php
        ├── appconf/            # Bootstrap & module configs
        ├── appfunc/            # Utility functions
        ├── app_node/           # Node.js Socket.IO server
        ├── services/           # JSON API endpoints
        │   ├── json_data.php   # Data queries
        │   └── json_scheme.php # Schema metadata
        ├── mdl/                # Dynamic modules (login, users, etc.)
        ├── actions.php         # Main action handler
        ├── index.php           # Entry point
        ├── conf.inc.php        # Main configuration
        ├── composer.json       # PHP dependencies
        └── tests/              # PHPUnit tests
            ├── Unit/
            └── Integration/
```

---

## Coding Standards

### PHP Requirements
```php
<?php
declare(strict_types=1);  // Always use strict types

namespace AppCommon;

use MongoDB\BSON\ObjectId;

class MongoCompat
{
    /**
     * Convert value to ObjectId
     */
    public static function toObjectId($value): ?ObjectId
    {
        // Implementation
    }
}
```

### Key Rules
1. **Strict types**: Add `declare(strict_types=1);` to all PHP files
2. **Modern syntax**: Use `[]` instead of `array()`, null coalescing `??`, arrow functions
3. **Type hints**: Always specify parameter and return types
4. **No error suppression**: Never use `@` operator
5. **Safe debugging**: Use `error_log()` — never `echo`/`print` (breaks AJAX)
6. **Naming**: 
   - Classes: `PascalCase` (e.g., `ClassApp`, `MongoCompat`)
   - Methods: `camelCase` (e.g., `findOne()`)
   - Properties: `camelCase` (e.g., `$sitebaseApp`)
   - Constants: `UPPER_SNAKE_CASE` (e.g., `MONGO_ENV`)

### MongoDB Guidelines (CRITICAL)
All MongoDB operations **MUST** use `AppCommon\MongoCompat`:

```php
use AppCommon\MongoCompat;

// Convert IDs
$id = MongoCompat::toObjectId($value);
$intId = MongoCompat::toIntSafe($value);

// Regex search
$filter = ['nomProduit' => MongoCompat::toRegex($search, 'i')];

// Date handling
$date = MongoCompat::toDate($value);

// Cursor iteration
foreach (MongoCompat::toIterable($cursor) as $doc) { ... }
```

**Never use legacy types directly:**
- ❌ `new MongoId()` → ✅ `MongoCompat::toObjectId()`
- ❌ `new MongoRegex()` → ✅ `MongoCompat::toRegex()`
- ❌ `new MongoDate()` → ✅ `MongoCompat::toDate()`

### File Headers
Preserve original headers and add modification dates:
```php
/**
 * MongoCompat.php — MongoDB Driver Compatibility Helper
 * Date: 2026-02-02
 * Time: 14:30
 * Modified: 2026-03-04
 */
```

---

## Testing

### Test Structure
- **Unit tests**: `idae/web/tests/Unit/` — Isolated class tests
- **Integration tests**: `idae/web/tests/Integration/` — Full stack tests
- **Base class**: `Idae\Tests\TestCase`
- **Naming**: `ClassNameTest.php`

### Example Test
```php
<?php
declare(strict_types=1);

namespace Idae\Tests\Unit;

use Idae\Tests\TestCase;
use AppCommon\MongoCompat;

class MongoCompatTest extends TestCase
{
    public function testToObjectIdReturnsObjectId(): void
    {
        $id = MongoCompat::toObjectId('507f1f77bcf86cd799439011');
        $this->assertInstanceOf(\MongoDB\BSON\ObjectId::class, $id);
    }
    
    public function testToObjectIdReturnsNullOnInvalid(): void
    {
        $id = MongoCompat::toObjectId('invalid');
        $this->assertNull($id);
    }
}
```

### Running Tests
```powershell
# All tests
composer test

# Single test
vendor/bin/phpunit --filter=MongoCompatTest

# Test suite
composer run test -- --testsuite=Unit
```

---

## Debugging

### Log Locations
| Log | Location |
|-----|----------|
| PHP errors | `/var/log/apache2/php-error.log` (in container) |
| Apache errors | `/var/log/apache2/error.log` |
| Node.js stdout | `idae/web/app_node/logs/socket-out.log` |
| Node.js stderr | `idae/web/app_node/logs/socket-err.log` |

### Safe Debugging Techniques
```php
// ✅ GOOD: Use error_log()
error_log('[DEBUG] Session status: ' . session_status());
error_log('[DEBUG] Value: ' . print_r($value, true));

// ❌ BAD: Never use echo/die (breaks AJAX)
// echo "debug: $value";  // Corrupts JSON responses
// die('stopped');        // Causes infinite browser hang
```

### Environment-Based Debug
```php
// Only show debug in LAN/preprod
if (defined('ENVIRONEMENT') && ENVIRONEMENT !== 'PROD') {
    error_log('[DEV] Debug info: ' . $info);
}
```

### Diagnostic Tools
```powershell
# Check container health
docker ps
docker inspect idae-legacy --format='{{.State.Health.Status}}'

# Test MongoDB connection
docker exec idae-legacy php check_mongo.php

# View recent errors
.\docker-logs.ps1 -Errors

# Follow logs in real-time
.\docker-logs.ps1 -Follow
```

---

## Common Pitfalls

| Issue | Solution |
|-------|----------|
| `sizeof(null)` error | Use `count($arr ?? [])` or `isset()` check |
| Field name mismatch | Use `nomProduit` not `nom` (append table name) |
| Socket connection refused | Use `host.docker.internal` from container |
| CORS errors | Ensure Socket.IO has explicit origins, not `*` |
| JSON parse failures | Check for PHP warnings in response (disable `display_errors`) |
| Session not persisting | Verify MongoDB connection, check `MDB_USER`/`MDB_PASSWORD` |
| Infinite redirect loop | Check session initialization, cookie settings |
| Cursor already iterated | Use `MongoCompat::toIterable()` or convert to array |

---

## Key Reference Files

| File | Purpose |
|------|---------|
| [`AGENTS.md`](AGENTS.md) | Coding standards, MongoDB rules, file headers |
| [`MONGOCOMPAT.md`](MONGOCOMPAT.md) | MongoDB compatibility helper reference |
| [`MIGRATION_PHASE_2.md`](MIGRATION_PHASE_2.md) | Phase 2 modernization plan |
| [`MIGRATION_STATUS.md`](MIGRATION_STATUS.md) | Current migration status |
| [`DEBUGGING.md`](DEBUGGING.md) | Safe debugging practices |
| [`HISTORY.md`](HISTORY.md) | Project history and evolution |
| [`SCHEMA.md`](SCHEMA.md) | Schema-driven metadata architecture |
| [`idae/web/README.md`](idae/web/README.md) | Full application documentation |

---

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `MDB_USER` | MongoDB username | (empty) |
| `MDB_PASSWORD` | MongoDB password | (empty) |
| `MDB_PREFIX` | MongoDB database prefix | (empty) |
| `MONGO_ENV` | Environment (dev/test/prod) | `dev` |
| `MONGO_HOST` | MongoDB host | `host.docker.internal` |
| `MONGO_TEST_DSN` | Test database DSN | `mongodb://mongo-test:27017` |
| `DEBUG_SESSION` | Enable session debugging | `0` |
| `DEBUG_DB` | Enable database debugging | `1` |

---

## Architecture Notes

### Bootstrap Flow
1. **Request** → `reindex.php` (session cleanup) → `index.php`
2. **Config** → `conf.inc.php` loads environment-specific settings
3. **Database** → MongoDB connection via `ClassApp` + `MongoCompat`
4. **Modules** → Auto-discover and initialize from `appconf/`
5. **Socket** → Node.js server handles real-time broadcasts

### Request Flow
```
Browser → Apache/PHP 8.2 → ClassApp (ORM) → MongoDB
                     ↓
                Node.js Socket.IO → Broadcast to subscribed clients
```

### Permission System
```php
// App-level permission
if (droit('ADMIN')) { ... }

// Table-level permission (C=Create, R=Read, U=Update, D=Delete, L=List)
if (droit_table($_SESSION['idagent'], 'R', 'produit')) { ... }
```

---

## Contributing

1. **Branch**: Create feature branches from `migration` branch
2. **Standards**: Follow `AGENTS.md` coding standards
3. **Tests**: Add tests for new functionality
4. **PR**: Open pull request targeting `migration` branch
5. **Docs**: Update relevant documentation files

### Before Committing
```powershell
# Run tests
composer test

# Static analysis
vendor/bin/phpstan analyse

# Check diffs
git diff HEAD
```

---

## Troubleshooting

### Container Won't Start
```powershell
# Check logs
docker logs idae-legacy

# Verify MongoDB connectivity
docker exec idae-legacy php check_mongo.php

# Emergency reset
.\docker-emergency.ps1
```

### Socket.IO Not Connecting
```powershell
# Check Node.js server
npm run status

# Restart socket server
npm run stop
npm run start

# Verify port 3005 is open
netstat -an | findstr 3005
```

### Session Issues
```powershell
# Enable session debugging
$env:DEBUG_SESSION=1
docker-compose restart

# View session logs
.\docker-logs.ps1 -Session

# Clear session collection (if needed)
# Use MongoDB shell or Compass
```

### PHP Errors
```powershell
# View PHP error log
docker exec idae-legacy tail -f /var/log/apache2/php-error.log

# Or use helper script
.\docker-logs.ps1 -Errors
```

---

## Project Status

**Current Phase**: Phase 2 Modernization (in progress)

**Milestones Achieved**:
- ✅ Application boots on PHP 8.2
- ✅ Login/authentication functional
- ✅ MongoDB modern driver integrated via `MongoCompat`
- ✅ Socket.IO real-time notifications working
- ✅ Docker development environment operational
- ✅ PHPUnit test suite established

**In Progress**:
- 🔄 Strict typing across all PHP files
- 🔄 Code modernization (remove deprecated patterns)
- 🔄 Test coverage expansion

---

## Contact & Support

- **Documentation**: See linked `.md` files in project root
- **Issues**: Create GitHub issues for bugs or questions
- **Migration Questions**: Update `MIGRATION_STATUS.md` or open discussion

---

**Last Updated**: 2026-03-27  
**Project Root**: `D:\boulot\wamp64\www\idae-legacy`  
**Main Branch**: `main`  
**Development Branch**: `migration`
