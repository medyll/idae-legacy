/**
 * app_socket.js — the socket.io command dispatcher (`receive_cmd`'s
 * act_count/act_stream_to/act_progress/... switch, plus the `socketModule`
 * ack handler that injects every AJAX-style fragment this app loads).
 *
 * Nothing else in this suite targets it directly, but almost everything
 * exercises it incidentally — every window open goes through `socketModule`,
 * and a grouped list's rows arrive only through `act_stream_to`. That
 * breadth is exactly why a migration bug here is dangerous: the failure
 * mode isn't a thrown error, it's silence. The one found while migrating
 * this file (fixed in the same commit) was `Element#update`'s HTML string
 * path: Prototype strips `<script>` tags out before setting innerHTML, then
 * evals each one afterward — plain `node.innerHTML = html` never executes
 * embedded `<script>` tags, in any browser, shim or not. Half this app's
 * server-rendered fragments end in exactly such a script (e.g.
 * `mdl/app/app_liste/app_liste.php:100`'s `load_table_in_zone(...)` call,
 * which is how a list's rows get requested at all) — miss that and windows
 * open with headers and nothing else, no console error, nothing to grep for.
 */
import { test, expect } from './fixtures/test-base';
import { TABLE, TABLE_VALUE } from './fixtures/auth';
import { closeWindow, openList, openRecord } from './fixtures/app';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('socket: inline <script> in a socketModule-delivered fragment still runs', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  // A list's window frame arrives via one socketModule ack; its rows arrive
  // only because that frame's HTML ends in a <script> tag calling
  // load_table_in_zone(...). If Element#update's script extraction/eval
  // broke, the window would render (headers, search box, empty body) but
  // never populate — the exact failure mode a plain assertion on the window
  // being visible would miss.
  const win = await openList(page, TABLE);
  await expect(win.locator('table.table_groupe')).toBeVisible();
  await expect(win.locator('tbody.div_tbody tr')).not.toHaveCount(0, { timeout: 30_000 });

  await closeWindow(win);
  guard.assertClean();
});

test('socket: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const socketWarnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;

    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('app/app_socket.js')) socketWarnings.push(message.text());
  });

  await page.evaluate(() => {
    (window as any).IDAE_SHIM_WARN = 1;
    (window as any).__idaeShimInstallWarn();
  });

  // Exercises receive_cmd's act_stream_to/act_count/act_progress branches
  // (list load, desktop tile counts already ran at boot) and socketModule
  // (every window open) in one pass.
  const list = await openList(page, TABLE);
  await expect(list.locator('tbody.div_tbody tr')).not.toHaveCount(0, { timeout: 30_000 });
  const record = await openRecord(page, TABLE, TABLE_VALUE);
  await expect(record.locator('.innerdisp')).toBeVisible();

  await closeWindow(record);
  await closeWindow(list);

  expect(socketWarnings).toEqual([]);
});
