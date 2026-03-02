# BMAD Dashboard — Idae Legacy Code Modernization

> Updated: 2026-03-02 | Team: 3 | Horizon: < 3 months

---

## Status

```
Phase 1 – Infrastructure & Bootstrap   ✅ DONE     (100%)
  Docker + PHP 8.2 + login functional

Phase 2 – ClassApp Core Migration      ⚠️  IN PROGRESS (25%)
  └─ __construct refactor              ⚠️  in progress
  └─ query / findOne                   ❌ pending
  └─ write methods (insert/update/…)   ❌ pending
  └─ aggregate / distinct              ❌ pending
  └─ FK methods                        ❌ pending
  └─ Unit tests                        ❌ pending

Phase 3 – Services & JSON API          ⏳ upcoming
Phase 4 – Modules & Templates          ⏳ upcoming
Phase 5 – Security & PHP Hardening     ⏳ upcoming
Phase 6 – QA & Regression             ⏳ upcoming
```

---

## Planning Artifacts

| Artifact | Status | Path |
|----------|--------|------|
| Product Brief | ✅ Done | `bmad/artifacts/product-brief.md` |
| PRD | ✅ Done | `bmad/artifacts/prd.md` |
| Architecture | ✅ Done | `bmad/artifacts/architecture.md` |
| Tech Spec | ❌ Missing | → run `/tech-spec` |
| Sprint 01 | ❌ Missing | → run `/sprint-planning` |

---

## Data Preservation Strategy (LOCKED)

**Approach**: MongoDB sidecar Docker (mongo:7, port 27018) for all tests.
**Backup**: daily cron `mongodump` → `./mongo_backup/YYYY-MM-DD/` (7-day retention).
**Guard**: `MONGO_ENV` env var — test code must never point to `host.docker.internal`.
**See**: PRD section "Data Preservation Strategy".

---

## Active Bugs

| ID | Severity | Title | Status |
|----|----------|-------|--------|
| BUG-01 | Medium | Node.js socket server refuses connections (port 3005) | Open |
| BUG-02 | Low | CleanStr signature mismatch (array_walk_recursive) | Patched / needs testing |

---

## Decisions (PRD closed)

| Question | Decision |
|----------|----------|
| LESS scope hors `appcss/` | Inconnu → **audit obligatoire en début de sprint SCSS** |
| `less.inc.php` compile quand ? | **Boot-time, actuellement cassé** → migration SCSS = fix + remplacement |
| PHPStan niveau | **Niveau 6** |
| Alerte backup | **Log fichier uniquement** (`mongo_backup/backup.log`) |

---

## Next Step

**Recommended**: `/sprint-planning` — découper le backlog en sprints sur la base du PRD + architecture.
