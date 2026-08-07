/**
 * Reference screenshots — the visual half of the baseline.
 *
 * prototype-surface.spec.ts catches missing APIs, the other specs catch broken
 * behavior; these catch what neither sees: a `setStyle` that writes the wrong
 * unit, a `getDimensions` off by a scrollbar, a Position.* drift — all silent
 * until a screen looks wrong.
 *
 * Four key screens, all reachable through the known-good TABLE/TABLE_VALUE
 * pair: the desktop shell, the list view, the record sheet, and the update
 * tab's form.
 *
 * Dynamic content is masked rather than asserted: `.tile_count` badges fill
 * with live counts over the socket, and the history panel lists
 * recently-viewed records — both change between runs without being
 * regressions. Screenshots are viewport-sized (not fullPage) so a window
 * positioned a few pixels lower does not fail the diff.
 *
 * Baselines are (re)generated with: npm run test:baseline
 */
import { test, expect, type Locator } from './fixtures/test-base';
import { openApp, TABLE, TABLE_VALUE } from './fixtures/auth';
import { closeWindow, openChrome, openList, openRecord } from './fixtures/app';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all four tests below — see fixtures/shared-boot.ts. Every
// test after the first opens a window for its screenshot; each must close
// it, or the next screenshot shows leftover windows stacked on top.
const getPage = sharedPage();

/** Live-data regions that change between runs without being regressions. */
function dynamicMasks(page: Parameters<typeof openApp>[0]) {
  // Everything on the desktop is live data: tiles and notes of the logged
  // agent, calendar counts, the right-hand history/explorer panel
  // (app_gui_desktop.php). What the desktop shot asserts is the shell
  // layout around them, not their contents.
  return [
    page.locator('.tile_count'),
    page.locator('#zone_agent_table'),
    page.locator('#zone_agent_tuile'),
    page.locator('#note_panel'),
    page.locator('[mdl="app/app_gui/app_gui_calendar"]'),
    page.locator('[main_auto_tree]'),
  ];
}

/** Waits for the window's own loading overlay to be gone before shooting. */
async function settled(win: Locator): Promise<void> {
  await expect(win.locator('.loading')).toHaveCount(0, { timeout: 30_000 });
}

test('snapshots: desktop shell', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  await expect(page).toHaveScreenshot('desktop.png', {
    mask: dynamicMasks(page),
    animations: 'disabled',
    // Masked live zones resize by a few pixels between runs (note panel,
    // calendar) and their unmasked borders jitter with them. Half a percent
    // of the viewport absorbs that boundary noise; anything structurally
    // wrong (missing bar, broken layout) is far above it.
    maxDiffPixelRatio: 0.005,
  });
  guard.assertClean();
});

test('snapshots: list view', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const win = await openList(page, TABLE);
  await expect(win.locator('tbody.div_tbody tr').first()).toBeVisible({ timeout: 30_000 });
  await settled(win);

  // The row area is masked: the list opens grouped by telephoneClient and
  // group collapse state is persisted per user, so which rows are expanded
  // varies between sessions. The shot asserts the window chrome, the
  // scheme-driven column headers, the pager and the footer — row content is
  // covered behaviorally by datatable.spec.ts.
  await expect(win).toHaveScreenshot('list.png', {
    mask: [win.locator('tbody.div_tbody')],
    animations: 'disabled',
  });
  await closeWindow(win);
  guard.assertClean();
});

test('snapshots: record sheet', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const win = await openRecord(page, TABLE, TABLE_VALUE);
  await settled(win);

  await expect(win).toHaveScreenshot('record.png', { animations: 'disabled' });
  await closeWindow(win);
  guard.assertClean();
});

test('snapshots: update tab form', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const win = await openChrome(page, 'app/app/app_update', `table=${TABLE}&table_value=${TABLE_VALUE}`);
  await expect(win.locator('form.Form')).toBeVisible({ timeout: 30_000 });
  await settled(win);

  await expect(win).toHaveScreenshot('update-form.png', { animations: 'disabled' });
  await closeWindow(win);
  guard.assertClean();
});
