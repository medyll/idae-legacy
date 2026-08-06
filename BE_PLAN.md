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
- [ ] `explorer.spec.ts` — `myddeExplorer.js` (370) + `myddeDatalist.js` (194) : panneau de droite du bureau
- [ ] `insertionq.spec.ts` — `app_insertionQ.js` (300) : pipeline « mutation DOM → ré-extension Prototype ». **Couplage le plus dur à défaire**
- [ ] `forms.spec.ts` — onglet Modifier (`app/app/app_update`), `$F()`, sérialisation, validation
- [ ] Réparer ou remplacer `smoke.spec.ts` et `uiux.spec.ts` (rouges : ils attendent `#main, .app-gui, #grid` qui n'existent pas)
- [ ] Snapshots de référence (`toHaveScreenshot`) sur 3-4 écrans clés, pour attraper les régressions de `setStyle`/`getDimensions`
- [ ] Script `"test:baseline": "npx playwright test --update-snapshots"` dans `playwright/package.json`
- [ ] **Critère de sortie : suite verte, Prototype chargé, snapshots commités**

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

   Commits : `2358881` (fixes PHP), `49d0ef4` (harness + spec).

**Piège de test découvert en écrivant `datatable.spec.ts`** : la recherche (`input[placeholder=Rechercher]`) est branchée sur `keyup` via délégation Prototype (`myddeExplorer.js`), pas sur `input` — `Locator.fill()` ne déclenche rien, il faut `pressSequentially()`. Et `act_search` par défaut (`where_search:'local'`) ne re-requête jamais le serveur : il masque/affiche les `<tr>` déjà chargées en place, le compteur du footer ne bouge pas.

**Environnement — Docker Desktop a plongé sous la charge des runs parallèles** (moteur API en erreur 500, DNS interne `host.docker.internal` en échec, `idae-socket` unhealthy). Auto-guéri après `docker restart idae-socket` + patience. Retenu : lancer la suite Playwright avec `--workers=1` sur cette stack — plusieurs contextes navigateur partageant la même session PHP (même `PHPSESSID` de `storageState`) en parallèle sature le socket. Le suffixe `--workers=1` est déjà le comportement par défaut observé dans cette session ; le fixer explicitement évite une régression si la config change.

---

## Phase 2 — Bundle IIFE d'idae-be

`@medyll/idae-be` est de l'ESM pur non bundlé (ES2020+, 89 KB sur 15 fichiers), sans deps runtime. Un seul esbuild suffit.

- [ ] Ajouter `@medyll/idae-be` en devDependency de `idae/web/app_node/` via `file:D:/development/idae/packages/idae-be` (tant que non publié) plutôt qu'un chemin relatif fragile
- [ ] Script `build:idae-be` dans `idae/web/app_node/package.json` (là où vit déjà `build:css`) :
      `esbuild @medyll/idae-be --bundle --format=iife --global-name=IdaeBe --target=es2017 --sourcemap --outfile=../javascript/vendor/idae-be/idae-be.iife.js`
- [ ] Confirmer le navigateur cible minimum réel avant de figer `--target` (downlevel de `?.` / `??` / champs privés)
- [ ] Commiter la sortie dans `idae/web/javascript/vendor/idae-be/` — le repo n'a aucun build à l'exécution, tout le vendor est commité
- [ ] Vérifier `window.IdaeBe` en console

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

- [ ] `shim-core.js`
- [ ] `shim-class.js`
- [ ] `shim-element.js`
- [ ] `shim-enumerable.js`
- [ ] `shim-event.js`
- [ ] `shim-ajax.js`
- [ ] `shim-effects.js`
- [ ] Flag dev `IDAE_SHIM_WARN` : `console.warn` + stack à chaque appel shimé → donne la liste réelle des call-sites à réécrire en Phase 5 en naviguant l'app, plutôt qu'en grepant
- [ ] Vérifier qu'aucun code ne dépend de la valeur de retour de `Element.extend` (devient un no-op : les méthodes sont sur le prototype)
- [ ] Ménage : supprimer `vendor/prototype/prototype-1.7.js` (copie morte), la référence morte à `prototype.js` dans `idae/web/bin/templates/app/appsite/page/page_body.latte:29`, et `app/app_prototype.js` (1 494 lignes, non chargé)

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
