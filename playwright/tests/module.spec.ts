/**
 * engine/module.js — reloadModule/reloadScope/closeModule, called from
 * app_socket.js's receive_cmd handlers (act_reload_module, act_upd_data,
 * act_close_mdl) to refresh or close every DOM node carrying a given
 * `mdl`/`scope` attribute.
 *
 * The boot renders several real `[mdl]` nodes on the desktop
 * (app_gui_calendar among them) — used here directly rather than waiting
 * for a server-pushed receive_cmd event, which would mean finding a way to
 * trigger one from outside; reloadModule/reloadScope's own logic (finding
 * the right node, rebuilding its `vars`, calling socketModule) is what
 * matters, not how something upstream decided to call them.
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('module: reloadModule finds the tagged node and re-invokes socketModule on it', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const target = page.locator('[mdl="app/app_gui/app_gui_calendar"]').first();
  await expect(target).toHaveCount(1);

  const call = await page.evaluate(() => {
    return new Promise<{ mdl: string; sameNode: boolean }>((resolve) => {
      const node = document.querySelector('[mdl="app/app_gui/app_gui_calendar"]') as any;
      const original = node.socketModule;
      node.socketModule = function (mdl: string) {
        node.socketModule = original; // restore before it actually fires
        resolve({ mdl, sameNode: this === node });
        return original.apply(this, arguments);
      };
      (window as any).reloadModule('app/app_gui/app_gui_calendar', '*');
    });
  });

  expect(call.mdl).toBe('app/app_gui/app_gui_calendar');
  expect(call.sameNode).toBe(true);

  guard.assertClean();
});

test('module: closeModule falls back to removing a node with no .close', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  // Give a disposable clone the mdl this test targets, distinct from the
  // real app_gui_calendar node, and no .close property — closeModule's
  // try/catch must fall through to remove() rather than throw.
  const removed = await page.evaluate(() => {
    const probe = document.createElement('div');
    probe.setAttribute('mdl', 'probe/module_close_test');
    probe.setAttribute('value', 'x');
    document.body.appendChild(probe);
    (window as any).closeModule('probe/module_close_test', 'x');
    return !document.body.contains(probe);
  });

  expect(removed).toBe(true);
  guard.assertClean();
});

test('module: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const moduleWarnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('engine/module.js')) moduleWarnings.push(message.text());
  });

  await page.evaluate(() => {
    (window as any).IDAE_SHIM_WARN = 1;
    (window as any).__idaeShimInstallWarn();
  });

  await page.evaluate(() => {
    (window as any).reloadModule('app/app_gui/app_gui_calendar', '*');
  });
  await page.waitForTimeout(500);

  expect(moduleWarnings).toEqual([]);
});
