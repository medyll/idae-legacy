/**
 * myddeDatalist — the autocomplete dropdown bolted onto `[datalist]` inputs
 * (`librairie/myddeDatalist.js`, instantiated by app_insertionQ.js's
 * `[datalist]` and `.heure` watchers).
 *
 * Nothing covered this widget before the phase-5 migration, and it is a bad
 * one to leave untested: it rewrites the DOM around an input it does not own
 * — wraps it in a new parent, injects a caret sibling, and parks its dropdown
 * on document.body rather than next to the field. Every one of those steps
 * fails silently. A broken wrap leaves the input working and the suggestions
 * unreachable; a dropdown that never gets positioned renders at 0,0 behind
 * the window.
 *
 * The update tab is the screen that carries them in this dataset (4 inputs,
 * all `app/app_select`).
 */
import { test, expect } from './fixtures/test-base';
import { TABLE, TABLE_VALUE } from './fixtures/auth';
import { closeWindow, openChrome } from './fixtures/app';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts. Every test opens
// the same update-tab window (same table/table_value → same window id) and
// must close it, or the next openChrome() waits forever for a "new" id.
const getPage = sharedPage();

function openUpdate(page: Parameters<typeof openChrome>[0]) {
  return openChrome(page, 'app/app/app_update', `table=${TABLE}&table_value=${TABLE_VALUE}`);
}

test('datalist: each input is wrapped, gets a caret, and owns a dropdown on body', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const win = await openUpdate(page);
  await expect(win.locator('form.Form')).toBeVisible({ timeout: 30_000 });

  const structure = await page.evaluate(() => {
    const inputs = Array.from(document.querySelectorAll('[datalist]'));
    return inputs.map((input) => {
      const listId = input.getAttribute('list');
      const dropdown = listId ? document.getElementById(listId) : null;
      return {
        // prepare(): wrap() puts a .wrapper_holder between the input and its
        // original parent, and the caret is inserted right after the input.
        wrapped: !!input.parentElement && input.parentElement.classList.contains('wrapper_holder'),
        hasCaret: !!input.nextElementSibling && input.nextElementSibling.classList.contains('fa-caret-down'),
        // The dropdown lives on body, not beside the field, so it can escape
        // the window's overflow.
        dropdownOnBody: !!dropdown && dropdown.parentElement === document.body,
        positioned: !!dropdown && dropdown.style.position === 'absolute' && dropdown.style.top !== '',
      };
    });
  });

  expect(structure.length).toBeGreaterThan(0);
  for (const entry of structure) expect(entry).toEqual({
    wrapped: true,
    hasCaret: true,
    dropdownOnBody: true,
    positioned: true,
  });

  await closeWindow(win);
  guard.assertClean();
});

test('datalist: focusing an input opens its dropdown and loads options', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const win = await openUpdate(page);
  await expect(win.locator('form.Form')).toBeVisible({ timeout: 30_000 });

  const input = win.locator('[datalist]').first();
  const listId = await input.getAttribute('list');
  const dropdown = page.locator(`#${listId}`);

  // populate defaults to true, so emit_populate() has already asked for the
  // options over the socket; focus is what reveals them.
  await input.focus();
  await expect(dropdown).toBeVisible({ timeout: 15_000 });
  await expect(dropdown.locator('a')).not.toHaveCount(0, { timeout: 15_000 });

  // onblur hides it again after its 150ms timer.
  await win.locator('form.Form').click({ position: { x: 2, y: 2 } });
  await expect(dropdown).toBeHidden({ timeout: 15_000 });

  await closeWindow(win);
  guard.assertClean();
});

test('datalist: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const datalistWarnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;

    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('librairie/myddeDatalist.js')) datalistWarnings.push(message.text());
  });

  await page.evaluate(() => {
  });

  const win = await openUpdate(page);
  await expect(win.locator('form.Form')).toBeVisible({ timeout: 30_000 });

  // Construction, focus/positioning, keyboard navigation and blur: the paths
  // that held most of this file's former shim surface.
  const input = win.locator('[datalist]').first();
  await input.focus();
  await input.press('ArrowDown');
  await input.press('ArrowUp');
  await page.waitForTimeout(500);

  await closeWindow(win);

  expect(datalistWarnings).toEqual([]);
});
