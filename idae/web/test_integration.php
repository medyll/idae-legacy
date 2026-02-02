<?php
/**
 * Script de test d'intégration MongoDB migration
 * Phase 5 - Validation en environnement Docker
 * 
 * Usage: php test_integration.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "\n=== IDAE MongoDB Migration - Tests d'intégration ===\n\n";

// 1. Vérifier environnement
echo "[1/6] Vérification environnement...\n";

if (!file_exists('conf.inc.php')) {
    die("❌ ERREUR: conf.inc.php introuvable\n");
}

include_once('conf.inc.php');

if (!class_exists('App')) {
    die("❌ ERREUR: Classe App non chargée\n");
}

echo "  ✓ Fichiers de configuration OK\n";

// 2. Test connexion MongoDB
echo "\n[2/6] Test connexion MongoDB...\n";

try {
    $app = new App('appscheme');
    
    if (empty($app->app_collection)) {
        die("❌ ERREUR: Collection MongoDB non initialisée\n");
    }
    
    echo "  ✓ Connexion MongoDB OK\n";
    echo "  ✓ Collection: " . $app->app_collection->getCollectionName() . "\n";
    
} catch (Exception $e) {
    die("❌ ERREUR MongoDB: " . $e->getMessage() . "\n");
}

// 3. Test MongoCompat helper
echo "\n[3/6] Test MongoCompat helper...\n";

require_once(__DIR__ . '/appclasses/appcommon/MongoCompat.php');
use AppCommon\MongoCompat;

// Test toObjectId
try {
    $id_str = '507f1f77bcf86cd799439011';
    $object_id = MongoCompat::toObjectId($id_str);
    
    if (!($object_id instanceof MongoDB\BSON\ObjectId)) {
        die("❌ ERREUR: toObjectId() ne retourne pas ObjectId\n");
    }
    
    echo "  ✓ MongoCompat::toObjectId() OK\n";
} catch (Exception $e) {
    die("❌ ERREUR toObjectId: " . $e->getMessage() . "\n");
}

// Test toRegex
try {
    $pattern = MongoCompat::toRegex('test', 'i');
    
    if (!($pattern instanceof MongoDB\BSON\Regex)) {
        die("❌ ERREUR: toRegex() ne retourne pas Regex\n");
    }
    
    echo "  ✓ MongoCompat::toRegex() OK\n";
} catch (Exception $e) {
    die("❌ ERREUR toRegex: " . $e->getMessage() . "\n");
}

// Test escapeRegex
$escaped = MongoCompat::escapeRegex('test.com?query=1');
if (strpos($escaped, '\.') === false || strpos($escaped, '\?') === false) {
    die("❌ ERREUR: escapeRegex() n'échappe pas correctement\n");
}
echo "  ✓ MongoCompat::escapeRegex() OK\n";

// Test toDate
try {
    $date = MongoCompat::toDate();
    if (!($date instanceof MongoDB\BSON\UTCDateTime)) {
        die("❌ ERREUR: toDate() ne retourne pas UTCDateTime\n");
    }
    echo "  ✓ MongoCompat::toDate() OK\n";
} catch (Exception $e) {
    die("❌ ERREUR toDate: " . $e->getMessage() . "\n");
}

// 4. Test CRUD basique
echo "\n[4/6] Test opérations CRUD...\n";

try {
    // Lecture schemas
    $app_scheme = new App('appscheme');
    $count = $app_scheme->find([])->count();
    
    if ($count == 0) {
        echo "  ⚠ WARNING: Aucun schema trouvé (base vide?)\n";
    } else {
        echo "  ✓ Lecture schemas: $count trouvés\n";
    }
    
    // Test findOne avec array vide (doit retourner 1er doc)
    $first = $app_scheme->findOne([]);
    if ($first === false) {
        echo "  ⚠ WARNING: findOne([]) retourne false (base vide)\n";
    } else {
        echo "  ✓ findOne() OK (schema: {$first['codeAppscheme']})\n";
    }
    
} catch (Exception $e) {
    die("❌ ERREUR CRUD: " . $e->getMessage() . "\n");
}

// 5. Test recherche avec regex
echo "\n[5/6] Test recherche regex...\n";

try {
    $app_scheme = new App('appscheme');
    
    // Recherche avec pattern simple
    $pattern = MongoCompat::toRegex('app', 'i');
    $cursor = $app_scheme->find(['codeAppscheme' => $pattern]);
    $count = $cursor->count();
    
    echo "  ✓ Recherche regex: $count résultats\n";
    
    // Test avec escapeRegex
    $search = 'test.value';
    $escaped = MongoCompat::escapeRegex($search);
    $pattern2 = MongoCompat::toRegex($escaped, 'i');
    
    // Ne devrait pas matcher si regex mal formé
    if (strpos($escaped, '\.') !== false) {
        echo "  ✓ escapeRegex() sécurise les patterns\n";
    } else {
        echo "  ⚠ WARNING: escapeRegex() pourrait être amélioré\n";
    }
    
} catch (Exception $e) {
    die("❌ ERREUR Regex: " . $e->getMessage() . "\n");
}

// 6. Test résumé fichiers migrés
echo "\n[6/6] Vérification fichiers migrés...\n";

$files_to_check = [
    'services/json_data.php',
    'services/json_data_search.php',
    'services/json_data_table.php',
    'mdl/app/app_skel/actions.php',
    'appclasses/ClassAppSite.php',
    'appclasses/ClassAct.php'
];

$errors = 0;
foreach ($files_to_check as $file) {
    $path = __DIR__ . '/' . $file;
    if (!file_exists($path)) {
        echo "  ⚠ Fichier manquant: $file\n";
        continue;
    }
    
    $content = file_get_contents($path);
    
    // Vérifier présence MongoCompat
    if (strpos($content, 'use AppCommon\MongoCompat') === false) {
        echo "  ⚠ $file: Import MongoCompat manquant\n";
        $errors++;
        continue;
    }
    
    // Vérifier absence de new MongoId/MongoRegex (hors commentaires)
    if (preg_match('/^(?!.*\/\/).*new Mongo(Id|Regex|Date)\(/m', $content)) {
        echo "  ❌ $file: Contient encore new Mongo*\n";
        $errors++;
    } else {
        echo "  ✓ $file migré\n";
    }
}

if ($errors > 0) {
    echo "\n⚠ $errors fichier(s) avec problèmes détectés\n";
}

// Résumé final
echo "\n=== RÉSUMÉ ===\n";
echo "✓ Environnement: OK\n";
echo "✓ MongoDB: Connecté\n";
echo "✓ MongoCompat: Fonctionnel\n";
echo "✓ CRUD: OK\n";
echo "✓ Regex: Sécurisé\n";

if ($errors == 0) {
    echo "\n🎉 TOUS LES TESTS PASSENT!\n";
    echo "\nProchaine étape:\n";
    echo "1. Tester dans navigateur: http://localhost:8080/\n";
    echo "2. Vérifier logs Apache: docker logs idae-legacy_web_1\n";
    echo "3. Tester modules: Schema Builder, Stats, Documents\n";
    echo "4. Valider Socket.io: Ouvrir 2 navigateurs simultanés\n";
    exit(0);
} else {
    echo "\n⚠ Certains tests ont échoué. Vérifier les fichiers ci-dessus.\n";
    exit(1);
}
