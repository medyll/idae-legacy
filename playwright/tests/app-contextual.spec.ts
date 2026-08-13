/**
 * app/app_contextual.js — right-click contextual menu. Self-instantiates
 * at load (last line of the file) and delegates `contextmenu` on
 * `[data-contextual]` anywhere in the page, loading its content through
 * socketModule.
 *
 * Unlike app_menu.js, this class stores its click handler once in
 * `this._clickHandler`, so add/removeEventListener actually match and the
 * outside-click listener really is removed.
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('app_contextual: builds #app_contextual_menu at load, hidden', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const state = await page.evaluate(() => {
    const el = document.getElementById('app_contextual_menu');
    return {
      exists: !!el,
      isBodyChild: el ? el.parentElement === document.body : false,
      hasClass: el ? el.classList.contains('contextmenu') : false,
      cached: el ? el.getAttribute('data-cache') : null,
      hidden: el ? el.style.display === 'none' : false,
    };
  });

  expect(state.exists).toBe(true);
  expect(state.isBodyChild).toBe(true);
  expect(state.hasClass).toBe(true);
  expect(state.cached).toBe('true');
  // build() calls hideMenu() synchronously.
  expect(state.hidden).toBe(true);

  guard.assertClean();
});

test('app_contextual: right-click marks the node, shows the menu, outside click dismisses', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const result = await page.evaluate(async () => {
    const menu = document.getElementById('app_contextual_menu') as HTMLElement;
    const target = document.createElement('div');
    target.setAttribute('data-contextual', 'table=probe&table_value=1');
    document.body.appendChild(target);

    // socketModule does a real server round trip; stub it so the test
    // stays hermetic — the menu's positioning and lifecycle is what's
    // under test, not the module fetch.
    const originalSocketModule = (menu as any).socketModule;
    let calledWith: any = null;
    (menu as any).socketModule = function (file: string, vars: string) {
      calledWith = {file, vars};
      return this;
    };

    target.dispatchEvent(new MouseEvent('contextmenu', {
      bubbles: true, cancelable: true, clientX: 120, clientY: 140,
    }));

    const marked = target.classList.contains('right_clicked');
    const shown = menu.style.display !== 'none';
    const left = menu.style.left;

    // An outside click runs onClick: hides the menu and clears the mark.
    document.body.click();
    const hiddenAfter = menu.style.display === 'none';
    const unmarked = !target.classList.contains('right_clicked');

    (menu as any).socketModule = originalSocketModule;
    target.remove();
    return {calledWith, marked, shown, left, hiddenAfter, unmarked};
  });

  expect(result.calledWith).toEqual({
    file: 'app/app_contextual/app_contextual',
    vars: 'table=probe&table_value=1',
  });
  expect(result.marked).toBe(true);
  expect(result.shown).toBe(true);
  expect(result.left).not.toBe('');
  expect(result.hiddenAfter).toBe(true);
  expect(result.unmarked).toBe(true);

  guard.assertClean();
});

test('app_contextual: deleted resize.js and cookie.js globals are gone', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const gone = await page.evaluate(() => ({
    resizeable: typeof (window as any).Resizeable,
    cookie: typeof (window as any).Cookie,
  }));

  // Both files were deleted: Resizeable had only commented-out
  // instantiations, and librairie/cookie.js was never in any loader list
  // (the app uses vendor/js.cookie.js and jsoncookie.js instead).
  expect(gone.resizeable).toBe('undefined');
  expect(gone.cookie).toBe('undefined');

  guard.assertClean();
});

test('app_contextual: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const warnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('app/app_contextual.js')) warnings.push(message.text());
  });

  await page.evaluate(() => {
  });

  await page.evaluate(() => {
    const menu = document.getElementById('app_contextual_menu') as HTMLElement;
    const target = document.createElement('div');
    target.setAttribute('data-contextual', 'table=probe');
    document.body.appendChild(target);

    const original = (menu as any).socketModule;
    (menu as any).socketModule = function () { return this; };

    target.dispatchEvent(new MouseEvent('contextmenu', {bubbles: true, cancelable: true, clientX: 10, clientY: 10}));
    (window as any).app_context.prototype.repositionMenu.call({element: menu, pageOffset: 10});
    document.body.click();

    (menu as any).socketModule = original;
    target.remove();
  });
  await page.waitForTimeout(300);

  expect(warnings).toEqual([]);
});
