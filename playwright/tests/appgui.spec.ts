/**
 * librairie/appGui.js — the desktop's tabbed app areas + task bar. One
 * caller, and a load-bearing one: app_gui_main.php:151 does
 * `window.JSGUI = new appGui($('mainApp'))`, so an instance exists on
 * every boot (smoke.spec.ts would fail outright if the constructor threw).
 *
 * `add()` builds real `.inArea` wrappers and task-bar buttons from
 * window.APP.APPTPL['taskBarButton'], so it is driven here against a
 * scratch container and a scratch task bar rather than the live desktop —
 * the point is the DOM this class builds, not where it gets attached.
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('appGui: the desktop instance exists and cleaned its element', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const state = await page.evaluate(() => ({
    isInstance: typeof (window as any).JSGUI === 'object' && (window as any).JSGUI !== null,
    hasAdd: typeof (window as any).JSGUI?.add === 'function',
    boundToMainApp: (window as any).JSGUI?.element === document.getElementById('mainApp'),
    // initialize() runs cleanWhitespace on its element: no whitespace-only
    // text nodes should remain as direct children.
    strayTextNodes: (window as any).JSGUI?.element
      ? Array.from((window as any).JSGUI.element.childNodes)
          .filter((n: any) => n.nodeType === 3 && !/\S/.test(n.nodeValue)).length
      : -1,
  }));

  expect(state.isInstance).toBe(true);
  expect(state.hasAdd).toBe(true);
  expect(state.boundToMainApp).toBe(true);
  expect(state.strayTextNodes).toBe(0);

  guard.assertClean();
});

test('appGui: add() builds an .inArea wrapper, an inner area and a task-bar button', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const result = await page.evaluate(() => {
    const container = document.createElement('div');
    container.id = 'ag_probe_container';
    document.body.appendChild(container);
    const taskBar = document.createElement('div');
    taskBar.id = 'ag_probe_taskbar';
    document.body.appendChild(taskBar);

    const gui = new (window as any).appGui(container);
    const element = gui.add({
      title: 'Probe Title',
      container: container,
      taskBar: taskBar,
      file: 'app/probe',
      vars: '',
    });

    // Ids are derived from the title: spaces to underscores, lowercased.
    const elementId = element.id;
    const wrapper = element.parentElement as HTMLElement;
    const button = document.getElementById('ongprobe_title');

    const out = {
      elementId,
      wrapperIsInArea: wrapper.classList.contains('inArea'),
      wrapperInContainer: wrapper.parentElement === container,
      buttonExists: !!button,
      buttonInTaskBar: button ? button.parentElement === taskBar : false,
      buttonText: button ? (button.textContent || '').includes('Probe Title') : false,
      // activate() ran as part of add() and marked the tab active.
      buttonActive: button ? button.classList.contains('active') : false,
    };

    // add() appends the wrapper to document.body first, then re-appends it
    // into the container, so cleaning up the container is enough.
    container.remove();
    taskBar.remove();
    return out;
  });

  expect(result.elementId).toBe('frmprobe_title');
  expect(result.wrapperIsInArea).toBe(true);
  expect(result.wrapperInContainer).toBe(true);
  expect(result.buttonExists).toBe(true);
  expect(result.buttonInTaskBar).toBe(true);
  expect(result.buttonText).toBe(true);
  expect(result.buttonActive).toBe(true);

  guard.assertClean();
});

test('appGui: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const warnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('librairie/appGui.js')) warnings.push(message.text());
  });

  await page.evaluate(() => {
    (window as any).IDAE_SHIM_WARN = 1;
    (window as any).__idaeShimInstallWarn();
  });

  await page.evaluate(() => {
    const container = document.createElement('div');
    document.body.appendChild(container);
    const taskBar = document.createElement('div');
    document.body.appendChild(taskBar);

    const gui = new (window as any).appGui(container);
    const opts = {title: 'Shim Probe', container: container, taskBar: taskBar, file: 'app/probe', vars: ''};
    gui.add(opts);
    gui.activate(opts);
    gui.close(opts);

    container.remove();
    taskBar.remove();
  });
  await page.waitForTimeout(300);

  expect(warnings).toEqual([]);
});
