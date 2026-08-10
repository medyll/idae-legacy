/**
 * librairie/observers.js — a single global click handler (`selfObservers`,
 * instantiated once at boot by `engine/initApp.js`): checkbox toggling via
 * doCheck/doUnCheck, `.autoNext` reveal, and `.hide_on_click` dismissal on
 * any outside click.
 *
 * The checkbox path is exercised against a real list checkbox (same
 * fixture shape as myddeview.spec.ts) rather than a constructed one —
 * this listener is on `document` itself, so it's already live on every
 * boot; the point here is proving doCheck/doUnCheck actually fire from a
 * real click, not just that the delegate was registered.
 */
import { test, expect } from './fixtures/test-base';
import { TABLE } from './fixtures/auth';
import { closeWindow, openList } from './fixtures/app';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('observers: clicking a list checkbox runs doCheck/doUnCheck (bugchk + row.selected)', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const win = await openList(page, TABLE);
  await expect(win.locator('tbody.div_tbody tr')).not.toHaveCount(0, { timeout: 30_000 });

  // These checkboxes are only revealed on row hover (CSS) — real for a
  // user, but Playwright's click() waits for genuine visibility and spins
  // forever without it. force:true dispatches the click regardless, which
  // is exactly what's under test here (the click handling, not the hover
  // CSS).
  const box = win.locator('tbody.div_tbody input[type=checkbox]').first();
  await box.click({force: true});
  await expect(box).toHaveAttribute('bugchk', 'bugchk');
  // Not tbody tr:first — grouped lists interleave "entete_groupe" header
  // rows that never carry a checkbox, so the checkbox's own row is
  // whichever <tr> actually contains it, not the first one in the table.
  const row = box.locator('xpath=ancestor::tr[1]');
  await expect(row).toHaveClass(/selected/);

  await box.click({force: true});
  await expect(row).not.toHaveClass(/selected/);

  await closeWindow(win);
  guard.assertClean();
});

test('observers: .autoNext toggles its next sibling and .active, .hide_on_click dismisses on outside click', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const result = await page.evaluate(() => {
    const trigger = document.createElement('a');
    trigger.className = 'autoNext';
    document.body.appendChild(trigger);
    const panel = document.createElement('div');
    panel.style.display = 'none';
    document.body.appendChild(panel);
    trigger.insertAdjacentElement('afterend', panel);

    const popover = document.createElement('div');
    popover.className = 'hide_on_click';
    document.body.appendChild(popover);
    const outside = document.createElement('button');
    document.body.appendChild(outside);

    trigger.click();
    const shownAfterFirst = panel.style.display !== 'none';
    const activeAfterFirst = trigger.classList.contains('active');

    trigger.click();
    const hiddenAfterSecond = panel.style.display === 'none';
    const activeAfterSecond = trigger.classList.contains('active');

    outside.click();
    const popoverHidden = popover.style.display === 'none';

    trigger.remove();
    panel.remove();
    popover.remove();
    outside.remove();

    return {shownAfterFirst, activeAfterFirst, hiddenAfterSecond, activeAfterSecond, popoverHidden};
  });

  expect(result.shownAfterFirst).toBe(true);
  expect(result.activeAfterFirst).toBe(true);
  expect(result.hiddenAfterSecond).toBe(true);
  expect(result.activeAfterSecond).toBe(false);
  expect(result.popoverHidden).toBe(true);

  guard.assertClean();
});

test('observers: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const warnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('librairie/observers.js')) warnings.push(message.text());
  });

  await page.evaluate(() => {
    (window as any).IDAE_SHIM_WARN = 1;
    (window as any).__idaeShimInstallWarn();
  });

  const win = await openList(page, TABLE);
  await expect(win.locator('tbody.div_tbody tr')).not.toHaveCount(0, { timeout: 30_000 });
  const box = win.locator('tbody.div_tbody input[type=checkbox]').first();
  await box.click({force: true});
  await box.click({force: true});
  await closeWindow(win);
  await page.waitForTimeout(300);

  expect(warnings).toEqual([]);
});
