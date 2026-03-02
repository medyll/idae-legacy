# Sprint 02 – SCSS Migration Complete + ClassApp Connection

**Duration:** 2026-03-16 → 2026-03-27
**Capacity:** 15 dev-days (3 devs)
**Planned:** 13 points

## Sprint Goal
All 29 LESS files are converted to SCSS and compile cleanly. The broken `less.inc.php` boot path is removed. `ClassApp.__construct` uses the modern MongoDB driver connection pattern.

---

## Stories

| ID | Epic | Title | Points | Priority |
|---|---|---|---|---|
| S2-01 | SCSS | Convert remaining 26 `.less` files to `.scss` (rename, fix variable syntax `@` → `$`) | 5 | Must |
| S2-02 | SCSS | Replace `@{location_fragment}` dynamic imports with explicit `@use`/`@forward` paths in `main.scss` and entrypoints | 2 | Must |
| S2-03 | SCSS | Convert all LESS mixins (`.boxshadow`, `.padding_more`, etc.) to SCSS `@mixin` / `@include` | 2 | Must |
| S2-04 | SCSS | Remove `less.inc.php` / `lessc.inc.php` from `conf.inc.php` boot path + visual regression check (compiled CSS diff) | 1 | Must |
| S2-05 | ClassApp | Refactor `ClassApp.__construct`: `MongoDB\Client` singleton, `typeMap` arrays, `MONGO_HOST` env var support | 3 | Must |

**Total:** 13 points — 2 points buffer.

---

## Dependencies
- S1-08 done (`build:css` pipeline operational)
- S1-09 done (core SCSS files already converted)
- LESS audit from S1-07 (import tree documented)

---

## Definition of Done
- [ ] `npm run build:css` compiles all entrypoints without errors
- [ ] Generated CSS in `appcss/generated/` is byte-for-byte identical (or diff reviewed and approved)
- [ ] `less.inc.php` is no longer `require`d in `conf.inc.php`
- [ ] `ClassApp.__construct` connects via `MongoDB\Client`, no `MongoClient` references
- [ ] No PHP errors on `docker-compose up` + login

## Risks
- LESS skin files (`skin_default.less`, `skin_seven.less`) may have deep mixin dependencies — allocate extra time if needed.
- `ClassApp.__construct` is the foundation for all subsequent story work; block S3 if it's not stable by end of sprint.
