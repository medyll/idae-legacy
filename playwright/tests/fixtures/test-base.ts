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
import { BASE, uiLogin, waitForAppReady } from './auth';

export const test = base.extend<{}, { workerStorageState: string }>({
  storageState: ({ workerStorageState }, use) => use(workerStorageState),

  workerStorageState: [
    async ({ browser }, use, workerInfo) => {
      const statePath = resolve(__dirname, `../.auth/worker-${workerInfo.parallelIndex}.json`);
      mkdirSync(dirname(statePath), { recursive: true });

      const context = await browser.newContext();
      const page = await context.newPage();

      // Real UI login only — no direct POST/GET against actions.php or
      // json_ssid.php from Node. Those endpoints and the localStorage mirror
      // they require (see fixtures/auth.ts: the socket data channel reads
      // PHPSESSID/SESSID from localStorage, never from the cookie jar) are
      // handled entirely client-side by the SPA's own login flow — a raw
      // HTTP call from the test runner has to hand-replicate that mirroring
      // to fake it, which drifts silently the moment the client flow
      // changes. Given how much of this stack is still shaky (see
      // BE_PLAN.md, HANG_TEST.md), the fixture should exercise the exact
      // path a real user takes, not a shortcut around it.
      await page.goto(BASE + '/', { waitUntil: 'domcontentloaded' });
      await uiLogin(page);
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
