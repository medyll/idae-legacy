# Schema-driven metadata (appscheme*)

This document summarizes the role and relationships of schema collections commonly named `appscheme*` (for example: `appscheme`, `appscheme_field`, `appscheme_has_field`, `appscheme_has_table_field`, `appscheme_group`, `appscheme_type`). It is intentionally implementation-agnostic and focuses on the metadata concepts, conventions, and runtime responsibilities used to drive dynamic UIs (forms, grids) and data naming.

## Purpose

- The platform is schema-driven: entities (tables) and their fields are defined in MongoDB metadata collections rather than in static SQL/DDL. The runtime reads these collections to build UI models (forms, grids) and to compute the real field names stored in data collections.

## Collections and roles

- `appscheme` — entity/table definition (meta-table).
  - Key fields: `idappscheme`, `codeAppscheme` (e.g. `produit`), `nomAppscheme` (label), flags like `hasTypeScheme`, `hasCodeScheme`, `hasOrdreScheme`.
  - Role: top-level declaration of an application entity. The server iterates `appscheme` to build application metadata used by the client.

- `appscheme_field` — catalog of reusable field definitions.
  - Key fields: `idappscheme_field`, `codeAppscheme_field` (e.g. `nom`, `prix`), `nomAppscheme_field` (label), `codeAppscheme_field_type` (type), `codeAppscheme_field_group`, `iconAppscheme_field`, `options`.
  - Role: canonical description of a field that can be reused across entities.

- `appscheme_field_type` — field type registry.
  - Key fields: `idappscheme_field_type`, `codeAppscheme_field_type` (e.g. `text`, `number`, `date`, `prix`, `fk`), `nomAppscheme_field_type` (human label), `renderer`/`validation` hints.
  - Role: describes available field types, rendering/formatting rules and any type-specific metadata (for example `prix` may imply currency formatting, `date` implies date formatting). Systems use this registry to map `codeAppscheme_field_type` from `appscheme_field` to presentation and validation logic.

- `appscheme_field_group` — grouping for fields used by UIs.
  - Key fields: `idappscheme_field_group`, `codeAppscheme_field_group` (e.g. `identification`, `commercial`), `nomAppscheme_field_group`, `iconAppscheme_field_group`, `ordreAppscheme_field_group`.
  - Role: groups fields into sections/tabs/cards in forms and templates. Templates read field groups to render section headers, icons and ordering. `appscheme_field` entries reference a group by `codeAppscheme_field_group` or `idappscheme_field_group`.

- `appscheme_icon` / `iconAppscheme` (icon fields on schemes and fields).
  - Usage: both scheme documents and field/group documents commonly carry icon fields (e.g. `iconAppscheme` on `appscheme`, `iconAppscheme_field` on `appscheme_field`, `iconAppscheme_field_group` on `appscheme_field_group`).
  - Role: lightweight UI hint used by templates to include an icon next to a table, field group or individual field. Typical values are font icon names (Font Awesome class suffixes like `tag`, `euro-sign`, or full `fa-tag`), and client templates usually render them as `<i class="fa fa-ICON"></i>`.
  - Notes: icon fields are optional and purely presentational; code should gracefully fall back to a default icon when missing.

- `appscheme_has_field` — association table: which `appscheme_field` are declared on a given `appscheme`.
  - Role: when building an entity's schema, the server reads `appscheme_has_field` to know which fields belong to that entity and with which flags (order, `in_mini_fiche`, visibility, etc.).

- `appscheme_has_table_field` — association for table-columns that reference fields from other tables.
  - Role: used to build grids/column models that include fields coming from linked entities; contains links like `idappscheme_field` and `idappscheme_link` (source table).

- `appscheme_group` / `appscheme_field_group` — logical groups of fields (sections, tabs, groups such as `identification`, `codification`).
  - Role: used by templates to render field groups and to structure forms.

- `appscheme_type` — enumerations / taxonomy values linked to an entity if `hasTypeScheme` is set.
  - Role: store type rows (e.g. product types) and cause the server to add a `nom<Table>_type` column when `hasTypeScheme` is true.

- `appscheme_base` — scheme base / host / namespace.
  - Typical fields: `idappscheme_base`, `codeAppscheme_base` (e.g. `sitebase_app`), `nomAppscheme_base` (label), possibly `mainscope_app` or other descriptive fields.
  - Role: groups and namespaces schemes; declares the logical "base" (database/host/namespace) where a scheme's collections live. Implementations use the base code to select or prefix the physical database name (for example via a `plug_base(base)` helper that returns a DB handle for `MDB_PREFIX + base`).
  - Usage notes:
    - Each `appscheme` document typically contains `codeAppscheme_base`/`nomAppscheme_base` to indicate the base it belongs to. UI and admin tools group schemes by base to help manage multi‑base/multi‑tenant setups.
    - Runtime code uses the base to `plug` into the correct database/collection (see conceptual `plug_base(base)` + `plug(base, table)` behavior). This allows different scheme collections to be stored in different MongoDB databases or logical hosts while keeping a single metadata model.
    - Common base examples in the codebase: `sitebase_app`, `sitebase_image`, `sitebase_pref`, `sitebase_sync` — each representing a logical database used for a particular purpose.

## Naming convention

- Real stored field name = `codeAppscheme_field` + ucfirst(`codeAppscheme`).
  - Example: field `nom` for table `produit` → stored field `nomProduit`.
- `appscheme_field` definitions are reusable; `appscheme_has_field` is the per-entity binding that finalizes usage (order, mini view, etc.).

## How the server assembles metadata (summary)

- At runtime the server (see `services/json_scheme.php`) does:
  1. Iterate `appscheme` records.
  2. For each `appscheme` (entity):
     - Fetch `appscheme_has_field` for declared fields.
     - Fetch `appscheme_field` for each referenced field to obtain type, label, icon, and group.
     - Fetch `appscheme_has_table_field` for columns that reference fields in other tables and build `columnModel`/`defaultModel` entries.
     - Build JSON structures used by the client: `fieldModel`, `miniModel`, `columnModel`, `defaultModel`, `hasModel`.
  3. Return an array of assembled `app_table_one` objects describing each entity.

These structures are consumed by the client (populates `APP.APPSCHEMES`) and drive dynamic UI generation (forms, grids, validation, labels, icons).

## Important flags that change generated models

- `hasTypeScheme` — causes server to add a `nom<Table>_type` column to the `columnModel`.
- `hasCodeScheme`, `hasOrdreScheme`, `hasRangeScheme` — modify generated `updateModel` / editable columns.

## `grilleFK` (forward foreign-key grids)

- Typical storage: an array field on an entity's scheme document (commonly named `grilleFK`) where each element declares a target table (and optional metadata such as `uid`, `ordreTable`, `table`).
- Purpose: declare forward relationships that should be displayed as embedded "grids" or linked lists inside the UI of the declaring entity. Think of `grilleFK` as a scheme-level hint: "when rendering entity A, also show related rows from entity B as a grid." 
- Typical runtime behavior (conceptual, implementation-agnostic):
  - Read the `grilleFK` array on the source entity scheme.
  - Resolve each referenced target table's scheme metadata (label, icons, primary id and name fields, base/collection identifiers).
  - Optionally filter out auxiliary schemes (status/type/category/group enumerations) if a system marks them as such.
  - Return a list keyed by the target table with metadata useful to fetch and render the grid client-side: target collection name, display label, id field name, display-name field, icons, ordering, etc.
  - The resulting metadata drives API responses and client rendering (for example adding entries under a `grille_FK` key in table data responses).

## Reverse `reverseGrilleFK` (reverse foreign-key relations)

- Purpose: discover which other entity schemes declare the current table in their `grilleFK`, and optionally enumerate or count related records for a specific record (reverse/derived relationships).
- Typical runtime behavior (conceptual):
  - Query scheme metadata across all entities to find schemes whose `grilleFK` references the target table.
  - For each referencing scheme, optionally query the referencing collection to count rows (or fetch a subset) related to a given primary id value of the target table.
  - Return a keyed structure that includes the referencing scheme's collection name, label, icon, scope/type, count of related rows, and optionally a sample list or full list depending on the usage.
- Common uses:
  - UI pages that show "reverse" lists (for example: on a `product` page, show lists of `order` rows that reference this product).
  - API endpoints that include reverse counts or lists in the object returned for a single record.

## Where this is commonly integrated

- API services that return table data often include both forward `grilleFK` metadata and reverse `grilleFK` results so clients can render embedded related grids and reverse lists. Implementations vary, but both forward and reverse semantics are useful for dynamic UIs driven by scheme metadata.



## Representative JSON examples

### Example: `services/json_scheme.php` output for `produit` (representative)

```json
{
  "codeAppscheme": "produit",
  "nomAppscheme": "Produit",
  "hasTypeScheme": 1,
  "hasCodeScheme": 1,
  "app_table_one": {
    "codeAppscheme": "produit",
    "nomAppscheme": "Produit",
    "hasTypeScheme": 1,
    "hasCodeScheme": 1
  },
  "columnModel": [
    {"field_name":"nomProduit","field_name_raw":"nom","title":"Nom","className":"main_field","iconAppscheme":"fa-tag"},
    {"field_name":"prixProduit","field_name_raw":"prix","title":"Prix","className":"css_field_number","iconAppscheme":"fa-euro-sign"},
    {"field_name":"nomProduit_type","field_name_raw":"nomProduit_type","title":"Type Produit","className":""}
  ],
  "defaultModel": [
    {"field_name":"nomProduit","field_name_raw":"nom","title":"Nom"},
    {"field_name":"prixProduit","field_name_raw":"prix","title":"Prix"}
  ],
  "hasModel": [
    {"field_name":"nomProduit","field_name_raw":"nom","title":"Nom"}
  ],
  "fieldModel": [
    {"field_name":"nomProduit","field_name_raw":"nom","field_name_group":"identification","iconAppscheme":"fa-tag","title":"Nom"},
    {"field_name":"prixProduit","field_name_raw":"prix","field_name_group":"commercial","iconAppscheme":"fa-euro-sign","title":"Prix"}
  ],
  "miniModel": [
    {"field_name":"nomProduit","field_name_raw":"nom","field_name_group":"identification","className":"main_field","iconAppscheme":"fa-tag","title":"Nom"}
  ]
}
```

### Example: `services/json_scheme_field.php` output (indexed by `codeAppscheme_field`)

```json
{
  "nom": {
    "idappscheme_field": 10,
    "codeAppscheme_field": "nom",
    "nomAppscheme_field": "Nom",
    "codeAppscheme_field_type": "text",
    "codeAppscheme_field_group": "identification",
    "iconAppscheme_field": "fa-tag",
    "options": {}
  },
  "prix": {
    "idappscheme_field": 11,
    "codeAppscheme_field": "prix",
    "nomAppscheme_field": "Prix",
    "codeAppscheme_field_type": "number",
    "codeAppscheme_field_group": "commercial",
    "iconAppscheme_field": "fa-euro-sign",
    "options": {"min":0}
  }
}
```

## Mermaid diagram (relations)

```mermaid
flowchart TB
  subgraph SchemaCollections
    AS[appscheme]
    AF[appscheme_field]
    AHF[appscheme_has_field]
    AHTF[appscheme_has_table_field]
    AG[appscheme_group]
    AT[appscheme_type]
  end

  AS -->|declares| AHF
  AS -->|uses (types)| AT
  AS -->|includes inter-table columns| AHTF
  AHF -->|references| AF
  AF -->|belongs to| AG
```

## Next steps / actionable items

- If you want the real, live JSON for a specific table from your environment, I can run a small PHP script or call the local endpoint `services/json_scheme.php?piece=scheme` (or `piece=fields`) and dump the actual output for a table like `produit`.
- I can also generate a more detailed ER-style diagram or a UML-like depiction if needed.

## Common default fields present on records

- Most data collections follow a set of common fields that UIs and templates expect. Naming typically follows the pattern `dateCreation<Table>` and `id<Table>` where `<Table>` is the capitalized collection code.
- Typical fields you will find (names vary slightly by installation but follow the pattern below):
  - `dateCreation<Table>` / `heureCreation<Table>` / `time<Table>` — creation timestamps (date, time, unix time).
  - `ordre` or `ordre<Table>` — ordering / position value used by lists and custom sorting.
  - `color` or `colorAppscheme` (scheme-level) — color hint used in cards and list headers; some installations also allow `color<Table>` per-record overrides.
  - `icon` or `iconAppscheme` (scheme-level) — icon hint for the scheme; templates can also use a per-record `icon<Table>` override.

- Notes:
  - `color` and `icon` are primarily present on scheme documents (`appscheme`) as `colorAppscheme` and `iconAppscheme` to style the entity; per-record fields are optional and used when records override the scheme defaults.
  - Templates and server helpers frequently reference these names directly (examples in templates: `{dateCreationDevis}`, `colorAppscheme`).

---

Generated: Modified automatically by Copilot assistant.
