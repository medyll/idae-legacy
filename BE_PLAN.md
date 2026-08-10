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

- [x] Remplacer le contenu de `require_hell` dans `idae/web/javascript/main_bag.js` par le bundle idae-be + les 7 shims, **au même rang** dans `require_trame` (3e sur 9) — tout ce qui suit dépend de la présence des globals
- [x] Vérifier que `fade` est bien shimé : `main_bag.js` appelle lui-même `$('main_progress_hold').fade('bounce')` et `$('body').setStyle({...})` après drainage de la queue
- [x] Rejouer la suite Playwright **sans `--update-snapshots`** : zéro erreur console, zéro diff de snapshot — tous les specs verts (retries de boot flake inclus ; `datatable: search hides non-matching rows` reste flaky mais échoue **aussi sans le swap** — `.fire` sur élément disparu, `app_socket.js:278`, apparu avec `d0c6ece` — pas une régression du shim). Ajout : sonde bridge `playwright/global-setup.ts`, échec explicite en 15 s si Apache/phpBridge est wedged (HANG_TEST.md)
- [ ] Évaluer la suppression de `vendor/sizzle.js` (chargé dans `require_scripts`, redondant avec `querySelectorAll`) — à valider séparément

---

## Phase 5 — Réécriture native progressive

Prototype sorti, on migre fichier par fichier vers l'API idae-be native, du plus dense au moins dense. Chaque fichier = un commit + la spec Playwright correspondante qui reste verte.

Ordre revu après vérification de ce que `main_bag.js` charge vraiment :

- [x] `app/app_datatable.js` (442) — API DOM/ES native, zéro appel direct aux shims vérifié par `datatable.spec.ts` avec `IDAE_SHIM_WARN=1`. Suite ciblée : chargement réel vert, recherche verte au retry avec le flake préexistant certifié `app_socket.js:278`, garde shim verte. En local, forcer `BASE_URL=http://127.0.0.1:8080` : `TEST_BASE_URL` dans `.env.testing` peut encore réintroduire `localhost` et son délai IPv6.
- [x] `librairie/myddeExplorer.js` (370) — natif, garde `IDAE_SHIM_WARN` verte (`explorer-shell.spec.ts`). Le fichier est passé en IIFE exposant `window.myddeExplorer` : il avait besoin de sept helpers locaux (délégation d'événements, `Form.serialize` sur un conteneur quelconque, `wrap`, `cleanWhitespace`, `stripTags`, query-string ↔ objet, `fire`), qu'idae-be ne fournit pas et qui n'ont pas été hissés dans un module partagé — ça imposerait de toucher le graphe de chargement de `main_bag.js`, ce que la Phase 5 n'a aucune raison de bousculer. Restent volontairement appelées : `socketModule`, `doCheck`/`doUnCheck` — API de l'app définie par `engine/methods.js`, pas API Prototype ; elles migreront avec ce fichier-là. Nouvelle spec : construction DOM (`act_expl_search_input` : attributs réécrits, `wrap()`, menu de portée à deux options), idempotence sur réouverture (`act_processed`), et garde anti-shim. Le chemin de recherche délégué était déjà couvert par `datatable.spec.ts`.
- [x] `app/app_insertionQ.js` (300) — natif, garde `IDAE_SHIM_WARN` verte (`insertionq.spec.ts`). Helpers locaux préfixés `iq_*`, dont trois qu'il fallait porter à la main : `cumulativeOffset`/`cumulativeScrollOffset` et un `clonePosition` compatible Prototype — celui d'idae-be décale par `transform` et prend d'autres options, alors que le seul appelant ici (la barre de planning des congés) dépend de la variante `top/left/width/height`. Piège trouvé : le watcher `.click_up` **injecte** `$(this).down(…).readAttribute(…)` dans un attribut `onclick`, qui s'exécute plus tard en contexte page — un fichier peut donc paraître natif tout en laissant une dépendance au shim dans une chaîne de caractères. Passé en `this.querySelector(…).getAttribute(…)`.

  La garde exclut explicitement les méthodes `Element.*` que l'app définit elle-même via `Element.addMethods` (`socketModule`, `loadModule`, `toggleContent`, `doRedim`, `doCheck`…). Elles sont enregistrées *à travers* le shim, donc `IDAE_SHIM_WARN` les signale, mais ce n'est pas de l'API Prototype : elles disparaîtront avec `engine/methods.js`.
- [x] `engine/methods.js` (253) — le nœud : c'est lui qui enregistrait les méthodes maison (`socketModule`, `loadModule`, `doCheck`…) via `Element.addMethods`, donc chaque fichier déjà migré retouchait le shim dès qu'il en appelait une. Elles sont maintenant installées directement sur `HTMLElement.prototype` (l'hôte que Prototype patchait). La signature change avec le mécanisme — `addMethods` passait le nœud en 1er argument, une méthode de prototype le reçoit dans `this` — mais aucun appelant n'est touché. Preuve : l'exclusion `APP_OWN_ELEMENT_METHODS` a pu être retirée de la garde d'`insertionq.spec.ts`.

  Supprimé plutôt que migré : `Appear`/`Fade`/`SlideDown`/`SlideUp` (wrappers Scriptaculous — tous les `.Appear(`/`.Fade(` du code sont des `Effect.*`, jamais ces méthodes-là), plus `Print`, `toggleSrc`, `animateCss`, `unCloneCopy`, `loadFragment` : zéro appelant.

  **Régression trouvée et corrigée** : le `kill()` d'origine faisait `purge()` / `remove()` / `purge()`. `purge` détachait tous les observers du nœud et de ses descendants, avec un effet de bord dont personne ne dépendait exprès mais dont tout dépendait en pratique — un handler encore en vol sur un nœud détruit en plein dispatch ne s'exécutait jamais. Rien de natif ne fait ça (on ne peut pas énumérer les listeners d'`addEventListener`). Concrètement : fermer une fenêtre la retire pendant que le clic remonte encore, et le handler `click` du conteneur appelait `give_focus()` → `makeOnTop` sur `null`. Corrigé dans `app_window.js:give_focus`, qui sort si la fenêtre n'existe plus — donner le focus à une fenêtre détruite n'a aucun sens. À garder en tête pour les fichiers suivants : **tout appelant de `kill()` doit tolérer de tourner contre un nœud déjà retiré.**
- [x] `app/app_window.js` (238) — natif, garde `IDAE_SHIM_WARN` verte (`window-gui.spec.ts`). `Effect.Fade` (seul appel Scriptaculous du fichier, dans `isReduced`) remplacé par une transition d'opacité native qui restaure `opacity` après avoir masqué, sinon le `show()` suivant rend un nœud invisible. Le fallback `Ajax.Updater` d'`ajaxLoad` — inatteignable en pratique, `app_socket.js` définit toujours `socket` — est passé en `fetch`, avec ré-exécution manuelle des `<script>` injectés (`innerHTML` ne les exécute pas, contrairement à `evalScripts`).

  **Piège** : `node.remove()` **n'est pas natif ici** — le shim remplace `Element.prototype.remove` par la version Prototype. Un fichier peut donc paraître migré et rappeler le shim à chaque suppression. Il faut `parentNode.removeChild(node)`. Corrigé aussi dans le `kill()` d'`engine/methods.js`. C'est la garde runtime qui l'a attrapé, pas la relecture.

  `content_loaded` garde son `removeEventListener` volontairement inopérant : l'original appelait `stopObserving` avec une fonction fraîchement `bind`ée, qui ne correspond jamais à celle attachée — le handler a donc toujours tourné à *chaque* `content:loaded`, pas seulement au premier. Le « réparer » changerait le comportement, pas le restaurerait.

  Découvert au passage : `appcss/dist/main.css` référence 5 images et une seule (`spinner.gif`) était copiée dans `dist/`, par un `copyFileSync` en dur dans le one-liner `build:css`. `max16.png` et `cancel.png` étaient donc en 404 sur chaque bouton de la barre des tâches. Remplacé par `appcss/copy-dist-images.mjs`, qui lit les références dans le CSS compilé et échoue le build si l'une d'elles ne se résout pas — un 404 de `background-image` est invisible jusqu'à ce qu'un utilisateur tombe sur l'écran concerné.
- [x] `librairie/myddeDatalist.js` (194) — natif, garde `IDAE_SHIM_WARN` verte. Aucune spec ne le couvrait : nouveau `datalist.spec.ts`. Mauvais fichier à laisser sans filet — il réécrit le DOM autour d'un input qu'il ne possède pas (l'enveloppe dans un nouveau parent, injecte un caret, et pose son dropdown sur `document.body` plutôt qu'à côté du champ), et chacune de ces étapes échoue en silence : un `wrap` cassé laisse l'input fonctionnel et les suggestions inatteignables, un dropdown non positionné rend en 0,0 derrière la fenêtre. L'onglet Modifier porte 4 de ces inputs dans le dataset de test.

  Portés à la main : `clonePosition` (variante Prototype, comme dans insertionQ), `viewportOffset`, `scrollTo`, `empty`, `previous`/`next` avec sélecteur. Corrigé au passage : `Entrée` sans élément surligné plantait sur `.first().readAttribute(...)` — la touche est déjà absorbée plus haut, on sort maintenant sans exception.
- [x] ~~`librairie/picPicker.js` (179)~~ — **supprimé, pas migré.** Chargé par `main_bag.js` mais jamais instancié : recherche sur tout le dépôt, `new picPicker(` n'existe nulle part, et la seule autre occurrence du nom était l'entrée du loader. 179 lignes de Prototype qui partaient au navigateur à chaque visite pour rien.
- [x] ~~`myui/TableGrid.js` (412)~~ — **tout l'arbre `myui/` supprimé** (12 fichiers, 5 320 lignes). La note « chargé à la demande par `mdl/app/app_dyn_table.php` » était fausse sur deux points : ce module ne charge aucun script, et rien n'appelle ce module. Le mot `myui` n'apparaît nulle part hors du dossier lui-même, et `MY` — l'objet racine sur lequel tout repose — n'est défini que dans `myui/myui.js`, que rien ne charge. `app_dyn_table.php` lève donc `MY is not defined` aujourd'hui déjà ; le supprimer ne change rien à son état. Le fichier PHP est conservé (un nom de module peut être référencé depuis Mongo, ce qu'un grep ne voit pas), mais il est mort.
- [~] Supprimer le code mort plutôt que le migrer :
  - [x] `librairie/crossfade.js` et `app/app_websocket.js` — supprimés, zéro référence dans le dépôt.
  - [x] `app/app_prototype.js` — déjà supprimé en Phase 3.
  - [x] `librairie/picPicker.js` — supprimé, voir ci-dessus.
  - [x] `myui/DatePicker.js`, `myui/ComboBox.js`, `myui/Autocompleter.js` — partis avec tout l'arbre `myui/`, voir ci-dessous.
- [ ] Supprimer chaque fichier de shim quand `IDAE_SHIM_WARN` ne remonte plus aucun call-site pour sa famille

### Inventaire runtime des call-sites restants (2026-08-09)

Mesuré en instrumentant l'app réelle : boot + liste + fiche + onglet Modifier, chaque appel shimé attribué à son fichier appelant par lecture de la pile. **2 099 appels**, dont :

- **1 209 sont internes au shim** — dont 1 204 `Element.setOpacity` émis par la boucle d'animation de `shim-effects.js:110`. Ce ne sont pas des call-sites applicatifs : ils disparaissent avec `shim-effects`. Ils sont déclenchés par deux `fade()` au boot (`app_bootstrap_init.js:96`, plus `afterAjaxCall.js:42`) — deux appels qui génèrent 1 204 itérations.
- **890 sont de vrais call-sites applicatifs**, répartis sur 10 fichiers.

| Shim | Appels applicatifs |
|---|---|
| `shim-core` (`$`, `$$`, `$A`, `$H`, `$w`) | 394 |
| `shim-element` | 227 |
| `shim-enumerable` | 131 |
| `shim-event` | 93 |
| `shim-class` (`Object.extend`, `Object.isString`) | 45 |

| Fichier appelant | Appels | API principales |
|---|---|---|
| `app/app_socket.js` | 318 | `$` 82, `$$` 65, `fire` 51+51, `Array.size` 23 |
| `librairie/sorttable.js` | 199 | `$` 76, `bindAsEventListener` 20, `Object.isString` 14 |
| `engine/afterAjaxCall.js` | 196 | `$` 92, `observe` 32+32, `$A` 10 |
| `engine/engine.js` | 68 | `$` 16, `Object.extend` 10, `writeAttribute` 8 |
| `librairie/autoToggle.js` | 45 | `$` 27, `Object.extend` 9, `cleanWhitespace` 9 |
| `app/app_tree.js` | 40 | `$A` 8, `Array.each` 8, `$w` 4 |
| `engine/module.js` | 17 | dispersé |
| `librairie/myddeNotifier.js`, `myddeview.js`, `app.js` | 7 | résiduel |

**Ce que ça change au plan.** La liste de Phase 5 avait été ordonnée par comptage `grep` statique sur les anciens fichiers. Aucun des 10 fichiers ci-dessus n'y figurait — et les fichiers qui y figuraient sont maintenant tous migrés ou supprimés. L'ordre réel, dicté par la mesure, est : `app_socket.js`, `sorttable.js`, `afterAjaxCall.js`, `engine.js`, `autoToggle.js`, `app_tree.js`, `module.js`.

**Correction du 2026-08-09, plus tard le même jour** : les trois `fade()` mesurés (`app_bootstrap_init.js`, `afterAjaxCall.js`, `myddeAttach.js:42`) étaient les seuls **déclenchés par les écrans de la sonde** — pas les seuls qui existent. Un grep sur `Effect.*`/`fade(` a trouvé beaucoup plus d'appelants réellement chargés par `main_bag.js`, jamais atteints parce que rien dans la sonde n'ouvrait de table éditable, d'upload, ou de notification : `librairie/appGui.js` (`Effect.Move`), `librairie/myddeAttach.js` (3 `fade()` de plus, non touchés), `librairie/myddeNotifier.js` (`Effect.Opacity`), `librairie/myddeupload.js`, `librairie/tableGui.js` (`Effect.Appear`), `librairie/validation.js` (`Effect.Appear`). `shim-effects` reste en place.

Les trois `fade()` mesurés sont remplacés par `fadeElement()` (helper natif partagé, `engine/methods.js` — même contrat : fondu vers 0, masquage, restauration de l'opacité, callback `afterFinish`). Vérifié en conditions réelles (pas seulement Playwright) : `hide_login()` fond, se masque, restaure son opacité, zéro erreur console.

Trouvé au passage, même motif que `picPicker`/`TableGrid` — du code chargé ou référencé mais mort :
- `librairie/growler.js` — non chargé par `main_bag.js`, zéro référence ailleurs. Supprimé.
- `librairie/myddeSlide.js` — définit la classe `myddeSlide`, jamais instanciée. Le seul appelant (`page_body.latte:295`) instancie `myddeSlideBox`, qui n'existe nulle part — référence déjà cassée avant cette suppression, comme `MY.TableGrid`. Supprimé.
- `app/app_bootstrap_init_old.js` — zéro référence dans tout le dépôt. Supprimé.

**Reste à faire pour supprimer `shim-effects`** : migrer `Effect.Move`/`Effect.Opacity`/`Effect.Appear`/`Effect.Parallel` dans les six fichiers listés ci-dessus, plus les 3 `fade()` non touchés de `myddeAttach.js`. Pas fait dans cette passe — la sonde d'inventaire ne les avait pas vus, donc le chiffrer aurait été une estimation, pas une mesure.

## Suppression de `shim-effects.js` (2026-08-09, suite)

Fait, avec un imprévu qui a changé la forme du travail.

**Les six callers réels, tous migrés :**
- `librairie/appGui.js:139` (`Effect.Move`, mode absolu) → `moveElementTo()`, helper local (un seul appelant dans tout le dépôt).
- `librairie/myddeNotifier.js:79`, `librairie/tableGui.js:54,56`, `librairie/validation.js:159` (`Effect.Opacity`/`Effect.Appear`) → `appearElement()`, partagé dans `engine/methods.js` (trois appelants réels).
- `main_bag.js:205` (`$('main_progress_hold').fade('bounce')`) → `fadeElement()`. L'argument `'bounce'` était déjà mort sous le shim : `Element#fade(options)` ne lit qu'un objet (`from`/`to`/`afterFinish`), jamais une chaîne — `Object.extend` sur une string itère ses index de caractères, ce qui ne produit aucune clé utile. Le fondu par défaut tournait déjà, sans jamais de « bounce ».
- `librairie/myddeAttach.js` : 2 vrais appelants migrés (`Progress[index]`, `this.element`) ; 3 autres (lignes 48, 232, 239) sont du code déjà mort — l'un dans un bloc commenté, les deux autres après un `return;` inconditionnel ligne 226. Laissés tels quels : ils ne chargent jamais le shim.

**Bug latent trouvé et corrigé, pas reproduit** : l'`Effect.Appear` du shim ne restaurait jamais `display` — seul `opacity` était animé (`setOpacity` dans `shim-element.js` ne touche que ça). Un élément inséré avec `style="display:none"` — exactement le cas de `validation.js`, qui construit ainsi son message d'erreur — restait invisible quelle que soit la durée du fondu, sur la branche de code censée le montrer. `appearElement()` restaure `display` si l'élément était masqué. Vérifié en conditions réelles : `myddeNotifier` → `growl()` → `appearElement` produit `opacity: 0.85` exactement (la cible passée), toast visible, texte présent. Le chemin `validation.js` n'a pas pu être déclenché par le jeu de données de test (le champ sondé n'a pas de règle active) — pas creusé davantage ; le code est le même que celui déjà vérifié pour `myddeNotifier`.

**L'imprévu : `Draggable`/`Draggables` vivaient dans le même fichier.** `shim-effects.js` ne portait pas que `Effect.*` — son en-tête l'annonçait (« … and Draggable(s) ») mais l'inventaire du matin n'avait mesuré que la famille `Effect`. Deux appelants réels et chargés : `librairie/cropper.js` (`CropDraggable` hérite de `Draggable` via `Class.create(Draggable, {...})` et surcharge plusieurs de ses méthodes) et `librairie/resizeGui.js`. Migrer `Draggable` en natif aurait voulu soit reproduire toute sa surface de méthodes pour que l'héritage de `CropDraggable` continue de fonctionner, soit réécrire à l'aveugle le comportement de sélection au clic-glissé de `cropper.js` — les deux plus risqués que d'isoler.

Extrait en fichier séparé, `vendor/idae-be-shim/shim-draggable.js` (Draggable/Draggables seuls, ~155 lignes), qui devient la dernière entrée de `require_hell` (c'est lui qui porte l'armement de `IDAE_SHIM_WARN` en fin de chaîne). `shim-effects.js` (441 lignes) supprimé.

**Nettoyage en cascade** : `shim-element.js` gardait cinq méthodes (`visualEffect`, `fade`, `appear`, `morph`, `highlight`) qui référençaient le `Effect` global — devenu `undefined` après la suppression. Zéro appelant, mais chacune aurait levé `Effect is not defined` à l'exécution : une mine plutôt qu'un mort inoffensif. Supprimées.

`prototype-surface.spec.ts` (le contrat de Phase 1/4) affirmait la présence de `Effect` et de huit de ses méthodes statiques — obsolète par construction, puisque plus rien n'y appelle. Ce n'est pas une régression à corriger, c'est le contrat qui doit suivre ce que l'app appelle réellement, comme documenté dans son propre en-tête. Mis à jour : `Effect` retiré de `GLOBAL_OBJECTS`, ses huit entrées retirées de `NAMESPACED`, `fade`/`appear`/`morph` retirés de `ELEMENT_METHODS`.

**Suite : 32/32.**

Réserve de méthode : aucun appel n'a été attribué à `inline (PHP/Latte)` sur ces écrans, mais ce n'est **pas** une preuve que les 719 `$()` des templates sont inertes — ils vivent surtout dans des attributs `onclick`, que cette sonde n'a pas déclenchés. Il faut une passe qui clique réellement avant de conclure sur `shim-core`.

Les templates PHP/Latte (719 `$()`) viennent en dernier, ou jamais — le shim `$`/`$$` peut rester en place indéfiniment pour eux, c'est ~30 lignes.

Remontée upstream vers `@medyll/idae-be` à envisager plus tard pour ce qui est générique : délégation d'événements, `Form.serialize`, Ajax robuste (le package n'a actuellement **aucune gestion d'erreur** dans `updateHttp`/`insertHttp` — un 404 est injecté comme contenu).

## Migration d'`app/app_socket.js` (2026-08-09)

Le fichier le plus appelé de l'inventaire (318 appels). Le dispatcheur de commandes socket.io : `receive_cmd` (switch `act_count`/`act_stream_to`/`act_progress`/…) et le handler `socketModule` qui injecte chaque fragment AJAX de l'app. Natif désormais, garde `IDAE_SHIM_WARN` verte (nouveau `socket.spec.ts`).

Détails mécaniques : `$$`/`$A` remplacés par `sk_qsa`, une tolérance générique aux valeurs d'attribut non citées (chiffre en tête, points) — copiée de `shim-core.js`'s `tolerantQueryAll` plutôt qu'appelée, pour que le fichier ne dépende plus de `shim-core.js` du tout. `node.remove()` évité (même piège que `app_window.js`/`myddeDatalist.js` : le shim le remplace, `parentNode.removeChild` est le seul vrai natif). Les trois blocs `data-count` identiques (`act_close_mdl`/`act_upd_data`/`act_add_data`) factorisés en `sk_refreshCounts()`, en préservant la divergence pré-existante entre eux (`act_close_mdl` passe `'id'+vars.table` à `runModule`, les deux autres `vars.table` — pas à cette passe de trancher si c'est un bug).

**Régression réelle introduite puis corrigée : `sk_fire` avec `.detail` au lieu de `.memo`.** `dom:stream_chunk` porte sa charge dans `event.memo` (lu ainsi par `app_datatable.js:291,1502`) — j'avais câblé un `CustomEvent` à la main avec `detail` sur ce site précis au lieu de passer par mon propre helper `sk_fire`. Suite verte au premier passage (32/32) parce qu'aucun test n'exerçait ce chemin de re-diffusion précis avec des données réellement en vol à ce moment — attrapé seulement à la relecture finale du fichier.

**La vraie régression, celle qui a pris du temps : `sk_update`/`sk_insert` n'exécutaient jamais les `<script>` injectés.** `Element#update()` de Prototype extrait les balises `<script>` avant l'assignation `innerHTML`, puis les `eval()` en différé (`.defer()`, ~10 ms) en scope global. `node.innerHTML = html` ne le fait jamais, dans aucun navigateur — un piège déjà rencontré et corrigé sur `app_window.js` (voir son entrée plus haut), mais que je n'ai pas anticipé en écrivant celui-ci. La moitié des fragments rendus côté serveur se terminent par un tel `<script>` : `mdl/app/app_liste/app_liste.php:100` appelle `load_table_in_zone(...)` en JS inline — c'est comme ça, et seulement comme ça, qu'une liste demande ses lignes. Sans l'`eval` différé, la fenêtre s'ouvre normalement (en-têtes, barre de recherche) mais reste vide pour toujours, **sans la moindre erreur console** — exactement le genre de silence que `socket.spec.ts` existe maintenant pour attraper.

Diagnostic : suite passée de 32/32 à 22/32 (10 échecs, dont `datatable: list loads real rows`), reproductible sur stack fraîche. Remonté par comparaison directe des logs serveur (`docker logs idae-socket`) entre la version originale (via shim, instrumentée au `sed`) et la mienne : même réponse `socketModule` de 28223 octets dans les deux cas, mais seule la version Prototype envoyait ensuite la requête `get_data(json_data_table, table=client, stream_to=...)` — la mienne ne l'envoyait jamais, parce que le `<script>` qui la déclenche n'était jamais exécuté. Correctif : `sk_update`/`sk_insertAt` reproduisent l'algorithme exact (`stripScripts` avant insertion, `eval` indirect différé sur le HTML original, scope global comme Prototype).

**Suite : 34/34** (32 + les 2 nouveaux tests de `socket.spec.ts`).

## Migration de `librairie/sorttable.js` (2026-08-10)

199 appels dans l'inventaire — le deuxième fichier le plus appelé. `sortableTable`, les en-têtes de tableau cliquables pour trier (`table.act_sort`, instancié par le watcher d'`app_insertionQ.js`). Natif désormais, garde `IDAE_SHIM_WARN` verte.

**Découverte en cours de route : le tri au clic est structurellement invisible sur l'écran principal de liste.** `app_datatable.js:639` masque volontairement son propre `<thead>` (`this.thead.hidden = true`) — l'en-tête visible réel que l'utilisateur voit et clique vient d'une zone flottante séparée, avec sa propre logique, sans rapport avec ce fichier. `sortableTable` s'instancie bel et bien sur chaque liste (attribut `isSortable` posé, aucune erreur), mais son `<thead>` n'est jamais visible ni cliquable sur ce chemin — la fonctionnalité existe, fonctionne, et n'est simplement jamais atteinte par un utilisateur réel sur les écrans que ce dépôt de test peut ouvrir. Les templates qui rendent VRAIMENT ce tri visible (`app_scheme_grille.php`, `skelbuilder_liste_*.php`, `document_liste.php`) sont des écrans admin/dev hors de portée facile du compte de test utilisé par cette suite.

Deux fichiers de test en conséquence : un test de construction sur l'écran réel (liste client, prouve zéro appel shim et zéro erreur), et trois tests fonctionnels contre une table synthétique construite à la volée (`new sortableTable(table)` sur un `<table>` créé et injecté par le test) — seule façon d'exercer le clic réel et la réorganisation des lignes sans dépendre d'un écran admin difficile à atteindre.

**Bug trouvé en écrivant les tests, pas en lisant le code : `bindAsEventListener` inverse l'ordre des arguments par rapport à `.bind()`.** `fn.bindAsEventListener(context, node)` produit un listener appelé `fn(event, node)` — l'événement réel en premier, les arguments liés après. `fn.bind(context, node)` produit l'inverse : `fn(node, event)`. J'avais traduit les deux `.bindAsEventListener(this, node)` du fichier par de simples `.bind(this, node)`, ce qui inversait silencieusement `event`/`node` dans `isClicked` et `setSizeTD` — `event.preventDefault` appelé sur un `<td>`, `node.classList.contains` appelé sur un `Event`. Deux `PAGEERROR` distincts (`reading 'contains'`, `reading 'offsetWidth'`) à l'exécution, zéro signal à la lecture du code migré (les deux fonctions ont la même arité, rien ne « a l'air faux »). Corrigé en câblant l'ordre explicitement (`function (event) { this.isClicked(event, node); }`) plutôt qu'en comptant sur l'ordre implicite d'un `.bind()`.

**Piège de données de test, pas de code** : `activeSort()` décide ascendant/descendant en comparant le contenu de la première ligne avant/après tri — si la ligne déjà en première position porte par coïncidence la valeur minimale, un tri ascendant a l'air d'un no-op et se fait inverser en descendant. Propriété réelle de l'algorithme (même heuristique sous Prototype), pas un bug de migration — corrigé en choisissant des données de test où la première ligne originale n'est pas déjà celle qui aurait la valeur minimale après tri.

Un défaut latent trouvé et durci plutôt que reproduit : `setSizeTD` déréférençait `node.previousElementSibling` sans le vérifier — plantait de façon identique sous Prototype (`$(node).previous()` sans sibling renvoie `undefined`, `.setStyle()` sur `undefined` lève la même erreur), donc pas une régression, mais un vrai risque révélé par le callback de redimensionnement tiers (`detect-element-resize.js`) qui peut se déclencher après suppression du nœud. Gardé désormais.

Aucune extraction/eval de `<script>` requise ici, contrairement à `app_socket.js` : tout le HTML construit ou déplacé dans ce fichier est généré côté client (en-têtes, réordonnancement de lignes), rien n'est un fragment récupéré du serveur.

**Suite : 37/37.**

## Migration d'`engine/afterAjaxCall.js` (2026-08-10)

196 appels dans l'inventaire — troisième fichier le plus appelé. Appelé sur chaque fragment chargé en AJAX (`app_socket.js`'s `socketModule`, `app_datatable.js`) pour câbler les gestionnaires de clic basés sur classe (`cancelClose`/`cancelClean`/`cancelRemove`/`cancelHide`/`cancelButton`/`cancelToggle`/`cancelFade`) que porte un formulaire ou fragment rendu côté serveur. Natif désormais.

Le seul point non trivial : `Event.element(event)`/`Form.Element#activate()` du shim, reproduits fidèlement (`aac_eventElement`, `aac_activate` — focus puis `select()` sauf si `type=hidden`, dans un `try/catch`). `unToggleContent`/`fadeElement` restent des appels à l'API native de l'app (`engine/methods.js`, déjà migré), pas au shim.

**Logique `mdlDiv`/`eval` préservée telle quelle, pas simplifiée.** Le fichier enveloppe l'id du nœud dans un littéral de tableau (`'["' + div.id + '"]'`), remplace le premier `/` par `'","'`, puis `eval()` le résultat et prend le dernier élément. Le 3ᵉ argument `'gi'` de `.replace()` n'a jamais rien fait (natif ou shimmé) — `String#replace` ne lit des flags que sur un motif `RegExp`, jamais sur une chaîne. Pour les ids réellement reçus ici (générés par `uniqid()`, jamais de `/`), ce mécanisme est un no-op complet : `frm` finit toujours par être `$(div)` lui-même, relu par son propre id. Gardé identique plutôt que « nettoyé », par principe de cette phase : ne pas changer un comportement qu'on n'a pas été chargé de corriger, même absurde.

Suite complète bruyante ce soir (7,8 à 9,1 min, 1-2 tests flaky à chaque run, jamais les mêmes) — signe de dégradation d'environnement déjà documentée dans ce fichier, pas une régression : une passe ciblée sur `forms.spec.ts`/`window-gui.spec.ts`/`insertionq.spec.ts` (les specs qui exercitent réellement les chemins `cancelClose`/`cancelFade`) tourne 9/9 propre en 3,9 min sans le moindre retry.

Nouveau `afterajaxcall.spec.ts` : `mdl/app/app/app_fiche.php:147` rend un vrai bouton `.cancelClose` (« Fermer ») sur chaque fiche — vérifié qu'il déclenche `dom:close` sur son parent après le délai de 350 ms, plus la garde anti-shim habituelle. Sur stack redémarrée : **39/39, zéro flaky, 6,6 min.**

**Suite : 39/39.**

## Migration d'`engine/engine.js` (2026-08-10)

68 appels dans l'inventaire — mais c'est le fichier le plus **critique** de la Phase 5, pas juste le plus appelé : `act_chrome_gui`/`ajaxMdl`/`ajaxInMdl` sont les primitives de navigation de l'app — quasiment chaque fenêtre ouverte dans toute la suite passe par l'une d'elles. Une suite verte est ici un signal particulièrement fort, puisque ce fichier est déjà exercé en continu par les 39 tests existants sans qu'aucun n'ait été écrit pour lui spécifiquement.

**Bug corrigé en écrivant la migration, pas en la testant** : `ajaxMdl`'s option `modalOn` — `$(options.modalOn).makeModal().modal().setStyle({zIndex:0})` — la valeur finale de la chaîne (et donc `dmp`/`ajaxOption.parent`) est le **div modal**, pas `modalOn` lui-même. `makeModal()` (natif, `engine/methods.js`) renvoie `modalOn` inchangé ; `.modal()` est l'accesseur qu'il attache, renvoyant le div overlay qu'il vient de créer. Ma première passe assignait `dmp = modalOn.makeModal()` — donc `modalOn` lui-même, pas le modal. Zéro appelant réel avec `modalOn` dans tout le dépôt (vérifié), donc inatteignable en pratique, mais corrigé quand même : c'est le genre d'erreur qu'une relecture attentive attrape, contrairement aux pièges `bindAsEventListener`/`<script>` des fichiers précédents qui ne se voient qu'à l'exécution.

**Deux ajax natifs file-local, mêmes contrats que ceux du shim, pas simplifiés.** `engine_ajaxRequest` (utilisé par `ajaxValidation`) rend le texte brut à `onComplete`, qui `eval()` lui-même, immédiatement — pas de `.defer()` ici, contrairement à `Element#update`. `engine_ajaxUpdater` (utilisé par `ajaxFormValidationReal`) a un contrat en deux couches distinctes, comme `shim-ajax.js`'s `Ajax.Updater` : le texte est TOUJOURS débarrassé de ses `<script>` avant d'être assigné en `innerHTML`, et SÉPARÉMENT, seulement si `options.evalScripts === true` est explicitement passé (ce qui EST le cas ici), le texte ORIGINAL est évalué en différé (~10 ms) — même leçon que `app_socket.js`, pour la même raison : un fragment de formulaire renvoyé par le serveur peut porter son propre `<script>` de suite.

`Form.serialize` reproduit à la main (`engine_formSerialize`) plutôt que remplacé par `FormData` : Prototype exclut **toujours** les boutons submit, quelle que soit l'option — `FormData`, elle, n'a aucune notion de « quel bouton a déclenché la soumission » hors d'un vrai événement submit, et inclurait la valeur de chaque bouton submit présent. Divergence réelle, pas cosmétique.

La même logique `mdlDiv`/`eval` que dans `afterAjaxCall.js`, mais **pas un no-op cette fois** : ici `file` est un vrai chemin `mdl` avec des `/` (ex. `"app/app/app_fiche"`). Comme `String#replace` avec un motif chaîne (pas regex) ne remplace jamais que la PREMIÈRE occurrence, `onlyFile` devient tout ce qui suit le premier `/`, pas littéralement le dernier segment du chemin. Gardé identique.

Nouveau test dans `forms.spec.ts` (garde anti-shim sur le double chemin `act_chrome_gui`/`ajaxFormValidation`, déjà exercé fonctionnellement par les deux tests existants du fichier) plutôt qu'un fichier séparé — ce spec couvrait déjà en détail la soumission réelle (corps POST, auto-close) avant même cette migration ; son en-tête, obsolète, a été réécrit pour décrire l'implémentation native plutôt que l'ancienne mécanique shimée.

**Suite : 40/40** (1 flaky lié au timing de boot, vert au retry — dégradation d'environnement déjà documentée, pas une régression).

## Migration de `librairie/autoToggle.js` (2026-08-10)

45 appels dans l'inventaire. Gère `.autoToggle` : « un seul élément actif à la fois » sous un conteneur — chaque LIGNE de tableau en est une (`app_datatable.js:752` et alentours), donc instancié à chaque rendu de liste (`app_datatable.js:413,424`). Natif désormais.

`autoPush`, la seconde classe que ce fichier définissait, supprimée plutôt que migrée : zéro appelant dans tout le dépôt (`new autoPush(` n'apparaît nulle part hors de sa propre définition) — même motif que `picPicker`/`TableGrid`/`growler` plus haut.

**Fausse piste suivie jusqu'au bout, et bien qu'elle ait été fausse.** Deux passes consécutives de la suite complète ont échoué sur `sorttable: constructs on a real list` — toujours le même test, jamais un autre — avec des durées de 16-18 min contre 2-8 min habituelles. Deux échecs identiques, ce n'est pas le bruit habituel (qui varie de run en run, documenté partout ailleurs dans ce fichier) : traité comme causal, pas comme de l'environnement. A/B propre : `git stash` sur `autoToggle.js` pour revenir à la version shimée, suite complète — 8 min, zéro échec sur `sorttable`, juste le bruit habituel. Restauration de ma version, deux passes de plus : l'une propre en 7,5 min, l'autre propre en 6 min. Sur quatre passes à code identique (ma version), deux échouent et deux réussissent — ce n'est donc **pas** une régression déterministe, malgré la coïncidence troublante des deux premières. Conclusion : volatilité réelle de l'environnement ce soir-là, plus sévère que d'habitude, pas causée par cette migration. Le protocole (isoler, comparer contre l'original, répéter avant de conclure) était le bon même si la conclusion finale infirme l'hypothèse de départ — mieux vaut ce détour que de committer une fausse cause ou, à l'inverse, de rejeter un vrai bug en l'attribuant trop vite à Docker.

Nouveau `autotoggle.spec.ts` : aucune spec n'exerçait le comportement réel avant (chaque ouverture de liste instancie déjà `autoToggle`, mais rien ne cliquait une ligne pour vérifier). Clic sur deux lignes successives, vérifié que la seconde devient active et la première le redevient pas.

**Suite : 42/42.**

---

## Migration d'`app/app_tree.js` (2026-08-10)

40 appels dans l'inventaire. Le comportement accordéon derrière `[auto_tree]` (structure posée par `app_insertionQ.js`) et `[main_auto_tree]`. Natif désormais.

Rien de nouveau côté pièges — les mêmes que d'habitude (`.up(selector)`, `.on()` à double signature délégué/direct, `.next()`/`.previous()`/`.down()` sans argument). `explorer.spec.ts`'s test « caret collapses and expands the section » exerçait déjà `clicked()` fonctionnellement avant cette migration ; ajout d'une garde `IDAE_SHIM_WARN` dédiée dans ce même fichier plutôt qu'un spec séparé, avec un clic aller-retour pour ne pas laisser le panneau d'historique du bureau dans un état différent pour le test suivant.

Suite complète verte sans échec répété cette fois (2 flaky de timing de boot, différents à chaque run, tous verts au retry) — pas besoin de l'A/B complet qu'a demandé `autoToggle.js`.

**Suite : 43/43.**

## Migration d'`engine/module.js` (2026-08-10)

17 appels dans l'inventaire — le dernier vrai fichier de la liste, le reste (~7 appels) étant dispersé sur `myddeNotifier.js`/`myddeview.js`/`app.js`. `reloadModule`/`reloadScope`/`closeModule`/`newValueModule`, appelés depuis les handlers `receive_cmd` d'`app_socket.js` (déjà natif) pour rafraîchir ou fermer chaque nœud portant un `mdl`/`scope` donné. Natif désormais.

Même contrat `Ajax.Updater` à deux couches (`stripScripts` toujours, `eval` différé si `evalScripts:true`) que dans `app_socket.js`/`engine.js` — les deux chemins de repli de ce fichier passent `evalScripts:true`, même s'ils ne sont jamais atteints en pratique (`typeof socket == 'object'` est toujours vrai une fois `app_socket.js` chargé). Portés fidèlement quand même, pas supprimés — ce n'est pas un appelant confirmé zéro comme `picPicker`, juste une branche qui ne s'exécute jamais dans ce déploiement précis.

Aucune spec n'exerçait ce fichier avant. Nouveau `module.spec.ts` : appel direct de `reloadModule('app/app_gui/app_gui_calendar', '*')` sur un vrai nœud `[mdl]` du bureau, vérifié qu'il retrouve le bon nœud et rappelle `socketModule` sur lui-même (pas un autre) ; et `closeModule` sur un nœud sans `.close`, vérifié qu'il bascule vers la suppression plutôt que de planter.

**Suite : 46/46.**

## Perf — cache-busting cassé, et l'instabilité socket sous WSL2

**Cache-busting.** `main_bag.js` faisait `?v=<Date.now()>` sur les ~90 fichiers JS/CSS à **chaque** chargement — pas un souci de dev, un souci de prod : tout utilisateur réel retéléchargeait tout, à chaque visite, pour toujours, sans jamais toucher le cache IndexedDB de `bag.js`. Fixé (commit `f4f090a`) : `appfunc/asset_versions.php` construit un manifeste `{chemin: mtime}` en scannant `javascript/`+`css/` récursivement (aucune liste dupliquée à synchroniser avec `require_trame`), injecté via `window.FILE_VERSIONS` avant `main_bag.js`. Chaque fichier n'est reversionné que si son mtime a changé.

Vérifié : 90 requêtes à froid → 3 si rien ne change (bag.js + main_bag.js, toujours frais par design, + un `<link>` orphelin non versionné) → **4 si exactement 1 fichier est modifié**, et c'est bien ce fichier-là le seul refetché.

Piège trouvé en même temps : `.htaccess` mettait **`index.php` lui-même** en cache navigateur 60h (`max-age=216000`, hérité de la règle générique `\.(html|php)$`). Comme `index.php` embarque le manifeste `FILE_VERSIONS` généré à la volée, un visiteur revenant dans les 60h gardait un manifeste figé, peu importe les vrais changements disque — ça neutralisait le fix silencieusement. Override `no-cache` ajouté spécifiquement pour `index.php`/`reindex.php` ; la règle générique (qui met potentiellement en cache navigateur d'autres endpoints `.php` dynamiques — `json_data.php`, `json_scheme.php`, etc. s'ils sont appelés en GET) n'a **pas** été touchée — à auditer séparément si ça devient un problème, hors scope ce soir.

**Instabilité socket.io pendant le boot Playwright.** Sous WSL2, les boots automatisés (Chromium piloté par Playwright) montraient parfois `WebSocket is closed before the connection is established` en plein milieu du chargement, suivi d'une reconnexion avec un nouveau socket ID — `schemeLoad()` ne survit pas à ce changement d'ID et reste bloqué jusqu'au timeout. **Isolé et innocenté le réseau** : un stress-test Node pur (socket.io-client, 15 connexions/déconnexions rapprochées, sans navigateur ni Playwright) donne 15/15 propre à la fois via le port forwardé WSL2 (`localhost:3005`, ~309ms/connexion) et via le réseau Docker interne (`idae-socket:3005`, sans passer par WSL2 du tout, ~3ms/connexion). Le port-forwarding WSL2 ajoute de la latence mais n'introduit aucune instabilité.

Hypothèse retenue (non vérifiée formellement, mais cohérente avec toutes les observations) : le boot exécute ~90 scripts de façon synchrone sur le thread JS principal (lecture/écriture IndexedDB + eval par `bag.js`) ; si ce thread reste bloqué assez longtemps, le client socket.io rate sa fenêtre de heartbeat et se croit déconnecté côté client, alors que la connexion réseau réelle n'a jamais bronché — faux positif déclenché par la charge CPU du boot, pas par le réseau. Le fix cache-busting réduit directement ce volume de travail synchrone (moins de fichiers à parser/eval sur un chargement répété), donc devrait atténuer ce risque sans action supplémentaire. Non re-testé formellement après le fix (session déjà très longue) — à confirmer à la prochaine suite complète.

Mitigation en attendant : `retries: 2` dans `playwright.config.ts`, `waitForAppReady`/`openApp` à 120-180s. Confirmé (par le retour utilisateur direct) que l'usage réel — un seul onglet, une seule connexion socket — n'est jamais exposé à ce flake ; c'est spécifique aux boots automatisés rapprochés.

Côté suite Playwright, deux optimisations orthogonales au fix cache (commit `73938f5`) :
- **Session par worker** (`fixtures/test-base.ts`) au lieu d'une session `storageState` unique partagée. Pas d'enforcement mono-session côté serveur, donc chaque worker peut avoir son propre `PHPSESSID` en toute sécurité. *(Correction : la justification initiale — « PHP sérialise les requêtes concurrentes sur un même fichier de session » — est fausse ici, voir la section verrou de session ci-dessous. La fixture reste souhaitable pour l'isolation, mais elle ne débloquait pas ce qu'on croyait.)*
- **Un seul boot par fichier de spec** (`fixtures/shared-boot.ts`) au lieu d'un boot par test — `test.beforeAll` + page réutilisée. Convertit `window-gui`, `datatable`, `explorer`, `insertionq`, `forms`, `uiux`, `snapshots`. Chaque test qui ouvre une fenêtre doit maintenant la fermer explicitement (plus d'isolation implicite page-par-test) — piège trouvé dans `snapshots.spec.ts` : une fenêtre non fermée s'empilait dans la capture d'écran du test suivant.

`workers` reste à **1** — le passage à 4 a été tenté deux fois ce soir et annulé les deux fois, à cause du flake socket ci-dessus (pas d'une vraie limite de ressources ; testé après le fix WSL2/Hyper-V avec 15.5GB de marge). La fixture per-worker est prête pour quand cette instabilité sera résolue ou jugée acceptable en pratique.

## Perf — `localhost` → `::1`, 21s perdues par connexion froide

Point de départ : « quand un travail PHP tourne en arrière-plan, impossible de recharger l'app, parfois il faut redémarrer le serveur » — un bug de longue date. Deux causes distinctes trouvées, une morte et une bien vivante.

**Le bug historique (mort).** `services/json_data_event.php` : endpoint SSE avec `set_time_limit(0)` + `while(true)` + `sleep(1)`, et surtout `session_write_close()` **commenté** (ligne 26). Sous l'ancien handler de sessions en fichiers, ce `session_start()` gardait un `flock` exclusif sur `/tmp/sess_<PHPSESSID>` pour toute la durée — c'est-à-dire indéfiniment. Toute autre requête du même navigateur bloquait dans `session_start()`, et seul un redémarrage d'Apache tuait le worker qui tenait le verrou. Exactement le symptôme décrit. Déjà neutralisé depuis : `die()` en ligne 5, et `app_sse.js` n'est plus dans `require_trame`.

Vérifié qu'il ne peut plus se reproduire : `.htaccess:7` coupe `session.auto_start`, et le handler actif est `user` (le handler Mongo de `ClassSession.php`), qui n'implémente aucun verrou. Test empirique — requête lente de 6s en vol, requête concurrente **même session** : bloquée 0,13s. Zéro sérialisation. Les `/tmp/sess_*` résiduels sont des fichiers à 0 octet.

Reste latent mais inoffensif tant qu'on est sur Mongo : `mdl/app/app_admin/app_csv.php` et `app_csv_contact.php` font `ignore_user_abort(true)` + `set_time_limit(0)` sans `session_write_close()`. Un retour aux sessions fichier ramènerait le gel.

**Le bug actif.** `localhost` résout `::1` en premier sur Windows, et le port-forward WSL2 de Docker ne binde qu'en IPv4. Chaque connexion TCP neuve mange ~21s de retries SYN avant de retomber sur IPv4. Mesuré : `[::1]:8080` → 21,06s (timeout), `127.0.0.1:8080` → 0,05s.

Invisible au quotidien parce que Chrome fait du Happy Eyeballs (bascule en ~250ms) et met le résultat en cache — d'où « dans la vraie vie l'app charge en 5 secondes ». Mais **Playwright y était exposé** : à l'époque de ce fix, `apiLogin` (helper `context.request`, HTTP côté Node, ordre DNS `verbatim`, sans course de fallback) faisait le login pour chaque worker. Chaque connexion fraîche stallait 21s. `apiLogin` a depuis été **supprimé** (`f71a326`, voir plus bas) — le point IPv6 reste vrai et corrigé, mais ce chemin de code n'existe plus.

Fix : `BASE`/`baseURL` passent à `http://127.0.0.1:8080` (`fixtures/auth.ts`, `playwright.config.ts`). Comme la détection d'hôte rejetait l'IP (`Host non configuré dans lan-hosts.json`), `conf.lan.inc.php` aliase maintenant `127.0.0.1`/`::1`/`0.0.0.0` sur l'entrée `localhost` — plutôt que dupliquer un bloc de credentials dans le JSON — et gère au passage le split de port sur les littéraux IPv6 bracketés (`[::1]:8080`, que l'`explode(':')` d'origine cassait). Sans l'alias, `$host_name` dégradait aussi en `"127"`.

Vérifié après fix : `json_ssid.php` répond en 0,39s sur 127.0.0.1, et le coût d'une connexion froide passe de **22,52s à 1,21s**.

À noter, vu au passage : `idae/config/lan-hosts.json` contient des mots de passe SMTP et MySQL en clair, committés dans git.

## Perf — login UI-only, retest workers:2, swap idae-be innocenté

**`apiLogin` supprimé** (`f71a326`). Faisait un POST/GET direct vers `actions.php`/`json_ssid.php` depuis Node (`context.request`), hors navigateur, puis répliquait à la main ce que le vrai flux de login fait côté client (mirror `PHPSESSID`/`SESSID` dans `localStorage` — le canal socket lit les identifiants là, jamais dans le cookie, cf. section « canal d'auth séparé » plus haut). Cette réplique manuelle était une hypothèse sur le comportement du client, pas le comportement réel — dérive silencieuse garantie si le flux de login change. `fixtures/test-base.ts` fait maintenant `page.goto` + `uiLogin` (vrai formulaire, vrai clic) pour chaque worker ; `uiLogin` est désormais le seul chemin de login de toute la suite.

**Timeouts retaillés sur mesure réelle, pas estimation.** Mesuré au navigateur (Chromium, réseau réel, hors Playwright) : boot froid à `#desktop` ~11,3s (106 requêtes), boot chaud (cache IndexedDB `bag.js`) ~8,9s (16 requêtes) — très loin du chiffre fossile « 60-90s sous charge » qui traînait dans les commentaires depuis avant le fix cache-busting (`f4f090a`) et n'avait jamais été remesuré. `openApp`/`waitForAppReady` : 120s → 20s. Sonde bridge (`global-setup.ts`) : 15s → 10s. `playwright.config.ts` `timeout` : 180s → 45s puis 60s (voir plus bas).

**Retest `workers: 2`, deux fois, même échec.** Avec le swap Phase 4 en place et les timeouts retaillés, deux tentatives à 2 workers (une à `timeout: 45000`, une à `timeout: 60000` après bump) — même signature les deux fois : 2-3 specs (`prototype-surface`, `uiux`, puis `forms`, `explorer`, `insertionq` au deuxième essai) épuisent leurs 2 retries sur `"beforeAll" hook timeout ... waiting for locator('#desktop')`. Le hook `beforeAll` de `shared-boot.ts` lui-même ne trouve jamais `#desktop` — 4 boots simultanés (2 workers × 2 boots chacun, cf. `test-base.ts` + `shared-boot.ts`) qui se contentent quelque part. Signature identique au mystère 4-workers documenté plus haut, jamais résolu, qui préexistait au swap.

**Le swap a été isolé et innocenté.** Pendant que le 2e retest tournait, login manuel via un seul onglet navigateur (aucune concurrence, même conteneur en vie) : `#desktop` rendu, 87 schemes chargés, données réelles dans tous les panneaux (clients, prospects, calendrier, badge tâches), **zéro erreur console**. Le swap idae-be est propre en usage normal — l'échec à 2+ workers est de la contention de concurrence, pas une régression de rendu de la Phase 4. La cause de la contention elle-même (NAT WSL2, Mongo hôte sous sessions concurrentes, autre chose) reste non identifiée.

**`workers` reste à 1** (`bf0f576`) — seule config prouvée fiable à ce jour. `timeout` reste à 60000 (marge inoffensive, gardée même à workers:1).

## Résolu — le « mystère des 4 workers » était un nom d'hôte

Tout ce qui précède sur la « contention de concurrence » (NAT WSL2, Mongo hôte, Apache) était faux. La cause tient en une ligne : `.env.testing` (non versionné) pose `TEST_BASE_URL=http://localhost:8080`, qui gagne sur le défaut `127.0.0.1` de `playwright.config.ts`.

Le client socket.io dérive son hôte de `document.domain` (`javascript/app/app_socket.js:26`), donc une page servie sur `localhost` ouvre `ws://localhost:3005` — résolution IPv6-first sous Windows contre un port-forward WSL2 qui ne binde qu'en IPv4. HTTP y survit grâce au Happy Eyeballs de Chrome ; **le WebSocket non** : sous boots concurrents il meurt en pleine poignée de main (`WebSocket is closed before the connection is established`), socket.io se reconnecte avec un nouveau sid, et le serveur répond les acks en vol à la connexion morte.

Mesuré (2026-08-09, boots à froid simultanés jusqu'à une UI utilisable) :

| Hôte | 4 boots | 8 boots |
|---|---|---|
| `localhost` | 1/4 | — |
| `127.0.0.1` | 4/4 (~9 s) | 8/8 (9-12,5 s) |

Ce n'était donc jamais de la lenteur : c'était un boot **pendu**. `get_data()` (`javascript/app/app.js`) n'avait aucune borne — un ack perdu laissait la promesse en attente pour toujours, `schemeLoad()` ne résolvait jamais, `APPSCHEMES` restait vide, ni formulaire de login ni `#desktop`, et pas une seule erreur console. C'est le « bug UI » qui faisait passer chaque échec pour de la contention.

Trois correctifs :

1. **`playwright.config.ts`** — `BASE_URL` est normalisé de `//localhost` vers `//127.0.0.1`, quoi que dise `.env.testing`. Le fichier d'env n'est pas versionné : le corriger localement ne protège personne d'autre.
2. **`javascript/app/app.js`** — `get_data()` a deux voies de récupération : ré-émission des requêtes en vol sur reconnexion socket (le vrai signal), et ré-émission courte à 10 s (3 tentatives) pour ce qui est ré-émissible. Ce qui streame (`stream_to`) ou écrit (`csv_export`) n'est jamais ré-émis — juste un filet à 45 s, au-dessus de la borne 30 s du pont Node, pour qu'un `json_data_table` lent ne soit pas pris pour un ack perdu. Revue Codex (via acp-team) sur cette version : rien de restant.
3. **`conf.lan.inc.php`** — révélé par le passage à `127.0.0.1`. `$host` était réécrit en `localhost` **avant** `DOCUMENTDOMAIN` et toutes les URL absolues, donc une page servie sur `127.0.0.1` rendait `<form action="http://localhost:8080/...">` : origine différente, cookie de session non envoyé, POST non authentifié, formulaire jamais fermé. Séparé en `$request_host` (tout ce qui part vers le navigateur) et `$host` (clé de lookup config uniquement).

Corrigé au passage, le plus vieux flake de la suite : `app_socket.js:278` refaisait `$$('[data-uniqid=…]')[0].fire()` 500 ms plus tard sans revérifier que le nœud existait encore.

**Résultat : 23/23 vert, 1,7 min, zéro flaky.** `workers` passe de 1 à **2**. 4 workers passe aussi (23/23) mais en 2,3 min — au-delà de 2, les boots se contentent entre eux ; ce n'est pas un problème de fiabilité, juste un plafond de débit tant que le boot reste un mur de scripts évalués en synchrone.

Caveat pré-existant relevé par la revue Codex, non corrigé : le Node construit `http://${DOCUMENTDOMAIN}/...` sans port (`app_node/src/web/routes.js:35,57,74`, `src/socket/handlers.js:161,180,199,228`). Sur un Apache hors port 80, ces callbacks tombent à côté. Antérieur à ce travail.

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
