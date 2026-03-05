# MIGRATION_PHASE_2.md

**Date:** 2026-02-07

## Phase 2 Modernization Inventory

### Context
Phase 1 of the migration is complete: the application boots and no major bugs are present. This document inventories all modernization opportunities for phase 2, with detailed actions, rationale, and references. The goal is to improve maintainability, security, performance, and developer experience.

---

## 1. PHP Modernization

### Quick Wins
- **Strict Types & Type Declarations**
  - Add `declare(strict_types=1);` and scalar/return type hints to all PHP files.
  - Rationale: Improves type safety and reduces runtime errors.
  - Files: All PHP source files, especially in `idae/web/`, `idae/web/services/`, `idae/web/actions.php`.
- **Short Array Syntax**
  - Replace legacy `array()` with `[]` throughout the codebase.
  - Rationale: Modern syntax, easier to read and maintain.
- **Error Handling**
  - Remove `@` error suppression and legacy error handling; use exceptions and try/catch blocks.
  - Rationale: Makes error handling explicit and debuggable.
- **Remove Deprecated Functions**
  - Replace any use of `mysql_*`, `ereg_*`, `split`, `each`, `create_function` with modern alternatives.
  - Rationale: Ensures compatibility with PHP 8.2 and future versions.

### Deeper Refactors
- **Dependency Injection & Autoloading**
  - Use Composer autoloading and dependency injection for better modularity and testability.
  - Files: `composer.json`, all class files in `idae/web/appclasses/`, `idae/web/classes/`.
- **Class-based Controllers/Services**
  - Refactor procedural scripts (e.g., `actions.php`, `postAction.php`) into class-based controllers.
  - Rationale: Improves code organization and testability.
- **Modern ORM/ODM**
  - Evaluate replacing custom ORM in `App`/`AppSite` with a modern ODM (e.g., Doctrine MongoDB) if feasible.
  - Blocker: Must maintain compatibility with dynamic schema and `MongoCompat` wrappers.

### Blockers/Dependencies
- All MongoDB types must use `MongoCompat` (see `MONGOCOMPAT.md`).
- File headers must be preserved (see `AGENTS.md`).

---

## 2. HTML & CSS Modernization

### Quick Wins
- **HTML5 Doctype & Semantic Elements**
  - Ensure all HTML uses `<!DOCTYPE html>` and semantic tags (`<main>`, `<nav>`, etc.).
  - Files: `idae/web/index.html`, `idae/web/test_notification.html`, all HTML templates.
- **Remove Inline Styles & Deprecated Tags**
  - Move inline styles to CSS, remove `<font>`, `<center>`, and similar deprecated tags.

### Deeper Refactors
- **CSS Modularization**
  - Refactor monolithic or inline CSS into modular, maintainable files (consider SASS/LESS if not already used).
  - Files: `idae/web/appcss/`.
- **Responsive Design**
  - Add media queries and mobile-first design patterns for all layouts.
- **Accessibility Improvements**
  - Add ARIA roles, labels, and improve keyboard navigation for all interactive elements.

### Blockers/Dependencies
- Some layouts may depend on table-based structures; refactoring may require UI redesign.

---

## 3. Application Logic & Architecture

### Quick Wins
- **Centralized Configuration**
  - Move hardcoded values to config files (`idae/web/conf.inc.php`, `idae/web/config/`).
- **Consistent Logging**
  - Standardize on `error_log()` for server-side logging (never use `echo`/`print`).

### Deeper Refactors
- **MVC Enforcement**
  - Move business logic out of views and into controllers/models.
- **API Layer Modernization**
  - Refactor `services/json_data.php` and related endpoints to use modern REST conventions and validation.

### Blockers/Dependencies
- The system uses a dynamic schema (`appscheme` collection), complicating static analysis and refactoring.

---

## 4. Security

### Quick Wins
- **Input Validation & Sanitization**
  - Ensure all user input is validated and sanitized, especially in endpoints like `actions.php`, `postAction.php`.
- **CSRF Protection**
  - Add CSRF tokens to all forms and AJAX endpoints.
- **Session Management**
  - Use secure session cookies and regenerate session IDs on login.

### Deeper Refactors
- **Authentication & Authorization**
  - Refactor to use modern authentication libraries and enforce permission checks (see `droit_table()` usage).
- **Dependency Updates**
  - Update all third-party libraries (see `composer.json`) to latest secure versions.

### Blockers/Dependencies
- Some authentication logic may be tightly coupled to legacy code and require careful migration.

---

## 5. Developer Experience

### Quick Wins
- **Composer Scripts & Autoloading**
  - Use Composer for dependency management and autoloading.
- **Docker Improvements**
  - Streamline Docker setup (`docker-compose.yml`, `Dockerfile`), add health checks, and document workflows (`DOCKER_SCRIPTS.md`).

### Deeper Refactors
- **Testing**
  - Introduce PHPUnit for backend and Jest for frontend tests.
- **CI/CD Integration**
  - Add GitHub Actions or similar for automated testing and deployment.

### Blockers/Dependencies
- Limited or missing tests for legacy code may slow modernization.

---

## 6. Anti-Patterns & Legacy Patterns

- **Direct Output for Debugging**
  - Remove all use of `echo`/`print` for debugging; use `error_log()` only.
- **Global State & Superglobals**
  - Reduce reliance on `$_GET`, `$_POST`, `$_SESSION` without abstraction.
- **Procedural Scripts**
  - Migrate procedural entry points to class-based structure.

---

## Summary Table

| Area                | Quick Wins                                   | Deeper Refactors                        | Blockers/Dependencies                |
|---------------------|----------------------------------------------|-----------------------------------------|--------------------------------------|
| PHP                 | Strict types, array syntax, error handling   | DI, autoloading, class controllers      | MongoCompat, header rules            |
| HTML/CSS            | HTML5, remove inline styles                  | Modular CSS, responsive, accessibility  | Table layouts                        |
| App Logic           | Config centralization, logging               | MVC, RESTful API                        | Dynamic schema                       |
| Security            | Input validation, CSRF, session management   | Auth refactor, dep updates              | Legacy auth flows                    |
| Dev Experience      | Composer, Docker scripts                     | Testing, CI/CD                          | Test coverage                        |

---

## Key Files & References

- `AGENTS.md`: Coding standards, header rules
- `MIGRATION.md`, `MONGOCOMPAT.md`: Migration status, MongoCompat usage
- `idae/web/actions.php`, `idae/web/services/json_data.php`: Core endpoints
- `idae/web/appcss/`: CSS
- `docker-compose.yml`, `Dockerfile`: Environment

---

**This document is intended as a detailed inventory and action plan for phase 2 modernization. All changes must preserve legacy compatibility and follow the standards in `AGENTS.md`.**

**Important:** All historical comments in source files (such as author names, creation dates, and original file headers) must be preserved during modernization, as required by project standards. All new comments and documentation must be written in English.
