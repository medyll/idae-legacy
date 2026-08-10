/**
 * autoToggle — delegated `.autoToggle` click handling: exactly one item
 * under a container carries `.active` at a time.
 * `librairie/autoToggle.js`, instantiated on every list's `<tbody>`
 * (app_datatable.js:413,424 — every row it renders gets the `.autoToggle`
 * class, app_datatable.js:752 and friends).
 *
 * Every spec that opens a list already exercises its *construction*
 * incidentally; none clicks a row to verify the actual "only one active"
 * behaviour, which is the part most likely to silently misbehave (rows
 * would still render and still be clickable even if `.active` accumulated
 * on every row instead of just the last one clicked).
 */
import { test, expect } from './fixtures/test-base';
import { TABLE } from './fixtures/auth';
import { closeWindow, openList } from './fixtures/app';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('autoToggle: clicking a row activates it and deactivates any previously active row', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const win = await openList(page, TABLE);
  const rows = win.locator('tbody.div_tbody tr.autoToggle');
  await expect(rows).not.toHaveCount(0, { timeout: 30_000 });

  const first = rows.nth(0);
  const second = rows.nth(1);

  await first.click();
  await expect(first).toHaveClass(/active/);

  await second.click();
  await expect(second).toHaveClass(/active/);
  await expect(first).not.toHaveClass(/active/);

  await closeWindow(win);
  guard.assertClean();
});

test('autoToggle: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const toggleWarnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('librairie/autoToggle.js')) toggleWarnings.push(message.text());
  });

  await page.evaluate(() => {
    (window as any).IDAE_SHIM_WARN = 1;
    (window as any).__idaeShimInstallWarn();
  });

  const win = await openList(page, TABLE);
  const rows = win.locator('tbody.div_tbody tr.autoToggle');
  await expect(rows).not.toHaveCount(0, { timeout: 30_000 });
  await rows.nth(0).click();

  await closeWindow(win);

  expect(toggleWarnings).toEqual([]);
});
