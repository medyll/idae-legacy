/**
 * Copy every image `appcss/dist/main.css` references into
 * `appcss/dist/images/`.
 *
 * `appcss/dist/` is gitignored and rebuilt, so anything the compiled CSS
 * points at has to be re-copied on every build or it 404s in the browser.
 * This used to be a single hardcoded `copyFileSync` for spinner.gif inside
 * the `build:css` one-liner, which meant the next missing image — `max16.png`
 * and `cancel.png`, found via the task-bar button — stayed broken until
 * someone happened to look at the network tab.
 *
 * Rather than a list to keep in sync, this parses the built CSS for
 * `images/<name>` references and resolves each against the known source
 * directories. An unresolved reference fails the build: a 404 in a
 * background-image is invisible until a user hits that exact screen.
 */
import { readFileSync, mkdirSync, copyFileSync, existsSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const cssPath = resolve(here, 'dist/main.css');
const outDir = resolve(here, 'dist/images');

// Ordered by preference: the LESS tree is what the legacy CSS was authored
// against, `css/images` holds what the rest of the app serves.
const sourceDirs = [
  resolve(here, 'less/images'),
  resolve(here, '../css/images'),
  resolve(here, 'generated/images'),
];

if (!existsSync(cssPath)) {
  console.error(`[copy-dist-images] ${cssPath} not found — run the sass build first.`);
  process.exit(1);
}

const css = readFileSync(cssPath, 'utf8');
const referenced = [...new Set([...css.matchAll(/images\/([A-Za-z0-9_.-]+)/g)].map((m) => m[1]))];

mkdirSync(outDir, { recursive: true });

const missing = [];
for (const name of referenced) {
  const source = sourceDirs.map((dir) => resolve(dir, name)).find(existsSync);
  if (!source) {
    missing.push(name);
    continue;
  }
  copyFileSync(source, resolve(outDir, name));
}

if (missing.length) {
  console.error(`[copy-dist-images] referenced by main.css but found in no source dir: ${missing.join(', ')}`);
  console.error(`[copy-dist-images] searched: ${sourceDirs.join(', ')}`);
  process.exit(1);
}

console.log(`[copy-dist-images] ${referenced.length} image(s) copied to appcss/dist/images/`);
