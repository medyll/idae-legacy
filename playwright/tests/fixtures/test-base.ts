/**
 * Per-worker authenticated session.
 *
 * `playwright.config.ts` used to force `workers: 1` because every spec
 * shared one `storageState` — one `PHPSESSID`. Concurrent requests on the
 * same PHP session serialize on PHP's own file-based session lock, and
 * during the migration this repeatedly took the socket bridge down under
 * load (see BE_PLAN.md). There is no server-side single-session
 * enforcement in this app (checked `mdl/app/app_login/actions.php` and the
 * socket handlers — a second login for the same agent does not invalidate
 * the first), so the fix is Playwright's documented pattern for
 * per-worker auth: each worker logs in once, gets its own `PHPSESSID`, and
 * genuinely runs in parallel at the PHP level.
 *
 * Every spec should import `test`/`expect` from this file instead of
 * `@playwright/test` directly.
 */
import { test as base, expect } from '@playwright/test';
import { mkdirSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { apiLogin, BASE, waitForAppReady } from './auth';

export const test = base.extend<{}, { workerStorageState: string }>({
  storageState: ({ workerStorageState }, use) => use(workerStorageState),

  workerStorageState: [
    async ({ browser }, use, workerInfo) => {
      const statePath = resolve(__dirname, `../.auth/worker-${workerInfo.parallelIndex}.json`);
      mkdirSync(dirname(statePath), { recursive: true });

      const context = await browser.newContext();
      const page = await context.newPage();

      // Visit the origin first so localStorage has somewhere to attach, then
      // log in over HTTP and mirror the session into localStorage the same
      // way the SPA's own login flow does — see fixtures/auth.ts for why the
      // cookie alone isn't enough (the socket data channel reads
      // PHPSESSID/SESSID from localStorage, never from the cookie jar).
      await page.goto(BASE + '/', { waitUntil: 'domcontentloaded' });
      const session = await apiLogin(context.request);
      await page.evaluate((s) => {
        localStorage.setItem('PHPSESSID', s.phpsessid);
        localStorage.setItem('SESSID', s.sessid);
      }, session);
      await waitForAppReady(page);

      await context.storageState({ path: statePath });
      await page.close();
      await context.close();

      await use(statePath);
    },
    { scope: 'worker' },
  ],
});

export { expect };
