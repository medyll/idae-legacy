/**
 * myddeview (librairie/myddeview.js) — shift-click / meta-click multi-row
 * selection, delegated over a checkbox class. Instantiated on every list's
 * file zone (`librairie/myddeExplorer.js:680`).
 *
 * myddeNotifier (librairie/myddeNotifier.js) — toast notifications
 * (`growl()`), instantiated directly by app_socket.js's notify handlers.
 *
 * Both migrated off the PrototypeJS compatibility shims 2026-08-10
 * (BE_PLAN.md phase 5). The 2026-08-09 inventory undercounted both files
 * badly — it only measured what the probe's screens actually triggered,
 * and neither a meta-click multi-select nor a toast was ever triggered
 * there.
 */
import { test, expect } from './fixtures/test-base';
import { TABLE } from './fixtures/auth';
import { closeWindow, openList } from './fixtures/app';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('myddeview: meta-click toggles selection and fires dom:selectionMade', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const win = await openList(page, TABLE);
  await expect(win.locator('tbody.div_tbody tr')).not.toHaveCount(0, { timeout: 30_000 });

  const box = win.locator('tbody.div_tbody input[type=checkbox]').first();
  await expect(box).toHaveCount(1);

  // mv_fire dispatches with bubbles:true on the myddeview element itself
  // (the file zone, not necessarily anything with a predictable class or
  // id) — listening on `document` catches it regardless of exactly which
  // node that is, instead of guessing at a selector for it.
  const events = await page.evaluate(() => {
    const seen: string[] = [];
    document.addEventListener('dom:selectionMade', () => seen.push('selectionMade'));
    document.addEventListener('dom:unSelectionMade', () => seen.push('unSelectionMade'));
    (window as any).__mvSeen = seen;
    return true;
  });
  expect(events).toBe(true);

  // Dispatched in-page rather than via Playwright's `modifiers: ['Meta']`:
  // that option presses the real OS-level Meta/Windows key through the
  // input pipeline, which on Windows steals focus to the Start Menu and
  // leaves the click's mouse-up (and the whole test) hanging until the
  // 60s timeout — confirmed via trace inspection (the click step never
  // returns; nothing after it is ever recorded). selectableClicked only
  // reads `event.metaKey` off the click event, so a synthetic MouseEvent
  // with that flag set exercises the exact same code path with none of
  // the OS side effects.
  const selectedAfterMeta = await page.evaluate((sel) => {
    const el = document.querySelector(sel) as HTMLElement;
    el.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, metaKey: true }));
    return el.classList.contains('selected');
  }, '#' + (await box.evaluate((el) => { if (!el.id) el.id = 'mv_probe_checkbox'; return el.id; })));
  expect(selectedAfterMeta).toBe(true);

  const seenAfterSelect = await page.evaluate(() => (window as any).__mvSeen.slice());
  expect(seenAfterSelect).toContain('selectionMade');

  // second meta-click toggles it back off
  const selectedAfterSecondMeta = await page.evaluate((sel) => {
    const el = document.querySelector(sel) as HTMLElement;
    el.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, metaKey: true }));
    return el.classList.contains('selected');
  }, '#mv_probe_checkbox');
  expect(selectedAfterSecondMeta).toBe(false);

  const seenAfterDeselect = await page.evaluate(() => (window as any).__mvSeen.slice());
  expect(seenAfterDeselect).toContain('unSelectionMade');

  await closeWindow(win);
  guard.assertClean();
});

test('myddeview: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const warnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('librairie/myddeview.js')) warnings.push(message.text());
  });

  await page.evaluate(() => {
  });

  const win = await openList(page, TABLE);
  await expect(win.locator('tbody.div_tbody tr')).not.toHaveCount(0, { timeout: 30_000 });

  const box = win.locator('tbody.div_tbody input[type=checkbox]').first();
  // Synthetic events, not `modifiers: [...]` — see the comment in the test
  // above; a real OS-level Meta keypress hangs the whole run on Windows.
  await box.evaluate((el) => {
    el.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, metaKey: true }));
    el.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, shiftKey: true }));
  });
  await page.waitForTimeout(500);

  await closeWindow(win);
  expect(warnings).toEqual([]);
});

test('myddeNotifier: growl() builds a toast and auto-dismisses it', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const noticeId = await page.evaluate(() => {
    const n = new (window as any).myddeNotifier();
    // growl() returns nothing, so the notice has to be found in the growler.
    // It is the LAST child, not the first: buildNotice appends. Reading
    // querySelector('.notifierNotice') — the first match — picked up whatever
    // toast the socket had already pushed ("Notification"), which is how this
    // test failed in a full-suite run and passed on its own.
    n.growl('Test de migration BE_PLAN', {});
    const notices = n.growler.querySelectorAll('.notifierNotice');
    const notice = notices[notices.length - 1] as HTMLElement;
    notice.id = 'be_plan_migration_probe';
    return notice.id;
  });

  const notice = page.locator(`#${noticeId}`);
  await expect(notice).toBeVisible();
  await expect(notice).toHaveText('Test de migration BE_PLAN');

  // Non-sticky notices self-remove after 5s (see myddeNotifier.js buildNotice).
  await expect(notice).toHaveCount(0, { timeout: 7_000 });

  guard.assertClean();
});

test('myddeNotifier: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const warnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('librairie/myddeNotifier.js')) warnings.push(message.text());
  });

  await page.evaluate(() => {
  });

  await page.evaluate(() => {
    const n = new (window as any).myddeNotifier({ sticky: true } as any);
    n.growl('probe sticky', { sticky: true });
  });
  await page.waitForTimeout(500);

  expect(warnings).toEqual([]);
});
