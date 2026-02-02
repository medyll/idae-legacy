# Migration Status - Phase 2

**Date**: 2026-02-02  
**Fichier**: ClassApp.php  
**Lignes**: 2135 (original: 2072)

## ✅ COMPLÉTÉ (Phase 1 + Début Phase 2)

### Phase 1: Setup
- ✅ MongoDB driver moderne installé (mongodb/mongodb v1.8.0)
- ✅ MongoCompat helper créé et testé (25 tests passés)
- ✅ Test harness migration créé (test_migration.php)
- ✅ Composer autoload configuré

### Phase 2: ClassApp.php - Connection Layer
- ✅ Backup créé (ClassApp.php.backup)
- ✅ Imports ajoutés (MongoDB\Client, MongoCompat)
- ✅ Classe ne extend plus \MongoClient (standalone class maintenant)
- ✅ Propriétés ajoutées ($mongoClient, $database, $collection)
- ✅ Constructeur __construct() migré vers driver moderne
- ✅ getMongoClient() créé (singleton pattern avec typeMap)
- ✅ Collections assignées avec selectCollection()

## ⏳ EN COURS (Suite Phase 2)

### Méthodes critiques à migrer

ClassApp.php a une **architecture complexe** avec méthodes interdépendantes :

#### Architecture découverte
```
App::__construct($table)
  → getMongoClient() [✅ MIGRÉ]
  → selectDatabase() [✅ MIGRÉ]
  → Collection assignments [✅ MIGRÉ]
  
App::findOne($vars, $out)
  → plug($base, $table) [❌ À ANALYSER]
    → Collection findOne()
    
App::query($vars, $page, $rppage, $fields)
  → plug($base, $table) [❌ À ANALYSER]
    → Collection find()
    
App::create_update($vars, $fields)
  → ... [❌ NON MIGRÉ]
  
App::insert($vars)
  → ... [❌ NON MIGRÉ]
  
App::update($vars, $fields, $upsert)
  → ... [❌ NON MIGRÉ]
  
App::remove($vars)
  → ... [❌ NON MIGRÉ]
```

#### Méthode `plug()` - CRITIQUE ⚠️
La méthode `plug($base, $table)` est utilisée PARTOUT et retourne une instance de collection dans un namespace spécifique. **Cette méthode est LA clé de l'architecture**.

**Stratégie**: Trouver et migrer `plug()` AVANT les autres méthodes.

## 📋 PROCHAINES ÉTAPES

### Étape immédiate: Localiser et migrer plug()

1. **Chercher plug() dans ClassApp.php**
   ```bash
   grep -n "function plug" ClassApp.php
   ```

2. **Analyser son fonctionnement**
   - Prend ($base, $table) en paramètres
   - Retourne instance de collection MongoDB
   - Utilisé par findOne, query, insert, update, remove

3. **Migrer plug() vers driver moderne**
   ```php
   function plug($base, $table) {
       $dbname = MDB_PREFIX . $base;
       $database = $this->mongoClient->selectDatabase($dbname);
       return $database->selectCollection($table);
   }
   ```

### Étapes suivantes (séquence logique)

1. ✅ plug() migré → **BLOQUANT pour tout le reste**
2. ⏳ findOne() migré (utilise plug())
3. ⏳ query() migré (utilise plug())
4. ⏳ insert() migré
5. ⏳ update() / create_update() migrés
6. ⏳ remove() migré
7. ⏳ distinct() / count() migrés (aggregation)
8. ⏳ get_grille_fk() / get_reverse_grille_fk() (FK relationships)

## 🚨 RISQUES IDENTIFIÉS

1. **plug() non trouvable**: Si plug() n'existe pas ou est hérité, architecture différente de prévue
2. **Méthodes ADODB**: Utilisation de `getNext()`, `fetchRow()` nécessite wrapper
3. **MongoId inline**: 50+ usages à convertir avec MongoCompat
4. **MongoRegex inline**: 60+ usages à convertir
5. **Performance**: Queries complexes peuvent ralentir avec nouveau driver

## 🔧 ACTIONS REQUISES

### Action 1: Trouver plug()
```bash
grep -A 10 "function plug" d:\boulot\wamp64\www\idae-legacy\idae\web\appclasses\appcommon\ClassApp.php
```

### Action 2: Vérifier usages de plug()
```bash
grep -n "->plug(" d:\boulot\wamp64\www\idae-legacy\idae\web\appclasses\appcommon\ClassApp.php | wc -l
```

### Action 3: Si plug() introuvable, chercher pattern collection access
```bash
grep -n "\$this->conn->" d:\boulot\wamp64\www\idae-legacy\idae\web\appclasses\appcommon\ClassApp.php
```

## 📊 MÉTRIQUES

| Catégorie | Total | Migré | Restant |
|-----------|-------|-------|---------|
| Connection setup | 1 | 1 | 0 |
| Query methods | 7 | 0 | 7 |
| Aggregation methods | 3 | 0 | 3 |
| FK methods | 10+ | 0 | 10+ |
| Utility methods | 30+ | 0 | 30+ |
| **TOTAL ESTIMATION** | **50+** | **1** | **49+** |

## 🎯 OBJECTIF PHASE 2

**Definition of Done**:
- [ ] plug() identifié et migré
- [ ] findOne() migré et testé
- [ ] query() migré et testé
- [ ] insert(), update(), remove() migrés
- [ ] Test harness validé sur vraie DB
- [ ] json_data.php retourne données identiques

**Blocage actuel**: Besoin de trouver plug() pour continuer.

## 💡 RECOMMANDATION

**NE PAS** continuer migration aveugle sans comprendre plug(). Risque de casser toute l'architecture.

**FAIRE**:
1. Grep pour trouver plug()
2. Analyser son rôle exact
3. Migrer plug() en priorité
4. Tester plug() isolément
5. PUIS continuer avec findOne/query/etc.

---

**Status**: ⏸️ PAUSED - En attente de localisation plug() avant de continuer.
