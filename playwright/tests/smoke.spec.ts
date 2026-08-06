/**
 * Smoke test: login and the desktop shell renders.
 *
 * Superseded selectors this file used to poll for (`#grid, .app-gui, #main`)
 * never exist in this app — the desktop shell is `#desktop`/`#taskBar`, and a
 * standalone in-file login duplicated what `fixtures/auth.ts` now does
 * correctly (including the localStorage mirror the socket data channel
 * needs — see `datatable.spec.ts` for why that matters).
 */
import { test, expect } from '@playwright/test';
import { openApp } from './fixtures/auth';
import { watchConsole } from './helpers/console-guard';

test('smoke: login and desktop shell renders', async ({ page, request }) => {
  const guard = watchConsole(page);
  await openApp(page);

  await expect(page.locator('#desktop')).toBeAttached();
  await expect(page.locator('#taskBar')).toBeAttached();

  // Session actually opened server-side, not just a rendered shell.
  const resp = await request.get((process.env.BASE_URL || 'http://localhost:8080') + '/services/json_ssid.php');
  const sess = await resp.json();
  expect(typeof sess.idagent).toBe('number');
  expect(sess.idagent).toBeGreaterThan(0);

  guard.assertClean();
});
