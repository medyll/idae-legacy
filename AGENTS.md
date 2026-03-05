# AGENTS

**Context**: Migration from Legacy (PHP 5.6/Mongo v1) to Modern (PHP 8.2/Mongo v1.15+).

---

## Environment & Build Commands

### Docker Development
```bash
docker-compose up                    # Start PHP 8.2 container with MongoDB on host.docker.internal
docker-compose down                  # Stop containers
docker-compose build --no-cache      # Rebuild without cache
```

### PHP Commands (run from `idae/web/`)
```bash
composer install                     # Install PHP dependencies
composer test                        # Run PHPUnit tests
composer run test -- --filter=TestName  # Run single test (e.g., --filter=ClassAppTest)
composer run test -- --testsuite=Unit   # Run specific test suite (Unit or Integration)
composer run test -- --testsuite=Integration

vendor/bin/phpunit                   # Direct PHPUnit invocation
vendor/bin/phpunit --filter=testName  # Run single test by name
vendor/bin/phpstan analyse           # Run static analysis (level 6)
vendor/bin/phpstan analyse --memory-limit=512M  # With increased memory
vendor/bin/phpstan analyse appclasses/ClassApp.php  # Analyze single file
```

### Node.js Commands
```bash
cd idae/web/app_node
npm install                           # Install Node dependencies
node idae_server.js                   # Start Socket.io server
```

### Debugging Scripts (PowerShell)
```powershell
docker-restart.ps1      # Restart Apache/container
docker-health.ps1       # Run diagnostics
docker-emergency.ps1    # Force reset
docker-logs.ps1         # View logs
```

---

## Code Style Guidelines

### PHP Basics
- **Always use strict types**: Add `declare(strict_types=1);` at the top of new PHP files
- **Use short array syntax**: `$arr = []` instead of `array()`
- **Use modern PHP features**: null coalescing `??`, spaceship operator `<=>`, arrow functions where appropriate
- **Remove deprecated functions**: No `@` error suppression, no `ereg_*`, no `mysql_*`

### Naming Conventions
- **Classes**: `PascalCase` (e.g., `ClassApp`, `MongoCompat`)
- **Methods**: `camelCase` (e.g., `findOne()`, `toObjectId()`)
- **Properties**: `camelCase` (e.g., `$sitebaseApp`)
- **Constants**: `UPPER_SNAKE_CASE` (e.g., `MONGO_ENV`)
- **Files**: Match class name (e.g., `ClassApp.php`)
- **Tables/Fields**: Append table suffix (e.g., `nom` → `nomProduit`, `id` → `idproduit`)

### Formatting
- **Indentation**: 4 spaces (no tabs)
- **Line length**: Keep under 120 characters when reasonable
- **Braces**: Same-line opening braces for classes/functions, newline for control structures
- **Imports**: Use `use` statements at top of file, grouped: internal → external → local

### Types & Type Hints
- **Return types**: Always specify return types when possible
- **Parameter types**: Use type hints for all function parameters
- **Nullable**: Use `?Type` for nullable parameters/returns
- **Union types**: Use `Type1|Type2` syntax (PHP 8+)

### Error Handling
- **Never use `echo`/`print` for debugging**: Breaks AJAX responses
- **Use `error_log()`**: For all debug output to `/var/log/apache2/php-error.log`
- **Use try/catch**: Wrap MongoDB operations and external calls
- **Never suppress errors**: No `@` operator

---

## MongoDB Guidelines (CRITICAL)

### ALWAYS Use MongoCompat
All MongoDB operations must use `AppCommon\MongoCompat`:

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

### Driver Semantics
- Use `MongoDB\Client` (library) — NOT `MongoClient` (legacy extension)
- Use `MongoDB\BSON\ObjectId` — NOT `MongoId`
- Use `MongoDB\BSON\Regex` — NOT `MongoRegex`
- Use `MongoDB\Database` and `MongoDB\Collection` classes

---

## File Headers & Comments

### Preserve Original Headers
```php
/**
 * MongoCompat.php — MongoDB Driver Compatibility Helper
 * Date: 2026-02-02
 * Time: 14:30
 */
```

### Add Modification Headers
For major changes, add:
```php
 * Modified: 2026-03-04
```

### New Files
Use English only. Include:
- Brief description
- `@package` annotation
- `@date` in YYYY-MM-DD format

---

## Testing Guidelines

### Test Structure
- Location: `idae/web/tests/Unit/` and `idae/web/tests/Integration/`
- Use `Idae\Tests\TestCase` as base class
- Follow naming: `ClassNameTest.php`

### Test Configuration
- Bootstrap: `tests/bootstrap.php`
- Test database: Configured via `MONGO_TEST_DSN` environment variable
- Test suites: Unit, Integration (defined in `phpunit.xml`)

### Writing Tests
```php
declare(strict_types=1);

namespace Idae\Tests\Unit;

use Idae\Tests\TestCase;

class ClassAppTest extends TestCase
{
    public static function setUpBeforeClass(): void { ... }
    protected function setUp(): void { ... }
    
    public function testFindOneReturnsDocument(): void { ... }
}
```

---

## Common Pitfalls

1. **Field naming**: Always append table name (e.g., `nomProduit` not `nom`)
2. **Type safety**: Use `MongoCompat::toIntSafe()` for IDs; avoid type juggling
3. **Session/auth**: Ensure `MDB_USER`/`MDB_PASSWORD` are set for MongoDB access
4. **Legacy code**: Many helpers still use v1 driver semantics—see MIGRATION.md

---

## Key References

- [MIGRATION.md](../../MIGRATION.md): Migration status, blockers, checklist
- [MONGOCOMPAT.md](../../MONGOCOMPAT.md): MongoDB compatibility helpers
- [MIGRATION_PHASE_2.md](../../MIGRATION_PHASE_2.md): Phase 2 modernization plan
- [DEBUGGING.md](../../DEBUGGING.md): Safe debugging and troubleshooting
- [idae/web/README.md](../../idae/web/README.md): Full architecture and directory map

---

## Safety Rules

- **Credentials**: Do not modify production credentials or secrets
- **Stability**: Prioritize backward compatibility for the legacy GUI framework
- **Testing**: Run `php test_migration.php` and `test_integration.php` after stack boots
