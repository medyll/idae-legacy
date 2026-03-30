# MIGR-C — Guide technique de migration Idae vers Node.js / HTML moderne

> Référence pour recréer l'application Idae (CMS legacy PHP/MongoDB) en stack Node.js + HTML5 moderne.
> Date: 2026-03-30

---

## Table des matières

1. [Vue d'ensemble de l'architecture](#1-vue-densemble)
2. [Définition du schéma](#2-définition-du-schéma)
3. [API Node.js — endpoints essentiels](#3-api-nodejs--endpoints-essentiels)
4. [Gestion des droits](#4-gestion-des-droits)
5. [Navigation pilotée par le schéma](#5-navigation-pilotée-par-le-schéma)
6. [Composants UI essentiels](#6-composants-ui-essentiels)
7. [Chargement et bootstrap côté client](#7-chargement-et-bootstrap-côté-client)
8. [Flux de données CRUD](#8-flux-de-données-crud)
9. [Menu contextuel](#9-menu-contextuel)
10. [Cache client](#10-cache-client)
11. [Checklist d'implémentation](#11-checklist-dimplémentation)

---

## 1. Vue d'ensemble

L'application Idae est un **CMS entièrement piloté par un schéma MongoDB**. Il n'y a pas de code UI hardcodé par entité — tout (colonnes, champs de formulaire, libellés, icônes, FK) est décrit dans des collections `appscheme*` et consommé dynamiquement par le frontend.

```
MongoDB (appscheme*)
      │
      ▼
API Node.js  ──►  /api/scheme     → window.APP.APPSCHEMES
              ──►  /api/data       → CRUD sur toutes les entités
              ──►  /api/auth       → session + droits
              ──►  /api/csrf       → token CSRF
      │
      ▼
SPA HTML  (bootstrap → schemeLoad → render)
```

**Stack cible recommandée**
- **Backend** : Node.js (Express ou Fastify) + `mongodb` driver officiel v6+
- **Frontend** : HTML5 vanilla (ou Svelte 5) — pas de framework lourd requis
- **Session** : `express-session` + MongoDB store (`connect-mongodb-session`)
- **Auth** : session cookie `HttpOnly; Secure; SameSite=Strict`

---

## 2. Définition du schéma

### 2.1 Collections MongoDB

| Collection | Rôle | Clé primaire |
|---|---|---|
| `appscheme` | Définition des entités (tables) | `idappscheme` (int) |
| `appscheme_field` | Catalogue des champs réutilisables | `idappscheme_field` (int) |
| `appscheme_has_field` | Liaison champ ↔ entité | compound |
| `appscheme_has_table_field` | Champs affichés dans la grille, ordonnés | `ordreAppscheme_has_table_field` |
| `appscheme_field_type` | Registre des types (text, date, prix, fk…) | `codeAppscheme_field_type` |
| `appscheme_field_group` | Groupes UI (identification, commercial…) | `codeAppscheme_field_group` |
| `appscheme_type` | Valeurs d'énumération quand `hasTypeScheme=1` | `codeAppscheme_type` |
| `appscheme_base` | Namespace / hôte de base de données | `codeAppscheme_base` |

### 2.2 Document `appscheme` — champs essentiels

```json
{
  "idappscheme": 12,
  "codeAppscheme": "produit",
  "nomAppscheme": "Produits",
  "iconAppscheme": "fa-box",
  "hasTypeScheme": 0,
  "hasCodeScheme": 1,
  "hasOrdreScheme": 0,
  "hasRangeScheme": 0,
  "app_field_name_id": "idproduit",
  "app_field_name_nom": "nomProduit"
}
```

### 2.3 Règle de nommage des champs

```
nomChampStocké = codeAppscheme_field + ucfirst(codeAppscheme)
```

Exemples :
| `codeAppscheme_field` | `codeAppscheme` | Champ MongoDB |
|---|---|---|
| `nom` | `produit` | `nomProduit` |
| `dateCreation` | `client` | `dateCreationClient` |
| `code` | `categorie` | `codeCategorie` |

> **Règle absolue** : ne jamais deviner le nom — toujours le calculer via cette formule.

### 2.4 Document `appscheme_field` — champs essentiels

```json
{
  "idappscheme_field": 5,
  "codeAppscheme_field": "nom",
  "nomAppscheme_field": "Nom",
  "codeAppscheme_field_type": "text",
  "codeAppscheme_field_group": "identification",
  "iconAppscheme_field": "fa-tag"
}
```

### 2.5 Types de champs (`codeAppscheme_field_type`)

| Type | Rendu UI | CSS class |
|---|---|---|
| `text` | `<input type="text">` | `css_field_text` |
| `date` | date picker | `date_field` |
| `heure` | time picker | `heure_field` |
| `color` | color picker | `color_field` |
| `prix` | montant formaté | `css_field_prix` |
| `fk` | select FK | `fk` |
| *(vide)* | FK implicite | `fk` |

### 2.6 Groupes de champs (`codeAppscheme_field_group`)

- `codification` — codes de référence
- `identification` — champs d'identité principaux (nom, prénom…)
- `commercial` — données commerciales
- `adresse` — bloc adresse (déclenche l'expansion `codePostal` + `ville`)
- `technique` — données techniques

---

## 3. API Node.js — endpoints essentiels

### 3.1 `/api/scheme` — schéma complet

Retourne un tableau d'objets, un par entité, avec les modèles pré-assemblés.

```js
// GET /api/scheme
// Retourne : Array<AppSchemeNode>

{
  "codeAppscheme": "produit",
  "nomAppscheme": "Produits",
  "iconAppscheme": "fa-box",
  "app_field_name_id": "idproduit",
  "app_field_name_nom": "nomProduit",
  "columnModel":  [ FieldDef ],   // colonnes grille par défaut (identification + FK)
  "defaultModel": [ FieldDef ],   // vue personnalisable (appscheme_has_table_field)
  "fieldModel":   [ FieldDef ],   // tous les champs déclarés
  "miniModel":    [ FieldDef ],   // champs mini-fiche
  "hasModel":     [ FieldDef ]    // champs identification uniquement, sans FK
}
```

```ts
interface FieldDef {
  field_name:       string;  // ex: "nomProduit"
  field_name_raw:   string;  // ex: "nom"
  field_name_group: string;  // ex: "identification"
  title:            string;  // libellé affiché
  className?:       string;  // CSS class
  icon?:            string;  // icône FontAwesome
}
```

### 3.2 `/api/scheme/fields` — catalogue des champs

```js
// GET /api/scheme/fields
// Retourne : Record<string, FieldDef[]>  — indexé par codeAppscheme
```

### 3.3 `/api/data` — CRUD générique

```
GET    /api/data/:table              → liste (avec pagination, tri, filtres)
GET    /api/data/:table/:id          → fiche unique
POST   /api/data/:table              → création
PUT    /api/data/:table/:id          → mise à jour
DELETE /api/data/:table/:id          → suppression
```

**Paramètres GET liste** :
```
?limit=50&offset=0&sort=nomProduit:asc&filter[actifProduit]=1
```

**Réponse liste** :
```json
{
  "data": [ { "idproduit": 1, "nomProduit": "Exemple" } ],
  "total": 142,
  "limit": 50,
  "offset": 0
}
```

### 3.4 `/api/data/search` — recherche full-text

```
GET /api/data/:table/search?q=terme&fields=nomProduit,codeProduit
```

### 3.5 `/api/csrf` — token CSRF

```js
// GET /api/csrf
{ "token": "abc123..." }
```

Toutes les requêtes mutantes (POST/PUT/DELETE) doivent inclure ce token dans le header `X-CSRF-Token`.

### 3.6 `/api/auth` — authentification

```
POST /api/auth/login    { login, password }  → session + cookie
POST /api/auth/logout
GET  /api/auth/me       → { idagent, login, groupe, droits }
```

---

## 4. Gestion des droits

### 4.1 Collections

```
agent  ──► agent_groupe  ──► agent_groupe_droit  ──► (table, C, R, U, D, L, CONF)
```

**`agent`** :
```json
{ "idagent": 3, "loginAgent": "jdupont", "idagent_groupe": 2 }
```

**`agent_groupe_droit`** :
```json
{
  "idagent_groupe": 2,
  "codeAppscheme": "produit",
  "C": true,   // Create
  "R": true,   // Read
  "U": true,   // Update
  "D": false,  // Delete
  "L": true,   // List
  "CONF": false
}
```

### 4.2 Fonctions de vérification (Node.js)

```js
// Vérifie une opération sur une table
async function droitTable(idagent, code, table) {
  const agent = await db.collection('agent').findOne({ idagent });
  const droit = await db.collection('agent_groupe_droit').findOne({
    idagent_groupe: agent.idagent_groupe,
    codeAppscheme: table
  });
  return droit?.[code] === true;
}

// Retourne la liste des tables autorisées pour un code
async function droitTableMulti(idagent, code) {
  const agent = await db.collection('agent').findOne({ idagent });
  const droits = await db.collection('agent_groupe_droit')
    .find({ idagent_groupe: agent.idagent_groupe, [code]: true })
    .toArray();
  return droits.map(d => d.codeAppscheme);
}

// Vérifie un flag applicatif (ADMIN, DEV, CONF)
async function droit(idagent, code) {
  const agent = await db.collection('agent').findOne({ idagent });
  return agent?.[code] === true;
}
```

### 4.3 Middleware Express

```js
function requireDroit(code) {
  return async (req, res, next) => {
    const idagent = req.session.idagent;
    const table = req.params.table;
    if (!idagent) return res.status(401).json({ error: 'Non authentifié' });
    const ok = await droitTable(idagent, code, table);
    if (!ok) return res.status(403).json({ error: 'Accès refusé' });
    next();
  };
}

// Usage :
router.get('/data/:table',        requireDroit('R'), listHandler);
router.post('/data/:table',       requireDroit('C'), createHandler);
router.put('/data/:table/:id',    requireDroit('U'), updateHandler);
router.delete('/data/:table/:id', requireDroit('D'), deleteHandler);
```

---

## 5. Navigation pilotée par le schéma

### 5.1 Principe

La navigation **n'est pas codée en dur**. Le menu est généré à partir de `window.APP.APPSCHEMES` filtré par les tables autorisées (`droitTableMulti(idagent, 'L')`).

### 5.2 Flux de navigation

```
1. Boot → GET /api/auth/me       → charge idagent + droits
2. Boot → GET /api/scheme        → charge window.APP.APPSCHEMES
3. Render menu → filtrer APPSCHEMES par tables où L=true
4. Clic menu → router.navigate('/table/produit')
5. Router → instancie le composant ListeView(scheme['produit'])
6. ListeView → GET /api/data/produit → render colonnes depuis columnModel
7. Clic ligne → router.navigate('/table/produit/42')
8. Router → instancie FicheView(scheme['produit'], id=42)
```

### 5.3 Structure du router

```js
const routes = {
  '/':                    () => DashboardView(),
  '/table/:table':        ({ table }) => ListeView(APP.APPSCHEMES[table]),
  '/table/:table/:id':    ({ table, id }) => FicheView(APP.APPSCHEMES[table], id),
  '/table/:table/new':    ({ table }) => FicheView(APP.APPSCHEMES[table], null),
};
```

### 5.4 Génération du menu

```js
async function buildMenu(idagent) {
  const tablesAutorisees = await fetch('/api/data/rights?code=L').then(r => r.json());
  return Object.values(APP.APPSCHEMES)
    .filter(s => tablesAutorisees.includes(s.codeAppscheme))
    .map(s => ({
      label: s.nomAppscheme,
      icon:  s.iconAppscheme,
      href:  `/table/${s.codeAppscheme}`
    }));
}
```

---

## 6. Composants UI essentiels

### 6.1 `ListeView` — grille de données

**Entrée** : `scheme` (un nœud de `APPSCHEMES`), droits

**Comportement** :
- Colonnes = `scheme.columnModel` (défaut) ou `scheme.defaultModel` (personnalisé)
- Chaque colonne a `field_name`, `title`, `className`, `icon`
- Tri, pagination, filtre rapide en query string
- Le champ `nom<Table>` reçoit la classe `main_field`
- Les FK reçoivent la classe `fk` et affichent le champ de l'entité liée

```html
<table class="idae-grid">
  <thead>
    <tr>
      <!-- pour chaque col dans scheme.columnModel -->
      <th class="${col.className}" data-field="${col.field_name}">
        <i class="fa ${col.icon}"></i> ${col.title}
      </th>
    </tr>
  </thead>
  <tbody>
    <!-- pour chaque row dans data -->
    <tr data-id="${row[scheme.app_field_name_id]}" data-contextual="${scheme.codeAppscheme}:${id}">
      <!-- pour chaque col -->
      <td class="${col.className}">${row[col.field_name]}</td>
    </tr>
  </tbody>
</table>
```

### 6.2 `FicheView` — formulaire entité

**Entrée** : `scheme`, `id` (null = création)

**Comportement** :
- Champs = `scheme.fieldModel` groupés par `field_name_group`
- Chaque groupe = `<fieldset>` avec `<legend>`
- Le type de champ (`field_name_group`, `className`) détermine le widget
- FK → `<select>` chargé via `/api/data/{table_fk}?fields=id,nom`

```js
function renderField(fieldDef, value) {
  switch (fieldDef.className) {
    case 'date_field':   return `<input type="date" name="${fieldDef.field_name}" value="${value}">`;
    case 'color_field':  return `<input type="color" name="${fieldDef.field_name}" value="${value}">`;
    case 'fk':           return FkSelect(fieldDef, value);
    default:             return `<input type="text" name="${fieldDef.field_name}" value="${value}">`;
  }
}
```

### 6.3 `MiniFiche` — panneau résumé

Utilise `scheme.miniModel` — affichage compact d'une entité.

```html
<div class="mini-fiche">
  <!-- pour chaque field dans scheme.miniModel -->
  <span class="${field.className}">
    <i class="fa ${field.icon}"></i>
    <label>${field.title}</label>
    <value>${data[field.field_name]}</value>
  </span>
</div>
```

### 6.4 `FkSelect` — champ de clé étrangère

```js
async function FkSelect(fieldDef, currentValue) {
  // table FK déduite de field_name_raw ou de field_name_group
  const fkTable = fieldDef.field_name_raw; // ex: "categorie"
  const fkScheme = APP.APPSCHEMES[fkTable];
  const items = await fetch(`/api/data/${fkTable}?limit=500`).then(r => r.json());

  return `<select name="${fieldDef.field_name}">
    ${items.data.map(item =>
      `<option value="${item[fkScheme.app_field_name_id]}"
        ${item[fkScheme.app_field_name_id] == currentValue ? 'selected' : ''}>
        ${item[fkScheme.app_field_name_nom]}
      </option>`
    ).join('')}
  </select>`;
}
```

---

## 7. Chargement et bootstrap côté client

### 7.1 Ordre de chargement

```
1. GET /api/csrf                   → window.APP.CSRF_TOKEN
2. GET /api/auth/me                → window.APP.SESSION (idagent, droits)
3. GET /api/scheme                 → window.APP.APPSCHEMES  (indexé par codeAppscheme)
4. GET /api/scheme/fields          → window.APP.APPFIELDS   (indexé par codeAppscheme)
5. buildMenu()                     → rendu du menu
6. router.init()                   → parse URL → render vue initiale
```

### 7.2 Structure `window.APP`

```js
window.APP = {
  CSRF_TOKEN: '',
  SESSION: { idagent: 0, login: '', groupe: '', isAdmin: false },
  APPSCHEMES: {},   // Record<codeAppscheme, AppSchemeNode>
  APPFIELDS: {},    // Record<codeAppscheme, FieldDef[]>
};
```

### 7.3 Appel AJAX sécurisé

```js
async function apiCall(method, url, body = null) {
  const opts = {
    method,
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-Token': window.APP.CSRF_TOKEN,
    },
    credentials: 'same-origin',
  };
  if (body) opts.body = JSON.stringify(body);
  const res = await fetch(url, opts);
  if (!res.ok) throw new Error(`${res.status} ${res.statusText}`);
  return res.json();
}
```

---

## 8. Flux de données CRUD

### 8.1 Lecture liste

```
Client → GET /api/data/produit?limit=50&offset=0
Server → droitTable(idagent, 'L', 'produit')
       → db.collection('produit').find({}).skip(0).limit(50)
       → { data: [...], total: N }
Client → render ListeView avec scheme.columnModel
```

### 8.2 Création

```
Client → POST /api/data/produit  { nomProduit: "Foo", codeProduit: "F01" }
       → Header: X-CSRF-Token: <token>
Server → droitTable(idagent, 'C', 'produit')
       → valider champs obligatoires (champs groupe 'identification')
       → insérer + retourner { id: <newId> }
Client → redirect vers /table/produit/<newId>
```

### 8.3 Mise à jour

```
Client → PUT /api/data/produit/42  { nomProduit: "Bar" }
Server → droitTable(idagent, 'U', 'produit')
       → db.collection('produit').updateOne({ idproduit: 42 }, { $set: body })
```

### 8.4 Suppression

```
Client → DELETE /api/data/produit/42
Server → droitTable(idagent, 'D', 'produit')
       → vérifier absence de dépendances FK
       → db.collection('produit').deleteOne({ idproduit: 42 })
```

---

## 9. Menu contextuel

Le menu contextuel s'active via `data-contextual` sur chaque ligne.

```html
<!-- Sur chaque <tr> de la grille -->
<tr data-contextual="produit:42">
```

```js
document.addEventListener('contextmenu', async (e) => {
  const target = e.target.closest('[data-contextual]');
  if (!target) return;
  e.preventDefault();

  const [table, id] = target.dataset.contextual.split(':');
  const scheme = APP.APPSCHEMES[table];

  // Construire le menu selon droits
  const items = [];
  if (await droitTable(APP.SESSION.idagent, 'R', table))
    items.push({ label: 'Voir', action: () => navigate(`/table/${table}/${id}`) });
  if (await droitTable(APP.SESSION.idagent, 'U', table))
    items.push({ label: 'Modifier', action: () => navigate(`/table/${table}/${id}/edit`) });
  if (await droitTable(APP.SESSION.idagent, 'D', table))
    items.push({ label: 'Supprimer', action: () => confirmDelete(table, id) });

  showContextMenu(e.pageX, e.pageY, items);
});
```

---

## 10. Cache client

- Utiliser `localStorage` ou `IndexedDB` (via `idbql`) pour cacher `APPSCHEMES` et `APPFIELDS`
- Invalider le cache à chaque changement de schéma (version hash ou `ETag`)
- Les données métier ne doivent **pas** être cachées longtemps — préférer un TTL court (5 min)

```js
const SCHEME_CACHE_KEY = 'idae_appschemes_v1';
const SCHEME_TTL = 60 * 60 * 1000; // 1h

async function loadSchemes() {
  const cached = JSON.parse(localStorage.getItem(SCHEME_CACHE_KEY) || 'null');
  if (cached && Date.now() - cached.ts < SCHEME_TTL) {
    return cached.data;
  }
  const data = await apiCall('GET', '/api/scheme');
  localStorage.setItem(SCHEME_CACHE_KEY, JSON.stringify({ ts: Date.now(), data }));
  return data;
}
```

---

## 11. Checklist d'implémentation

### Backend Node.js
- [ ] Connexion MongoDB avec `mongodb` driver v6+
- [ ] Endpoint `GET /api/scheme` — assemblage `columnModel`, `defaultModel`, `fieldModel`, `miniModel`, `hasModel`
- [ ] Endpoint `GET /api/scheme/fields`
- [ ] Endpoints CRUD `/api/data/:table`
- [ ] Endpoint `GET /api/csrf` avec token rotatif
- [ ] Endpoints auth `/api/auth/login|logout|me`
- [ ] Middleware `requireDroit(code)` sur tous les endpoints data
- [ ] Calcul automatique du nom de champ : `field + ucfirst(table)`
- [ ] Gestion des FK : jointure ou lookup MongoDB pour enrichir les listes

### Frontend
- [ ] Bootstrap séquentiel : CSRF → auth → scheme → fields → menu → router
- [ ] `window.APP.APPSCHEMES` indexé par `codeAppscheme`
- [ ] Composant `ListeView` pilotée par `columnModel`
- [ ] Composant `FicheView` groupée par `field_name_group`
- [ ] Composant `MiniFiche` pilotée par `miniModel`
- [ ] `FkSelect` async pour les champs FK
- [ ] Router hash-based ou History API
- [ ] Menu généré depuis APPSCHEMES filtré par droits `L`
- [ ] Menu contextuel sur `[data-contextual]`
- [ ] Cache localStorage pour les schémas
- [ ] Header `X-CSRF-Token` sur toutes les mutations

### Sécurité
- [ ] Sessions `HttpOnly; Secure; SameSite=Strict`
- [ ] CSRF token vérifié côté serveur sur POST/PUT/DELETE
- [ ] Validation des entrées avant insertion MongoDB
- [ ] Aucune sortie debug dans les réponses JSON

---

## Annexe — Exemple de nœud `APPSCHEMES` complet

```json
{
  "codeAppscheme": "produit",
  "nomAppscheme": "Produits",
  "iconAppscheme": "fa-box",
  "app_field_name_id": "idproduit",
  "app_field_name_nom": "nomProduit",
  "hasTypeScheme": 0,
  "hasCodeScheme": 1,
  "columnModel": [
    { "field_name": "nomProduit",  "field_name_raw": "nom",  "title": "Nom",       "className": "main_field", "icon": "fa-tag" },
    { "field_name": "codeProduit", "field_name_raw": "code", "title": "Code",      "className": "css_field_text" },
    { "field_name": "nomCategorie","field_name_raw": "categorie", "title": "Catégorie", "className": "fk", "icon": "fa-folder" }
  ],
  "defaultModel": [ /* idem + champs personnalisés */ ],
  "fieldModel": [ /* tous les champs de l'entité */ ],
  "miniModel": [
    { "field_name": "nomProduit",  "title": "Nom",  "className": "main_field" },
    { "field_name": "prixProduit", "title": "Prix", "className": "css_field_prix" }
  ],
  "hasModel": [
    { "field_name": "nomProduit",  "title": "Nom" },
    { "field_name": "codeProduit", "title": "Code" }
  ]
}
```
