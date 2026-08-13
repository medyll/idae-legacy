/**
 * Boots the app once per spec file instead of once per test.
 *
 * `main_bag.js` cache-busts every script on every load — there is no warm
 * boot, ever (~10-45s per `openApp()` depending on load). A file with N
 * tests that each call `openApp(page)` pays that cost N times for no
 * reason: nothing about the boot itself is under test after the first one.
 *
 * Usage:
 *
 *   import { sharedPage } from './fixtures/shared-boot';
 *   const getPage = sharedPage();
 *   test('...', async () => {
 *     const page = getPage();
 *     ...
 *   });
 *
 * Trade-off: tests in the same file now share one page/session instead of
 * getting a fresh one each. Every test that opens something (a window, an
 * edit) must close/reset it before returning — Playwright no longer does
 * that for you between tests in the same file. Tests across *different*
 * files are unaffected; each file still gets its own page.
 */
import type { Page } from '@playwright/test';
import { test } from './test-base';
import { openApp } from './auth';

export function sharedPage(setup?: (page: Page) => Promise<void>): () => Page {
  let page: Page;

  test.beforeAll(async ({ browser, workerStorageState }) => {
    page = await browser.newPage({ storageState: workerStorageState });
    await openApp(page);
    if (setup) await setup(page);
  });

  test.afterAll(async () => {
    await page?.close();
  });

  return () => page;
}
