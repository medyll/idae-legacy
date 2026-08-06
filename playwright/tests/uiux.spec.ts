/**
 * Basic layout / responsive checks.
 *
 * Superseded selectors this file used to poll for (`#grid, .app-gui, #main`)
 * never exist in this app; the mobile-viewport assertion was
 * `toBeGreaterThanOrEqual(0)`, true unconditionally. Rewritten against real
 * selectors from the desktop shell.
 */
import { test, expect } from '@playwright/test';
import { openApp } from './fixtures/auth';
import { watchConsole } from './helpers/console-guard';

test('uiux: desktop shell renders with a title and a menu toggle', async ({ page }) => {
  const guard = watchConsole(page);
  await openApp(page);

  expect(await page.title()).not.toBe('');
  await expect(page.locator('#desktop')).toBeAttached();
  await expect(page.locator('#taskBar')).toBeAttached();

  // The waffle icon opens #gui_pane — the app's main menu, present at any
  // viewport width.
  const menuToggle = page.locator('.ms-Icon--waffle');
  await expect(menuToggle).toBeVisible();

  guard.assertClean();
});

test('uiux: menu toggle still works at a mobile viewport', async ({ page }) => {
  const guard = watchConsole(page);
  await openApp(page);
  await page.setViewportSize({ width: 375, height: 800 });

  // The waffle's onclick is `$('gui_pane').toggle()` — a Prototype Element
  // method that flips the inline `display` style, not a class.
  const menuToggle = page.locator('.ms-Icon--waffle');
  await expect(menuToggle).toBeVisible();

  const guiPane = page.locator('#gui_pane');
  const before = await guiPane.isVisible();
  await menuToggle.click();
  if (before) {
    await expect(guiPane).toBeHidden();
  } else {
    await expect(guiPane).toBeVisible();
  }

  guard.assertClean();
});
