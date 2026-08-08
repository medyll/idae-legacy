/**
 * Shared authentication + app-readiness helpers.
 *
 * The SPA boots through `main_bag.js`, which loads ~60 scripts sequentially via
 * bag.js. A cache-buster is appended to every entry, so nothing is ever served
 * from the bag cache and a cold boot takes 10-20s. Never assert on the DOM
 * before `waitForAppReady()` resolves.
 */
import type { Page } from '@playwright/test';
import { installShimPreview } from './shim-preview';

/**
 * 127.0.0.1, not "localhost": on Windows "localhost" resolves to ::1 first,
 * and Docker's WSL2 port-forward only binds IPv4. Chromium hides this behind
 * Happy Eyeballs (~250ms fallback), but Playwright's `request` fixture is
 * Node-side HTTP with `verbatim` DNS ordering and no fallback race, so every
 * fresh connection stalled ~21s on SYN retries before reaching the stack.
 * conf.lan.inc.php aliases the loopback addresses onto the `localhost` host
 * entry so the app's host detection accepts this.
 */
export const BASE = process.env.BASE_URL || 'http://127.0.0.1:8080';
export const USER = process.env.PLAYWRIGHT_USER || '';
export const PASS = process.env.PLAYWRIGHT_PASS || '';
export const TABLE = process.env.TEST_TABLE || 'client';
export const TABLE_VALUE = process.env.TEST_TABLE_VALUE || '';

/**
 * Fills and submits the in-page login form.
 *
 * Deliberately the *only* way this suite logs in. An earlier version had an
 * `apiLogin` shortcut that POSTed straight to `actions.php` and GETed
 * `json_ssid.php` from Node, bypassing the browser entirely — then had to
 * hand-replicate what the SPA's own login flow does client-side (mirroring
 * `PHPSESSID`/`SESSID` into `localStorage`, since the socket data channel
 * reads credentials from there, never from the cookie jar — see
 * `app_bootstrap_init.js`). That mirror was a guess at the client's
 * behavior, not the client's behavior, and would drift silently the moment
 * the real login flow changed. Given how much of this stack is still shaky
 * (BE_PLAN.md, HANG_TEST.md), the fixture exercises the exact path a real
 * user takes instead of a shortcut around it.
 *
 * Only reached when the stored session is missing or expired — the schema JSON
 * loads for anonymous visitors too, so an unauthenticated page still looks
 * "booted". `#desktop` is the real proof of a session.
 */
export async function uiLogin(page: Page): Promise<void> {
  const login = page.locator('input[name=loginAgent]');
  await login.waitFor({ state: 'visible', timeout: 15_000 });
  await login.fill(USER);
  await page.locator('input[name=passwordAgent]').fill(PASS);
  await page.locator('#formIdentificationUtilisateur input[type=submit], #formIdentificationUtilisateur button').first().click();
}

/**
 * Resolves once bag.js has drained its queue, `schemeLoad()` has populated
 * `window.APP.APPSCHEMES`, and the authenticated desktop shell is in the DOM.
 *
 * Deliberately does not key off `#main_progress_hold` being hidden: that panel
 * is faded out by Scriptaculous `Effect.Fade`, which the idae-be migration
 * removes. This condition stays valid on both sides of the swap.
 */
export async function waitForAppReady(page: Page, timeout = 20_000): Promise<void> {
  await page.waitForFunction(
    () => {
      const app = (window as any).APP;
      const schemesLoaded = !!app && !!app.APPSCHEMES && Object.keys(app.APPSCHEMES).length > 0;
      return schemesLoaded && !!document.getElementById('desktop');
    },
    undefined,
    { timeout }
  );
}

/** Navigates to the app root, logs in if needed, and waits for the boot to finish. */
export async function openApp(page: Page): Promise<void> {
  // SHIM_PREVIEW=1 swaps Prototype/Scriptaculous for the idae-be bundle +
  // shims at the network layer (see fixtures/shim-preview.ts).
  if (process.env.SHIM_PREVIEW === '1') {
    await installShimPreview(page);
  }
  await page.goto(BASE + '/', { waitUntil: 'domcontentloaded' });

  // Either the stored session already holds and the desktop appears, or the
  // login panel does and we authenticate through it.
  // 20s: measured directly (Chromium, real network, 2026-08-08) after the
  // per-file cache-busting fix (f4f090a) — cold boot to login-form-visible
  // is ~11.3s (106 requests), warm (bag.js IndexedDB cache hit) ~8.9s (16
  // requests). The "60-90s under load" figure this comment used to cite
  // predates that fix and was never re-measured; do not resurrect it as a
  // reason to widen this back out. 20s leaves ~2x headroom over the
  // measured cold-boot ceiling for CI/worker contention.
  const desktop = page.locator('#desktop');
  const loginForm = page.locator('input[name=loginAgent]');
  await Promise.race([
    desktop.waitFor({ state: 'attached', timeout: 20_000 }),
    loginForm.waitFor({ state: 'visible', timeout: 20_000 }),
  ]);

  if ((await desktop.count()) === 0) {
    await uiLogin(page);
  }
  await waitForAppReady(page);
}
