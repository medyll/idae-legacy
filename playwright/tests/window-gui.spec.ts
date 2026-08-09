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

test('window: the reduce button hides the window into the task bar', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  // isReduced() used to call Scriptaculous' Effect.Fade; it now runs a native
  // opacity transition (wg_fadeOut in app_window.js). The contract is
  // unchanged and is what this asserts: the window goes away visually, and
  // appGui puts a button for it in the task bar.
  const win = await openRecord(page, TABLE, TABLE_VALUE);
  const containerId = await win.getAttribute('id');

  const buttonsBefore = await page.locator('#taskBar > *').count();

  await win.locator('.handledisp .buttonreduce').click();

  await expect(win).toBeHidden({ timeout: 15_000 });
  await expect(page.locator('#taskBar > *')).toHaveCount(buttonsBefore + 1, { timeout: 15_000 });

  // The container is only hidden, never removed — reopening from the task bar
  // depends on it still being there.
  expect(await page.locator(`#${containerId}`).count()).toBe(1);

  // Clean up: a reduced window is still in the DOM under the same id, so the
  // next test's openRecord() would wait forever for a "new" window that never
  // appears. initialize() parks the instance's own close() on the innerdisp
  // element (id = the container id minus its "container" prefix).
  await page.evaluate((id) => {
    const inner = document.getElementById(id.replace(/^container/, ''));
    if (inner && typeof (inner as any).close === 'function') (inner as any).close();
  }, containerId!);
  await expect(page.locator(`#${containerId}`)).toHaveCount(0, { timeout: 15_000 });

  guard.assertClean();
});

test('window: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const windowWarnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;

    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('app/app_window.js')) windowWarnings.push(message.text());
  });

  await page.evaluate(() => {
    (window as any).IDAE_SHIM_WARN = 1;
    (window as any).__idaeShimInstallWarn();
  });

  // Build, focus, drag-position and tear down: the full lifecycle in one pass.
  const first = await openRecord(page, TABLE, TABLE_VALUE);
  const second = await openList(page, TABLE);
  await first.locator('.handledisp').click();
  await closeWindow(second);
  await closeWindow(first);

  expect(windowWarnings).toEqual([]);
});
