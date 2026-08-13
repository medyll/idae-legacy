# BE_PLAN — Remplacement de PrototypeJS par @medyll/idae-be

> Branche : `feat/idae-be-migration`
> Créé : 2026-08-05

## Reprise — état au 2026-08-13, dernier commit `86e32bb`

Lire ceci avant de continuer, puis lire les sections "Reprise" du bas du
fichier (ordre chronologique inverse au-dessus de cette section) pour le
détail des décisions et des bugs déjà rencontrés — ne pas les refaire.

**Où c'en est** : 8 → 3 shims (`shim-core`, `shim-class`, `shim-event`,
dans `idae/web/javascript/vendor/idae-be-shim/`). `shim-effects`,
`shim-draggable`, `shim-enumerable`, `shim-element` et `shim-form` sont
supprimés. `vendor/sizzle.js` a également été retiré du chargeur et du disque.

**Suppression de `shim-element` (13/08)** : audit refait sans lookbehind sur
les JS chargés et tous les gabarits PHP/Latte/TPL. Les quatre appels actifs
étaient trois `Element#match` dans `app_explorer_search.php` et un
`Element#show` dans `image_dyn.php`, migrés vers `matches()` et
`style.display = ''`. Huit `Insertion.Top` résiduels dans quatre gabarits ont
été remplacés par `insertion: true` : `app_socket.js` ne testait déjà que la
truthiness de l'option avant son propre `sk_insert(..., {top: ...})`. Le seul
couplage interne, `Event.findElement` (`match` + `up`), est maintenant natif ;
`shim-event` installe lui-même `on/observe/stopObserving/fire`.
Validation bornée sur `127.0.0.1` : `prototype-surface` 2/2,
`template-api-guard` 1/1, `template-parse-guard` 1/1, `shim-warn` 1/1,
`smoke` 1/1 et `explorer` 4/4. Aucun processus Playwright orphelin.

**Suppression de `shim-form` (13/08)** : les 50 appels
exécutables de `Form.serialize`, `Form.serializeElements` et
`Element#serialize` ont été remplacés dans 31 gabarits par
`serializeFields`, helper natif partagé dans `engine/methods.js`. Il garde
le contrat historique (%20, valeurs multiples répétées, champs désactivés,
unchecked, file/image/submit exclus) et accepte formulaire, conteneur,
collection ou champ seul sans étendre `HTMLElement.prototype`. La copie
locale de `myddeExplorer.js`, le chargement et le fichier de shim sont retirés.
Les deux derniers defaults Playwright exécutables en `localhost` ont aussi été
normalisés vers `127.0.0.1`.
Validation bornée, sans retry : `form-serialize` 1/1,
`prototype-surface` 2/2, `template-api-guard` 1/1,
`template-parse-guard` 1/1, `shim-warn` 1/1, `smoke` 1/1 et
`explorer` 4/4. Les 31 gabarits passent `php -l` et les quatre fichiers JS
modifiés passent `node --check`.

**Ce qui reste à faire, dans l'ordre** :
1. Réauditer `shim-class` et `shim-event` après Form : zéro appelant gabarit,
   mais leurs usages JS et leurs dépendances à Core doivent être prouvés avant
   toute suppression.
2. `shim-core.js` (405 lignes, `$`/`$$`/`$A`/`$H`/`$w`/`$F`/`$R`, `Hash`,
   `ObjectRange`) — à faire **en dernier**. Ne pas y toucher avant que les
   autres familles soient vides.

**Méthode qui marche** (voir commits `66d9cd3`, `9128c80`, `d73367a`) :
- Grep les vrais appelants **avant** de migrer — JS chargé (`main_bag.js`'s
  `require_trame`) ET gabarits PHP/Latte (les attributs `onclick`/`onsubmit`
  inline sont invisibles à un grep JS-only et ont causé 3 régressions
  silencieuses en Phase 3/4 — `Form.serialize`, `Effect.*`, `.fade()`).
- **Piège vérifié à la main** : un motif avec lookbehind
  `(?<![\w$])\.\s*nom\s*\(` ne matche que `).foo(`/` .foo(`, jamais
  `element.select(` — c'est-à-dire quasiment tous les vrais sites. A produit
  un faux "zéro appelant" sur 4 méthodes dans le commit `9128c80`. Ne pas
  mettre de lookbehind avant le point.
- Chaque site migré reçoit un commentaire expliquant la sémantique
  Prototype exacte remplacée (ex: `.up(sel)` exclut self, `closest()` non —
  d'où `parentElement.closest(...)` ; `.next()` sans argument =
  `nextElementSibling`, pas un scan de tous les frères).
- Vérifier chaque `.php`/`.html` touché : `docker exec idae-legacy php -l
  <chemin absolu container, préfixé /var/www/html/idae/web/...>` — **jamais
  le chemin hôte**, git-bash le mutile même avec `MSYS_NO_PATHCONV=1`.
- Après édition JS : `node --check <fichier>`.
- Avant de lancer Playwright : `docker restart idae-legacy` si le backend a
  tourné longtemps (dégrade de ~30s à 10-25min de latence par test sans ça).
- Suite complète : `cd playwright && npx playwright test --reporter=line`.
  106 tests actuellement, tous verts. Le sous-ensemble rapide de garde-fous à
  lancer après chaque modif de shim : `prototype-surface`,
  `template-api-guard`, `template-parse-guard`, `shim-warn`, `smoke`.
- `template-api-guard.spec.ts` a un garde-fou interne
  (`found.methods.size > 3`, anciennement `> 10`) qui casse mécaniquement à
  chaque suppression de site vivant — c'est prévu, baisser le seuil et
  documenter pourquoi (déjà fait deux fois, voir commit `d73367a`).
- Ne jamais naviguer sur `localhost` — `http://127.0.0.1:8080` ou un nom
  `*.lan`. Voir CLAUDE.md, cause = résolution IPv6 sous WSL2.

**Doc jumelle** : `MIGRATION_STATUS.md` peut être en retard sur ce fichier ;
`BE_PLAN.md` est la source vivante pour ce chantier précis.

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
| `shim-form.js` (ex-`shim-ajax.js`) | `Form.serialize`, `Form.serializeElements`, `Field` — parties `Ajax.*` et `PeriodicalExecuter` supprimées le 11/08 | natif |
| `shim-effects.js` | `Effect.*` (29 appels), `fade`, `Draggable` | transitions CSS + Web Animations API |

- [x] `shim-core.js`
- [x] `shim-class.js`
- [x] `shim-element.js`
- [x] `shim-enumerable.js`
- [x] `shim-event.js`
- [x] `shim-ajax.js` (découpé en `shim-form.js` le 11/08 — voir plus bas)
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
- [x] `vendor/sizzle.js` supprimé : aucun appel applicatif à `Sizzle`, aucun shim ne le référence, et `$$` utilise déjà `querySelectorAll` via `tolerantQueryAll`. Retiré de `require_scripts` ; `prototype-surface.spec.ts` (2/2) et `smoke.spec.ts` verts sur `127.0.0.1`.

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

## Résidus `myddeNotifier.js` / `myddeview.js` / `app.js` (2026-08-10)

Fermeture de l'inventaire du 2026-08-09 : les ~7 appels restants pointaient sur trois fichiers, aucun aussi trivial que le compte le suggérait — même leçon que `sorttable.js`/`app_socket.js` plus haut, la sonde du matin ne mesurait que ce que ses écrans avaient réellement déclenché.

**`myddeview.js`** — sélection multiple shift-click / meta-click sur les listes, déléguée sur `input[type=checkbox]`. Instancié sur **chaque** zone fichier de liste (`myddeExplorer.js:680`, `act_file_zone`), donc bien plus exercé en pratique que « Object.extend 1, $ 1 » ne le disait — la sonde n'a simplement jamais fait de shift/meta-click. Réécriture complète (`.select`/`.without`/`.first`/`.indexOf`/`.up`/`.writeAttribute`/`.toggleClassName`/`.identify`/`.size`/`.fire`/`Event.stop` → natif), avec l'aide `mv_up` reproduisant `Element#up(selector)` (part du parent, jamais de l'élément lui-même).

**`myddeNotifier.js`** — toasts (`growl()`), instancié directement par les handlers `notify`/`act_notify` d'`app_socket.js`. L'inventaire n'avait vu que l'`Effect.Opacity` déjà migré (`appearElement`) ; tout le reste (constructeur, `buildNotice`, `removeNotice`) était invisible à la sonde faute de toast déclenché. Décision notable : `this.growler.wrap(document.body)` remplacé par `document.body.appendChild(this.growler)` — équivalence exacte, pas une simplification, puisque `wrap()` sur un élément sans `parentNode` (le cas ici, div fraîchement créée) ne fait déjà que ça côté Prototype. Confirmé en lisant `shim-element.js` que `remove(options)` a toujours ignoré son argument, shim ou vrai Prototype — `n.remove({duration:0.3})` devient `mn_remove(n)` sans porter le duration.

**`app.js`** — les deux `Object.extend` restants (`get_data`, `upd_data`) → `Object.assign`. Au passage, `go_json` (fonction non appelée, trouvée en relisant tout le fichier) supprimée : zéro appelant réel (`grep` sur tout le dépôt hors `vendor/`), et elle référençait elle-même du code mort (`windowJSGUI`, `APP.APPOBJ.build_big`, aucun des deux présent ailleurs dans la base). Cohérent avec la politique phase 5 : supprimer le mort plutôt que le migrer.

Nouveau `myddeview-notifier.spec.ts` : meta-click sur une checkbox de liste réelle → classe `selected` posée/retirée + événements `dom:selectionMade`/`dom:unSelectionMade` ; `growl()` instancié directement (comme le fait `app_socket.js`), toast rendu puis auto-disparu après 5s ; garde `IDAE_SHIM_WARN` sur les deux fichiers.

**Deux pièges trouvés dans le test, aucun dans le code migré.**

1. `modifiers: ['Meta']` de Playwright pend sous Windows. Le test meta-click a échoué quatre fois de suite avec un timeout plein de 60s — la première fois faisait suspecter le hook `beforeAll` de `shared-boot.ts` (boot lent sous charge système : des dizaines de processus Node orphelins trouvés vivants, `curl` vers l'app à 2,3s pour une simple redirection), mais un run propre à 1 worker, docker relancé, aucune concurrence, a reproduit le même échec — cette fois avec la trace complète : le test passe l'ouverture de liste, trouve la checkbox, puis `box.click({modifiers:['Meta']})` **ne retourne jamais**, rien n'est enregistré après ce step avant le timeout. `modifiers:['Meta']` fait presser la vraie touche Windows au niveau du pipeline d'input de Chromium — sous Windows ça sort du navigateur et ouvre le menu Démarrer, ce qui vole le focus et laisse le mouse-up du clic (et donc le test) pendu indéfiniment. `selectableClicked` (`myddeview.js`) ne fait que lire `event.metaKey` sur l'événement de clic, donc un `MouseEvent` synthétique dispatché en page (`metaKey: true`, sans passer par le pipeline d'input réel) exerce le même chemin de code sans l'effet de bord OS.

2. Une fois le clic corrigé, la trace a montré la classe `selected` posée correctement mais `dom:selectionMade` jamais reçu. `mv_fire` (dans `myddeview.js`) dispatche bien l'événement avec `bubbles:true` sur l'élément racine passé à `new myddeview(...)` — c'est le test qui écoutait au mauvais endroit (un sélecteur CSS deviné plutôt que garanti). Corrigé en écoutant sur `document` : les événements bubblés y arrivent quel que soit le nœud exact d'origine.

Les deux corrigés dans le test (dispatch direct + écoute sur `document`), rien changé côté `myddeview.js`. **Suite (fichier seul) : 4/4 vert** (le premier essai du meta-click a échoué une fois à 0ms avant ces deux fixes — cascade normale d'un boot à froid concurrent avec un autre run laissé tourner par erreur, pas reproduit après isolation propre).

## Migration d'`app/app_chat.js` (2026-08-10)

Reprise après correction : la section « shim-effects » plus haut avait déjà fermé les `Effect.*`/`Draggable` de tout le dépôt le 09/08 — ce qui restait shimmé dans `appGui.js`/`myddeAttach.js`/`myddeupload.js`/`tableGui.js`/`validation.js` est le reste de leur surface (`Class.create`, `Object.extend`, `$`/`$$`, `.each`, `.observe`, `.up()`…), jamais l'animation. Mesure élargie par comptage statique (hors `vendor/`) pour retrouver le vrai plus gros fichier non touché : `app_chat.js`, 74 occurrences, 442 lignes de fonctions globales (pas de `Class.create`).

Chargé sans condition par `main_bag.js` (`require_app`) sur **chaque** boot — donc son code de premier niveau (`socket_app_chat = io(...)`, les cinq `$('body').on('click', …)` délégués) tourne déjà à chaque test de la suite, que la fonctionnalité chat soit visible ou non. Vérifié à la relecture : `app/app_chat/app_chat_panel.php` — le seul appelant réel d'`appchat_init`/`appchat_panel_toggle`, et la seule source du markup `.app_chat_button`/`.appchat_connected`/`.appchat_disconnected` — n'est référencé nulle part ailleurs dans le dépôt (`grep` sur `app_chat_panel`/`app_chat_button`, hors `vendor/`). Pas supprimé pour autant : le fichier reste chargé et sa connexion socket + ses délégations sont réelles, seule la branche panneau est aujourd'hui inatteignable — même catégorie que la branche `evalScripts:true` jamais empruntée d'`engine/module.js`, portée fidèlement plutôt que retirée.

Helpers `ac_*` : `ac_el`, `ac_qsa`, `ac_up` (part du parent, jamais de soi — même contrat que `mv_up`), `ac_delegate`, `ac_toQueryString` (remplace `$H(obj).toQueryString()` pour des objets plats, aucun besoin de sérialisation imbriquée ici), `ac_show`/`ac_hide`/`ac_remove`.

**Correction (2026-08-10, plus tard) :** `chat_user_remove` référence `chat_tracker_timer` (sans le préfixe `appchat_`) — pas un bug. C'est un global déclaré par `app_keepon.js`, chargé avant ce fichier dans `main_bag.js` ; ce dernier définit ses propres `chat_user_add`/`update`/`remove` sur le même tableau, et la version de ce fichier écrase la sienne au chargement (les deux fichiers assignent le global nu, l'ordre de chargement décide qui gagne) — partager le tableau de trackers est ce qui rend cet écrasement sûr. Trouvé en migrant `app_keepon.js` juste après ; commentaire corrigé dans le code.

Nouveau `app-chat.spec.ts` : construit le markup réel d'`app_chat_panel.php` en fixture détachée (même approche que le test de repli `closeModule` de `module.spec.ts`) pour exercer `appchat_panel_toggle`/`appchat_agent_state_retrieve` — chemin sinon inatteignable via un vrai écran. Garde `IDAE_SHIM_WARN`. **3/3 vert.**

## Migration d'`app/app_planning.js` (2026-08-10)

Deuxième plus gros fichier mesuré après `app_chat.js` (58 occurrences). Fonctions globales de drag & drop pour le planning/calendrier — `[data-dragtache]` déposé sur `[data-droptache]` — délégué sur `document.body`, réel : le markup `[data-droptache]` vit dans quatre templates (`app_planning_quoti/hebdo/mens.php`, `calendrier_day.php`).

Helpers `ap_*` habituels (`ap_el`, `ap_qsa`, `ap_identify`, `ap_writeAttributes`, `ap_setStyle`, `ap_fire`, `ap_delegate`). `.select()` scopé à un élément → `querySelectorAll` scopé ; `.sortBy()` → tri natif direct puisque `ap_qsa` renvoie déjà un tableau simple (pas besoin du détour Schwartzian-transform de Prototype) ; `.up()` sans expression → `parentNode` direct (confirmé dans `shim-element.js` : sans filtre, `up()` renvoie le premier ancestor non filtré, donc le parent).

**Vrai bug de shim trouvé, pas dans ce fichier mais dans `shim-element.js` lui-même — corrigé à la source.** `node.hasAttribute('heuredebut')` (ligne native, pas un appel Prototype) plantait avec `RangeError: Maximum call stack size exceeded`. La pile montrait `HTMLDivElement.hasAttribute` s'appelant lui-même à l'infini, depuis `shim-element.js`. Cause : `Methods.hasAttribute = function(name){ return this.hasAttribute(name); }`, installé sans condition sur `NativeElement.prototype` via `Object.extend(NativeElement.prototype, Methods)` — ça écrase le `hasAttribute` natif du navigateur par une fonction qui s'appelle elle-même. Bug latent depuis le début du shim, jamais déclenché avant parce qu'aucun fichier migré n'appelait `.hasAttribute()` directement (les fichiers Prototype d'origine passaient tous par `readAttribute`/vérification de valeur, jamais par ce raccourci). Cassait `.hasAttribute()` pour **toute l'app**, code déjà migré compris — un fichier peut être 100% natif et se faire quand même planter par un shim qu'il ne savait même pas appeler. Entrée supprimée de `Methods` : `Element.prototype.hasAttribute` natif suffit déjà, aucune raison de le shimmer.

Nouveau `app-planning.spec.ts` : construit un `[data-dragtache]`/`[data-droptache]` en fixture, `dragstart`+`drop` réels (`DataTransfer` natif — l'hypothèse initiale d'un mode « protégé » empêchant `getData`/`setData` en dispatch scripté s'est révélée fausse une fois le vrai bug trouvé ; `DataTransfer` natif marche tel quel), vérifie déplacement + écriture d'attributs. Deuxième test construit trois `.dyntache` (deux au même `data-heureDebut`) et vérifie le partage de largeur/marge et l'empilement `zIndex` par `offsetTop`. Garde `IDAE_SHIM_WARN`. **3/3 vert**, plus `smoke`/`prototype-surface`/`insertionq` revérifiés propres après le patch du shim (6/6).

Incident d'environnement au passage : les conteneurs `idae-legacy`/`idae-socket` ont disparu de `docker ps -a` en cours de session (ni arrêtés ni visibles, juste absents) — recréés via `docker compose up -d`, aucune perte de données (Mongo tourne dans un conteneur séparé, `idae-mongo-test`, resté up 2 jours sans interruption).

## Migration de `librairie/myddeAttach.js` (2026-08-10)

Troisième plus gros fichier mesuré (49 occurrences), classe `Class.create` — widget drag & drop / upload par input fichier. Réel et largement instancié : `app_wallpaper.php`, `app_img_upload.php`, `mail_send.php` (×2), `document_liste_drop.php`, `app_fiche_document.php`, `app_fiche_maxi_entete.php` (`new myddeAttach(...)`, confirmé par grep sur `mdl/`).

Helpers `ma_*` habituels. Point notable : `.on(event, handler)` à deux arguments (sans sélecteur) — vu dans ce fichier pour `dragend` sur `document.body` — n'est **pas** de la délégation : `shim-event.js:175` (`delegateOn`) route ce cas vers `Event.observe(this, eventName, selector)` quand `handler` est `undefined`, donc un simple listener direct sur l'élément. Traduit en `addEventListener` nu, sans wrapper de délégation.

Formulaire sérialisé via `engine_formSerialize` (`engine/engine.js`, déjà natif, chargé avant ce fichier) plutôt que de réimplémenter `Form.serialize`. `String#evalScripts.bind(content).defer()` (réponse XHR à évaluer, pas du HTML à insérer) → `setTimeout(() => engine_evalScripts(content), 10)`, même contrat de défilement à 10 ms que `Function#defer`.

Bloc mort laissé tel quel : `UploadFile` a un `return;` inconditionnel avant tout le second XHR (`this.xhrArr[index] = ...`) — déjà signalé mort dans `496f371` (commit des `Effect.*`), pas retouché, toujours du Prototype verbatim en dessous.

Nouveau `myddeattach.spec.ts` : construit un élément + formulaire en fixture, vérifie que `dragenter` construit `.zone` (enfant réel, visible), que `dragend` sans drop la ré-cache, que `drop` lit bien `action` du formulaire et bascule `dropped`. Garde `IDAE_SHIM_WARN`. **3/3 vert au premier essai** — pas de piège cette fois, `smoke.spec.ts` revérifié propre.

## Suppression de `librairie/myddeupload.js` (2026-08-10)

Prochain candidat par taille après `myddeAttach.js` (46 occurrences). Même famille que `picPicker.js`/`myui/`/`growler.js`/`myddeSlide.js`/`app_bootstrap_init_old.js`/`go_json` : **zéro appelant réel**, confirmé par grep sur tout le dépôt hors `vendor/`. La classe `myddeUpload` n'est instanciée nulle part. Les deux seuls autres hits pour « myddeUpload/myddeupload » : les deux entrées de chargement (`main.js`, `main_bag.js`) et un `id="myddeUpload<?=$time?>"` dans `app_img_upload.php` — une coïncidence de nommage, cet écran instancie en réalité `myddeAttach`, pas cette classe.

Migration commencée par erreur (fichier entièrement réécrit en natif) avant de vérifier les appelants — reprise dans le bon ordre pour les fichiers suivants : vérifier zéro-appelant *avant* de migrer, pas après. Fichier supprimé, retiré des deux listes de chargement (`main.js:124`, `main_bag.js:79`). `smoke.spec.ts` revérifié vert après suppression.

## Migration d'`app/app_keepon.js` (2026-08-10)

Suivant par taille après `myddeAttach.js` (45 occurrences), `myddeupload.js` s'étant révélé mort (voir ci-dessus — appelants vérifiés *avant* migration cette fois). Canal socket de présence/« glue », chargé sans condition, réel : `#socket_keep_on_status` est rendu par `app_gui_main.php` (le bureau lui-même), donc présent à chaque boot.

Découverte en le lisant : `chat_user_add`/`chat_user_update`/`chat_user_remove` sont définis **ici aussi**, en plus d'`app_chat.js` — les deux fichiers assignent le même global nu, `app_chat.js` chargeant après écrase la version de ce fichier. Ce qui a permis de corriger une erreur de doc de la session précédente : le commentaire sur `chat_user_remove` dans `app_chat.js` affirmait que `chat_tracker_timer` n'était « déclaré nulle part » (donc `ReferenceError` garanti) — faux. C'est un `var` global déclaré ici (`app_keepon.js`, chargé avant), partagé entre les deux fichiers exprès. Commentaire corrigé dans le code et dans l'entrée BE_PLAN correspondante plus haut.

**Vrai bug préexistant trouvé, celui-ci confirmé** : `keepon_connect_agent`/`keepon_disconnect_agent`, appelés par les délégués `.keepon_connected`/`.keepon_disconnected`, ne sont définis **nulle part** dans tout le dépôt (grep). Cliquer ces boutons plante avec `ReferenceError`, aujourd'hui comme avant la migration — laissé tel quel, commenté. Leur markup (`app_keepon_panel.php`) n'est référencé par aucun template, comme `app_chat_panel.php` — même catégorie d'UI inatteignable.

Helpers `kp_*` habituels. Nouveau `app-keepon.spec.ts` : exerce `kp_show`/`kp_hide`/`kp_update` sur le vrai `#socket_keep_on_status` du bureau, et le délégué `glue_reserve` (fixture construite, `socket_keep_on.emit` intercepté pour vérifier `RESERVEDID`/`IDAGENT`). Garde `IDAE_SHIM_WARN`. **3/3 vert**, plus `smoke`/`app-chat` revérifiés propres (4/4).

## Migration de `librairie/observers.js` (2026-08-10)

Suivant par taille (40 occurrences), un seul délégué click global (`selfObservers`, instancié une fois au boot par `engine/initApp.js:3`) : bascule checkbox (`doCheck`/`doUnCheck`), révélation `.autoNext`, fermeture `.hide_on_click` sur clic extérieur. Réel et vérifié load-bearing — enregistré sur `document` directement, donc actif dès le boot.

`document.on('click', handler)` (2 arguments, sans sélecteur) → même règle que `myddeAttach.js`/`myddeupload.js` : pas de la délégation, `shim-event.js` patche aussi `Document.prototype`/`HTMLDocument.prototype` en plus d'`Element.prototype` pour ça. Traduit en `document.addEventListener` nu.

Deux pièges trouvés dans le test, aucun dans le code migré :
1. La checkbox de liste ciblée porte `class="avoid"` et n'est révélée qu'au survol de ligne (CSS) — invisible par défaut. `box.click()` de Playwright attend une vraie visibilité et boucle 60s avant d'échouer. `force: true` contourne l'attente d'actionabilité (le hover CSS n'est pas ce qui est testé ici).
2. Cette liste groupe ses lignes : la première `<tr>` est une ligne d'en-tête de groupe (`class="entete_groupe"`), sans checkbox. `tbody tr:first` récupérait la mauvaise ligne pour vérifier `.selected`. Corrigé en remontant depuis la checkbox elle-même (`box.locator('xpath=ancestor::tr[1]')`) plutôt que de deviner l'index de ligne.

Nouveau `observers.spec.ts` : clic réel sur une checkbox de liste (bugchk + `tr.selected`), fixture construite pour `.autoNext`/`.hide_on_click`. Garde `IDAE_SHIM_WARN`. **3/3 vert**, plus `smoke`/`myddeview-notifier` revérifiés propres (5/5).

## Migration d'`app/app_calendrier.js` (2026-08-10)

Suivant par taille (`resizeGui.js`/37 exclu — `Draggable`, décidé hors scope le 09/08). 35 occurrences, `Class.create` — nav du widget calendrier (mois précédent/suivant, sélecteurs mois/année), plus un mode optionnel « calendrier-popup pour un input » (`data-calendar_target`). Auto-instancié par `app_insertionQ.js:561` sur tout nœud `[data-app_calendrier]`. Réel : écran `app/app_calendrier/app_calendrier_echeance`, ouvert par la tuile bureau `app_gui_calendar` (`act_chrome_gui`).

`$(this.element).on('click', selector, handler)` — vraie délégation cette fois (3 arguments avec sélecteur, scopée à `this.element`), contrairement aux formes à 2 arguments trouvées dans `myddeAttach.js`/`myddeupload.js`/`observers.js`.

Branche laissée inatteignable, comme d'habitude documentée plutôt que retirée : `$$(this.element.readAttribute('data-calendar_target')).invoke('setValue', ...)`. `data-calendar_target` contient un id brut d'élément (posé côté serveur depuis `$_POST['calendar_target']`, `app_calendrier.php:16`), jamais un sélecteur CSS — le traiter comme tel via `$$`/`querySelectorAll` ne matche jamais rien, et aucune méthode `setValue` n'existe nulle part ailleurs dans l'app de toute façon (seule occurrence : la copie vendorée de vrai Prototype dans `flotr/`, sans rapport).

Nouveau `app-calendrier.spec.ts` : ouvre le vrai écran via `openChrome`, vérifie que les deux zones `.cf_module` (`data-nav_zone`/`data-nav_cal`) reçoivent le même `scope`/`value` généré par `identify()`, puis clique `.previous_month` en conditions réelles (aller-retour serveur) et vérifie que le titre du mois change effectivement. Garde `IDAE_SHIM_WARN`. **2/2 vert au premier essai**, `smoke` revérifié propre.

## Suppression de `librairie/validation.js` (2026-08-10)

Suivant par taille (34 occurrences) — mort. Bibliothèque tierce vendorée (« Really Easy Field Validation » d'Andrew Tetlaw, 2007, en-tête de licence inclus), 288 lignes, `Validator`/`Validation` (`Class.create`). Jamais activée : `new Validation(...)` n'apparaît nulle part hors du fichier lui-même (grep sur tout le dépôt). Les classes CSS `validate-email`/`validate-number`/etc. existent bien dans 14 templates, mais ce sont des crochets décoratifs morts — rien ne les scanne puisque personne n'instancie jamais `Validation` sur un formulaire. Seule trace d'activité passée : le commentaire d'`engine/methods.js` documentant le fix `appearElement` du 09/08 (une seule ligne migrée à l'époque, sur un fichier par ailleurs jamais câblé).

Supprimé, retiré des deux listes de chargement (`main.js:113`, `main_bag.js:69`). `smoke.spec.ts` revérifié vert.

## Migration de `librairie/myddeSelection.js` (2026-08-10)

31 occurrences, `Class.create` — sélection au lasso (rubber-band) sur la zone fichier d'une liste. Vrai appelant : `myddeExplorer.js:711` (`act_drag_selection_zone`).

**Portée réelle plus étroite que prévu.** L'instanciation est conditionnée par `build_expl_drag_selection_zone` (ligne 208), qui exige un attribut `expl_drag_selection_zone` dans le markup — présent sur exactement **deux** écrans dans tout le dépôt : `app_document/document_liste.php:53` et `app_prod/app_prod.php:43`. La liste générique utilisée par le reste de la suite n'en construit jamais. Trouvé en debug : la première version du test dispatché sur `[expl_file_zone]` d'une liste client ne déclenchait rien, aucun garde ni exception — simplement aucun listener sur ce nœud.

**Deux bugs préexistants portés verbatim, commentés dans le code :**
1. `this.startX = Event.pointerX` (et `startY`) assignent la *fonction*, jamais son résultat — l'original n'a jamais passé `event`. `startY` n'est lu nulle part, et l'unique comparaison sur `startX` dans `onMouseMove` garde un bloc vide (`//code`) : rien d'observable n'en dépend.
2. `document.viewport.getScrollOffsets()` renvoyait un tableau-like `[x, y]` (accès indexé OK), mais `getDimensions()` renvoie un `{width, height}` nu — donc `vp_size[1]` est, et a toujours été, `undefined`. La branche de scroll-vers-le-bas compare contre `NaN` et n'a jamais pu s'exécuter ; seul le scroll-vers-le-haut fonctionne. Porté tel quel.

Nouveau `myddeselection.spec.ts` : classe instanciée directement sur une fixture construite (même approche qu'`app-chat`/`app-keepon`/`myddeattach`, plutôt que de traîner la suite vers des données de test document/prod), avec deux items positionnés en absolu — un dans le rectangle de drag, un hors champ. Vérifie création/position/opacité/`startPos` de `#drag_selection`, son dimensionnement au `mousemove`, sa suppression au `mouseup`, et que `checkSelect` ne marque `.selected` que l'item qui chevauche. Garde `IDAE_SHIM_WARN`. **3/3 vert**, plus `smoke`/`explorer-shell` revérifiés propres (4/4).

## Migration d'`app/app_conge.js` (2026-08-10)

31 occurrences, fonctions globales — drag & drop du tableau de congés (`[data-dragconge]` déposé sur `[data-dropzone=conge]`). Forme quasi identique à `app_planning.js` (mêmes cinq délégués `dragstart`/`dragend`/`dragover`/`dragleave`/`drop` + un `dblclick`), à deux différences près : le drop **repositionne** le nœud via `clonePosition` au lieu de le re-parenter, et **ne persiste rien** — l'appel `ajaxValidation` est commenté dans la source, le déplacement est purement client.

`clonePosition` : réutilisation du portage déjà écrit pour `app_insertionQ.js` (variante Prototype `top/left/width/height`, pas celle d'idae-be qui décale par `transform` et prend d'autres options), avec son `cumulativeOffset`. Troisième fichier à en avoir besoin après `app_insertionQ.js` et `myddeDatalist.js` — toujours recopié localement plutôt que hissé dans un module partagé, pour ne pas toucher au graphe de chargement de `main_bag.js`.

Nouveau `app-conge.spec.ts` : fixture construite (le vrai tableau de congés demanderait des données de test que la suite ne porte pas), vérifie l'opacité pendant le drag, le fond `#FC3` posé au `dragover` puis retiré au `dragleave`, l'écriture de `datedebut` au drop, le repositionnement `left`/`top` sur le slot avec largeur inchangée (`setWidth:false`), et le retour à `opacity:1` au `dragend`. Garde `IDAE_SHIM_WARN`. Une assertion fausse de ma part au premier essai (`#FC3` écrit `252, 51, 3` au lieu de `rgb(255, 204, 51)` — `#FC3` s'étend en `#FFCC33`), corrigée. **2/2 vert**, plus `smoke`/`app-planning` revérifiés propres (4/4).

## Suppression de `librairie/lightview.js` (2026-08-10)

30 occurrences — mort, et heureusement. Bibliothèque tierce vendorée (Lightview 2.0.0_rc5, Nick Stakenburg, 2008), 25 Ko, visionneuse d'images modale bâtie sur Prototype/Scriptaculous. **Zéro référence** dans tout le dépôt : absente des deux listes de chargement, d'aucun PHP/Latte, aucun CSS, et le dossier `images/lightview/` qu'elle attend n'existe même pas.

Point de licence noté au passage : elle est distribuée sous **Creative Commons BY-ND** (« No Derivative Works »). L'avoir migrée en natif — c'est-à-dire réécrire ses internes — aurait constitué une œuvre dérivée, donc une violation de licence. La supprimer n'en est pas une. Si un fichier tiers sous BY-ND réapparaît plus tard dans la file de migration et qu'il est *vivant*, il faudra soit le remplacer par autre chose, soit le laisser sur le shim — pas le réécrire.

Supprimée. `smoke.spec.ts` revérifié vert (formalité : le fichier n'était chargé nulle part, sa suppression ne pouvait rien casser).

## Migration d'`app/app_quickfind.js` (2026-08-10)

28 occurrences, `Class.create` — filtre client sur une liste : la saisie masque tout nœud `tag` dont le texte ne contient pas la requête. Instancié par `app_insertionQ.js:244` sur tout `[data-quickFind]`. Réel : 6 écrans (`app_dispatch_inner`, `app_fiche_maxi_liste`, `app_scheme_field_type`, `app_scheme_has_field`, `app_user_pref_scheme`, `appsite_scheme_values`).

À ne pas confondre avec la fonction globale `quickFind(value, where, tag, spy)` d'`engine/engine.js`, appelée en `onkeyup` inline par des templates plus anciens (`document_client_liste.php`, `mdlDocument.php`, `app_droit_liste.php`, `search_item_check.php`) — même idée, code sans rapport, pas touché ici.

`Element.hide.defer(node)` / `Element.show.defer(node)` : `Function#defer` de Prototype = `setTimeout(..., 10)` avec les arguments transmis, et `Element.hide` est la forme statique générique prenant le nœud en 1er argument. Porté en `setTimeout(function(){ node.style.display = ... }, 10)` — le report de 10 ms est conservé, il est observable (le test l'attend explicitement).

**Bug préexistant porté verbatim, commenté dans le code** : le chemin `spy` est cassé. `data-quickFind-spy` fournit un id d'élément (`"uyt"` sur `app_scheme_field_type.php` et `app_scheme_has_field.php`) — aucun élément ne porte cet id dans tout le dépôt. `get_count()` insère donc son markup `spy_element` **après** l'input, puis va le chercher **dans** l'input via `querySelector` — or un `<input>` est un élément vide, `querySelector` y renvoie toujours `null`. `this.options.spy.update(...)` lève un `TypeError`. Taper dans ces deux champs de recherche plante déjà aujourd'hui, exactement pareil sous le shim.

Nouveau `app-quickfind.spec.ts` : fixture construite (chaque écran réel exige ses propres données de scheme/dispatch), vérifie l'insertion de l'icône de recherche après l'input, le filtrage effectif — dont un match à travers un `<b>` imbriqué, ce qui prouve la comparaison après `stripTags` — et la restauration de toutes les lignes à la vidange du champ. Garde `IDAE_SHIM_WARN`. **2/2 vert au premier essai**.

## Migration de `librairie/tableGui.js` (2026-08-10)

26 occurrences, `Class.create` — dimensionne les cellules d'une grille en parts égales du parent, puis les fait apparaître. Un seul appelant vivant : `app_planning_mens.php:164` (`new tableGui($('tablePlanningMensuel'), {numRow: N, onlyClass: 'caseMois'})`) ; l'autre site (`mdlCalendrierListYear.php:45`) est commenté. Les `Effect.Appear` de ce fichier avaient déjà été migrés le 09/08 (`496f371`) — ne restait que le reste de la surface shim.

**Divergence préexistante trouvée, préservée et documentée** : `document.getElementsByClassName(cls, container)`. Prototype 1.6 honorait ce second argument et scopait la recherche au conteneur ; le shim ne patche que `Element.prototype`, jamais `Document`, donc cet appel tombe sur la méthode **native**, qui ignore le second argument et balaie tout le document. La divergence est arrivée avec le swap Phase 3/4, pas avec cette migration. Conservée telle quelle : l'unique appelant vivant n'a qu'une grille de ce type à l'écran, donc scopé et document-wide renvoient les mêmes nœuds. À garder en tête si un second planning mensuel apparaît un jour dans la même page.

Autre branche laissée verbatim : sans `onlyClass`, `this.allChild` vient de `childNodes` — qui inclut les nœuds texte, dépourvus de `.style`, donc la boucle `build()` planterait sur le moindre espace entre balises. Aucun appelant vivant n'emprunte ce chemin (le seul est commenté). Et l'écouteur `'Resize'` (R majuscule) est un nom d'événement custom que rien n'émet dans l'app : listener jamais déclenché, porté tel quel.

Nouveau `tablegui.spec.ts` : fixture construite (l'écran planning mensuel réel exige ses propres données), vérifie le partage de hauteur (parent 300 px / `numRow: 3` → 100 px par cellule) et que la grille, partie d'`opacity: 0`, est bien révélée par `appearElement`. Garde `IDAE_SHIM_WARN`. **2/2 vert au premier essai** — et premier fichier validé **sans redémarrage Docker**, cf. la note de méthode ci-dessous.

## Migration d'`app/app_menu.js` + suppression d'`autoLoad.js` et `sortdiv.js` (2026-08-11)

Trois fichiers d'un coup, la file par taille ayant donné deux morts consécutifs.

**`librairie/autoLoad.js` (25 occurrences) — supprimé.** Absent des *deux* listes de chargement, jamais instancié (`new autoLoad(` n'existe nulle part), aucune référence depuis un autre JS. Le seul hit hors du fichier est un nom de classe CSS (`class="autoLoad-recordcount"` sur un `<tr>` de `mail_liste_tbody.php`) — markup inerte sans la classe JS pour le lire.

**`librairie/sortdiv.js` (24 occurrences) — supprimé.** Cas légèrement différent : bien chargé par les deux loaders, donc parti au navigateur à chaque boot, mais `new sortDiv(` n'apparaît nulle part. Retiré des deux listes (`main.js:118`, `main_bag.js:74`).

**`app/app_menu.js` (23 occurrences) — migré.** Celui-là est tout sauf mort : chargé par `main_bag.js:57`, **il s'auto-instancie** en dernière ligne de fichier (`new app_menu()`) et pose un délégué global sur `[data-menu]`. Load-bearing sur chaque liste — l'input de recherche de `myddeExplorer` reçoit précisément `data-menu` via `act_expl_search_input`, et son menu de portée à deux options est le nœud frère que ce fichier révèle.

Quatrième réutilisation du portage `clonePosition` (après `app_insertionQ.js`, `myddeDatalist.js`, `app_conge.js`), plus un portage de `getDimensions`/`getHeight`/`getWidth` : la version Prototype mesure `clientWidth`/`clientHeight` en forçant temporairement l'élément visible s'il est en `display:none` — ce qui compte ici, les menus sont mesurés *avant* d'être affichés.

**Fuite préexistante portée verbatim, commentée** : `onDataMenu` ajoute `onClickBtn` avec un `.bind()` neuf à chaque clic sur `[data-menu]`, et le `removeEventListener` correspondant dans `onClickBtn` re-`bind()` encore — un autre objet fonction, donc il ne retire jamais rien. Ces écouteurs s'accumulent depuis toujours pour la durée de vie de la page.

Nouveau `app-menu.spec.ts` : vérifie l'auto-instanciation (`#div_app_menu` enfant de `body`, classe posée), puis qu'un clic sur un `[data-menu]` révèle et positionne son frère (`display`, `position:absolute`, `left`/`top` posés par `clonePosition`, classe `hide_on_click` ajoutée pour qu'`observers.js` puisse le fermer). Garde `IDAE_SHIM_WARN`. **3/3 vert au premier essai**, plus `smoke`/`explorer-shell` revérifiés propres — soit **7/7** au total, ce qui couvre aussi les deux suppressions.

## Migration de `librairie/textarea.js` (2026-08-11)

22 occurrences. Trois choses dans ce fichier, trois traitements différents.

**`ResizingTextArea` — supprimé.** Classe définie ici, instanciée nulle part dans le dépôt.

**`resizeInput` — migré.** Input auto-dimensionné, bien vivant : `app_insertionQ.js:419` et `myddeDatalist.js:179`.

**`nl2br` — gardé exprès, et exprès *non* dédupliqué.** Piège trouvé en vérifiant avant de supprimer : `app_php.js:11` définit un global du même nom avec une implémentation **différente** (il remplace le saut de ligne par `<br />` ; celui d'ici conserve le saut de ligne et insère `<br>` devant, en sautant les occurrences précédées de `>`). `main_bag.js` charge `app_php.js` en ligne 27 et ce fichier en ligne 75 — donc **c'est cette version-ci qui gagne**, et c'est elle que l'app exécute réellement, y compris pour `app_socket.js:672`. Supprimer le fichier en bloc aurait silencieusement changé le rendu de chaque mise à jour live-data. Bon rappel : « fichier dont il ne reste qu'une classe morte » ne veut pas dire « fichier supprimable » tant qu'on n'a pas regardé ses globals.

Quirk préexistant porté verbatim : le `<span>` de mesure est mesuré **avant** d'être ajouté au document, donc la première mesure vaut toujours 0 (un élément détaché n'a pas de `clientWidth`). La largeur initiale est donc `0px`, rattrapée par le `minWidth: 80px` de la ligne suivante ; les mesures suivantes, au `keydown`, sont correctes.

Nouveau `textarea.spec.ts` : épingle les deux comportements qui échoueraient en silence — quel `nl2br` gagne réellement, et le fait que le span de mesure soit bien attaché puis re-mesuré à la frappe. Garde `IDAE_SHIM_WARN`. **3/3 vert au premier essai**, plus `datalist` (consommateur réel de `resizeInput`) et `smoke` revérifiés propres — **7/7**.

## Migration d'`app/app_functions.js` (2026-08-11)

22 occurrences, fourre-tout de globals. Recensement des appelants fonction par fonction avant de toucher quoi que ce soit — 16 fonctions, split net : 9 vivantes, 7 à zéro appelant.

**Supprimées (0 appelant, grep sur tout le dépôt hors `vendor/`/`flotr/`) :**
- `registerMdl` — corps déjà inatteignable de toute façon (`return ''` en première ligne), et personne ne l'appelait.
- `chekIdle` / `isIdle` / `isIdleMove` / `isIdleMoveOut` — tout le cluster « idle ». Son seul point d'entrée était `$('body').observe('mousemove', chekIdle)`, commenté, comme les hooks focus/blur juste en dessous. **Sa suppression élimine les trois `Ajax.Request` du fichier** (les deux autres étaient dans `registerMdl`).
- `gereDate`, `edit_in_place` — aucun appelant.

**Migrées (appelants réels) :** `openDoc` (6), `mce_area` (3), `changeCnameTrick` (4), `popopen` (7), `chkDispZone` (3), `clean_string` (5), `save_setting_autoNext` (11), `save_settings` (**39**), `del_settings` (3).

**`changeCnameTrick` : cassée, laissée cassée exprès.** Elle retourne `'<?= rtrim(HTTPCUSTOMERSITE, '/') ?>/'` — mais c'est un `.js` servi tel quel, aucun handler PHP n'est configuré pour cette extension (vérifié dans `.htaccess`), donc la balise part littéralement dans la chaîne. Ses quatre appelants (`engine.js:251,327`, `module.js:143,183`) construisent tous une URL du type `changeCnameTrick() + 'mdl/' + file` — et sont tous à l'intérieur des replis `typeof socket == 'object'`, jamais exécutés puisqu'`app_socket.js` définit toujours `socket`. C'est précisément pour ça qu'une valeur de retour aussi cassée n'a jamais fait surface. La corriger reviendrait à modifier un chemin inatteignable et non testé, et le vrai correctif (faire entrer une constante serveur dans un `.js` statique) est une décision séparée.

Quirk porté verbatim dans `openDoc` : le `{document.body.appendChild(down_doc)}` n'est pas un `else` mais un bloc nu, donc l'`appendChild` s'exécute à chaque appel et re-parente la même iframe.

Nouveau `app-functions.spec.ts` : vérifie que les 9 globals vivants existent **et que les 7 supprimés ont bien disparu**, puis épingle les deux qui calculent réellement contre le DOM (`chkDispZone` ramène un nœud hors-écran dans le viewport ; `save_setting_autoNext` poste bien le `display` du **frère suivant**, après son debounce de 500 ms) plus `clean_string`. Garde `IDAE_SHIM_WARN`. **5/5 vert au premier essai**, plus `smoke`/`forms` revérifiés propres — **9/9**.

## Migration de `librairie/appGui.js` (2026-08-11)

16 occurrences, `Class.create` — zones d'application à onglets du bureau + barre des tâches. Un seul appelant, mais central : `app_gui_main.php:151` fait `window.JSGUI = new appGui($('mainApp'))`, donc une instance existe à **chaque boot** (si le constructeur levait, `smoke.spec.ts` tomberait immédiatement). `moveElementTo`, en tête de fichier, avait déjà été migré le 09/08 (`496f371`, ex-`Effect.Move`) et n'est pas retouché.

Portages notables : `String#gsub(' ', '_')` (motif chaîne → remplace toutes les occurrences) en `split/join` ; `Element#siblings()` en « enfants du parent moins soi » ; `cleanWhitespace()` ; et `.insert({before: node})` en `insertBefore`. `.remove()` passe par `parentNode.removeChild` — le piège habituel, `Element.prototype.remove` étant remplacé par la version Prototype dans le shim.

**Bug préexistant préservé, commenté dans `activate()`** : `delta = eval(parent.offsetLeft) - …`. Ce `parent` n'est pas `daParent` mais **`window.parent`** — une fenêtre de premier niveau n'a pas d'`offsetLeft`, donc c'est `undefined - nombre` → `NaN`. `moveElementTo` écrit ensuite `"NaNpx"`, que le CSS rejette : la branche non-`fitScreen` du mode « slide » n'a donc jamais déplacé quoi que ce soit. Une ligne au-dessus, la branche `fitScreen` utilise correctement `daParent.offsetLeft` et fonctionne.

Nouveau `appgui.spec.ts` : vérifie que l'instance `window.JSGUI` du bureau existe, est bien liée à `#mainApp` et que son `cleanWhitespace()` a bien retiré les nœuds texte vides ; puis qu'`add()` construit réellement le wrapper `.inArea`, la zone interne (`frm` + titre slugifié) et le bouton de barre des tâches (`ong` + titre), correctement parentés et marqués `active`. Garde `IDAE_SHIM_WARN`. **3/3 vert au premier essai**, plus `smoke` et `window-gui` revérifiés propres — **8/8**.

## Migration d'`app/app_contextual.js` + suppression de `resize.js` et `cookie.js` (2026-08-11)

**`librairie/resize.js` (15 occurrences) — supprimé.** Bibliothèque tierce (Thomas Fakes 2005, dérivée de script.aculo.us — licence MIT cette fois, pas de problème d'œuvre dérivée comme pour `lightview.js`), une seule classe `Resizeable`. Ses deux seules instanciations sont **commentées** (`app_insertionQ.js:333`, `app_planning_tache.php:90`). Chargée à chaque boot pour rien. Retirée des deux listes.

**`librairie/cookie.js` (12 occurrences) — supprimé.** Jamais chargé : absent des deux loaders, qui utilisent d'autres bibliothèques de cookies (`vendor/js.cookie.js` et `jsoncookie.js` — attention au faux positif, `Cookies` au pluriel ≠ ce `Cookie`). Zéro usage de `Cookie.*` dans tout le dépôt.

**`app/app_contextual.js` (11 occurrences) — migré.** Menu contextuel au clic droit, chargé par `main_bag.js:56` et **auto-instancié** en dernière ligne (`new app_context()`), délégué sur `[data-contextual]`.

Deux points à noter :
- **Enveloppé dans une IIFE**, contrairement à l'original. Ses helpers seraient sinon entrés en collision au scope global avec ceux d'`app_chat.js`, autre fichier global de la même page (préfixe `ac_` déjà pris) — d'où le préfixe `ctx_` et l'IIFE.
- **Pas la fuite d'`app_menu.js`** : cette classe stocke son handler une bonne fois dans `this._clickHandler`, donc `addEventListener`/`removeEventListener` reçoivent le même objet fonction et l'écouteur est réellement retiré. Contraste utile avec `app_menu.js`, qui re-`bind()` des deux côtés et n'enlève jamais rien.

Nouveau `app-contextual.spec.ts` : vérifie la construction de `#app_contextual_menu` au boot (enfant de `body`, classe, `data-cache`, masqué) ; puis qu'un clic droit marque le nœud (`right_clicked`), appelle `socketModule` avec le bon module et les bonnes vars, affiche et positionne le menu — et qu'un clic extérieur le referme et démarque. Un test dédié vérifie aussi que `Resizeable` et `Cookie` ont bien disparu du global. Garde `IDAE_SHIM_WARN`. **4/4 vert au premier essai**, plus `smoke` — **5/5**.

## Migration d'`app/app_live_data.js`, suppression de `niceForm.js`, et une vraie régression attrapée (2026-08-11)

**`librairie/niceForm.js` (9 occurrences) — supprimé.** Le JS n'est chargé par aucun loader — seule la feuille de style homonyme (`niceForm/niceForm.css`) l'est, d'où le faux positif au grep — et la classe n'est instanciée nulle part.

**`app/app_datatable.js` (10 occurrences) — rien à faire.** Vérifié ligne par ligne : les 10 « occurrences » sont toutes dans des blocs commentés ou sont des affectations `.memo` sur de vrais `CustomEvent` natifs. Le fichier était déjà 100 % natif depuis sa migration en début de Phase 5. Le comptage statique par grep surestime, c'est attendu.

**`app/app_live_data.js` (9 occurrences) — migré.** Mises à jour live pilotées par socket : rafraîchissement throttlé des champs modifiés côté serveur, plus le re-parentage tache/congé sur les plannings. Chargé sans condition ; ses `act_*` sont les cibles du `receive_cmd` d'`app_socket.js`.

### La régression : `querySelectorAll` natif est plus strict que le shim

Le nouveau spec a immédiatement échoué sur :

```
SyntaxError: '[data-table=probe][data-table_value=5] [data-field_name=nomProbe]'
is not a valid selector
```

Une valeur d'attribut **non quotée commençant par un chiffre** est du CSS invalide. Le moteur de sélecteurs de Prototype l'acceptait, et le shim reproduisait cette tolérance : `__idaeQSA` (`shim-core.js`) tente `querySelectorAll`, rattrape le `SyntaxError`, re-quote les valeurs d'attribut et réessaie. Mon `ld_qsa` appelait `querySelectorAll` nu.

Ce n'est pas un cas limite : **`table_value` est une clé primaire entière partout dans Idae**. Tout `act_upd_data` sur un enregistrement réel serait parti en exception — c'est-à-dire toutes les mises à jour live, en silence côté utilisateur.

Le même piège dormait dans **deux fichiers déjà commités** : `app_chat.js` (9 sites `[data-appid=<sid>]`) et `app_keepon.js` (1 site) — les `APPID` sont des ids de session PHP, qui commencent très souvent par un chiffre. Leurs specs ne l'avaient pas attrapé parce qu'elles utilisent des ids de sonde alphabétiques. Les trois helpers (`ld_qsa`, `ac_qsa`, `kp_qsa`) portent maintenant le même repli tolérant que le shim, commenté. Balayage des 15 autres fichiers migrés : aucun autre n'interpole dans un sélecteur d'attribut non quoté.

**Leçon à retenir pour la suite** : dès qu'un helper `*_qsa` reçoit un sélecteur construit par concaténation, il lui faut le repli tolérant. Le shim masquait cette différence partout, et une spec avec des données « propres » (ids alphabétiques) ne la révèle pas.

Nouveau `app-live-data.spec.ts` : rafraîchissement de champ + `dom:data_reload` (avec `table_value` **numérique**, précisément le cas qui plantait), non-traitement d'une table non souscrite, suppression par `act_close_mdl`, et disparition du global `niceForm`. Garde `IDAE_SHIM_WARN`. **12/12** avec `app-chat`, `app-keepon` et `smoke` revérifiés après le back-fix.

Note d'environnement : la sonde `global-setup.ts` a signalé Apache/phpBridge coincé en cours de route (`did not answer within 10s`) — c'est le cas où le redémarrage des conteneurs est légitime, contrairement au restart systématique abandonné plus bas.

## Échec instructif — l'inventaire runtime global n'est pas mesurable avec `IDAE_SHIM_WARN` (2026-08-11)

Objectif : après le ratissage de la file par taille, mesurer **ce qui appelle encore réellement les shims**, pour savoir quels `shim-*.js` sont supprimables. Quatre tentatives, aucune exploitable. Rien de commité côté code : les specs expérimentales et le rapport produit ont été supprimés plutôt que livrés. Ce qu'on en retient vaut le détour, parce que ça invalide l'approche et pas seulement l'implémentation.

**Ce qui a été essayé, et pourquoi ça casse.**

1. Armer `IDAE_SHIM_WARN` via `addInitScript` (donc *avant* le chargement des shims, pour couvrir le boot) + collecte par `page.on('console')`. Chaque appel shimé lève une `Error` pour capturer une pile, et chaque message traverse CDP un par un. **13+ minutes sans produire de rapport**, deux fois.
2. Agrégation *dans la page* (override de `console.warn`, que `shimWarn` résout à l'appel) au lieu de CDP, boot non instrumenté. Terminé en 26 s — mais rapport **tout à zéro**.
3. Ce zéro était faux. Un **témoin positif** ajouté au run (appeler délibérément `hasClassName`/`$$`/`Array#include` après l'armement) a lui aussi compté 0 : la mesure ne fonctionnait pas, et sans ce témoin j'aurais publié « plus aucun appel shim sur liste et calendrier », ce qui était faux.
4. Le même témoin monté en spec autonome, avec collecte CDP non bornée, a fait **tomber Node en OOM à 4 Go** (`FATAL ERROR: Ineffective mark-compacts near heap limit`) en 134 s, puis, une fois borné, a cassé le tracing Playwright (`Cannot read properties of undefined (reading 'traceName')`).

**Le diagnostic.** L'instrument fire — abondamment, c'est l'OOM qui le prouve. Mais une fois armé sur un bureau vivant, le flot est **continu** : les timers de fond, les handlers socket (`app_keepon`, `app_chat`), les observers `insertionQ` continuent d'appeler des méthodes shimées indéfiniment. `installWarnWraps` enveloppe aussi `Function.prototype` (donc chaque `.bind()`), `Array.prototype` et `String.prototype`. Armer, c'est donc dégrader la page de plusieurs ordres de grandeur en permanence, pas prendre une mesure ponctuelle.

**Conséquence rassurante pour tout le travail précédent** : les gardes par fichier ne sont *pas* vides. Elles filtrent par fichier appelant et n'affirment que « mon fichier n'est pas dans le flot » ; elles survivent uniquement parce que leur fenêtre d'observation fait ~300–500 ms. C'est aussi pour ça qu'un inventaire global serait de toute façon noyé sous le bruit de fond.

**Ce qu'il faudrait pour y arriver** (non fait) : un compteur bien moins cher — incrémenter `family.name` dans un objet **sans** capturer de pile (le `throw`/`e.stack` est le coût dominant), et n'échantillonner la pile que sur les N premiers appels de chaque clé. Sans attribution par fichier, ça ne dit pas *qui* appelle ; avec échantillonnage, ça le dit à coût borné. À reprendre si la suppression des `shim-*.js` redevient prioritaire.

**Statut de la question d'origine, honnêtement** : toujours ouverte. On ne sait pas quels `shim-*.js` sont supprimables. Ce qu'on sait : les 15 fichiers migrés cette session ont chacun leur garde verte, et `shim-effects` a bien été supprimé le 09/08 par la voie statique (grep + vérification des appelants), qui reste la méthode praticable.

## Suite complète — `shim-warn.spec.ts` était le bouchon, et deux fragilités de test (2026-08-11)

Première tentative de faire tourner la suite **entière** de la session (99 tests). Elle a mis au jour trois choses, dont une vraie correction.

**1. `shim-warn.spec.ts` bloquait la suite.** Cette spec préexistante armait `IDAE_SHIM_WARN` via `addInitScript` — donc *avant* le chargement des shims — et collectait toutes les alertes d'un boot à froid dans un tableau non borné. C'est exactement la configuration qui, dans les essais d'inventaire ci-dessus, a fait tomber Node en OOM à 4 Go en 134 s. Ici elle immobilisait la suite **17 min et plus** sur ce seul fichier, ce qui explique qu'aucun run complet n'aboutissait.

Borner le collecteur n'a pas suffi : un boot instrumenté dépasse à lui seul le budget de 60 s quand il s'exécute en séquence (41 s en isolation, échec à 1 min en suite). Réécrite pour **armer après le boot** et déclencher un seul appel délibéré (`document.body.hasClassName(...)`) : **226 ms**, et elle prouve exactement la même chose.

Ce point vaut d'être retenu : cette spec est le témoin positif de tout l'édifice. Les gardes « n'appelle plus les shims » de chaque fichier affirment `toEqual([])`, ce qui passe aussi bien si l'instrument n'enregistre rien. C'est elle qui les rend probantes — d'où le message d'échec explicite qu'elle porte désormais.

**2. `snapshots: list view` photographie une donnée volatile.** Échec constaté, puis vert au réessai. Inspection de l'image de différence : tout est identique au pixel près sauf **un compteur de lignes, « 5 » au lieu de « 8 »**, en bas à gauche. C'est le nombre d'enregistrements en base — que `crud.spec.ts` fait varier en créant puis supprimant une fiche. Aucune régression visuelle. La capture masque déjà la zone de données (bloc magenta) mais pas ce compteur ; à masquer aussi si le faux positif devient gênant.

**3. Le backend se dégrade sous une longue série.** Mesuré pendant le run : `json_scheme.php` passe de ~0,4 s à 4,5 s puis 8 s, jusqu'au blocage complet (timeout, `000`) constaté une fois — c'est le cas légitime de `docker restart`, signalé par la sonde `global-setup.ts`. Tous les échecs restants de ce run ont cette signature : `0 ms` (le hook `beforeAll` n'aboutit pas, le corps du test ne s'exécute jamais) ou `1.0m` (timeout de boot), et **tous repassent au réessai**. Aucun n'est imputable au code migré.

## Correction — `vendor/prototype/` et `vendor/scriptaculous/` : la Phase 3 ne les avait pas supprimés (2026-08-11)

La Phase 3 affirme les avoir supprimés « comme poids mort une fois le shim en place ». `CLAUDE.md` reprenait l'affirmation et interdisait de les réintroduire. **Les deux dossiers sont toujours là** : `javascript/vendor/prototype/` (204 Ko) et `javascript/vendor/scriptaculous/` (160 Ko, 8 fichiers).

Ils ne sont en revanche réellement plus chargés, vérifié : `index.php` ne tire que `main_bag.js`, qui ne référence ni l'un ni l'autre. Le seul fichier qui les référence encore est `javascript/main.js` — une configuration RequireJS de l'ancien chargeur, elle-même chargée par personne. Donc du poids mort dans le dépôt, pas du code expédié au navigateur : sans effet sur les utilisateurs, mais trompeur pour quiconque reprend le sujet.

**Supprimés depuis** (11/08, après vérification de `main.js`) : les deux dossiers et `javascript/main.js`, soit 364 Ko et 9 fichiers. `CLAUDE.md` décrit maintenant l'état réel. À noter : `javascript/flotr/` embarque sa propre copie de Prototype 1.6 — elle appartient à flotr, ne pas y toucher.

## Quels `shim-*.js` sont supprimables ? Mesure statique, et un demi-tour (2026-08-11)

La mesure runtime ayant échoué (section plus haut), reprise par la voie statique — celle qui avait déjà permis de supprimer `shim-effects`. Résultat : **aucun des six shims restants n'est supprimable en l'état**, et la tentative sur le plus prometteur a failli casser six écrans.

**`shim-draggable` — non.** Deux appelants réels : `cropper.js` (`Class.create(Draggable, …)`, `Draggables.register`) et `resizeGui.js` (`new Draggable`). Ce sont exactement les deux fichiers écartés du périmètre le 09/08. Tant qu'ils ne sont pas traités, il reste.

**`shim-ajax` — non, et c'est instructif.** La mesure de surface disait « supprimable » : plus aucun `new Ajax.*` nulle part hors `vendor/` et `flotr/`, et son unique référence — le bloc `Ajax.Responders` d'`initApp.js` — ne peut par construction jamais se déclencher sans une requête `Ajax.Request` pour la produire. Bloc mort, fichier apparemment libre.

Suppression faite… puis annulée, en deux temps :

1. `prototype-surface.spec.ts` a immédiatement signalé `PeriodicalExecuter` manquant. Ce fichier n'exporte pas que `Ajax` : aussi `PeriodicalExecuter` (zéro appelant, celui-là), **`Form` et `Field`**. Bon rappel de l'utilité de ce contrat de surface : il attrape ce qu'un fichier fournissait *en plus* de ce qu'annonce son nom.
2. Vérification de `Form` — et là, l'arrêt net. Les **gabarits** en dépendent à 52 endroits : 31 `Form.serialize`, 13 `$(f).serialize()`, 8 `Form.serializeElements`. Tous dans des attributs `onclick` inline (`produit_tarif_gamme_update.php`, `produit_tarif_gamme_update_all.php`, `app_document_tag.php`, `document_liste.php`…) — précisément la catégorie qu'aucune sonde runtime ne voit tant qu'on ne clique pas.

Tout a été remis en état : fichier, entrée de chargement, contrat de surface.

**Bug préexistant trouvé au passage** : `Form.serializeElements` n'existe **pas** comme méthode statique dans le shim — seule une variante d'élément est posée via `Element.addMethods`. Les 8 appels `Form.serializeElements($(…).select('.selectable'))` des écrans `produit_tarif_gamme_*` lèvent donc `Form.serializeElements is not a function`. Régression du swap Phase 3/4 (le vrai Prototype fournissait cette statique), jamais détectée parce que ces écrans ne sont pas couverts. Laissé de côté sur le moment pour ne pas mêler une correction fonctionnelle à une suppression annulée — **corrigé depuis, voir la section suivante**.

## `shim-ajax.js` → `shim-form.js` : le découpage, et un second bug du même swap (2026-08-11)

Reprise des deux points laissés ouverts ci-dessus. Dans l'ordre, parce que le second a changé le périmètre du premier.

### 1. Deux API manquantes, pas une

En vérifiant les 13 appels `$(f).serialize()` des gabarits — je les croyais sains — j'ai énuméré ce que les deux blocs `Element.addMethods` de `shim-ajax.js` posent réellement : `serializeElements, getInputs, disable, enable, focusFirstElement, request` puis `activate, clear, present, getValue, setValue`. **Pas de `serialize`.**

Mesuré dans le navigateur plutôt que conclu par lecture, la lecture ayant déjà suffi à me tromper une fois cette session :

```
Form_serializeElements: "undefined"      el_serialize: "undefined"
static_result: "THROW: w.Form.serializeElements is not a function"
serialize_result: "THROW: $f.serialize is not a function"
```

**21 appels cassés, pas 8** : 8 statiques + 13 méthodes d'élément. Tous atteints depuis des attributs `onclick`/`onsubmit` inline, donc ils n'échouent qu'au clic d'un utilisateur — invisibles à tout grep JS comme à toute sonde runtime des modules chargés.

Correctif : `serializeElements` n'est pas du code neuf, c'est la boucle qui vivait déjà en ligne dans `Form.serialize`, sortie telle quelle, `Form.serialize` étant redéfini comme `serializeElements(getElements(form))` — les deux chemins ne peuvent donc plus diverger. La méthode d'élément `serialize` aiguille sur `tagName` : Prototype pose `Form#serialize` et `Field#serialize` comme deux jeux typés distincts, alors que l'`addMethods` du shim n'est pas typé et atterrit sur tous les éléments ; sans la branche, `input.serialize()` rendrait `''` là où Prototype rend `name=value`.

`form-serialize.spec.ts` couvre l'exclusion des champs `disabled` et du bouton `submit`, l'expansion d'un `select multiple`, l'appel sur sous-ensemble qui justifie l'existence de la statique, et le sens « champ seul ».

### 2. Le découpage

Une fois les gabarits réellement fonctionnels, le découpage annoncé a pu se faire. Supprimés : `Ajax.Request`, `Ajax.Updater`, `Ajax.PeriodicalUpdater`, `Ajax.Responders`, `PeriodicalExecuter`, `Form#request`. **650 → 270 lignes.**

`engine/initApp.js` perd son bloc `Ajax.Responders` — code inatteignable, et rien n'est perdu : `content:loaded` est émis nativement par `app_socket.js`, `app_window.js` et `engine/methods.js:312`, qui appellent aussi `afterAjaxCall()`. La déduplication de requêtes (`onlyLatestOfClass`) n'a pas de remplaçant parce qu'elle n'avait aucun producteur — le nom n'apparaissait que dans ce fichier.

`prototype-surface.spec.ts` perd `Ajax`, `Ajax.Request/Updater/Responders` et `PeriodicalExecuter`, et gagne `Form`, `Form.serialize`, `Form.serializeElements`. Ce contrat décrit ce dont l'app dépend, pas ce que Prototype offrait ; c'est d'ailleurs lui qui avait attrapé `PeriodicalExecuter` lors du demi-tour, et lui qui n'avait **pas** attrapé les 21 appels cassés — d'où l'ajout des deux entrées `Form`.

**Ménage adjacent** : `playwright/tests/fixtures/shim-preview.ts` supprimé. Il servait à faire tourner la suite contre le swap Phase 4 avant que `main_bag.js` ne s'y engage, en interceptant `prototype-1.7.3.js` au niveau réseau. Sans objet depuis la Phase 4, et pourri de toute façon : il listait `shim-effects.js` (supprimé), ignorait `shim-draggable.js`, et interceptait un `prototype-1.7.3.js` qui n'existe plus dans le dépôt.

**Ce qui reste vrai** : `shim-form.js` n'est pas supprimable. Ses 52 appels de gabarits le tiennent.

## `shim-enumerable.js` supprimé — l'audit des 67 noms, et le bug de mon propre regex (2026-08-12)

Le commit précédent (`shim-form` rendu autonome) refusait explicitement de supprimer
`shim-element.js` et `shim-enumerable.js`, au motif que ma mesure « zéro appelant »
reposait sur un motif à moi qui n'avait jamais été confronté aux 67 noms que
`prototype-surface.spec.ts` asserte un par un. Cet audit-là, le voici.

### Le bug du motif — 5 lignes qui invalidaient tout le comptage

Le premier passage utilisait `(?<![\w$])\.\s*<nom>\s*\(`. Ce lookbehind exige un
caractère **non-mot avant le point**. Il ne matche donc que ` .foo(` et `).foo(`
— jamais `element.select(`, c'est-à-dire quasiment tous les vrais sites d'appel.
Constaté en le testant à la main :

```
'this.element.select(1)'  → False
'a.next ()'               → False
'x .select('              → True
```

C'est exactement la faute qui avait produit la régression `$('news_zoom').toggle()`
plus tôt dans cette migration : un classificateur trop étroit pour savoir ce qu'il
déclarait sûr. Le lookbehind supprimé, `select` passe de 0 à 3, `remove` de 0 à 52,
`update` de 0 à 13, `bind` de 242 à 295.

### Le résultat, une fois les fichiers morts écartés

Écartés parce qu'introuvables dans `require_trame` **et** dans tout chargeur
dynamique : `autobahn.min.js`, `require.js`, `app_test.js`, `app_draggable.js`,
`librairie/tinyeditor.js`, `ms-lib-prototype/`, `node_modules`.

Sur les 49 noms Array / String / Function / Number, **un seul** a un appelant
vivant : `Array#each`, dans les trois modules Google-Maps
(`app_custom_map.php`, `app_custom_map_zone.php`, `app_custom_ville_map.php`),
tous de la forme identique `markers.each(function (node, index) {...})` sur un
tableau ordinaire. Passés en `forEach` — même contrat, index compris.

Le reste des occurrences textuelles était soit un homonyme natif
(`String#replace`, `Promise.all/reject`, `Function#bind` — 283 sites vivants,
désormais servis par le natif, dont le curry correspond à celui de Prototype),
soit la méthode d'objet d'une bibliothèque (`util.toArray` de `query-engine.js`).

### Ce que la suppression a coûté aux autres shims

`shim-enumerable` n'avait plus d'appelant applicatif, mais trois shims lui
empruntaient encore des méthodes. Chacun a reçu ses helpers locaux, comme
`shim-form` la veille :

| shim | ce qui était emprunté | remplacement |
|---|---|---|
| `shim-core` | `Array#include`, `String#strip`, `Hash#map` | `indexOf`, `trim`, `_each` |
| `shim-class` | `String#gsub` ×2 (dans `Template#evaluate`) | `cls_gsub` / `cls_gsubLiteral` |
| `shim-element` | `stripScripts`, `evalScripts`+`defer`, `camelize`, `blank`, `include`, `$w().each`, `$A(this).without` | helpers `el_*` |
| `shim-event` | `Function#defer` | déjà gardé (`fn.defer ? … : fn()`) |

Deux points méritaient mieux qu'un renommage :

**`Template#evaluate` ne peut pas devenir `String.replace`.** `Template.Pattern`
est une regex **non globale** ; `replace` substituerait le premier `#{...}` et
laisserait le reste du gabarit intact. `gsub` de Prototype re-matche le reste en
boucle — c'est ce qui fait marcher un gabarit à plusieurs placeholders. Et
l'itérateur reçoit le **tableau** de match (`match[1]`, `match[3]` sont lus), pas
la liste `(match, p1, p2, …)` de `replace`. `cls_gsub` reproduit la boucle, garde
anti-match-vide comprise. La sonde de comportement du spec utilise maintenant deux
placeholders : à un seul, elle ne verrait justement pas ce bug-là.

**`Element.ClassNames` aurait silencieusement vidé l'attribut `class`.**
`add()`/`remove()` faisaient `$A(this)`, qui atteignait le `toArray` du mixin
Enumerable via la branche « `'toArray' in l'objet` » de `$A`. Sans ce mixin, la
branche rate, `$A` retombe sur sa boucle par `length`, et un `ClassNames` — qui n'a
pas de `length` — revient à `[]`. `toArray` est désormais une méthode propre de
`ClassNames`.

### `shim-element` reste, et c'est mesuré

Contrairement à `enumerable`, `shim-element` a des appelants vivants : `setStyle`
(`main_bag.js`), `observe`, `hide`, `up`, `show`, `hasClassName` ×2, `next` — dans
`app_calendrier_echeance.php`, `app_component.html`, `app_explorer.php`,
`app_explorer_search.php`. Le sondage `hasClassName` de `shim-warn.spec.ts` reste
donc valide sans modification : c'est `shim-element` qui le fournit.

**8 → 5 shims** (`core`, `class`, `element`, `event`, `form`), **656 lignes** de
moins.

### Un flake de suite, et sa vraie cause

`myddeview-notifier.spec.ts` a échoué en suite complète (3 tentatives) et passé
seul. Le spec lisait `growler.querySelector('.notifierNotice')` — le **premier**
match — alors que `buildNotice` fait `appendChild` : il attrapait le toast que la
socket avait déjà poussé (« Notification »). Corrigé en visant le dernier. Rien à
voir avec les shims ; le spec était faux depuis le début et ne le montrait que
quand une notification arrivait avant lui.

## `shim-class` et `shim-event` vidés de leurs derniers appelants (2026-08-12)

Suite du même audit : les deux shims les plus légers n'avaient plus qu'un et
deux sites de gabarits respectivement.

**`shim-class`** — `app_prod_fiche.php` faisait `new Template(...).evaluate(res)`
puis `tolototo.update(out)` (identifiant nu + `.update()` de `shim-element`). Le
gabarit interpole `#{champ}` dans le HTML existant du conteneur ; ses clés sont
plates, sans la syntaxe pointée/crochets de `Template`. Remplacé par un
`replace(/#\{([^}]*)\}/g, …)` direct sur `innerHTML`, plus `.innerHTML =`. Un
seul site, donc pas de helper file-local séparé.

**`shim-event`** — deux sites : `app_calendrier_echeance.php` appelait
`.observe('dom:act_click', …)` alors que tous les autres producteurs/consommateurs
de ce même événement custom dans le code (`app_calendrier.js`,
`myddeDatalist.js`, `app_conge.php`, `app_planning.php`, `app_stat_dispatch.php`…)
utilisent déjà `addEventListener` + `event.memo` — celui-ci était le seul
survivant sous l'ancienne API. Et `skelbuilder_input.php` faisait
`Event.element(event)`, qui n'est rien d'autre que `event.target`.

**Bonus au passage, shim-element** : l'audit avait raté deux choses en les
classant "native" à tort.
- `app_explorer_search.php` : `.up('.searchMdl')` (ancêtre le plus proche,
  hors self) et `.next()` (frère suivant, sans sélecteur) sur 4 sites, plus
  `.hasClassName()` ×2 et `.insert({after: …})`. `.up()` devient
  `parentElement.closest(...)` — `closest()` inclut self, Prototype non, d'où le
  scope sur `parentElement`. `.next()` devient `nextElementSibling`.
  `.insert({after})` devient `insertAdjacentElement('afterend', …)`, qui déplace
  aussi un nœud déjà attaché comme le faisait Prototype.
- `.readAttribute('data-table'/'data-table_value')` dans
  `app_promo_zone_build.php` et `app_scheme_has_field.php` : ni dans la table de
  traduction de lecture de Prototype ni dans les attributs booléens, donc
  `getAttribute` pur.
- `app_component.html` : `.up('#auto_expl_preview_zone')` cherchait un ancêtre
  par id fixe — remplacé par un accès direct `getElementById(...)`. `.hide()`
  devient `style.display = 'none'`.
- Trois faux positifs corrigés dans l'inventaire lui-même : `myddeDatalist.js`
  et `myddeSelection.js` n'appelaient déjà que `window.scrollTo`/`global.scrollTo`
  natifs — l'audit du jour précédent comptait ces occurrences sans vérifier
  qu'il s'agissait bien de l'API globale et non de la méthode d'élément.

**`shim-element` et `shim-form` restent** : mesurés à nouveau après ce ménage,
0 appel de `shim-class`/`shim-event` dans les gabarits ; `shim-element` en garde
19 (surtout des méthodes `Array#/String#` homonymes natives, réparties sur peu
de fichiers) ; `shim-form` en garde ~47, le vrai chantier.

**Effet de bord attendu sur le filet** : `template-api-guard.spec.ts` a un
garde-fou interne — `found.methods.size > N` — pour détecter si son propre
parcours de fichiers est cassé (zéro résultat = bug du test, pas du code). Le
nettoyage a fait passer le compte de noms Prototype distincts trouvés dans les
gabarits de 10 à 9, sous le seuil de 10 posé quand ce garde-fou a été écrit.
Le seuil n'est pas une cible à défendre, seulement un « le parcours a bien
tourné » ; abaissé à 5 et documenté comme tel — le compte réel continuera de
baisser au fil de la Phase 5.

Toute la suite (106 tests) verte après ce commit.

## L'angle mort des gabarits, troisième occurrence — `Effect.*` (2026-08-11)

Le découpage de `shim-ajax` réglé, j'ai voulu chiffrer le « chantier de gabarits » annoncé plus bas. Le comptage a d'abord fait remonter autre chose.

### Mesure

Surface Prototype recherchée dans `*.php` / `*.latte` / `*.tpl` (hors `flotr/`) :

| | | | | | |
|---|---|---|---|---|---|
| `$(` **694** | `.select()` 90 | `.readAttribute()` 88 | `.first()` 70 | `.show()` 44 | `.bind()` 44 |
| `.up()` 43 | `$$(` 41 | `.serialize()` 38 | `.observe()` 31 | `.fire()` 26 | `.invoke()` 21 |
| `.hide()` 21 | `.each()` 20 | `Event.` 19 | `.update()` 18 | `.next()` 18 | `.size()` 10 |
| `.setStyle()` 10 | `Insertion.` 8 | `$A(` 5 | **`Effect.` 4** | `Position.` 1 | `Element.` 1 |

Hors `flotr/`, `vendor/` et `adodb/` — ces deux derniers sont des bibliothèques PHP serveur sans JS inline, et les inclure noie le signal (1 449 occurrences de `Builder` à elles seules). Des faux positifs subsistent, `.select(` et `.first(` attrapant aussi du PHP applicatif. L'ordre de grandeur est bon et confirme le chiffre de ~719 `$()` cité plus bas.

### Ce que `Effect. 4` voulait dire

`shim-effects.js` a été supprimé le 09/08 « une fois chaque appelant réel migré ». Chaque appelant **JavaScript**. Trois appels vivent dans des blocs `<script>` de gabarits :

- `postAction.php:118` et `:144` — `new Effect.Highlight(node)`
- `mdl/app/app_calendrier/mdlCalendrierListYear.php:44` — `new Effect.Appear($('dynlistYear'))`

(un quatrième, `page_body.latte:306`, est en commentaire.)

Ils lèvent `Effect is not defined` depuis le 09/08.

**Et `prototype-surface.spec.ts` a activement validé la suppression** : `Effect` en a été retiré avec la note « ce contrat décrit ce dont l'app dépend, pas un musée de ce que Prototype offrait ». Le raisonnement est juste ; l'inventaire sur lequel il s'appuyait ne regardait que le JS. J'ai écrit cette note moi-même deux jours plus tôt.

### Correctif

`appearElement` existait déjà dans `engine/methods.js` — substitution directe. `highlightElement` ajouté à côté, même contrat Scriptaculous : flash `#ffff99`, transition vers le fond calculé, puis restauration du `backgroundColor` inline d'origine — y compris la chaîne vide, ce qui laisse une règle CSS ou un `:hover` reprendre la main. Transition CSS plutôt que boucle de frames : les deux appels de `postAction.php` suppriment le nœud 500 ms plus tard, seul le flash initial est jamais vu.

### La garde : `template-api-guard.spec.ts`

Corriger les trois appels ne vaut rien si le quatrième passe pareil. Le trou d'outillage se bouche par un test qui **dérive** sa liste au lieu de la coder en dur :

1. côté Node, parcours de `idae/web/**/*.{php,latte,tpl}` (hors `flotr/`, `vendor/`, `adodb/`), commentaires de ligne retirés — sinon le `//new Effect.ScrollTo` de `page_body.latte` maintiendrait une API morte en vie ;
2. extraction des `Namespace.membre` et des `.methode(` appartenant au vocabulaire Prototype (ensemble fermé : matcher tous les `.foo(` d'un fichier PHP noierait le signal) ;
3. côté navigateur, chaque nom est cherché sur `$(element)`, `Array/String/Function/Number.prototype` — il passe si **au moins un** hôte le fournit. Le scan ne sait pas à quel receveur appartient un `.foo(` dans un gabarit, et n'a pas besoin de le savoir : il ne doit écarter que « personne ne le fournit ». Une collision avec un nom de méthode PHP est donc inoffensive.

Supprimer un shim dont un gabarit dépend fait maintenant échouer ce test, sans que personne ait eu à penser à le mettre à jour. 20 s.

**Premier run, première prise** — `Element.clone`, dans le gestionnaire de drop de `mdl/app/app_newsletter/app_newsletter_item_liste.php:92` :

```js
tmpdiv = Element.clone($$('[dragged]').first(), true);
```

`Element.clone` n'existe pas. Ni dans le shim, **ni dans PrototypeJS 1.7.3** — la méthode n'a jamais fait partie de l'API. Cette ligne lève `Element.clone is not a function` depuis qu'elle a été écrite, tuant le gestionnaire avant tout ce qui suit. Antérieur à la migration, sans rapport avec elle. Remplacé par `cloneNode(true)`, la copie profonde visée.

C'est le meilleur argument possible pour la garde : elle a trouvé, à sa première exécution, un bug que personne ne cherchait.

### Le vrai enseignement, corrigé

Trois fois de suite — `Form.serializeElements`, `Form#serialize`, `Effect.*` — le même schéma : une API retirée ou jamais posée, zéro appelant JS, des appelants dans des attributs `onclick`/`onsubmit` inline, et une détection uniquement au clic d'un utilisateur. **Aucun de nos outils ne regardait les gabarits** : ni le grep de migration, ni `IDAE_SHIM_WARN` (qui n'instrumente que ce qui est appelé pendant la navigation de test), ni `prototype-surface.spec.ts` (liste écrite à la main depuis un inventaire JS).

Ce n'était pas une série de trois étourderies, c'était un trou d'outillage — désormais bouché par `template-api-guard.spec.ts`. Toute suppression de shim était un pari jusqu'ici ; elle est maintenant vérifiable.

**Corollaire, et c'est le vrai enseignement** : `shim-core`, `shim-class`, `shim-element`, `shim-enumerable` et `shim-event` sont tenus par les gabarits, pas par le JS applicatif. Le JS est migré ; ce sont les ~719 `$()` et consorts en PHP/Latte qui maintiennent toute la couche en vie. **La suite de la migration n'est pas un problème JavaScript, c'est un chantier de gabarits** — et il n'a jamais été chiffré.

## Note de méthode — redémarrer Docker entre deux fichiers est inutile (2026-08-10)

Pendant une bonne partie de cette session j'ai relancé `docker restart idae-socket idae-legacy` après chaque fichier migré, avant de lancer la suite. Inutile, vérifié :

- `docker-compose.yml:21` monte `./idae:/var/www/html/idae` en bind mount → une édition côté hôte est visible dans le conteneur immédiatement, sans rebuild ni restart.
- `build_asset_version_manifest` (`appfunc/asset_versions.php:28`) recalcule le manifeste depuis `getMTime()` **à chaque requête** — donc chaque chargement de page produit un nouveau `?v=`, ce qui contourne le cache IndexedDB de `bag.js`.
- Playwright ouvre de toute façon un contexte navigateur neuf par run : IndexedDB vide, aucun cache d'assets à invalider.

La confusion venait du « protocole de volatilité d'environnement » plus haut : celui-là concerne les workers Playwright pendus et la stack WSL2 dégradée (symptôme : échecs répétés dans le hook `beforeAll`), pas la prise en compte des fichiers modifiés. Le restart reste le bon réflexe **quand la suite se met à échouer au boot**, jamais comme étape systématique. ~40 s gagnées par fichier.

**Précision ajoutée le 11/08, après avoir failli mal appliquer ma propre règle.** Le backend se dégrade progressivement sous une longue série de tests — `json_scheme.php` mesuré à 0,39 s à froid, puis 4,5 s, 5,2 s, jusqu'au blocage complet. Passé ~3-4 s par requête, `smoke` ne peut plus passer : le boot enchaîne trop d'appels pour tenir dans son budget de 60 s, et il échoue **de façon déterministe, pas aléatoire** — trois tentatives d'affilée, toutes en « timeout pendant la mise en place de la page ». Après `docker restart`, le même test passe en 15 s.

Donc la règle complète : ne pas redémarrer entre deux fichiers (inutile, le montage est direct et le manifeste recalculé à chaque requête), **mais redémarrer entre deux longues séries**. Et surtout : trois échecs identiques d'affilée sur un test qui passait ne veulent pas dire « régression » — mesurer la latence du backend avant de conclure quoi que ce soit.

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

## Rechute — `localhost` cassé aussi dans le navigateur, et le retry qui l'a amplifié (2026-08-10)

Le correctif du 9 août ne couvrait que Playwright (`BASE_URL` normalisé). L'usage humain, lui, est resté sur `http://localhost:8080` — donc le même trou noir, en permanence. Symptôme rapporté : « la migration est un échec », boot cassé, UI qui gèle, reconnexions socket en boucle.

Mesuré (2026-08-10, machine de dev Windows + Docker Desktop + WSL2 `networkingMode=mirrored`) :

| Cible | Résultat |
|---|---|
| `curl --ipv6 http://localhost:3005/health` | **000 après 21,05 s** |
| `curl --ipv4 http://localhost:3005/health` | **200 en 3 ms** |
| `Resolve-DnsName localhost` | **AAAA `::1` en premier**, puis A `127.0.0.1` |
| `tactac.idae.preprod.lan` | **A seul** (127.0.0.1) → 8080 : 302, 3005 : 200 en 7 ms |

Le serveur est hors de cause : handshake engine.io identique sur `localhost` et sur `127.0.0.1` dès lors qu'on force IPv4 (`curl --ipv4`, deux `sid` valides). Ce n'est ni CORS, ni le cookie, ni l'origine — uniquement la famille d'adresse.

Précision utile pour lire les logs : dans la stack client, `WebSocket is closed before the connection is established` est la **conséquence**, pas la cause. L'ordre réel est `Polling.onData → Socket.onError → Socket.onClose → freezeTransport → WS.doClose` : le polling reçoit un paquet d'erreur engine.io, la socket se ferme, et la sonde WebSocket en cours est tuée au passage. Le paquet d'erreur lui-même n'a pas été lu — si le problème réapparaît une fois l'hôte corrigé, c'est là qu'il faut creuser.

### L'amplificateur, dans le correctif du 9 août lui-même

`get_data_hook_reconnect()` s'abonnait à `['reconnect', 'connect']`. socket.io v2 émet **les deux** pour une seule reconnexion : chaque cycle ré-émettait donc tout le in-flight **en double**. Sur un transport qui bat de l'aile, une churn tolérable devient une tempête : par cycle, `json_scheme` (306 Ko) + `json_scheme_field` (53 Ko) ×2, dix cycles en dix secondes, Apache à court de workers, puis `[PHP-BRIDGE] Timeout after 30000ms`. Le remède amplifiait la cause.

Corrigé : écoute de `'reconnect'` seul (socket.io tamponne les emits faits avant connexion et les vide au `connect`, donc `'connect'` n'était pas nécessaire au premier boot non plus), plus un compteur d'époque garantissant un renvoi par entrée et par reconnexion.

À noter : le timeout `phpBridge` de 30 s (8 août, `83ef31c`) n'a rien cassé — il a rendu visible un hang jusque-là muet. Le message d'erreur est nouveau, pas le défaut.

### Tentative infra abandonnée

`docker-compose.yml` publie désormais `0.0.0.0` **et** `[::]` sur 8080/3005. Après `--force-recreate`, `docker port` annonce bien `[::]:3005`, et pourtant `curl --ipv6` timeoute encore à 21 s — et `Get-NetTCPConnection` ne trouve **aucun** listener hôte sur ces ports, en v4 comme en v6. Le relais WSL2 en mode miroité ne forwarde pas `::1`. Les lignes `[::]` sont conservées (correctes sur un hôte Linux) mais **inertes ici** ; les commentaires du fichier le disent explicitement pour que personne ne les prenne pour le correctif.

Piste non poursuivie : `hostAddressLoopback` dans `.wslconfig` — exige `wsl --shutdown`, portée machine entière, résultat incertain. Mauvais rapport bénéfice/risque face à une solution qui marche déjà.

### Règle retenue

**Ne jamais naviguer sur `localhost`.** Utiliser `127.0.0.1:8080` ou un nom du fichier `hosts` (`tactac.idae.preprod.lan`, `maw.idae.preprod.lan`, tous deux présents dans `idae/config/lan-hosts.json` et couverts par la regex CORS `.lan` du serveur). Les noms `hosts` sont immunisés : ce fichier est IPv4 par construction — d'où le fait que le bug n'existait pas à l'époque où l'app se naviguait sur `*.lan`.

Non corrigeable côté app : l'hôte socket vient de `document.domain`, ce qui est juste. Le réécrire sans réécrire la page scinderait le jar de cookies (`localhost` ≠ `127.0.0.1`), `PHPSESSID` ne suivrait pas, et `json_ssid` signalerait un désaccord à chaque boot — une boucle de login à la place d'une boucle de socket.

Anomalie relevée, non corrigée (fichier système, hors périmètre) : le `hosts` Windows contient une ligne malformée `127.0.0.1 maw.idae.preprod.lan::1 localhost` — un `::1 localhost` collé sans retour à la ligne. Windows ignore les entrées `localhost` de ce fichier, donc ce n'est probablement pas causal, mais l'intention derrière mérite d'être élucidée.

### Validé au runtime (2026-08-10, boot sur `127.0.0.1:8080`)

```
[SOCKET] Connecting to: http://127.0.0.1:3005
[SOCKET] ✓ Connected successfully: nPGX1ff9vItpUax9AAAM
 ok scheme / log ok / json_ssid
```

Aucun `[get_data] socket reconnected - re-emitting`, aucune erreur WebSocket, `schemeLoad()` résout.

| | Avant | Après |
|---|---|---|
| Connexions socket par boot | ~10, une par seconde | 2 |
| `json_scheme` par boot | ~20 | 1 |
| Reconnexions | en boucle | 0 |

**Adresses retenues : `http://127.0.0.1:8080` (défaut, y compris pour Playwright) ou un nom du fichier `hosts` (`tactac.idae.preprod.lan`, `maw.idae.preprod.lan`). `localhost` est abandonné** — il ne fonctionne plus depuis le passage à WSL2 et ne sera pas remis en service.

### Reste ouvert

- **Deux connexions serveur pour un seul `✓ Connected` client.** Constant sur tous les boots du log, anciens compris. Sans rapport avec la tempête, mais inexpliqué : quelque chose ouvre une seconde socket.
- **`json_data_table` timeout à 30 s sur `agent_note` / `agent_tuile`.** Observé dans le log initial (17:34:05) puis à 18:19:22, 18:19:55, 18:20:17 — ces trois derniers **hors tempête**, avec seulement deux connexions actives. Ce n'est donc pas une conséquence de la saturation Apache : requête réellement lente ou bloquée. Meilleur candidat pour la plainte « interface peu réactive, voire freeze » : le desktop charge `agent_table`, `agent_tuile` et `agent_note` en parallèle au boot, et une tuile pendue à 30 s donne une UI qui paraît morte. Corrélation, pas preuve — à instruire séparément.
- La plainte « beaucoup de choses ne fonctionnent pas » n'est toujours pas qualifiée, faute de cas précis.

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
