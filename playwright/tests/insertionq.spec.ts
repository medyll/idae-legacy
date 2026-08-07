/**
 * The insertionQ pipeline — `app/app_insertionQ.js` (300 Prototype calls),
 * the DOM-mutation-observer layer that re-extends every node the SPA inserts
 * after the initial page load. Every other spec in this suite exercises it
 * incidentally (opening a window, clicking a panel link); this one drives a
 * complete, self-contained round trip end to end.
 *
 * `app_gui_calendar.php` renders `.tile_count[data-count][data-count_auto]`
 * with no text. insertionQ's `[data-count]` watcher (app_insertionQ.js)
 * observes it appear and fires `runModule('services/json_data_table', ...)`
 * over the socket. The Node bridge replies over `upd_data`
 * (app_node/src/socket/handlers.js); the client's listener
 * (app_socket.js:161-163) does `$(count_id).addClassName('animated
 * bounce'); $(count_id).update(count)`. Nothing about this path is called
 * directly from a test — insertionQ has to notice the node, and
 * `Element#update` has to work — which makes it a good canary for the
 * migration: a shim that gets `MutationObserver`-driven extension or
 * `Element.update` wrong leaves this badge permanently empty, silently.
 */
import { test, expect } from './fixtures/test-base';
import { TABLE, TABLE_VALUE } from './fixtures/auth';
import { closeWindow, openList, openRecord } from './fixtures/app';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for both tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('insertionQ: an auto-count badge fills in via the socket round trip', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const badge = page.locator('.tile_count[data-count][data-count_auto]').first();
  await expect(badge).toBeVisible({ timeout: 30_000 });

  // Starts empty in the server-rendered HTML; insertionQ + the socket round
  // trip are what puts a number in it.
  await expect(badge).not.toHaveText('', { timeout: 30_000 });
  await expect(badge).toHaveText(/^\d+$/);

  guard.assertClean();
});

test('insertionQ: a second window\'s dynamic content re-extends independently', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  // Two windows opened back to back means insertionQ has to correctly
  // re-scope its watchers to freshly-inserted subtrees twice in a row,
  // rather than once at boot. A list then a record sheet: both module graphs
  // are known-good (window-gui.spec.ts drives the same pair), whereas a
  // second hardcoded table name is not guaranteed to exist in every dataset.
  const list = await openList(page, TABLE);
  const record = await openRecord(page, TABLE, TABLE_VALUE);

  await expect(list.locator('table.table_groupe')).toBeVisible();
  await expect(record.locator('.innerdisp')).toBeVisible();
  await expect(list.locator('tbody.div_tbody tr')).not.toHaveCount(0, { timeout: 30_000 });

  await closeWindow(record);
  await closeWindow(list);
  guard.assertClean();
});
