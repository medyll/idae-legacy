/**
 * The contract of the PrototypeJS → idae-be migration.
 *
 * The migration is complete: this spec proves the old globals and element
 * extensions stay gone while their native replacements keep working.
 *
 * It is deliberately a *surface* check, not a behaviour check: it answers "is
 * the symbol there, and is it the right kind of thing", which is what catches a
 * shim file that forgot an export. Behaviour is covered by the feature specs.
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for both tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

const REMOVED_GLOBALS = [
  '$', '$$', '$A', '$H', '$w', '$F', '$R',
  'Prototype', 'Try', 'Hash', 'ObjectRange', 'Class', 'Template',
  'Form', 'Ajax', 'Effect',
];

test('prototype surface: legacy globals and element methods are absent', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const residual = await page.evaluate(
    (globalNames) => {
      const w = window as any;
      const probe = document.createElement('div');
      const methods = ['on', 'observe', 'stopObserving', 'fire', 'serialize'];
      return {
        globals: globalNames.filter((name) => w[name] !== undefined),
        elementMethods: methods.filter((name) => typeof (probe as any)[name] === 'function'),
      };
    },
    REMOVED_GLOBALS
  );

  expect(residual).toEqual({ globals: [], elementMethods: [] });
  guard.assertClean();
});

test('prototype surface: native replacements behave', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const results = await page.evaluate(() => {
    const probe = document.createElement('div');
    probe.id = 'pw_surface_probe';
    probe.innerHTML = '<span class="a b">one</span><span class="a">two</span>';
    document.body.appendChild(probe);

    try {
      const el = document.getElementById('pw_surface_probe') as HTMLElement;
      const spans = document.querySelectorAll('#pw_surface_probe span.a');
      const child = el.querySelector('span') as HTMLElement;

      child.classList.add('marked');
      child.setAttribute('data-probe', 'yes');
      child.style.color = 'rgb(1, 2, 3)';

      return {
        idReturnsElement: el === probe,
        queryCount: spans.length,
        selectCount: el.querySelectorAll('span').length,
        downIsFirstSpan: child === probe.firstElementChild,
        upIsProbe: child.parentElement === probe,
        addClassName: child.classList.contains('marked'),
        readAttribute: child.getAttribute('data-probe'),
        getStyleColor: getComputedStyle(child).color,
        functionBindThis: (function (this: any) { return this.v; }).bind({ v: 42 })(),
        arrayFrom: Array.from({ 0: 'x', 1: 'y', length: 2 }).join(','),
      };
    } finally {
      probe.remove();
    }
  });

  expect(results).toMatchObject({
    idReturnsElement: true,
    queryCount: 2,
    selectCount: 2,
    downIsFirstSpan: true,
    upIsProbe: true,
    addClassName: true,
    readAttribute: 'yes',
    getStyleColor: 'rgb(1, 2, 3)',
    functionBindThis: 42,
    arrayFrom: 'x,y',
  });
  guard.assertClean();
});

// A third test used to live here re-checking a cold boot's console output
// and desktop landmarks. Once this file shares one boot across its tests
// (see fixtures/shared-boot.ts), a test called that would no longer be
// checking a *cold* load — it'd be checking state after the two tests above
// already ran. smoke.spec.ts asserts the same thing (#desktop, #taskBar,
// zero console errors) against a genuinely fresh boot; nothing lost.
