# Sprint 04 – ClassAppAgg + Integration Tests + JSON Services

**Duration:** 2026-04-13 → 2026-04-24
**Capacity:** 15 dev-days (3 devs)
**Planned:** 14 points

## Sprint Goal
Integration tests validate that all JSON API endpoints return structurally identical responses. `json_data.php`, `json_scheme.php`, and `json_data_search.php` are fully modernized. BUG-01 (Socket.IO) investigated and resolved or formally triaged.

---

## Stories

| ID | Epic | Title | Points | Priority |
|---|---|---|---|---|
| S4-01 | ClassApp | Extract `ClassAppAgg.php` (extends `ClassApp`): aggregation helpers — strict types, unit tests | 3 | Must |
| S4-02 | Testing | Write `Integration/JsonDataTest.php`: assert `json_data.php` response shape equals legacy snapshot for 3+ tables | 3 | Must |
| S4-03 | Services | Modernize `json_scheme.php` + `json_data_search.php` (MongoCompat::toRegex, strict types, English comments) | 3 | Must |
| S4-04 | Services | Modernize `json_data_table.php` + `json_data_table_row.php` (strict types, MongoCompat, English comments) | 3 | Should |
| S4-05 | BugFix | Investigate + fix BUG-01: Node.js socket server refuses connections on port 3005 | 2 | Should |

**Total:** 14 points — 1 point buffer.

---

## Dependencies
- S3-01 → S3-04 done (ClassApp methods stable)
- S1-03 done (integration TestCase + sidecar seeded with realistic data for JSON shape assertions)

---

## Definition of Done
- [ ] `Integration/JsonDataTest.php` green — JSON key structure matches legacy snapshot
- [ ] `json_scheme.php` returns identical `columnModel`/`fieldModel`/`miniModel` shape
- [ ] `json_data_search.php` uses `MongoCompat::toRegex(preg_quote($input))` — no raw regex
- [ ] PHPStan level 6 — 0 violations on all modified service files
- [ ] BUG-01: either fixed (socket reconnects) or documented as won't-fix with clear rationale

## Risks
- JSON shape regression is the highest-risk item — the SPA breaks silently if a key is missing. Use strict `assertArrayHasKey` assertions on all top-level keys.
- `json_data.php` may depend on undiscovered methods of `ClassApp` not yet migrated — unblock immediately if found.
