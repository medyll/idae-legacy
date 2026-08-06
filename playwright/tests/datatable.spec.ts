/**
 * List / datatable — `app/app_datatable.js` (442 Prototype calls), the
 * densest file in the codebase.
 *
 * Data for this widget does not travel over HTTP. `load_table_in_zone()`
 * instantiates `BuildTbl`, which calls `get_data('json_data_table', ...)`
 * (`javascript/app/app.js`), and that goes out as `socket.emit('get_data', ...)`
 * to the Node bridge (`app_node/src/socket/handlers.js` →
 * `services/phpBridge.js`), which replays it to PHP over its own HTTP call
 * carrying `PHPSESSID` read out of the emitted payload. The client only ever
 * puts that value in `localStorage`, never just the cookie. Get that wrong —
 * as a cookie-only `storageState` does — and the window renders correctly
 * with a real column header pulled from the schema, but every list silently
 * shows "0 résultats" with no console error and no failed request to blame.
 * `fixtures/auth.ts` / `global-setup.ts` carry the fix.
 *
 * Chasing that symptom down also surfaced two unrelated, real PHP 8.2
 * crashes on this exact request path — `services/json_data_table.php:478`
 * (indexing a scalar distinct value as if it were a record, in the default
 * groupBy-by-phone the list opens with) and
 * `appclasses/appcommon/ClassApp.php:2236` (`stripslashes(null)` on an empty
 * `textelibre` field). Both were silent under PHP 7 and fatal under 8.2;
 * fixed alongside this spec.
 */
import { test, expect } from '@playwright/test';
import { openApp, TABLE } from './fixtures/auth';
import { closeWindow, openList } from './fixtures/app';
import { watchConsole } from './helpers/console-guard';

test('datatable: list loads real rows through the socket data channel', async ({ page }) => {
  const guard = watchConsole(page);
  await openApp(page);

  const win = await openList(page, TABLE);
  const table = win.locator('table.table_groupe');
  await expect(table).toBeVisible();

  // Column headers come from the live schema (appscheme_has_field), not a
  // hardcoded template — this is the JSON produced by json_scheme.php.
  await expect(table.locator('thead tr')).toHaveCount(1);
  const headerCount = await table.locator('thead td').count();
  expect(headerCount).toBeGreaterThan(1);

  // The regression this spec exists to catch: rows actually arrive.
  const footer = win.locator('.tbl_footer');
  await expect(footer).not.toContainText('0 résultats', { timeout: 30_000 });
  await expect(win.locator('tbody.div_tbody tr')).not.toHaveCount(0, { timeout: 30_000 });

  await closeWindow(win);
  guard.assertClean();
});

test('datatable: search hides non-matching rows client-side', async ({ page }) => {
  const guard = watchConsole(page);
  await openApp(page);

  const win = await openList(page, TABLE);
  const rows = win.locator('tbody.div_tbody tr');
  await expect(rows).not.toHaveCount(0, { timeout: 30_000 });

  // myddeExplorer.act_search defaults to where_search:'local' (myddeExplorer.js):
  // it never re-queries the server or changes the footer's total-loaded count —
  // it hides/shows the already-loaded <tr> elements in place, 250ms after the
  // last keystroke. The footer figure is a red herring for this feature.
  //
  // Bound on 'keyup' via delegated Prototype Element#on, not 'input' —
  // Locator.fill() only fires input/change, so it silently does nothing here.
  // pressSequentially() types real keys.
  const search = win.locator('input[placeholder=Rechercher]');
  await search.pressSequentially('zzz_no_such_client_zzz', { delay: 20 });
  await expect(rows.filter({ visible: true })).toHaveCount(0, { timeout: 15_000 });

  await search.fill('');
  await search.press(' ');
  await search.press('Backspace');
  await expect(rows.filter({ visible: true })).not.toHaveCount(0, { timeout: 15_000 });

  guard.assertClean();
});
