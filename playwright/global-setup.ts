/**
 * Logs in once and stores the session, so specs skip the login form.
 * The bag.js boot still runs per page load — only the credential round-trip is
 * saved.
 *
 * Two auth channels must both be captured. The PHP cookie authenticates plain
 * HTTP endpoints; the socket.io data channel (`get_data`, `runModule` — see
 * `javascript/app/app.js`) instead reads `PHPSESSID`/`SESSID` out of
 * `localStorage` on every emit. An API-only login never touches localStorage,
 * so a `storageState` built purely from `request.newContext()` produces pages
 * that look logged in (cookie present, `#desktop` renders) but get empty data
 * back from every socket-backed list and datatable. A real page visit after
 * login is what populates both.
 */
import { chromium } from '@playwright/test';
import { mkdirSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { apiLogin, BASE, STORAGE_STATE } from './tests/fixtures/auth';

export default async function globalSetup() {
  const statePath = resolve(__dirname, STORAGE_STATE);
  mkdirSync(dirname(statePath), { recursive: true });

  const browser = await chromium.launch();
  const context = await browser.newContext();
  const page = await context.newPage();

  // Visit the origin first so localStorage has somewhere to attach, then log
  // in over HTTP and mirror the session into localStorage the same way the
  // SPA's own login flow does (see mdl/app/app_login/actions.php).
  await page.goto(BASE + '/', { waitUntil: 'domcontentloaded' });
  const session = await apiLogin(context.request);
  await page.evaluate((s) => {
    localStorage.setItem('PHPSESSID', s.phpsessid);
    localStorage.setItem('SESSID', s.sessid);
  }, session);

  await context.storageState({ path: statePath });
  await browser.close();
}
