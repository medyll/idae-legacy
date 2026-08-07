# BE_PLAN — Remplacement de PrototypeJS par @medyll/idae-be

> Branche : `feat/idae-be-migration`
> Créé : 2026-08-05

## Contexte

Le SPA `idae/web/javascript/` repose sur PrototypeJS 1.7.3 + Scriptaculous, chargés par `bag.js` via le groupe `require_hell` de `idae/web/javascript/main_bag.js:13`. Prototype est mort depuis ~2015 : il patche les prototypes natifs (`Array`, `String`, `Element`), ce qui bloque toute modernisation du front et casse par intermittence avec les libs récentes déjà chargées (Chart.js, swiper, tinymce, draggabilly).

Objectif : sortir Prototype + Scriptaculous du bundle et faire reposer le DOM sur `@medyll/idae-be` (`D:\development\idae\packages\idae-be`, v1.96.3), **sans réécrire les 93 fichiers JS ni les 181 fichiers PHP/Latte d'un coup**, et sous filet de tests Playwright.

Décisions actées :
- **Shim de compatibilité** au-dessus d'idae-be plutôt que réécriture directe.
- Les trous d'API sont comblés **localement dans idae-legacy**, pas upstream.
- Livraison via **bundle IIFE esbuild** pour rester compatible avec le loader `bag.js`.

## Ampleur mesurée

| Surface | Volume |
|---|---|
| JS app (hors vendor) | 93 fichiers / 27 151 lignes |
| `$()` | 1 667 en JS + 719 dans PHP/Latte (181 fichiers) |
| `Element.*` | ~2 074 appels réels (`readAttribute` 306, `setStyle` 202, `select` 199, `observe` 154, `addClassName` 120, `update` 118, `insert` 107) |
| `Class.create` / `Object.extend` | 55 / 82 |
| Extensions Array/String | ~480 réelles (`each` 125, `invoke` 104, `bindAsEventListener` 61, `size` 57) |
| `Ajax.*` | 21 (7 `Ajax.Request`, 6 `Ajax.Updater`) |
| `Effect.*` / Scriptaculous | 29 `Effect.*`, 3 `Draggable`, 4 `Autocompleter` |
| Tests JS existants | `playwright/tests/` — 3 specs, 227 lignes. **Seul filet.** |

## Couverture d'idae-be

**Couvert** : sélection, traversal (`up/next/previous/siblings/children/closest/find/findAll`), classes, attributs, dataset, styles, insertion/suppression, events basiques (`on/off/fire`), position (`clonePosition/snapTo/overlapPosition`), timers, `fetch`/`updateHttp`/`insertHttp`.

**Absent → shim local** : délégation d'événements, `Form.serialize`, Enumerable/`$A`/`$H`/`$w`/`$F`/`$R`, `Class.create`/`Object.extend`, `Effect.*` + Scriptaculous, Ajax robuste (query-string, `onFailure`, statuts, abort), `getDimensions`/`cumulativeOffset`/`viewportOffset`, `bindAsEventListener`.

**Piège connu** : `WalkHandler.methodize` retourne les éléments *trouvés* (jQuery-style), sauf `findAll()` qui retourne l'original — incohérence à contourner dans le shim (`walk.ts` ~340-360).

---

## Phase 0 — Commit + push du travail en cours

- [x] `feat(webmcp): JSON action endpoint, MCP errors and bounded paging` (`32283da`)
- [x] `chore(settings): allow docker exec and php -l batch lint` (`c9ec5bc`)
- [x] `git push -u origin codex/fix-php82-compat-errors`

---

## Phase 1 — Branche dédiée + filet Playwright (baseline)

- [x] Créer la branche `feat/idae-be-migration`
- [x] Écrire `BE_PLAN.md` à la racine

Le shim ne peut pas être validé sans référence. On capture le comportement **avec Prototype encore chargé**.

Config existante : `playwright/playwright.config.ts`, `baseURL` = `http://localhost:8080`, specs dans `playwright/tests/`.

- [x] `fixtures/auth.ts` — login + `storageState` via `global-setup.ts`, et `waitForAppReady()`
- [x] `fixtures/app.ts` — `openChrome` / `openRecord` / `openList` + attente des `.cf_module`
- [x] `helpers/console-guard.ts` — fixture qui échoue le test sur toute erreur console. **Détecteur principal** de régression : un `$` manquant ou un `Element.xxx is not a function` remonte immédiatement
- [x] `prototype-surface.spec.ts` — assertion de la surface d'API via `page.evaluate()`. **Doit passer identiquement avant et après le swap — c'est le contrat du shim**
- [x] `window-gui.spec.ts` — `app_window.js` (238) : ouverture/fermeture d'une fiche et d'une liste, deux fenêtres coexistantes
- [x] `datatable.spec.ts` — `app_datatable.js` (442) : colonnes issues du schéma, chargement des lignes via le canal socket, filtre de recherche côté client
- [x] `explorer.spec.ts` — panneau « historique » du bureau (`app_gui_panel.php`) : liens réels via `[act_chrome_gui]`, `[auto_tree]`/`.auto_tree_caret`
- [x] `smoke.spec.ts` / `uiux.spec.ts` réécrits contre de vrais sélecteurs (`#desktop`, `.ms-Icon--waffle`) — ils pollaient `#main, .app-gui, #grid`, inexistants dans cette app
- [x] `insertionq.spec.ts` — `app_insertionQ.js` (300) : **écrit, vérifié vert** (2/2, ~45 s à environnement sain)
- [x] `forms.spec.ts` — onglet Modifier (`app/app/app_update`), `$F()`, sérialisation (`Form.serialize` + `Ajax.Updater`), auto-close. A révélé un crash PHP 8.2 (`skelMdl::doCurl` non statique, `postAction.php:201`) et un asset manquant (`appcss/dist/images/spinner.gif`) — corrigés
- [x] Snapshots de référence (`toHaveScreenshot`) sur 4 écrans clés (bureau, liste, fiche, onglet Modifier) — zones à données vivantes masquées (tuiles, notes, calendrier, panneau historique, lignes groupées), tolérance 0,5 % sur le bureau pour le jitter de bordure des zones masquées
- [x] Script `"test:baseline": "npx playwright test --update-snapshots"` dans `playwright/package.json`
- [x] **Critère de sortie : suite verte (20/20), Prototype chargé, snapshots commités. Phase 1 close.**

**Perf suite — constat de session** : chaque test reboote toute l'app (~60 scripts, cache-buster systématique) ; à froid ~45 s pour 2 tests, mais sous charge Docker (Desktop up plusieurs jours) un boot peut atteindre 60-90 s. Si l'environnement devient lent en cours de session : `docker restart idae-legacy idae-socket` suffit — inutile de toucher aux tests. Piste d'accélération non implémentée : page partagée par fichier (`test.beforeAll` + contexte réutilisé) pour diviser le nombre de boots par spec.

### Prochaine session — Phase 2

Phase 2 (bundle esbuild d'idae-be) — voir plus bas, rien n'a changé. Démarrer par l'ajout de `@medyll/idae-be` en devDependency de `idae/web/app_node/`.

### Ce que l'exploration a établi

**Amorçage.** `main_bag.js` ajoute un cache-buster à chaque entrée, donc le cache de `bag.js` ne sert jamais : ~60 scripts recharge à chaque visite, 10-20 s avant que l'app soit utilisable. D'où le timeout à 120 s.

**Signal de ready.** `APP.APPSCHEMES` se peuple **aussi pour un visiteur anonyme** — une page non authentifiée a donc l'air « bootée ». La condition retenue est `APPSCHEMES` peuplé **et** `#desktop` présent. On évite volontairement `#main_progress_hold` masqué, qui dépend de `Effect.Fade`.

**Prototype patche `HTMLElement.prototype`, pas `Element.prototype`.** Le contrat vérifie donc les méthodes sur un élément vivant : c'est ce dont le code dépend réellement, et ça laisse le shim libre de choisir son hôte.

**Sélecteurs stables** (relevés sur l'app réelle) :

| Élément | Sélecteur |
|---|---|
| Navigation | `act_chrome_gui(file, vars)` — ex. `('app/app/app_fiche', 'table=client&table_value=63376')` |
| Fenêtre | `.containerdisp`, id = `container` + slug(file+vars) |
| Barre de titre | `.handledisp .titlefrm` / `.buttonclose` / `.buttonreduce` / `.popperdisp` |
| Contenu | `.innerdisp[table][mdl]` |
| Placeholders de module | `.cf_module[mdl=...]`, remplis par un second aller-retour AJAX |
| Datatable | `#app_liste_{table}_`, `table.ethop.table_groupe`, `tbody.div_tbody`, `.tbl_footer` |
| Onglets de fiche | `a.cancelClose` avec `onclick="act_chrome_gui(...)"` ou `ajaxInMdl(...)` |

**Code mort confirmé** — non chargé par `main_bag.js` et non référencé côté PHP : `myui/DatePicker.js`, `myui/ComboBox.js`, `myui/Autocompleter.js`, `librairie/crossfade.js`, `app/app_websocket.js`, `app/app_prototype.js`. `myui/TableGrid.js` (412 hits) n'est chargé qu'à la demande par `mdl/app/app_dyn_table.php`, et `librairie/canvasjs.min.js` par `mdl/app/app_stat/statistique.php`. Ça retire ~1 000 appels Prototype du périmètre réel et **change l'ordre de la Phase 5**.

**Résolu — le « 0 résultats » avait deux causes, aucune liée au shim :**

1. **Canal d'auth séparé pour les données.** `services/json_data_table.php` et consorts ne sont pas appelés en HTTP direct mais via `socket.emit('get_data', ...)` (`javascript/app/app.js`). Le pont Node (`app_node/src/socket/handlers.js` → `services/phpBridge.js`) authentifie son propre appel HTTP vers PHP avec le `PHPSESSID` lu dans le **payload émis**, que le client ne source jamais que depuis `localStorage` — jamais depuis le cookie. Un `storageState` construit uniquement via `request.newContext()` (sans jamais visiter de page) produit une session avec cookie valide mais `localStorage` vide : la fenêtre s'affiche connectée, mais chaque liste reçoit une réponse vide, sans erreur console, sans requête en échec. Fix dans [global-setup.ts](playwright/global-setup.ts) / [fixtures/auth.ts](playwright/tests/fixtures/auth.ts) : visiter l'origine avant de logger, puis miroir `PHPSESSID`/`SESSID` dans `localStorage` comme le fait le vrai flux de login (`mdl/app/app_login/actions.php`).
2. **Deux vrais crashs PHP 8.2**, révélés une fois l'auth correcte (masqués avant car la requête n'atteignait jamais ce code avec des données réelles) :
   - [json_data_table.php:478](idae/web/services/json_data_table.php:478) — la liste `client` s'ouvre par défaut avec `groupBy=telephoneClient` (bouton « Grouper » actif dans l'UI). Pour les branches non-`grille`, `$arr_dist` est déjà la valeur scalaire du groupe (voir `$table_value` juste au-dessus dans chaque `case`), pas un enregistrement — l'indexer par nom de champ (`$arr_dist[$groupBy]`) était un warning PHP7 silencieux (« Illegal string offset »), devenu `TypeError` fatal en PHP8.
   - [ClassApp.php:2236](idae/web/appclasses/appcommon/ClassApp.php:2236) — `stripslashes(null)` sur un champ `textelibre` vide, strict depuis PHP 8.1.

   Commits : `2358881` (fixes PHP), `49d0ef4` (harness + spec). Suivi par un 3e crash du même acabit, révélé par `forms.spec.ts` : `skelMdl::doCurl` appelé statiquement alors qu'il était déclaré en méthode d'instance — fatal en PHP 8 (`postAction.php:201` à chaque soumission de formulaire `auto_close`). Déclaré `static` (il n'utilise pas `$this`). Asset manquant découvert au passage : `.loading` référence `images/spinner.gif` relatif à `appcss/dist/`, absent du build commité → copie de `css/images/spinner.gif` vers `appcss/dist/images/` (dossier ignoré par git) et ajout de la copie au script `build:css` pour qu'elle survive à un rebuild.

**Piège de données de test** : le 2e test d'`insertionq.spec.ts` ouvrait une liste `prospect` — table absente du dataset de test, la fenêtre ne rend jamais `table.table_groupe`. Réduit à liste `client` + fiche `client` (couple déjà éprouvé par `window-gui.spec.ts`) : prouver la ré-extension ne demande pas deux tables, seulement deux sous-arbres insérés dynamiquement. Ne pas hardcoder de nom de table non vérifié dans un spec.

**Piège de test découvert en écrivant `datatable.spec.ts`** : la recherche (`input[placeholder=Rechercher]`) est branchée sur `keyup` via délégation Prototype (`myddeExplorer.js`), pas sur `input` — `Locator.fill()` ne déclenche rien, il faut `pressSequentially()`. Et `act_search` par défaut (`where_search:'local'`) ne re-requête jamais le serveur : il masque/affiche les `<tr>` déjà chargées en place, le compteur du footer ne bouge pas.

**Environnement — Docker Desktop a plongé sous la charge des runs parallèles** (moteur API en erreur 500, DNS interne `host.docker.internal` en échec, `idae-socket` unhealthy). Auto-guéri après `docker restart idae-socket` + patience. Retenu : lancer la suite Playwright avec `--workers=1` sur cette stack — plusieurs contextes navigateur partageant la même session PHP (même `PHPSESSID` de `storageState`) en parallèle sature le socket. Le suffixe `--workers=1` est déjà le comportement par défaut observé dans cette session ; le fixer explicitement évite une régression si la config change.

---

## Phase 2 — Bundle IIFE d'idae-be

`@medyll/idae-be` est de l'ESM pur non bundlé (ES2020+, 89 KB sur 15 fichiers), sans deps runtime. Un seul esbuild suffit.

- [x] Ajouter `@medyll/idae-be` en devDependency de `idae/web/app_node/` via `file:D:/development/idae/packages/idae-be` (tant que non publié) plutôt qu'un chemin relatif fragile
- [x] Script `build:idae-be` dans `idae/web/app_node/package.json` (là où vit déjà `build:css`) :
      `esbuild @medyll/idae-be --bundle --format=iife --global-name=IdaeBe --target=es2017 --sourcemap --outfile=../javascript/vendor/idae-be/idae-be.iife.js`
- [x] Cible `es2017` retenue : le bundle charge déjà Chart.js / swiper / tinymce, qui exigent ce niveau — pas de navigateur plus ancien à préserver
- [x] Commiter la sortie dans `idae/web/javascript/vendor/idae-be/` — le repo n'a aucun build à l'exécution, tout le vendor est commité (90,9 Ko + sourcemap)
- [x] Vérifier `window.IdaeBe` en console — 20 exports, `IdaeBe.be('#id')` opérationnel sur un élément vivant

---

## Phase 3 — Couche de shim

Emplacement : `idae/web/javascript/vendor/idae-be-shim/` (colocalisé avec le bundle qu'il consomme).

| Fichier | Contenu | Base |
|---|---|---|
| `shim-core.js` | `$`, `$$`, `$A`, `$H`, `$w`, `$F`, `$R`, `Prototype.*`, `Try.these` | `be()` / `querySelectorAll` |
| `shim-class.js` | `Class.create`, `Object.extend`, `$super`, `Hash` | classes ES + `Object.assign` |
| `shim-element.js` | ~45 méthodes `Element.*` sur `Element.prototype` | délégation vers `be(this)` |
| `shim-enumerable.js` | extensions `Array`/`String`/`Number`/`Function` (`each`, `invoke`, `pluck`, `bindAsEventListener`, `toQueryString`, `stripTags`, `gsub`, `camelize`, `defer`…) | natif |
| `shim-event.js` | `Event.observe/stop/element`, délégation, `Element#fire` via `CustomEvent` | `be().on/off/fire` |
| `shim-ajax.js` | `Ajax.Request`, `Ajax.Updater`, `Ajax.Responders`, `PeriodicalExecuter` | `fetch` + `be().updateHttp` |
| `shim-effects.js` | `Effect.*` (29 appels), `fade`, `Draggable` | transitions CSS + Web Animations API |

- [x] `shim-core.js`
- [x] `shim-class.js`
- [x] `shim-element.js`
- [x] `shim-enumerable.js`
- [x] `shim-event.js`
- [x] `shim-ajax.js`
- [x] `shim-effects.js`
- [x] Flag dev `IDAE_SHIM_WARN` : `console.warn` + stack à chaque appel shimé → donne la liste réelle des call-sites à réécrire en Phase 5 en naviguant l'app, plutôt qu'en grepant
- [x] Vérifier qu'aucun code ne dépend de la valeur de retour de `Element.extend` (devient un no-op : les méthodes sont sur le prototype)
- [x] Ménage : supprimer `vendor/prototype/prototype-1.7.js` (copie morte), la référence morte à `prototype.js` dans `idae/web/bin/templates/app/appsite/page/page_body.latte:29`, et `app/app_prototype.js` (1 494 lignes, non chargé)

### Principes

1. **Contrat = `prototype-surface.spec.ts`.** Le shim n'implémente que ce que le code appelle réellement — pas la totalité de Prototype.
2. **On garde le patch des prototypes natifs.** C'est laid, mais c'est ce qui permet aux 719 `$()` en PHP et aux 2 074 `Element.*` de continuer à marcher sans toucher un fichier. Le patch rétrécit à mesure que la Phase 5 avance.
3. `app_insertionQ.js` est la pièce à traiter en premier : c'est lui qui ré-étend les nœuds insérés dynamiquement.

---

## Phase 4 — Swap dans le loader

- [ ] Remplacer le contenu de `require_hell` dans `idae/web/javascript/main_bag.js` par le bundle idae-be + les 7 shims, **au même rang** dans `require_trame` (3e sur 9) — tout ce qui suit dépend de la présence des globals
- [ ] Vérifier que `fade` est bien shimé : `main_bag.js` appelle lui-même `$('main_progress_hold').fade('bounce')` et `$('body').setStyle({...})` après drainage de la queue
- [ ] Rejouer la suite Playwright **sans `--update-snapshots`** : zéro erreur console, zéro diff de snapshot
- [ ] Évaluer la suppression de `vendor/sizzle.js` (chargé dans `require_scripts`, redondant avec `querySelectorAll`) — à valider séparément

---

## Phase 5 — Réécriture native progressive

Prototype sorti, on migre fichier par fichier vers l'API idae-be native, du plus dense au moins dense. Chaque fichier = un commit + la spec Playwright correspondante qui reste verte.

Ordre revu après vérification de ce que `main_bag.js` charge vraiment :

- [ ] `app/app_datatable.js` (442)
- [ ] `librairie/myddeExplorer.js` (370)
- [ ] `app/app_insertionQ.js` (300)
- [ ] `engine/methods.js` (253)
- [ ] `app/app_window.js` (238)
- [ ] `librairie/myddeDatalist.js` (194)
- [ ] `librairie/picPicker.js` (179)
- [ ] `myui/TableGrid.js` (412) — chargé à la demande par `mdl/app/app_dyn_table.php`, pas au boot
- [ ] Supprimer le code mort plutôt que le migrer : `myui/DatePicker.js`, `myui/ComboBox.js`, `myui/Autocompleter.js`, `librairie/crossfade.js`, `app/app_websocket.js`, `app/app_prototype.js`
- [ ] Supprimer chaque fichier de shim quand `IDAE_SHIM_WARN` ne remonte plus aucun call-site pour sa famille

Les templates PHP/Latte (719 `$()`) viennent en dernier, ou jamais — le shim `$`/`$$` peut rester en place indéfiniment pour eux, c'est ~30 lignes.

Remontée upstream vers `@medyll/idae-be` à envisager plus tard pour ce qui est générique : délégation d'événements, `Form.serialize`, Ajax robuste (le package n'a actuellement **aucune gestion d'erreur** dans `updateHttp`/`insertHttp` — un 404 est injecté comme contenu).

---

## Perf — cache-busting cassé, et l'instabilité socket sous WSL2

**Cache-busting.** `main_bag.js` faisait `?v=<Date.now()>` sur les ~90 fichiers JS/CSS à **chaque** chargement — pas un souci de dev, un souci de prod : tout utilisateur réel retéléchargeait tout, à chaque visite, pour toujours, sans jamais toucher le cache IndexedDB de `bag.js`. Fixé (commit `f4f090a`) : `appfunc/asset_versions.php` construit un manifeste `{chemin: mtime}` en scannant `javascript/`+`css/` récursivement (aucune liste dupliquée à synchroniser avec `require_trame`), injecté via `window.FILE_VERSIONS` avant `main_bag.js`. Chaque fichier n'est reversionné que si son mtime a changé.

Vérifié : 90 requêtes à froid → 3 si rien ne change (bag.js + main_bag.js, toujours frais par design, + un `<link>` orphelin non versionné) → **4 si exactement 1 fichier est modifié**, et c'est bien ce fichier-là le seul refetché.

Piège trouvé en même temps : `.htaccess` mettait **`index.php` lui-même** en cache navigateur 60h (`max-age=216000`, hérité de la règle générique `\.(html|php)$`). Comme `index.php` embarque le manifeste `FILE_VERSIONS` généré à la volée, un visiteur revenant dans les 60h gardait un manifeste figé, peu importe les vrais changements disque — ça neutralisait le fix silencieusement. Override `no-cache` ajouté spécifiquement pour `index.php`/`reindex.php` ; la règle générique (qui met potentiellement en cache navigateur d'autres endpoints `.php` dynamiques — `json_data.php`, `json_scheme.php`, etc. s'ils sont appelés en GET) n'a **pas** été touchée — à auditer séparément si ça devient un problème, hors scope ce soir.

**Instabilité socket.io pendant le boot Playwright.** Sous WSL2, les boots automatisés (Chromium piloté par Playwright) montraient parfois `WebSocket is closed before the connection is established` en plein milieu du chargement, suivi d'une reconnexion avec un nouveau socket ID — `schemeLoad()` ne survit pas à ce changement d'ID et reste bloqué jusqu'au timeout. **Isolé et innocenté le réseau** : un stress-test Node pur (socket.io-client, 15 connexions/déconnexions rapprochées, sans navigateur ni Playwright) donne 15/15 propre à la fois via le port forwardé WSL2 (`localhost:3005`, ~309ms/connexion) et via le réseau Docker interne (`idae-socket:3005`, sans passer par WSL2 du tout, ~3ms/connexion). Le port-forwarding WSL2 ajoute de la latence mais n'introduit aucune instabilité.

Hypothèse retenue (non vérifiée formellement, mais cohérente avec toutes les observations) : le boot exécute ~90 scripts de façon synchrone sur le thread JS principal (lecture/écriture IndexedDB + eval par `bag.js`) ; si ce thread reste bloqué assez longtemps, le client socket.io rate sa fenêtre de heartbeat et se croit déconnecté côté client, alors que la connexion réseau réelle n'a jamais bronché — faux positif déclenché par la charge CPU du boot, pas par le réseau. Le fix cache-busting réduit directement ce volume de travail synchrone (moins de fichiers à parser/eval sur un chargement répété), donc devrait atténuer ce risque sans action supplémentaire. Non re-testé formellement après le fix (session déjà très longue) — à confirmer à la prochaine suite complète.

Mitigation en attendant : `retries: 2` dans `playwright.config.ts`, `waitForAppReady`/`openApp` à 120-180s. Confirmé (par le retour utilisateur direct) que l'usage réel — un seul onglet, une seule connexion socket — n'est jamais exposé à ce flake ; c'est spécifique aux boots automatisés rapprochés.

Côté suite Playwright, deux optimisations orthogonales au fix cache (commit `73938f5`) :
- **Session par worker** (`fixtures/test-base.ts`) au lieu d'une session `storageState` unique partagée — celle-ci forçait `workers: 1` (PHP sérialise les requêtes concurrentes sur un même fichier de session). Pas d'enforcement mono-session côté serveur, donc chaque worker peut avoir son propre `PHPSESSID` en toute sécurité.
- **Un seul boot par fichier de spec** (`fixtures/shared-boot.ts`) au lieu d'un boot par test — `test.beforeAll` + page réutilisée. Convertit `window-gui`, `datatable`, `explorer`, `insertionq`, `forms`, `uiux`, `snapshots`. Chaque test qui ouvre une fenêtre doit maintenant la fermer explicitement (plus d'isolation implicite page-par-test) — piège trouvé dans `snapshots.spec.ts` : une fenêtre non fermée s'empilait dans la capture d'écran du test suivant.

`workers` reste à **1** — le passage à 4 a été tenté deux fois ce soir et annulé les deux fois, à cause du flake socket ci-dessus (pas d'une vraie limite de ressources ; testé après le fix WSL2/Hyper-V avec 15.5GB de marge). La fixture per-worker est prête pour quand cette instabilité sera résolue ou jugée acceptable en pratique.

---

## Fichiers critiques

- `idae/web/javascript/main_bag.js` — graphe de chargement, groupe `require_hell`
- `idae/web/index.php` — points d'entrée `bag.js` / `main_bag.js`
- `idae/web/javascript/vendor/idae-be/` — bundle (nouveau)
- `idae/web/javascript/vendor/idae-be-shim/` — couche de compat (nouveau)
- `idae/web/app_node/package.json` — script `build:idae-be`
- `playwright/playwright.config.ts` + `playwright/tests/`

## Vérification

```bash
docker-compose up --build
```

```bash
cd playwright && npx playwright test
```

- Phase 1 : suite verte avec Prototype → baseline + snapshots commités.
- Phase 2 : `npm run build:idae-be` dans `idae/web/app_node/`, `window.IdaeBe` présent.
- Phase 4 : même suite, `--reporter=list`, zéro diff de snapshot, zéro erreur console.
- Phase 5 : après chaque fichier migré, suite complète + navigation manuelle de l'écran concerné avec `IDAE_SHIM_WARN=1`.

Logs serveur en parallèle : `docker-logs.ps1` (aucun output client autorisé — tout passe par `error_log()`).
