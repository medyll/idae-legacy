# AGENTS

**Context**: Migration from Legacy (PHP 5.6/Mongo v1) to Modern (PHP 8.2/Mongo v1.15+).

## Guidelines
- **Follow MIGRATION.md & MONGOCOMPAT.md**: These are the primary sources of truth.
- **Environment**: PHP 8.2, Node.js 18, MongoDB (Host via `host.docker.internal`), Docker.
- **Stability**: Prioritize backward compatibility for the legacy GUI framework ensuring stability over modernization.

## Safety & Style
- **Credentials**: Do not modify production credentials or secrets.
- **Comments**: **Preserve** original `Date:`/`Time:` headers in files. Add a `Modified: YYYY-MM-DD` line for major changes.
- **Language**: English only for new comments and documentation.

## Code Patterns
- **MongoCompat**: ALWAYS use `AppCommon\MongoCompat` for Mongo types (ObjectId, Regex, Date).
- **Driver**: Use `MongoDB\Client` (library) semantics, not `MongoClient` (extension).
