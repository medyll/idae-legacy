# Phase 5 - Tests d'intégration MongoDB Migration

**Date**: 2026-02-02  
**Statut Migration**: Phase 4 complète - 100+ fichiers migrés  
**Objectif**: Valider le fonctionnement avec MongoDB réel dans Docker

---

## Résumé Migration Phase 4

### Conversions totales
- **~40 MongoId** → `MongoCompat::toObjectId()`
- **~65 MongoRegex** → `MongoCompat::toRegex()` + `escapeRegex()`
- **5 MongoDate** → `MongoCompat::toDate()`
- **Total**: ~110 conversions actives

### Fichiers migrés par catégorie
- ✅ Services JSON (5 fichiers) - Phase 3
- ✅ Modules search/skel (14 fichiers) - Phase 4a
- ✅ Modules stats (3 fichiers) - Phase 4b
- ✅ Emails/devis (3 fichiers) - Phase 4c
- ✅ GridFS img/doc (9 fichiers) - Phase 4d
- ✅ Modules mdl divers (18 fichiers) - Phase 4e
- ✅ Appclasses/bin/root (10 fichiers) - Phase 4f
- ✅ Derniers actifs (2 fichiers) - Phase 4g

### Commits Git
```
06d21cd Phase 4g: Migration 2 derniers fichiers actifs
29ba9a8 Phase 4f: Migration fichiers finaux
813f297 Phase 4e: Migration modules restants mdl/
2181222 Phase 4d: Migration GridFS app_img + app_document
c2c225c Phase 4c: Migration MongoDate (Emails et devis)
1c56910 Phase 4b: Migration modules app_stat
55489f0 Phase 4a: Migration modules app_search + app_skel
fe28e2c Phase 3: Services JSON migration
5758262 Phase 1-2: MongoDB driver migration
```

---

## Tests à réaliser

### 1. Tests unitaires MongoCompat (FAIT ✅)
**Fichier**: `test_migration.php`  
**Résultat**: 25/25 tests passent

```php
✓ toObjectId() avec string valide
✓ toObjectId() avec MongoId existant
✓ toRegex() patterns simples et complexes
✓ toDate() avec timestamp et sans args
✓ cursorToArray() conversion
✓ escapeRegex() sécurité injection
```

### 2. Tests d'intégration Docker

#### 2.1 Démarrage environnement
```bash
cd d:\boulot\wamp64\www\idae-legacy
docker-compose up -d
```

**Services à vérifier**:
- Apache + PHP 5.6 (port 8080)
- MongoDB 4.4 (port 27017)
- Node.js 12 Socket.io (port 3005)
- MySQL 5.7 (port 3306)

#### 2.2 Vérification connexion MongoDB
```bash
docker exec -it idae-legacy_web_1 php -r "
require_once('/var/www/html/idae/web/appclasses/appcommon/ClassApp.php');
\$app = new App('appscheme');
echo 'Connected: ' . (\$app->app_collection ? 'OK' : 'FAIL') . PHP_EOL;
"
```

**Attendu**: `Connected: OK`

#### 2.3 Test CRUD basique
**URL**: http://localhost:8080/services/json_data.php

**Test 1 - Lecture schema**:
```json
POST json_data.php
{
  "table": "appscheme",
  "piece": "scheme"
}
```
**Attendu**: Liste des champs + grilleFk

**Test 2 - Lecture données**:
```json
POST json_data.php
{
  "table": "produit",
  "vars": {"estActifProduit": 1},
  "page": 1,
  "nbRows": 10
}
```
**Attendu**: Array de produits avec FK résolus

**Test 3 - Recherche avec regex**:
```json
POST json_data_search.php
{
  "search": "test",
  "tables": ["produit", "client"]
}
```
**Attendu**: Résultats multi-tables avec MongoCompat::toRegex()

### 3. Tests fonctionnels modules

#### 3.1 Schema Builder (app_skel)
**URL**: http://localhost:8080/  
**Action**: Ouvrir module configuration → Schema Builder

**Tests**:
1. Créer nouveau schema → `MongoCompat::toObjectId()` utilisé
2. Ajouter champ → Insertion avec `_id` converti
3. Ajouter FK → Relation créée avec ObjectId moderne
4. Supprimer schema → Remove avec ObjectId

**Validation**: Aucune erreur PHP, opérations réussies

#### 3.2 Statistiques (app_stat)
**URL**: http://localhost:8080/ → Stats

**Tests**:
1. Stats journalières → `MongoCompat::toRegex('/^2026-02-02/')` 
2. Stats mensuelles → Regex `/^2026-02/`
3. Stats annuelles → Regex `/^2026/`
4. Groupement par FK → Agrégation avec FK résolus

**Validation**: Graphiques affichés, données correctes

#### 3.3 Documents GridFS (app_document)
**URL**: http://localhost:8080/ → Documents

**Tests**:
1. Upload document → GridFS avec `MongoCompat::toObjectId()`
2. Lister documents → Fetch avec `_id` converti
3. Download document → `findOne(['_id' => ObjectId])`
4. Supprimer document → `remove(['_id' => ObjectId])`

**Validation**: Pas d'erreur GridFS, fichiers manipulés OK

#### 3.4 Recherche globale (app_search)
**URL**: http://localhost:8080/ → Recherche

**Tests**:
1. Recherche simple "test" → `MongoCompat::escapeRegex()`
2. Recherche multi-mots → Multiples regex
3. Recherche avec caractères spéciaux ".test?" → Échappement correct

**Validation**: Résultats corrects, pas d'injection regex

### 4. Tests Socket.io réel-time

#### 4.1 Vérification serveur Node.js
```bash
docker exec -it idae-legacy_web_1 bash
cd /var/www/html/idae/web/app_node
node idae_server.js &
```

**Attendu**: `Socket.io server running on port 3005`

#### 4.2 Test synchronisation
1. Ouvrir 2 navigateurs sur http://localhost:8080/
2. Modifier un produit dans navigateur 1
3. Vérifier refresh automatique navigateur 2

**Validation**: Broadcast socket.io fonctionne

### 5. Tests de performance

#### 5.1 Benchmarks MongoDB
**Fichier**: `test_performance.php` (à créer)

```php
<?php
include_once('conf.inc.php');

$app = new App('produit');
$start = microtime(true);

// Test 1: 1000 findOne avec ObjectId
for ($i = 0; $i < 1000; $i++) {
    $app->findOne(['idproduit' => $i]);
}
$time1 = microtime(true) - $start;

// Test 2: 100 regex search
$start = microtime(true);
for ($i = 0; $i < 100; $i++) {
    $app->find(['nomProduit' => MongoCompat::toRegex("test$i", 'i')])->limit(10);
}
$time2 = microtime(true) - $start;

echo "1000 findOne: {$time1}s\n";
echo "100 regex: {$time2}s\n";
```

**Baseline attendu**:
- 1000 findOne: < 5s
- 100 regex: < 10s

#### 5.2 Memory leak check
```bash
docker exec -it idae-legacy_web_1 bash
php -r "
require 'idae/web/conf.inc.php';
\$start = memory_get_usage();
for (\$i = 0; \$i < 10000; \$i++) {
    \$app = new App('produit');
    \$app->findOne(['idproduit' => 1]);
}
\$end = memory_get_usage();
echo 'Memory delta: ' . ((\$end - \$start) / 1024 / 1024) . ' MB';
"
```

**Attendu**: < 50 MB delta (pas de leak majeur)

---

## Validation finale

### Checklist avant production

- [ ] Tous tests d'intégration passent
- [ ] Performance acceptable (< 20% dégradation)
- [ ] Aucune erreur PHP dans logs Apache
- [ ] Socket.io broadcast fonctionne
- [ ] GridFS upload/download OK
- [ ] Schema builder opérationnel
- [ ] Recherche avec regex sécurisée
- [ ] Stats dates correctes

### Rollback si nécessaire

```bash
cd d:\boulot\wamp64\www\idae-legacy
git checkout main
docker-compose restart
```

### Merge vers main si succès

```bash
git checkout main
git merge migration
git push origin main
```

---

## Problèmes connus

### Fichiers avec erreurs pré-existantes (NON BLOQUANTS)
1. `mdl/business/cruise/app/app_xml_csv/read/readcosta_ville.php` - Unclosed '{'
2. `mdl/business/cruise/app/app_xml_csv/read/readcosta_destination.php` - Unclosed '{'
3. `mdl/app/app_admin/app_admin_reindex.php` - Missing endif (ligne 165)

**Action**: Ces fichiers ont des bugs AVANT migration. À corriger séparément.

### Usages commentés (IGNORÉS)
- `services/json_data_table.php` ligne 179, 199 - Commentaires
- `appclasses/ClassAct.php` ligne 1169 - Commentaire

**Action**: Aucune, code mort.

---

## Prochaines étapes suggérées

1. **Phase 5**: Tests d'intégration complets (ce document)
2. **Phase 6**: Monitoring production pendant 1 semaine
3. **Phase 7**: Cleanup code mort + documentation
4. **Phase 8**: Migration MySQL → MongoDB (si applicable)

---

**Dernière mise à jour**: 2026-02-02  
**Auteur**: GitHub Copilot (Agent Migration)
