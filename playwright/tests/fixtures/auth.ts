/**
 * Shared authentication + app-readiness helpers.
 *
 * The SPA boots through `main_bag.js`, which loads ~60 scripts sequentially via
 * bag.js. A cache-buster is appended to every entry, so nothing is ever served
 * from the bag cache and a cold boot takes 10-20s. Never assert on the DOM
 * before `waitForAppReady()` resolves.
 */
import type { APIRequestContext, Page } from '@playwright/test';
import { installShimPreview } from './shim-preview';

export const BASE = process.env.BASE_URL || 'http://localhost:8080';
export const USER = process.env.PLAYWRIGHT_USER || '';
export const PASS = process.env.PLAYWRIGHT_PASS || '';
export const TABLE = process.env.TEST_TABLE || 'client';
export const TABLE_VALUE = process.env.TEST_TABLE_VALUE || '';

export interface ApiSession {
  phpsessid: string;
  /** `idagent`, mirrored into `localStorage.SESSID` by the app's own login flow. */
  sessid: string;
}

/**
 * Server-side login. Returns the identifiers the SPA itself mirrors into
 * `localStorage` after a real form login (see `mdl/app/app_login/actions.php`
 * and `javascript/app/app_bootstrap_init.js`).
 *
 * Getting the HTTP cookie is not the whole story: `services/json_data_table.php`
 * and friends are called from the browser through socket.io
 * (`javascript/app/app.js` → `get_data`/`socket.emit`), and the Node bridge
 * (`app_node/src/services/phpBridge.js`) authenticates its server-to-server
 * call to PHP using `PHPSESSID` read out of the emitted payload — which the
 * client reads from `localStorage`, not from the cookie jar. A page that only
 * has the cookie renders a logged-in shell but gets empty data back from every
 * socket-backed list.
 */
export async function apiLogin(request: APIRequestContext): Promise<ApiSession> {
  const resp = await request.post(BASE + '/mdl/app/app_login/actions.php', {
    form: { F_action: 'app_log', type: 'agent', loginAgent: USER, passwordAgent: PASS },
  });
  if (!resp.ok()) throw new Error(`login failed: HTTP ${resp.status()}`);

  const check = await request.get(BASE + '/services/json_ssid.php');
  const sess = await check.json();
  if (!sess || typeof sess.idagent !== 'number' || sess.idagent <= 0) {
    throw new Error(`login did not open a session: ${JSON.stringify(sess)}`);
  }
  return { phpsessid: String(sess.PHPSESSID || ''), sessid: String(sess.idagent) };
}

/**
 * Fills and submits the in-page login form.
 *
 * Only reached when the stored session is missing or expired — the schema JSON
 * loads for anonymous visitors too, so an unauthenticated page still looks
 * "booted". `#desktop` is the real proof of a session.
 */
export async function uiLogin(page: Page): Promise<void> {
  const login = page.locator('input[name=loginAgent]');
  await login.waitFor({ state: 'visible', timeout: 60_000 });
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
export async function waitForAppReady(page: Page, timeout = 120_000): Promise<void> {
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
  // 120s, matching waitForAppReady below: main_bag.js cache-busts every
  // script on every load, so a boot regularly takes 60-90s under load, not
  // just the 10-20s a quiet container gives you (see BE_PLAN.md) — and this
  // fixture can run a *second* boot back-to-back in the same worker (see
  // fixtures/shared-boot.ts), which has been observed pushing past 90s even
  // on an otherwise idle container.
  const desktop = page.locator('#desktop');
  const loginForm = page.locator('input[name=loginAgent]');
  await Promise.race([
    desktop.waitFor({ state: 'attached', timeout: 120_000 }),
    loginForm.waitFor({ state: 'visible', timeout: 120_000 }),
  ]);

  if ((await desktop.count()) === 0) {
    await uiLogin(page);
  }
  await waitForAppReady(page);
}
