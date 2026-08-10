/**
 * app/app_calendrier.js — calendar widget nav (prev/next month, month/year
 * pickers), auto-instantiated via insertionQ on any `[data-app_calendrier]`
 * node (app/app_insertionQ.js:561). Real screen:
 * app/app_calendrier/app_calendrier_echeance, opened from the desktop's
 * "app_gui_calendar" tile (`act_chrome_gui="app/app_calendrier/
 * app_calendrier_echeance"`, app_gui_calendar.php) — reached here directly
 * via `openChrome`, same as openList/openRecord.
 */
import { test, expect } from './fixtures/test-base';
import { openChrome, closeWindow } from './fixtures/app';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('app_calendrier: wires scope/value on both cf_module zones and reacts to nav clicks', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const win = await openChrome(page, 'app/app_calendrier/app_calendrier_echeance');
  await expect(win.locator('[data-nav_cal]')).toHaveCount(1);

  const navZone = win.locator('[data-nav_zone]');
  const navCal = win.locator('[data-nav_cal]');

  // Constructor wiring: both cf_module zones get scope/value set to the
  // same identify()'d id.
  const navZoneModule = navZone.locator('.cf_module');
  const navCalModule = navCal.locator('.cf_module');
  const zoneScope = await navZoneModule.getAttribute('scope');
  const calScope = await navCalModule.getAttribute('scope');
  expect(zoneScope).toBeTruthy();
  expect(zoneScope).toBe(calScope);
  expect(await navZoneModule.getAttribute('value')).toBe(zoneScope);
  expect(await navCalModule.getAttribute('value')).toBe(zoneScope);

  // A real month title is showing before any nav click.
  await expect(navCal.locator('.change_month')).not.toHaveText('');

  // previous_month: real delegated click, real server round trip
  // (loadModule + reloadScope) — the point is that it runs without
  // throwing and the month title actually changes.
  const monthBefore = await navCal.locator('.change_month').innerText();
  await navCal.locator('.previous_month').click();
  await expect(navCal.locator('.change_month')).not.toHaveText(monthBefore, { timeout: 15_000 });

  await closeWindow(win);
  guard.assertClean();
});

test('app_calendrier: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const warnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('app/app_calendrier.js')) warnings.push(message.text());
  });

  await page.evaluate(() => {
    (window as any).IDAE_SHIM_WARN = 1;
    (window as any).__idaeShimInstallWarn();
  });

  const win = await openChrome(page, 'app/app_calendrier/app_calendrier_echeance');
  await win.locator('[data-nav_cal] .next_month').click();
  await page.waitForTimeout(800);
  await win.locator('[data-nav_cal] .change_month').click();
  await page.waitForTimeout(300);

  await closeWindow(win);
  expect(warnings).toEqual([]);
});
