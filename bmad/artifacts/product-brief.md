# Product Brief — Idae Legacy Code Modernization

**Date**: 2026-03-02
**Status**: Active

---

## Situation actuelle

L'infrastructure est **opérationnelle** :
- PHP 8.2 tourne dans Docker (`docker-compose up --build`, port 8080)
- `mongodb/mongodb` v2+ est actif comme driver
- Le login fonctionne — la critical path PHP est validée
- Node.js 18 (Socket.IO) est refactorisé en ESM (`app_node/src/`)

## Ce qui reste à faire

Moderniser le **code PHP** pour remplacer tous les patterns du driver Mongo v1.x
(`MongoClient`, `MongoId`, `MongoRegex`, `MongoDate`, curseurs ADODB-style)
par les équivalents modernes, en passant par `AppCommon\MongoCompat`.

Fichiers cibles principaux :
- `appclasses/appcommon/ClassApp.php` (2072 lignes — en cours)
- `services/json_data*.php` (endpoints JSON consommés par le SPA)
- `mdl/*` (modules métier)

## Non-Goals

- Réécrire le frontend (PrototypeJS + bag.js restent intacts).
- Changer la structure des collections MongoDB.
- Refonte UX.

## Critères de succès

1. Tous les scripts de test passent (`test_migration.php`, `test_integration.php`).
2. Les endpoints `json_data.php` / `json_scheme.php` retournent le même JSON qu'avant.
3. Aucune erreur PHP en log lors des workflows utilisateur standard.
4. Socket.IO stable (BUG-01 résolu).

## Contraintes clés

- `error_log()` uniquement pour le debug — jamais d'echo vers le client.
- Préserver les headers `Date:`/`Time:` dans les fichiers legacy.
- Nouveau code et commentaires en anglais.
- `MongoCompat` est le seul point d'entrée pour les types MongoDB.
