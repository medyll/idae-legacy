/**
 * Window chrome — `app/app_window.js` (238 Prototype calls) plus
 * `librairie/appGui.js` and `librairie/resizeGui.js`.
 *
 * Opening a record window exercises the whole dynamic-insertion path: AJAX
 * fragment → `.containerdisp` inserted into `#inBody` → insertionQ re-extends
 * the new nodes → title-bar buttons become live. If the shim breaks
 * `Element.observe`, `Element.up`, or `Class.create`, the close button stops
 * responding and this spec fails.
 */
import { test, expect } from './fixtures/test-base';
import { TABLE, TABLE_VALUE } from './fixtures/auth';
import { closeWindow, openList, openRecord } from './fixtures/app';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for both tests below — see fixtures/shared-boot.ts. Both tests
// already close every window they open, so nothing carries over.
const getPage = sharedPage();

test('window: open a record sheet, then close it', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const win = await openRecord(page, TABLE, TABLE_VALUE);

  await expect(win).toHaveClass(/containerdisp/);
  await expect(win.locator('.handledisp .titlefrm')).toContainText(/\S/);

  // The inner pane carries the entity it was opened for.
  const inner = win.locator('.innerdisp');
  await expect(inner).toHaveAttribute('table', TABLE);
  await expect(inner).toHaveAttribute('mdl', 'app/app/app_fiche');

  // Title-bar controls are wired by the window class, not by inline handlers.
  await expect(win.locator('.handledisp .buttonclose')).toBeVisible();
  await expect(win.locator('.handledisp .buttonreduce')).toBeVisible();
  await expect(win.locator('.handledisp .popperdisp')).toBeVisible();

  await closeWindow(win);
  guard.assertClean();
});

test('window: two windows coexist and close independently', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const record = await openRecord(page, TABLE, TABLE_VALUE);
  const list = await openList(page, TABLE);

  await expect(page.locator('.containerdisp')).toHaveCount(2);

  await closeWindow(record);
  await expect(list).toHaveCount(1);

  await closeWindow(list);
  await expect(page.locator('.containerdisp')).toHaveCount(0);
  guard.assertClean();
});
