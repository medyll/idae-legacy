# Audit de modernisation IDAE Legacy

Date : 2026-11-08. Source : dépôt local et conteneur `idae-legacy`.
BMAD et ses déclarations de progression sont exclus des preuves et restent inchangés.

## État constaté

Le projet exécute PHP 8.2.33 et ext-mongodb 2.5.2. Le verrou Composer contient
mongodb/mongodb 2.1.2, PHPUnit 11.5.55 et PHPStan 1.12.33. La migration du runtime
ne signifie pas que tous les chemins applicatifs sont compatibles.

Inventaire : 978 fichiers PHP, hors répertoires vendor, node_modules, dom2pdf,
phpthumb et adodb. Ce périmètre conserve des bibliothèques embarquées dans les
répertoires applicatifs. 24 fichiers déclarent strict_types=1, soit environ 2,5 %.
Ce comptage textuel mesure une adoption, pas la qualité du typage.

Le lint complet réussit sur 977 fichiers et échoue sur
`idae/web/mdl/dyn/dyn_devis_html.php:132` : accès à un offset avec accolades,
syntaxe supprimée de PHP. Il émet 430 messages de dépréciation ; ce nombre ne
représente pas 430 défauts uniques. L'accessibilité HTTP de ce module reste à vérifier.

## Architecture et DDD

```text
Navigateur / interface historique
             |
actions.php / services/*.php / modules PHP
             |
App : métadonnées + accès aux données + orchestration
     |                     |
ClassAppAgg / ClassAppFk    MongoCompat et adaptateurs
                           |
                         MongoDB

Notifications : PHP <-> passerelle Node / Socket.IO <-> navigateur
```

Le moteur schema-driven constitue une base à préserver : appscheme décrit les
tables, champs et relations. Les modules clients portent des particularités métier.
Ce découpage n'établit pas, à lui seul, des bounded contexts DDD.

`appclasses/appcommon/ClassApp.php` définit la classe globale `App` : 2 339 lignes,
78 déclarations de fonctions, connexion globale PERSIST_CON, propriétés dynamiques,
chargement des métadonnées et opérations CRUD. Les extractions ClassAppAgg et
ClassAppFk réduisent une partie du volume, mais dépendent toujours de cette façade.
« Agg » désigne ici des opérations de regroupement, pas des agrégats métier DDD.

Constats SOLID : responsabilités nombreuses et dépendance directe à MongoDB
dans App ; injection et tests isolés difficiles. LSP/OCP ne peuvent pas recevoir
un verdict global sans examiner les contrats et leurs appelants. Ne pas créer
une interface par classe pour produire artificiellement un score SOLID.

La cible proposée conserve App comme façade compatible et extrait, dans les
emplacements existants, les responsabilités prouvées par les tests. Les règles
métier identifiées justifieront éventuellement des objets valeur ou services de
domaine. Aucun nouveau découpage transversal n'est arrêté par cet audit.

## Risques prioritaires et preuves

| Priorité | Constat | Preuve / limite |
|---|---|---|
| P0 | Compatibilité plateforme non satisfaite | check-platform-reqs sort avec 2 : ext-imap absente et Smarty 3.1.48 déclare PHP ^5.2 ou ^7.0. Cela ne prouve pas que toutes ses fonctions échouent. |
| P0 | Dépendances concernées par des avis | composer audit --locked : deux avis MongoDB, dont un élevé, deux Smarty moyens ; exploitabilité applicative non démontrée. |
| P0 | Contrôle de lecture par table manquant dans un service | services/json_data.php:11-12 documente l'absence de droit_table_enforce('R', $table) ; vérifier aussi bootstrap et parcours HTTP avant de conclure sur l'exploitation. |
| P0 | Chaîne de validation incomplète | PHPUnit/PHPStan absents de vendor/bin ; test_integration.php sort avec 255 après avertissements d'en-têtes. Cause fatale exacte à diagnostiquer. |
| P1 | Installation contourne les vérifications | composer.json désactive TLS, secure-http et platform-check ; Dockerfile utilise --ignore-platform-reqs. |
| P1 | Requêtes répétées pendant les regroupements | ClassAppAgg.php:140-160 effectue findOne et une requête de comptage dans la boucle ; coût réel à mesurer. |
| P1 | Couplage et typage partiel | App, propriétés dynamiques, dépendances globales et bootstrap partagé. |
| P2 | Duplication de bibliothèque | appclasses/ClassPHPMailer.php et classes/ClassPHPMailer.php sont identiques octet pour octet ; cartographier les inclusions avant consolidation. |

CSRF existe et est appelé dans actions.php et json_action.php : une recherche du
seul mot « csrf » en minuscules aurait donné un diagnostic erroné. De même, les
occurrences textuelles de MongoClient ou eval peuvent se trouver dans des
commentaires. Aucun total d'occurrences n'est assimilé à un défaut exécutable.

Le manifeste Node annonce >=18 mais contient des dépendances récentes et un
chemin dev absolu `file:D:/development/idae/packages/idae-be`. Vérifier les engines
réels, le verrouillage et la construction sur une machine vierge. Aucun audit npm
ni benchmark de charge n'a été exécuté durant cette passe.

## Lots exécutables en autonomie

Chaque lot conserve un diff réversible, ses résultats de contrôle et son point
de reprise. Un lot bloqué n'empêche pas les lots indépendants. Les commits ne
sont pas créés automatiquement par cet audit.

| Lot | Travail concret | Preuve de sortie |
|---|---|---|
| 01 : référence | Produire un inventaire reproductible, restaurer les outils dev dans un environnement isolé, diagnostiquer le script d'intégration et interdire les faux succès. Corriger la syntaxe PHP incompatible. | Lint sans erreur ; PHPUnit et PHPStan exécutables ; résultat explicite pour chaque contrôle, y compris ceux bloqués. |
| 02 : sécurité | Caractériser autorisations autorisé/refusé et séparation des bases ; appliquer les contrôles manquants avec les helpers existants. Réactiver TLS et vérifier les exigences plateforme. | Tests HTTP 401/403 et parcours autorisé ; aucune lecture inter-base ; installation reproductible avec certificats valides. |
| 03 : dépendances | Traiter MongoDB et Smarty séparément ; vérifier usages IMAP et Elephant.io avant remplacement ou retrait. Retirer les contournements devenus inutiles. | composer validate/check-platform-reqs/audit ; tests de rendu, BSON, curseurs et notifications ; liste des avis résiduels. |
| 04 : contrats | Caractériser réponses JSON/JS, pagination, identifiants, erreurs, CRUD, sessions et événements Socket.IO. Isoler toutes les écritures dans MongoDB de test. | Contrats publics inchangés ; tests négatifs ; aucun test écrivant dans les bases métier. |
| 05 : factorisation | Cartographier appelants de App ; extraire une responsabilité par changement dans la structure existante. Traiter les duplications actives avant le code supposé mort. Typer progressivement les frontières. | Tests avant/après ; aucune nouvelle erreur statique ; façade App compatible ; pas d'augmentation opportuniste de baseline. |
| 06 : performances | Instrumenter requêtes et métadonnées ; mesurer distinct_rs, listes et chargement initial ; comparer lots de requêtes, projections et agrégation. Examiner explain sur données de test représentatives. | p50/p95, nombre de requêtes, mémoire et taille des réponses avant/après sur le même jeu ; aucune troncature nouvelle. |
| 07 : exploitation | Ajouter CI après stabilisation des commandes, logs structurés sans secrets, délais d'attente, contrôles de santé représentatifs et vérification de restauration des sauvegardes. | Pipeline reproductible ; scénario de panne ; restauration dans une instance isolée ; procédure de retour arrière. |

Autres axes : cohérence entre écriture et notification, idempotence des actions
rejouées, autorisations de champs, cloisonnement client/base, exposition des
diagnostics et sauvegardes sous DocumentRoot, encodage, migrations de métadonnées,
limites des filtres et pagination. Ce sont des pistes à examiner, pas des
vulnérabilités toutes confirmées.

## Règles d'exécution et limites

L'autonomie couvre investigation, correctifs locaux, tests et documentation.
Elle ne suppose ni connaissance des invariants métier absents du dépôt, ni droit
de modifier les secrets, les données réelles ou de déployer. En cas d'ambiguïté
métier, conserver le comportement et documenter le blocage ; poursuivre ailleurs.
Les changements d'index se préparent sur une instance isolée avant toute application réelle.

Pas de cache partagé avant définition de l'invalidation et du cloisonnement.
Pas de typage strict massif sans tests des appelants : les coercitions PHP
historiques constituent des comportements observables. Pas de montée de runtime
ou de réécriture Node imposée à partir de recommandations documentaires anciennes.

État de cette passe : audit et plan rédigés ; aucun code applicatif modifié,
aucun lot de modernisation déclaré terminé. Le test de migration annonce 25/0,
mais tolère un échec de connexion : il ne valide pas l'intégration MongoDB.
PHPUnit, PHPStan, couverture et performances restent non mesurés.

Journaux et codes de sortie : `tmp/audit-modernisation-2026-11-08/results.json`
et fichiers `.log` voisins. Le shell de lint complet retourne le statut de la
dernière itération ; les erreurs individuelles consignées font foi.
