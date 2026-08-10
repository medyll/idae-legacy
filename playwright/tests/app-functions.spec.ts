/**
 * app/app_functions.js — grab-bag of app globals. After this migration it
 * keeps only the nine functions with real callers (save_settings alone has
 * 39); the zero-caller ones were deleted, including the whole idle cluster
 * that carried this file's three Ajax.Request call sites.
 *
 * The survivors are mostly pure or fire-and-forget (they call
 * ajaxValidation and return), so this spec pins the two that actually
 * compute something against the DOM — chkDispZone and
 * save_setting_autoNext — plus clean_string, and asserts the deleted names
 * are really gone.
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('app_functions: the live globals are present and the dead ones are gone', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const state = await page.evaluate(() => {
    const live = ['openDoc', 'mce_area', 'changeCnameTrick', 'popopen', 'chkDispZone',
                  'clean_string', 'save_setting_autoNext', 'save_settings', 'del_settings'];
    const dead = ['registerMdl', 'chekIdle', 'isIdle', 'isIdleMove', 'isIdleMoveOut',
                  'gereDate', 'edit_in_place'];
    return {
      missingLive: live.filter((n) => typeof (window as any)[n] !== 'function'),
      stillDefined: dead.filter((n) => typeof (window as any)[n] === 'function'),
    };
  });

  expect(state.missingLive).toEqual([]);
  expect(state.stillDefined).toEqual([]);

  guard.assertClean();
});

test('app_functions: chkDispZone pulls an off-screen node back inside the viewport', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const result = await page.evaluate(() => {
    const node = document.createElement('div');
    Object.assign(node.style, {position: 'absolute', width: '200px', height: '100px'});
    document.body.appendChild(node);

    // Pushed far past the right edge and above the top edge.
    node.style.left = (window.innerWidth + 500) + 'px';
    node.style.top = '0px';
    (window as any).chkDispZone(node);
    const clampedLeft = parseInt(node.style.left, 10);

    node.style.left = '10px';
    node.style.top = '-50px';
    // offsetTop is read-only and reflects the -50px, so the "above the
    // viewport" branch applies.
    (window as any).chkDispZone(node);
    const clampedTop = node.style.top;

    node.remove();
    return {clampedLeft, clampedTop, viewportWidth: window.innerWidth};
  });

  // Clamped to viewport.width - containerWidth (200px wide node).
  expect(result.clampedLeft).toBe(result.viewportWidth - 200);
  expect(result.clampedTop).toBe('0px');

  guard.assertClean();
});

test('app_functions: save_setting_autoNext posts the next sibling\'s display', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const sent = await page.evaluate(() => {
    return new Promise<string>((resolve) => {
      const host = document.createElement('div');
      document.body.appendChild(host);
      const trigger = document.createElement('a');
      host.appendChild(trigger);
      const panel = document.createElement('div');
      panel.style.display = 'none';
      host.appendChild(panel);

      const original = (window as any).ajaxValidation;
      (window as any).ajaxValidation = function (action: string, path: string, pars: string) {
        (window as any).ajaxValidation = original;
        host.remove();
        resolve(pars);
      };

      (window as any).save_setting_autoNext(trigger, 'probe_key');
    });
  });

  // Reads the *next sibling's* computed display, debounced 500ms.
  expect(sent).toBe('key=probe_key&value=none');

  guard.assertClean();
});

test('app_functions: clean_string slugifies', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const out = await page.evaluate(() => (window as any).clean_string('Hello   World! Foo'));
  expect(out).toBe('hello-world-foo');

  guard.assertClean();
});

test('app_functions: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const warnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('app/app_functions.js')) warnings.push(message.text());
  });

  await page.evaluate(() => {
    (window as any).IDAE_SHIM_WARN = 1;
    (window as any).__idaeShimInstallWarn();
  });

  await page.evaluate(() => {
    const node = document.createElement('div');
    Object.assign(node.style, {position: 'absolute', width: '200px', height: '100px', left: '99999px'});
    document.body.appendChild(node);
    (window as any).chkDispZone(node);
    (window as any).clean_string('A B c!');
    (window as any).changeCnameTrick();
    node.remove();
  });
  await page.waitForTimeout(300);

  expect(warnings).toEqual([]);
});
