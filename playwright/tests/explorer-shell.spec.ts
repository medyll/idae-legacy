/**
 * myddeExplorer — the shell around every list: toolbar buttons, sort zones,
 * drag & drop, preview pane, client-side search
 * (`javascript/librairie/myddeExplorer.js`).
 *
 * Distinct from `explorer.spec.ts`, which covers the desktop history panel
 * (`app_gui_panel.php` + insertionQ) and never touches this class.
 *
 * `datatable.spec.ts` already exercises one myddeExplorer path — the
 * delegated `keyup` search — because that is where the search input lives.
 * What is verified here instead is the DOM this class *builds* rather than
 * the DOM it reacts to: `act_expl_search_input()` rewrites the search input's
 * attributes, wraps it in a new element and injects a two-option scope menu
 * next to it. That construction is pure myddeExplorer, runs on every list
 * open, and is exactly what a broken `wrap()` or a broken `createElement`
 * chain would silently drop — it fails open, with the list still looking
 * perfectly fine.
 */
import { test, expect } from './fixtures/test-base';
import { TABLE } from './fixtures/auth';
import { closeWindow, openList } from './fixtures/app';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('explorer shell: the search input is adopted and wrapped with its scope menu', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const win = await openList(page, TABLE);
  await expect(win.locator('tbody.div_tbody tr')).not.toHaveCount(0, { timeout: 30_000 });

  // Both attributes are written by act_expl_search_input(), never by the
  // server-rendered markup.
  const search = win.locator('[expl_search_button]').first();
  await expect(search).toHaveAttribute('placeholder', 'Rechercher');
  await expect(search).toHaveAttribute('data-menu', 'data-menu');

  // wrap(): the input gained a new parent, and the scope menu was appended
  // into that same wrapper as its sibling.
  const menu = search.locator('xpath=../div//div[contains(@class,"contextmenu")]');
  await expect(menu).toHaveCount(1);
  await expect(menu.locator('a')).toHaveCount(2);
  await expect(menu.locator('a').first()).toHaveText(/éléments visibles/);
  await expect(menu.locator('a').nth(1)).toHaveText('Partout');

  await closeWindow(win);
  guard.assertClean();
});

test('explorer shell: reopening a list does not re-wrap the search input', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  // act_expl_search_input() guards on an `act_processed` flag, and
  // build_expl_search_button() is called again on every `content:loaded`.
  // Without the guard each reload nests another wrapper and another menu.
  //
  // Counted inside the search input's own wrapper, not window-wide: a list
  // window carries a second, server-rendered `.contextmenu.toggler` in its
  // toolbar that has nothing to do with this class.
  const menusAroundSearch = () =>
    page.evaluate(() => {
      const windows = document.querySelectorAll('.containerdisp');
      const win = windows[windows.length - 1] as HTMLElement;
      const input = win.querySelector('[expl_search_button]');
      if (!input || !input.parentElement) return -1;
      return input.parentElement.querySelectorAll('.contextmenu.toggler').length;
    });

  const win = await openList(page, TABLE);
  await expect(win.locator('tbody.div_tbody tr')).not.toHaveCount(0, { timeout: 30_000 });
  expect(await menusAroundSearch()).toBe(1);

  await closeWindow(win);

  const again = await openList(page, TABLE);
  await expect(again.locator('tbody.div_tbody tr')).not.toHaveCount(0, { timeout: 30_000 });
  expect(await menusAroundSearch()).toBe(1);

  await closeWindow(again);
  guard.assertClean();
});

test('explorer shell: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const explorerWarnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;

    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('librairie/myddeExplorer.js')) explorerWarnings.push(message.text());
  });

  await page.evaluate(() => {
  });

  const win = await openList(page, TABLE);
  await expect(win.locator('tbody.div_tbody tr')).not.toHaveCount(0, { timeout: 30_000 });

  // Exercise the delegated search path too — that is where the bulk of this
  // file's former shim surface lived.
  const search = win.locator('input[placeholder=Rechercher]');
  await search.pressSequentially('zz', { delay: 20 });
  await page.waitForTimeout(1_000);

  await closeWindow(win);

  expect(explorerWarnings).toEqual([]);
});
