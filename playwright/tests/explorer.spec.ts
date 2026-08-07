/**
 * Desktop history panel — `app/app_gui/app_gui_panel.php` rendered client-side
 * by `app_insertionQ.js`'s `[act_chrome_gui]` watcher.
 *
 * Unlike every other spec in this suite, this one never calls
 * `act_chrome_gui()` directly from `page.evaluate()`. insertionQ observes each
 * `[act_chrome_gui]` node as it's inserted and writes a plain `onclick`
 * attribute onto it (`app_insertionQ.js`); the row a user actually clicks is a
 * real anchor with a real inline handler, wired via `Element.observe` /
 * `readAttribute` / `writeAttribute`. That's a meaningfully different path
 * from a synthetic call — it's what breaks first if the shim's insertionQ
 * hookup silently no-ops.
 */
import { test, expect } from './fixtures/test-base';
import { closeWindow } from './fixtures/app';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all three tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('desktop panel: recently-viewed records are real clickable links', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const panelLink = page.locator('#desktop [act_chrome_gui="app/app/app_fiche"]').first();
  await expect(panelLink).toBeVisible({ timeout: 30_000 });

  // insertionQ must have run: onclick is injected dynamically, never present
  // in the server-rendered HTML.
  await expect(panelLink).toHaveAttribute('onclick', /act_chrome_gui\(/);

  guard.assertClean();
});

test('desktop panel: clicking a record link opens its window', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const panelLink = page.locator('#desktop [act_chrome_gui="app/app/app_fiche"]').first();
  await expect(panelLink).toBeVisible({ timeout: 30_000 });
  const label = (await panelLink.textContent())?.trim();

  await panelLink.click();

  const win = page.locator('.containerdisp').last();
  await expect(win).toBeVisible({ timeout: 30_000 });
  if (label) {
    await expect(win.locator('.handledisp .titlefrm')).toContainText(label, { ignoreCase: true });
  }

  await closeWindow(win);
  guard.assertClean();
});

test('desktop panel: caret collapses and expands the section', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  // The div carrying `onclick="save_setting_autoNext(...)"` only persists the
  // collapsed/expanded preference (app_functions.js) — it reads the current
  // state, it doesn't change it. The actual toggle is `app_tree.js`, wired to
  // a `.auto_tree_caret` that `[auto_tree]`'s insertionQ watcher injects
  // (app_insertionQ.js), scoped per panel via `.up('.auto_tree')`.
  const panel = page.locator('#desktop [auto_tree_main]').first();
  const caret = panel.locator('.auto_tree_caret').first();
  await expect(caret).toBeVisible({ timeout: 30_000 });

  const body = panel.locator('.auto_tree_next').first();
  const before = await body.isVisible();

  await caret.click();
  if (before) {
    await expect(body).toBeHidden();
  } else {
    await expect(body).toBeVisible();
  }

  guard.assertClean();
});
