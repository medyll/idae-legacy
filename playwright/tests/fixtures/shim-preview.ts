/**
 * Shim preview — runs the whole suite against the idae-be bundle + shims
 * instead of Prototype/Scriptaculous, without touching main_bag.js.
 *
 * Enabled by setting SHIM_PREVIEW=1. openApp() registers request routes
 * before the first navigation:
 *   - prototype-1.7.3.js is fulfilled with the concatenation of the idae-be
 *     IIFE bundle and the seven shim files (in load order),
 *   - the three Scriptaculous files are fulfilled with an empty script.
 *
 * This is the Phase 4 swap, previewed through the network layer: the same
 * scripts the loader will eventually serve, same position in require_hell.
 */
import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';
import type { Page } from '@playwright/test';

// Resolved from the working directory (playwright/) rather than __dirname,
// which shifts depending on how the TS is loaded (tsc, tsx, type-stripping).
const WEB_ROOT = resolve(process.cwd(), '../idae/web');
const SHIM_DIR = 'javascript/vendor/idae-be-shim';

const SHIM_FILES = [
  'javascript/vendor/idae-be/idae-be.iife.js',
  `${SHIM_DIR}/shim-core.js`,
  `${SHIM_DIR}/shim-class.js`,
  `${SHIM_DIR}/shim-enumerable.js`,
  `${SHIM_DIR}/shim-element.js`,
  `${SHIM_DIR}/shim-event.js`,
  `${SHIM_DIR}/shim-ajax.js`,
  `${SHIM_DIR}/shim-effects.js`,
];

let cachedBundle: string | null = null;

function shimBundle(): string {
  if (cachedBundle) return cachedBundle;
  cachedBundle = SHIM_FILES
    .map((file) => readFileSync(resolve(WEB_ROOT, file), 'utf8'))
    .join('\n;\n');
  return cachedBundle;
}

/** Registers the require_hell replacement routes on the page. */
export async function installShimPreview(page: Page): Promise<void> {
  await page.route(/vendor\/prototype\/prototype-1\.7\.3\.js/, (route) =>
    route.fulfill({ contentType: 'application/javascript', body: shimBundle() })
  );
  await page.route(/vendor\/scriptaculous\/(scriptaculous|effects|dragdrop)\.js/, (route) =>
    route.fulfill({ contentType: 'application/javascript', body: '// replaced by idae-be shim (SHIM_PREVIEW)' })
  );
}
