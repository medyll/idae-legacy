/**
 * app/app_menu.js — global contextual-menu handler. Self-instantiates at
 * load (last line of the file) and delegates clicks on `[data-menu]`
 * anywhere in the page, revealing the element's next sibling as a menu.
 *
 * Load-bearing on every list: myddeExplorer's search input is stamped with
 * `data-menu` by act_expl_search_input, and its two-option scope menu is
 * the sibling this file reveals (explorer-shell.spec.ts asserts that menu
 * is built; this file asserts clicking actually opens it).
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('app_menu: instantiates itself at load and owns #div_app_menu', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const state = await page.evaluate(() => {
    const el = document.getElementById('div_app_menu');
    return {
      exists: !!el,
      isBodyChild: el ? el.parentElement === document.body : false,
      hasClass: el ? el.classList.contains('context_app_menu') : false,
      // build() calls hideMenu(), which hides on a 250ms timer.
      display: el ? el.style.display : null,
    };
  });

  expect(state.exists).toBe(true);
  expect(state.isBodyChild).toBe(true);
  expect(state.hasClass).toBe(true);

  guard.assertClean();
});

test('app_menu: clicking a [data-menu] element reveals and positions its sibling', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const result = await page.evaluate(async () => {
    const host = document.createElement('div');
    Object.assign(host.style, {position: 'absolute', left: '40px', top: '80px'});
    document.body.appendChild(host);

    const trigger = document.createElement('a');
    trigger.setAttribute('data-menu', 'data-menu');
    trigger.textContent = 'open';
    Object.assign(trigger.style, {display: 'block', width: '60px', height: '20px'});
    host.appendChild(trigger);

    const menu = document.createElement('div');
    menu.className = 'am_probe_menu';
    menu.style.display = 'none';
    menu.innerHTML = '<a>one</a><a>two</a>';
    host.appendChild(menu);

    trigger.click();
    await new Promise((r) => setTimeout(r, 50));

    const shown = menu.style.display !== 'none';
    const positioned = menu.style.position;
    // hide_on_click is added so observers.js's outside-click handler can
    // dismiss it later.
    const dismissible = menu.classList.contains('hide_on_click');
    // clonePosition placed it against the trigger, offset by its height.
    const hasLeft = menu.style.left !== '';
    const hasTop = menu.style.top !== '';

    host.remove();
    return {shown, positioned, dismissible, hasLeft, hasTop};
  });

  expect(result.shown).toBe(true);
  expect(result.positioned).toBe('absolute');
  expect(result.dismissible).toBe(true);
  expect(result.hasLeft).toBe(true);
  expect(result.hasTop).toBe(true);

  guard.assertClean();
});

test('app_menu: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const warnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('app/app_menu.js')) warnings.push(message.text());
  });

  await page.evaluate(() => {
    (window as any).IDAE_SHIM_WARN = 1;
    (window as any).__idaeShimInstallWarn();
  });

  await page.evaluate(async () => {
    const host = document.createElement('div');
    Object.assign(host.style, {position: 'absolute', left: '40px', top: '80px'});
    document.body.appendChild(host);
    const trigger = document.createElement('a');
    trigger.setAttribute('data-menu', 'data-menu');
    host.appendChild(trigger);
    const menu = document.createElement('div');
    menu.style.display = 'none';
    menu.innerHTML = '<a>one</a>';
    host.appendChild(menu);

    trigger.click();
    await new Promise((r) => setTimeout(r, 50));
    // An outside click drives onClickBtn.
    document.body.click();
    await new Promise((r) => setTimeout(r, 320));
    host.remove();
  });
  await page.waitForTimeout(300);

  expect(warnings).toEqual([]);
});
