# Suivi de modernisation

Ce journal repose sur les commandes exécutées, sans utiliser BMAD comme preuve.

## 2026-11-08 : compatibilité et contrôles

- Audit indépendant : commit `68c2280`, poussé sur origin/main.
- Syntaxe PHP : commit `a67ead5`, poussé ; deux accès avec accolades remplacés
  dans dyn_devis_html.php. Le lint ciblé passe sur PHP 8.2.33.
- Test de migration : le ping consomme désormais le curseur de commande ;
  une connexion impossible ou une extension absente fait échouer le contrôle.
  La liste des bases n'est plus nécessaire au ping et n'est plus affichée.
- Preuve positive : MONGO_ENV=test, sidecar mongo-test, 26 succès, sortie 0.
- Preuve négative : MONGO_ENV=test et MONGO_TEST_DSN=mongodb://127.0.0.1:1,
  25 succès et 1 échec, sortie 1.

Le sidecar idae-mongo-test, précédemment arrêté, a été démarré pour les tests.
Aucune écriture de test ne vise le MongoDB métier.

PHPUnit 11.5.55 est installé dans /tmp/idae-audit-tools du conteneur, avec les
versions du verrou existant. Cette installation d'investigation laisse le vendor
applicatif intact et active TLS. Elle contourne uniquement les exigences IMAP et
la borne PHP supérieure de Smarty pour pouvoir exécuter les outils ; cela ne
valide pas la compatibilité des dépendances applicatives.

Résultat initial : 126 tests, 298 assertions, 3 erreurs et 2 avertissements.
Les erreurs viennent de tests appelant countBy, statsForField et search sur
ClassAppAgg, qui n'expose aucune de ces méthodes. Ces tests ne justifient pas,
à eux seuls, d'ajouter des fonctionnalités à l'application.
Journaux locaux : tmp/audit-modernisation-2026-11-08/.

Deux avertissements corrigés : update() vérifie l'absence d'identifiant sans
accéder à une clé inexistante ; distinct() en mode brut ne recherche plus les
métadonnées de la table liée, utilisées uniquement pour le mode complet.
La suite Unit passe : 108 tests, 172 assertions, sortie 0, aucun avertissement.

PHPStan a réellement été exécuté au niveau 6 : 3 267 erreurs de fichiers et
5 motifs d'exclusion devenus inutiles. Le journal conserve le détail. Aucune
baseline n'a été créée pour masquer cette dette ; le total inclut des
bibliothèques embarquées et des symboles historiques non résolus.
