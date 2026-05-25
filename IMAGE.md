# Image Handling System — IDAE Application

## Overview

Images are stored in **MongoDB GridFS** (primary) with a **filesystem cache** layer for performance. Multiple GridFS collections serve different purposes (wallpapers, general images, documents, email attachments).

---

## 1. Configuration

**File:** `idae/web/conf.inc.php`

### Image Size Presets

```php
$IMG_SIZE_ARR = [
    'square'    => ['120', '120'],
    'small'     => ['210', '140'],
    'large'     => ['650', '430'],
    'wallpaper' => ['1920', '1080']
];
```

### Thumbnail Build Configuration

```php
$buildArr = [
    'tiny'        => ['100', '25'],
    'squary'      => ['68', '68'],
    'largy'       => ['325', '215'],
    'wallpapery'  => ['100', '25']
];
```

### Path Constants

| Constant | Value | Purpose |
|----------|-------|---------|
| `DOCUMENTROOT` | `SITEPATH` | Web root directory |
| `FLATTENIMGDIR` | `{CUSTOMERPATH}images_base/{CUSTOMERNAME}/` | Filesystem cache |
| `FLATTENIMGHTTP` | `{HTTPCUSTOMERSITE}images_base/{CUSTOMERNAME}/` | HTTP cache URL |
| `HTTPIMAGES` | `{HTTPCUSTOMERSITE}/images/` | General images |
| `ICONPATH` | `images/icones/` | Icon directory |

---

## 2. GridFS Collections

| Collection | Database | Purpose |
|------------|----------|---------|
| `wallpaper` | `sitebase_image` | User wallpapers |
| `fs` | `sitebase_image` | General images (default bucket) |
| `source` | `sitebase_image` | Original images (uncompressed) |
| `ged_bin` | `sitebase_ged` | Document storage |
| `ged_client` | `sitebase_ged` | Client documents |
| `email_attach` | `sitebase_email` | Email attachments |

### Access Pattern

```php
// Via plug_base (most common)
$APP->plug_base('sitebase_image')->getGridFs('wallpaper');
$APP->plug_base('sitebase_image')->getGridFs(); // defaults to 'fs'

// Via MongoCompat
MongoCompat::getGridFs('sitebase_image', 'wallpaper');
```

### GridFS Schema (Metadata)

```php
$ins = [
    'filename'       => $file_name,        // e.g., "produit-small-123.jpg"
    'size'           => $codeImage,         // e.g., "produit-small-123"
    'filesize'       => $_POST['filesize'],
    'filetype'       => $_POST['filetype'],
    'metatag'        => [],                 // Tags for categorization
    'table'          => $table,             // Parent table name
    'tag'            => $_POST['mongoTag'], // Same as table
    'time'           => time(),
    'date'           => date('Y-m-d'),
    'heure'          => date('H:i:s'),
    'idagent_owner'  => $_SESSION['idagent'],
    'real_filename'  => $_POST['filename'], // Original filename
    'thumb'          => 1,                  // Optional: thumb flag
    'idagent'        => $_SESSION['idagent'] // Optional: owner for wallpapers
];
```

---

## 3. Upload Handling

### A. Drag-and-Drop: `myddeAttach.js`

**File:** `idae/web/javascript/librairie/myddeAttach.js`

- Accepts: `image/png`, `image/jpeg`, `image/gif`
- Uses XMLHttpRequest with progress tracking
- Custom headers: `X_FILENAME`, `X-File-Name`, `X-File-Size`, `X-File-Type`
- Target: `mdl/app/app_img/actions.php?F_action=addDoc`

### B. Upload Action Handler

**File:** `idae/web/mdl/app/app_img/actions.php`

| Action | Line | Description |
|--------|------|-------------|
| `deleteImageMongo` | 20 | Delete from GridFS |
| `addDoc` | 33 | Main upload handler |
| `tagDocument` | 234 | Tag with metadata |
| `setmetadata` | 248 | Update metadata |
| `deleteDoc` | 262 | Delete document |
| `dropDoc` | 273 | Move between collections |
| `multiDoc` | 291 | Bulk operations |

**Upload Flow (`addDoc`):**
1. Validates `base`, `filename`, `table`, `table_value`
2. Determines image size from `$IMG_SIZE_ARR` based on `codeTailleImage`
3. Reads raw bytes from `php://input`
4. Stores original in GridFS `fs` collection with metadata
5. Generates thumbnails via `makeGdThumb()`
6. Writes flattened cache copy to filesystem
7. Triggers display reload

### C. Wallpaper Upload

**File:** `idae/web/mdl/app/app_user_pref/actions.php`

| Action | Line | Description |
|--------|------|-------------|
| `uploadWallPaper` | 9 | Saves to `DOCUMENTROOT/images/background/` |
| `setWallPaper` | 22 | Updates agent settings, stores in GridFS |
| `delWallPaper` | 45 | Deletes from filesystem |

### D. Thumbnail Generation

**File:** `idae/web/appfunc/function_site.php` (line 496)

```php
makeGdThumb($bytes, $codeTailleImage, $file_name, $tag, $table, $table_value);
```

Generates multiple sizes from original image, stores each variant in GridFS.

---

## 4. Display/Retrieval

### A. URL Rewriting (.htaccess)

```
RewriteRule ^(.*)appimg-(.*).(.*)$ appimgdsp.php?_id=$2
RewriteRule ^img_src-(.*).jpg$ mdl/app/app_img/app_img_src.php?image=$1&type=jpg
```

### B. Wallpaper Display

**File:** `idae/web/appimgdsp.php`

Retrieves from `sitebase_image.wallpaper` GridFS:

```php
$base = $APP->plug_base('sitebase_image')->getGridFs('wallpaper');
$dsp = $base->findOne(['filename' => $_GET['_id']]);
```

**Cache headers:**
```
Expires: Mon, 26 Jul 1997 05:00:00 GMT
Cache-Control: cache, must-revalidate
Content-type: image/jpg
```

### C. Image Source Resolver

**File:** `idae/web/appclasses/ClassAct.php` — `Act::imgSrc()` (lines 217-323)

1. Queries GridFS for image by filename
2. Creates filesystem cache in `FLATTENIMGDIR/{tag}/`
3. Returns HTTP URL `FLATTENIMGHTTP/{tag}/{filename}`

### D. Filesystem Cache

```
{FLATTENIMGDIR}/
  {table}/
    {image_name}.jpg
    {image_name}-square.jpg
    {image_name}-small.jpg
```

Example: `images_base/maw/produit/square/produit-small-123.jpg`

### E. Browser Caching (.htaccess)

```
ExpiresByType image/jpg "access plus 1 week"
Cache-Control: max-age=2592000, public
```

---

## 5. Reference Tracking

### Foreign Key Pattern

Images reference parent records via metadata:

```php
$name_id = 'id' . $table; // e.g., 'idclient', 'idproduit'
```

### Lookup Flow

1. Document in collection has image reference field (e.g., `imageClient`)
2. On display, `Act::imgSrc()` queries GridFS with `filename` or `metadata.table_value`
3. Returns URL to embed in `<img src="...">`

### Cleanup

Images are deleted via:
- `deleteImageMongo`: Removes by `metadata.iddocument`
- `deleteDoc`: Removes by `metadata.table` + `metadata.id{table}`
- Orphan images never cleaned automatically

---

## 6. Known Image Types

| Type Code | Dimensions | Usage |
|-----------|------------|-------|
| `thumb` | 50x50 | Auto-generated |
| `square` | 120x120 | Product thumbnails |
| `tiny` | 135x68 | List view thumbs |
| `small` | 210x140 | Small previews |
| `large` | 650x430 | Detail view |
| `long` | 1000x245 | Banner images |
| `wallpaper` | 1920x1080 | User wallpapers |

---

## 7. Key Functions

### PHP Image Functions

**File:** `idae/web/appfunc/function_site.php`

| Function | Line | Purpose |
|----------|------|---------|
| `imageBytesResize()` | 326 | Resize image from bytes |
| `gridImage()` | 355 | Get thumbnail from GridFS |
| `cropImage()` | 370 | Crop with coordinates |
| `thumbImage()` | 397 | Create thumbnail |
| `reflectImage()` | 408 | Create reflection |
| `makeGdThumb()` | 496 | Generate sized thumbnails |

### MongoCompat GridFS

**File:** `idae/web/appclasses/appcommon/MongoCompat.php`

| Method | Line | Purpose |
|--------|------|---------|
| `getGridFs()` | 312 | Get GridFS bucket |
| `getGridFsBucketName()` | 337 | Get bucket name |
| `storeBytes()` | 516 | Store file in GridFS |
| `MongoGridFS` class | 475 | GridFS wrapper |
| `MongoGridFSFile` class | 551 | File wrapper |

### Act Class

**File:** `idae/web/appclasses/ClassAct.php`

| Method | Line | Purpose |
|--------|------|---------|
| `imgSrc()` | 217 | Get image URL from GridFS |
| `imgApp()` | 497 | Get multiple image sizes |

---

## 8. Image URL Patterns

| Pattern | Handler | Source |
|---------|---------|--------|
| `appimg-{id}.jpg` | `appimgdsp.php` | GridFS `wallpaper` |
| `img_src-{table}-{size}-{id}.jpg` | `Act::imgSrc()` | GridFS `fs` + cache |
| `/images/icones/*` | Static files | Filesystem |

---

## 9. Complete Upload Flow

```
User drops image in myddeAttach
    │
    ├─ XHR POST to mdl/app/app_img/actions.php?F_action=addDoc
    │   ├─ Validates base, filename, table, table_value
    │   ├─ Reads bytes from php://input
    │   ├─ Stores original in GridFS 'fs' with metadata
    │   ├─ Calls makeGdThumb() → generates all size variants
    │   │   └─ Each variant stored in GridFS with size suffix
    │   ├─ Writes flattened cache to FLATTENIMGDIR/{table}/
    │   └─ Triggers reloadModule()
    │
    └─ Act::imgSrc() on next display
        ├─ Checks FLATTENIMGDIR for cached file
        │   └─ Returns FLATTENIMGHTTP URL if found
        └─ Falls back to GridFS query, writes to cache
```

---

## 10. Known Issues

1. **No orphan cleanup**: Images deleted from GridFS only when explicitly requested
2. **Wallpaper uses filesystem**: `uploadWallPaper` saves to filesystem, not GridFS
3. **delWallPaper is broken**: Tries `unlink()` on filesystem but wallpapers may be in GridFS
4. **No image compression**: Original images stored as-is in GridFS `source`
5. **Filename collisions**: No unique constraint on `filename` field in GridFS