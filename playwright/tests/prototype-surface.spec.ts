/**
 * The contract of the PrototypeJS → idae-be migration.
 *
 * Every symbol asserted here is one the application actually calls (counted
 * across `idae/web/javascript/` and the PHP/Latte templates). This spec must
 * pass identically before and after `require_hell` is swapped for the idae-be
 * bundle plus its shims — that is the whole point of it.
 *
 * It is deliberately a *surface* check, not a behaviour check: it answers "is
 * the symbol there, and is it the right kind of thing", which is what catches a
 * shim file that forgot an export. Behaviour is covered by the feature specs.
 */
import { test, expect } from '@playwright/test';
import { openApp } from './fixtures/auth';
import { watchConsole } from './helpers/console-guard';

/** Globals the app calls directly. */
const GLOBAL_FUNCTIONS = ['$', '$$', '$A', '$H', '$w', '$F', '$R'];

const GLOBAL_OBJECTS = ['Prototype', 'Ajax', 'Event', 'Element', 'Effect', 'Position', 'Insertion', 'Try'];

const CONSTRUCTORS = ['Class', 'Template', 'Hash', 'ObjectRange', 'PeriodicalExecuter', 'Draggable', 'Draggables'];

/**
 * Methods the app calls on an element, ordered by call count.
 *
 * Checked on a live element rather than on `Element.prototype`: Prototype 1.7.3
 * patches `HTMLElement.prototype`, a shim may well choose a different host, and
 * what the application actually depends on is that `someElement.foo()` works.
 */
const ELEMENT_METHODS = [
  'readAttribute', 'setStyle', 'select', 'observe', 'addClassName', 'update', 'insert',
  'hide', 'up', 'show', 'writeAttribute', 'identify', 'fire', 'remove', 'removeClassName',
  'hasClassName', 'next', 'down', 'getStyle', 'getDimensions', 'getHeight', 'stopObserving',
  'getWidth', 'childElements', 'previous', 'visible', 'empty', 'wrap', 'clonePosition',
  'cumulativeOffset', 'siblings', 'scrollTo', 'toggleClassName', 'setOpacity', 'classNames',
  'toggle', 'makePositioned', 'viewportOffset', 'inspect', 'purge', 'relativize', 'replace',
  // Scriptaculous, called by main_bag.js itself on the boot progress bar
  'fade', 'appear', 'morph',
];

const ARRAY_METHODS = [
  'each', 'invoke', 'first', 'last', 'size', 'without', 'include', 'clone', 'toArray',
  'reject', 'all', 'any', 'collect', 'findAll', 'detect', 'pluck', 'sortBy', 'compact', 'inject',
];

const STRING_METHODS = [
  'stripTags', 'strip', 'toQueryParams', 'stripScripts', 'unescapeHTML', 'escapeHTML',
  'gsub', 'sub', 'scan', 'evalScripts', 'camelize', 'evalJSON', 'capitalize', 'blank',
  'include', 'truncate', 'underscore', 'dasherize', 'toArray',
];

const FUNCTION_METHODS = ['bind', 'bindAsEventListener', 'defer', 'delay', 'curry', 'argumentNames', 'methodize', 'wrap'];

const NUMBER_METHODS = ['toPaddedString', 'times', 'succ'];

/** Static members reached through a namespace object. */
const NAMESPACED = [
  ['Class', 'create'],
  ['Object', 'extend'],
  ['Ajax', 'Request'],
  ['Ajax', 'Updater'],
  ['Ajax', 'Responders'],
  ['Event', 'observe'],
  ['Event', 'stop'],
  ['Event', 'element'],
  ['Element', 'extend'],
  ['Element', 'addMethods'],
  ['Effect', 'Appear'],
  ['Effect', 'Fade'],
  ['Effect', 'Parallel'],
  ['Effect', 'Opacity'],
  ['Effect', 'Move'],
  ['Effect', 'Scale'],
  ['Effect', 'SlideUp'],
  ['Effect', 'SlideDown'],
  ['Insertion', 'After'],
  ['Insertion', 'Before'],
  ['Insertion', 'Top'],
  ['Insertion', 'Bottom'],
  ['Position', 'prepare'],
  ['Position', 'cumulativeOffset'],
  ['Position', 'within'],
  ['Position', 'absolutize'],
  ['Position', 'relativize'],
];

test('prototype surface: every API the app calls is present', async ({ page }) => {
  const guard = watchConsole(page);
  await openApp(page);

  const missing = await page.evaluate(
    ([globalFns, globalObjs, ctors, elementMethods, arrayMethods, stringMethods, fnMethods, numberMethods, namespaced]) => {
      const out: Record<string, string[]> = {};
      const push = (group: string, name: string) => {
        (out[group] ||= []).push(name);
      };
      const w = window as any;

      for (const name of globalFns) {
        if (typeof w[name] !== 'function') push('global functions', `${name} (${typeof w[name]})`);
      }
      for (const name of globalObjs) {
        if (w[name] === undefined || w[name] === null) push('global objects', name);
      }
      for (const name of ctors) {
        if (typeof w[name] !== 'function' && typeof w[name] !== 'object') push('constructors', name);
      }
      const probe = document.createElement('div');
      document.body.appendChild(probe);
      const el = typeof w.$ === 'function' ? w.$(probe) : probe;
      for (const name of elementMethods) {
        if (typeof el?.[name] !== 'function') push('element methods', name);
      }
      probe.remove();
      for (const name of arrayMethods) {
        if (typeof Array.prototype[name as any] !== 'function') push('Array.prototype', name);
      }
      for (const name of stringMethods) {
        if (typeof String.prototype[name as any] !== 'function') push('String.prototype', name);
      }
      for (const name of fnMethods) {
        if (typeof Function.prototype[name as any] !== 'function') push('Function.prototype', name);
      }
      for (const name of numberMethods) {
        if (typeof Number.prototype[name as any] !== 'function') push('Number.prototype', name);
      }
      for (const [ns, member] of namespaced) {
        if (w[ns] === undefined || w[ns][member] === undefined) push('namespaced', `${ns}.${member}`);
      }
      return out;
    },
    [
      GLOBAL_FUNCTIONS, GLOBAL_OBJECTS, CONSTRUCTORS, ELEMENT_METHODS,
      ARRAY_METHODS, STRING_METHODS, FUNCTION_METHODS, NUMBER_METHODS, NAMESPACED,
    ] as const
  );

  expect(missing, `missing API surface:\n${JSON.stringify(missing, null, 2)}`).toEqual({});
  guard.assertClean();
});

test('prototype surface: core helpers actually behave', async ({ page }) => {
  const guard = watchConsole(page);
  await openApp(page);

  const results = await page.evaluate(() => {
    const w = window as any;
    const probe = document.createElement('div');
    probe.id = 'pw_surface_probe';
    probe.innerHTML = '<span class="a b">one</span><span class="a">two</span>';
    document.body.appendChild(probe);

    try {
      const el = w.$('pw_surface_probe');
      const spans = w.$$('#pw_surface_probe span.a');
      const child = el.down('span');

      child.addClassName('marked');
      child.writeAttribute('data-probe', 'yes');
      child.setStyle({ color: 'rgb(1, 2, 3)' });

      return {
        dollarReturnsElement: el === probe,
        dollarDollarCount: spans.length,
        selectCount: el.select('span').length,
        downIsFirstSpan: child === probe.firstElementChild,
        upIsProbe: child.up() === probe,
        addClassName: child.hasClassName('marked'),
        readAttribute: child.readAttribute('data-probe'),
        getStyleColor: child.getStyle('color'),
        // Enumerable over a real Array
        arrayEach: (() => { let n = 0; [1, 2, 3].each((v: number) => { n += v; }); return n; })(),
        arrayPluck: [{ x: 1 }, { x: 2 }].pluck('x').join(','),
        stringStripTags: '<b>hi</b>'.stripTags(),
        stringCamelize: 'foo-bar'.camelize(),
        functionBindThis: (function (this: any) { return this.v; }).bind({ v: 42 })(),
        classCreateWorks: (() => {
          const K = w.Class.create({ initialize(v: number) { (this as any).v = v; }, get() { return (this as any).v; } });
          return new K(7).get();
        })(),
        hashGet: new w.Hash({ a: 5 }).get('a'),
        objectRange: w.$R(1, 3).toArray().join(','),
        dollarW: w.$w('a b c').length,
        dollarA: w.$A({ 0: 'x', 1: 'y', length: 2 }).join(','),
      };
    } finally {
      probe.remove();
    }
  });

  expect(results).toMatchObject({
    dollarReturnsElement: true,
    dollarDollarCount: 2,
    selectCount: 2,
    downIsFirstSpan: true,
    upIsProbe: true,
    addClassName: true,
    readAttribute: 'yes',
    getStyleColor: 'rgb(1, 2, 3)',
    arrayEach: 6,
    arrayPluck: '1,2',
    stringStripTags: 'hi',
    stringCamelize: 'fooBar',
    functionBindThis: 42,
    classCreateWorks: 7,
    hashGet: 5,
    objectRange: '1,2,3',
    dollarW: 3,
    dollarA: 'x,y',
  });
  guard.assertClean();
});

test('boot: cold load and login produce no console errors', async ({ page }) => {
  const guard = watchConsole(page);
  await openApp(page);

  // The desktop shell is the visible proof the SPA finished wiring itself up.
  await expect(page.locator('#desktop')).toBeAttached();
  await expect(page.locator('#mainApp')).toBeAttached();
  await expect(page.locator('#taskBar')).toBeAttached();

  guard.assertClean();
});
