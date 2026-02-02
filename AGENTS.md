# AGENTS

This repository uses automated agents for maintenance tasks.

## Guidelines
- Prioritize stability and backwards compatibility (legacy PHP 5.6 / Node.js 12).
- Avoid unnecessary refactors or modernization.
- Keep changes minimal, targeted, and well-scoped.
- Prefer existing project patterns and conventions.
- Ensure the project includes and follows MIGRATION.md and MONGOCOMPAT.md.
- Document any operational changes in the main README if needed.

## Safety
- Do not modify production credentials or secrets.
- Do not change environment detection or deployment defaults unless explicitly requested.

## Code History & Comments
- **Preserve authorship comments**: Keep existing code history markers in comments, especially **Date and Time** information:
  ```php
  /**
   * Created by PhpStorm.
   * User: Mydde
   * Date: 23/05/14      <-- PRESERVE THIS
   * Time: 20:26         <-- PRESERVE THIS
   */
  ```
- **English only**: All code comments must be written in English, including modification notes.
- **Rationale**: Date/Time stamps track code origin and evolution in a legacy codebase without Git history. Do not remove them during refactoring or editing.
- **Only add**: When making major modifications (e.g., migrations, major refactoring), add a new comment line:
  ```php
  * Modified: 2026-02-02 - Migrated to MongoDB v2.0 driver (Agent)
  ```
  Keep the original dates intact.

## Communication
- Keep responses concise and focused on the requested change.
