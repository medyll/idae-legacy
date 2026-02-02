# MIGRATION.md — MongoDB Driver Upgrade PHP 5.6 → Modern

**Objectif**: Migrer de MongoDB driver v1.x (obsolète) vers driver MongoDB-PHP moderne, garantissant compatibilité complète avec architecture existante.

**Scope**: 50+ fichiers MongoId, 60+ fichiers MongoRegex, 16 services JSON, ClassApp.php 2072 lignes, 4 modules métier.

**Propriétaire**: IA Copilot (implémentation)

---

## État actuel (snapshot février 2026)

### Versions en production
- **PHP**: 5.6 (Dockerfile `FROM php:5.6-apache`)
- **MongoDB driver**: mongo PECL v1.x (obsolète, fin support 2015)
- **Node.js**: 12 (Dockerfile)
- **Socket.io Node**: 1.4.8 (package.json)
- **MongoDB client Node**: 2.2.10 (package.json)

### Statistiques codebase
| Catégorie | Count | Fichiers affectés |
|-----------|-------|------------------|
| MongoId usage | 50+ | appclasses/ClassApp.php, services/*, mdl/*, appfunc/* |
| MongoRegex usage | 60+ | services/json_data.php, json_search.php, mdl/*, appfunc/* |
| MongoDate usage | 40+ | business logic layers, stats modules |
| Services JSON | 16 | json_data.php, json_scheme.php, json_data_table.php, json_data_search.php, json_data_event.php, postAction.php, actions.php, +9 autres |
| ClassApp methods | 40+ | query(), findOne(), create_update(), remove(), update(), distinct(), aggregation, FK handling |
| Modules | 4 | app/, business/, customer/, dyn/ |

### Architecture existante (dépendances)
```
index.php (PHP bootstrap)
  → conf_init.php (scheme registration)
    → ClassApp.php (ORM core, héritage App)
      → MongoClient (driver v1)
        → MongoDB collections

json_*.php endpoints (PHP services)
  → ClassApp methods
    → MongoId/MongoRegex conversions inline
      → MongoDB queries

idae_server.js (Node.js socket)
  → mongodb@2.2.10 (client Node natif)
    → MongoDB direct
```

### Breaking changes identifiés
- **MongoId**: Constructor `new MongoId()` → `new ObjectId()`
- **MongoRegex**: `new MongoRegex()` → regex string + flags
- **MongoDate**: `new MongoDate()` → Date object
- **Cursors**: ADODB-style `$rs->fetchRow()` → MongoDB native `foreach($cursor as $doc)`
- **Insert/Update/Delete**: `insert()` → `insertOne()`, `update()` → `updateOne()`
- **Distinct**: `distinct()` → aggregation pipeline
- **Indexes**: `createIndex()` signature change

### Definition "DONE" par phase
Validation minimale pour avancer phase suivante:
- Tous fichiers phase refactorisés (grep confirms)
- Tests curl/JSON retournent même structure qu'avant (diff-safe)
- Erreurs PHP/MongoDB logs < 5 CRITICAL errors
- Socket.io events deliver correctement (si concerné)

---

## Phases d'exécution linéaires

### Phase 1: Setup dépendances & préparation

**Objectif**: Installer driver MongoDB moderne, structurer pour refactor progressif.

**Actions**:
- [ ] Déterminer version driver PHP cible (mongodb v1.15 compat layer recommandée pour max compatibility)
- [ ] Créer répertoire `appclasses/appcommon/` structure helpers partagés
- [ ] Installer via Composer: `composer require mongodb/mongodb:^1.15`
- [ ] Créer test harness `test_migration.php` pour validation inline
- [ ] Vérifier autoload Composer fonctionne (require `vendor/autoload.php`)
- [ ] Créer MongoDB client instance (new `MongoDB\Client()`)
- [ ] Tester connexion MongoDB (ping, list databases)

**Checkpoint**: Driver installé, MongoDB client peut ping base existante, autoload résolu.

**Bloquant?** Impossible avancer si driver non installé.

---

### Phase 2: ClassApp.php refactor — Core ORM (CRUD basique)

**Objectif**: Migrer ClassApp.php vers driver moderne, endpoints CRUD simples fonctionnent.

**Actions**:
- [ ] Refactor `__construct($table)` → init new `MongoDB\Client` + select database/collection
- [ ] Refactor `query($filter)` → MongoDB `find($filter)` retourne native cursor
- [ ] Refactor `findOne($filter)` → MongoDB `findOne($filter)` retourne array ou null
- [ ] Refactor `create_update($search, $values, $options)` → `updateOne($search, ['$set' => $values], $options)` + handle insert case
- [ ] Refactor `insert($data)` → `insertOne($data)` retourne insertedId
- [ ] Refactor `remove($filter)` → `deleteOne($filter)` ou `deleteMany($filter)`
- [ ] Refactor cursor iteration: `while ($rs->fetchRow())` → `foreach ($cursor as $doc)`
- [ ] Valider ClassApp::__construct() toujours accepte `$table` param, assign collection
- [ ] Créer MongoCompat helper (voir MONGOCOMPAT.md) avec méthodes statiques
- [ ] Import MongoCompat dans ClassApp pour conversions MongoId/Regex

**Checkpoint**: ClassApp queries CRUD simples fonctionnent, test.php retourne données identiques.

**Notes**: 
- MongoId conversions inline pour tests (Phase 5 utilise MongoCompat systématiquement)
- Héritage App reste unchanged (parent class toujours extensible)

---

### Phase 3: ClassApp.php refactor — Méthodes complexes (FK, aggregation)

**Objectif**: FK queries, aggregation pipeline, indexes fonctionnent.

**Actions**:
- [ ] Refactor `get_grille_fk($table)` → query FK metadata, handle new driver
- [ ] Refactor `get_reverse_grille_fk($table, $id)` → query reverse relationships
- [ ] Refactor `count($filter)` → `countDocuments($filter)` ou aggregation
- [ ] Refactor `stats()` / `groupBy()` → aggregation pipeline syntax
- [ ] Refactor `createIndex($fields)` → `createIndex($fields)` new signature
- [ ] Refactor field naming dynamic: `codeAppscheme_field + table` → MongoDB actual field names
- [ ] Handle MongoDate conversions (dates from PHP returned as DateTime objects)
- [ ] Validate FK relationships queries return same data structure

**Checkpoint**: FK queries return correct linked records, aggregation pipeline executes, indexes created.

**Notes**:
- Field naming critical: verify `nomProduit` = `nom` + `Produit` table concatenation
- MongoDate handling may require casting (new driver returns DateTime, v1 returned MongoDate)

---

### Phase 4: Services JSON layer — Data endpoints

**Objectif**: json_data.php, json_scheme.php et data endpoints retournent JSON identique à avant.

**Actions**:
- [ ] Refactor `json_data.php` (main data fetch): handle new ClassApp queries + cursor iteration
- [ ] Refactor `json_scheme.php` (schema retrieval): unchanged mostly, validate output JSON
- [ ] Refactor `json_data_table.php` (table detail): FK resolution, data formatting
- [ ] Refactor `json_data_search.php` (search): MongoRegex → regex string conversions
- [ ] Refactor `json_data_event.php` (real-time trigger): MongoDB query side effects
- [ ] Validate each endpoint returns identical JSON structure (diff against production logs if available)
- [ ] Handle pagination (`page`, `nbRows`) on new cursors
- [ ] Handle sorting and filtering parameters

**Checkpoint**: POST /services/json_data.php returns identical JSON to v1 driver version.

**Notes**:
- `json_search.php` heavy MongoRegex user (60+ patterns) → prioritize MongoCompat here
- Pagination: ensure skip/limit work identically

---

### Phase 5: Services JSON layer — Métier endpoints

**Objectif**: Business logic endpoints (CRM, invoicing, etc) work with new driver.

**Actions**:
- [ ] Refactor `postAction.php` (action handlers): data mutation via new ClassApp
- [ ] Refactor `actions.php` (specific actions): validate business logic flows
- [ ] Refactor export/import endpoints: handle bulk insert/update on new driver
- [ ] Refactor IMAP integration (if applicable): email service endpoints
- [ ] Refactor scheduled task handlers: ensure MongoDB queries execute correctly
- [ ] Validate data mutation workflows (create→update→delete sequences)

**Checkpoint**: CRM workflows (create client→add contact→generate invoice) work end-to-end.

**Notes**:
- Bulk operations may need `insertMany()` / `updateMany()` instead of loops
- Transactions (if used) may need MongoDB 4.0+ driver changes

---

### Phase 6: Modules métier — Batch conversion

**Objectif**: app/, business/, customer/, dyn/ modules refactored and load without MongoDB errors.

**Actions**:
- [ ] Inventory module types: identify which use ClassApp directly vs via json_data.php
- [ ] Refactor `mdl/app/` modules (core UI): database-facing code
- [ ] Refactor `mdl/business/` modules (métier): CRM, invoicing logic
- [ ] Refactor `mdl/customer/` modules (CRM specific): customer data handling
- [ ] Refactor `mdl/dyn/` modules (dynamic): schema-driven UI generation
- [ ] Validate each module loads without MongoDB errors (socket.io console.log)
- [ ] Verify data returned to client identical structure

**Checkpoint**: All 4 module types load, socket.io broadcasts data correctly.

**Notes**:
- Modules may use inline MongoId queries (check for `new MongoId()` in mdl files)
- MongoCompat usage systematized here (Phase 4+ should use it)

---

### Phase 7: Node.js modernisation — Socket server

**Objectif**: idae_server.js uses modern MongoDB driver, socket events work.

**Actions**:
- [ ] Refactor MongoDB client in idae_server.js: `new MongoDB\Client()` instead of v1
- [ ] Refactor session management MongoDB queries
- [ ] Refactor socket event handlers (subscribe, unsubscribe, broadcast)
- [ ] Refactor HTTP endpoint `/postScope` (scope broadcast)
- [ ] Refactor HTTP endpoint `/run` (PHP module execution)
- [ ] Refactor HTTP endpoint `/runModule` (PHP module execution + broadcast)
- [ ] Validate MongoId conversions Node.js side (ObjectId string ↔ binary handling)
- [ ] Test socket.io connection + authentication flow

**Checkpoint**: socket.io connects, subscribe works, broadcasts deliver to clients.

**Notes**:
- Node.js mongodb@2.2.10 may need upgrade to 4.x+ for modern features (check idae_server.js)
- Session state may be stored in MongoDB → validate format changes

---

### Phase 8: Intégration complète — Client-Server sync

**Objectif**: Full stack working end-to-end: PHP clients, Node.js socket, real-time synchronization.

**Actions**:
- [ ] Validate PHP session + socket.io session linking (PHPSESSID ↔ socket auth)
- [ ] Validate real-time broadcasts: PHP updates → socket.io → client refresh
- [ ] Test concurrent updates (two clients modify same record simultaneously)
- [ ] Validate race conditions (last-write-wins or conflict handling)
- [ ] Validate data consistency MongoDB ↔ client caches
- [ ] Load test: 10+ concurrent socket connections, observe lock/deadlock behavior
- [ ] Verify error propagation (MongoDB error → JSON error → client alert)

**Checkpoint**: End-to-end real-time sync stable, no data corruption in concurrent scenarios.

**Notes**:
- Session timeout handling critical (invalidate socket if PHP session expires)
- Socket.io room filtering must respect agent permissions (still functioning?)

---

### Phase 9 (optionnel): Optimisations & nettoyage

**Objectif**: Performance tuning, legacy code cleanup.

**Actions**:
- [ ] Profile slow queries (enable MongoDB profiling)
- [ ] Optimize index usage (explain() on heavy queries)
- [ ] Add Redis caching if applicable (schema cache, query cache)
- [ ] Remove dead ADODB code paths (if unused after migration)
- [ ] Verify memory usage (new driver may have different memory footprint)
- [ ] Document performance baselines (response time P50/P95, throughput)

**Checkpoint**: Performance within acceptable thresholds (no regression > 10%).

---

## Décisions architecturales majeures

### Décision 1: Helper MongoCompat vs inline conversions
| Critère | Inline | MongoCompat wrapper |
|---------|--------|-------------------|
| **Maintenabilité** | Difficile (60+ répétitions) | Facile (une source) |
| **Testabilité** | Fragment (tester chaque site) | Global (tester helper seul) |
| **Performance** | Minimal overhead | +1-2% fonction call overhead |
| **Rollout** | Immédiat partout | Graduel (utiliser phase par phase) |
| **Recommandation** | ❌ Non | ✅ **OUI** (voir MONGOCOMPAT.md) |

### Décision 2: Refactor ClassApp big-bang vs incremental
| Critère | Big-bang | Incremental |
|---------|----------|------------|
| **Risk** | Élevé (tous endpoints cassés) | Faible (test chaque étape) |
| **Duration** | 2-3 jours concentration | 3-4 jours (avec validations) |
| **Testing** | Gruesome debugging | Incrémental (test au fur à mesure) |
| **Recommandation** | ❌ Non | ✅ **OUI** (phases 2-3) |

### Décision 3: Services isolation vs shared ClassApp
| Critère | Isolated Class per service | Shared ClassApp parent |
|---------|--------------------------|----------------------|
| **Code duplication** | Élevé (refactor x16 endpoints) | Minimal (1 source) |
| **Coupling** | Loose | Tight (architecture existante) |
| **Testability** | Indépendant | Interdépendant |
| **Migration cost** | Énorme | Minimal (1 class refactor) |
| **Recommandation** | ❌ Non | ✅ **OUI** (garder héritage) |

### Décision 4: MongoDB downtime migration données
| Critère | Batch script | Live conversion hook |
|---------|-------------|-------------------|
| **Downtime** | 1-2h (full DB migration) | 0h (lazy convert on read) |
| **Data consistency** | Garanti post-migration | Eventually consistent |
| **Rollback** | Restore snapshot | Revert code only |
| **Recommandation** | ✅ **OUI** (maintenance window) | ❌ Risqué |

---

## Next Steps

1. **Valider Phase 1** (driver installation) avant Phase 2
2. **Créer MONGOCOMPAT.md** (voir document séparé) — interface helper classe
3. **Exécuter Phases 2-3** en concentration ClassApp seulement
4. **Blocker Phase 4** jusqu'à Phases 2-3 100% validées
5. **Paralléliser optionnellement**: Phase 7 (Node.js) peut commencer en concurrence Phases 4-5 (indépendant)

---

**Document living**: Mise à jour bihebdomadaire après validation phase (chaque lundi soir post-test).

**Escalade bloquée**: Si une phase > 2 jours, escalade immédiate pour redirection.

