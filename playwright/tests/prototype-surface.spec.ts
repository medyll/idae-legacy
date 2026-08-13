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
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all three tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

/** Globals the app calls directly. */
const GLOBAL_FUNCTIONS = ['$', '$$', '$A', '$H', '$w', '$F', '$R'];

// 'Effect' dropped 2026-08-09: shim-effects.js was deleted once every real
// caller of Effect.*/Element#fade() migrated to native code (fadeElement/
// appearElement in engine/methods.js, or a small file-local helper — see
// BE_PLAN.md's 2026-08-09 entries). This spec is the contract for what the
// app still depends on, not a museum of what Prototype once offered — an
// API this file asserted forever after its last caller was gone would have
// blocked deleting the shim that provided it.
//
// 'Ajax' dropped 2026-08-11 for the same reason: no `new Ajax.*` survives
// outside vendor/ and flotr/'s own
// bundled Prototype 1.6, and the last reference — the Ajax.Responders pair in
// engine/initApp.js — was unreachable code, since Responders only fire for
// requests created through Ajax.Request/Ajax.Updater. 'Form' dropped
// 2026-08-13 after those template callers moved to serializeFields.
const GLOBAL_OBJECTS = ['Prototype', 'Event', 'Try'];

// 'PeriodicalExecuter' dropped 2026-08-11 with the Ajax namespace it shipped
// alongside. It never had a caller anywhere in the app — asserting it kept a
// class alive that nothing had ever constructed.
// 'Draggable' / 'Draggables' dropped 2026-08-11, when shim-draggable.js was
// deleted. librairie/cropper.js was its only caller — CropDraggable subclasses
// Draggable — and now carries its own copy, so nothing global provides these
// any more. Asserting them here would have blocked the deletion for the sake
// of a class no shim owns.
const CONSTRUCTORS = ['Class', 'Template', 'Hash', 'ObjectRange'];

// Element compatibility now belongs only to shim-event. The generic Element
// surface was removed with shim-element on 2026-08-13.
const ELEMENT_METHODS = ['on', 'observe', 'stopObserving', 'fire'];

// ARRAY_METHODS / STRING_METHODS / FUNCTION_METHODS / NUMBER_METHODS — 49
// names in all — were dropped 2026-08-12 together with shim-enumerable.js,
// the file that provided every one of them.
//
// Each name was audited individually against the loaded JavaScript and the
// SPA's templates before the file went. Exactly one had a live caller:
// Array#each, in the three Google-Maps modules (app_custom_map.php,
// app_custom_map_zone.php, app_custom_ville_map.php), all of the identical
// shape `markers.each(function (node, index) {...})` over a plain array.
// Those are now forEach and the semantics are the same, index included.
//
// Everything else that a textual scan turned up was one of:
//   - a dead file (autobahn.min.js, require.js, app_test.js, app_draggable.js,
//     librairie/tinyeditor.js, ms-lib-prototype/ — none reachable from
//     main_bag.js's require_trame nor from any dynamic loader),
//   - a same-named native (String#replace, Promise.all/reject,
//     Function#bind — 283 live `.bind(this)` sites, all served by the native
//     from now on, whose currying matches Prototype's for these),
//   - or a library's own object method (query-engine.js's util.toArray).
//
// The first pass of that audit used a regex with a `(?<![\w$])` lookbehind
// before the dot, which only matched `).foo(` and ` .foo(` and therefore
// missed `element.select(` — nearly every real call site. The numbers here
// come from the corrected scan.

/** Static members reached through a namespace object. */
const NAMESPACED = [
  ['Class', 'create'],
  ['Object', 'extend'],
  ['Event', 'observe'],
  ['Event', 'stop'],
  ['Event', 'element'],
];

test('prototype surface: every API the app calls is present', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const missing = await page.evaluate(
    ([globalFns, globalObjs, ctors, elementMethods, namespaced]) => {
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
      for (const [ns, member] of namespaced) {
        if (w[ns] === undefined || w[ns][member] === undefined) push('namespaced', `${ns}.${member}`);
      }
      return out;
    },
    [GLOBAL_FUNCTIONS, GLOBAL_OBJECTS, CONSTRUCTORS, ELEMENT_METHODS, NAMESPACED] as const
  );

  expect(missing, `missing API surface:\n${JSON.stringify(missing, null, 2)}`).toEqual({});
  guard.assertClean();
});

test('prototype surface: core helpers actually behave', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const results = await page.evaluate(() => {
    const w = window as any;
    const probe = document.createElement('div');
    probe.id = 'pw_surface_probe';
    probe.innerHTML = '<span class="a b">one</span><span class="a">two</span>';
    document.body.appendChild(probe);

    try {
      const el = w.$('pw_surface_probe');
      const spans = w.$$('#pw_surface_probe span.a');
      const child = el.querySelector('span') as HTMLElement;

      child.classList.add('marked');
      child.setAttribute('data-probe', 'yes');
      child.style.color = 'rgb(1, 2, 3)';

      return {
        dollarReturnsElement: el === probe,
        dollarDollarCount: spans.length,
        selectCount: el.querySelectorAll('span').length,
        downIsFirstSpan: child === probe.firstElementChild,
        upIsProbe: child.parentElement === probe,
        addClassName: child.classList.contains('marked'),
        readAttribute: child.getAttribute('data-probe'),
        getStyleColor: getComputedStyle(child).color,
        // The four Enumerable probes that sat here (Array#each, Array#pluck,
        // String#stripTags, String#camelize) went with shim-enumerable on
        // 2026-08-12 — see the note above the element-methods list.
        functionBindThis: (function (this: any) { return this.v; }).bind({ v: 42 })(),
        // Template is the one Class.create consumer left in the templates,
        // and its evaluate() no longer runs through String#gsub — shim-class
        // carries a local cls_gsub. Two placeholders, because the bug a
        // single-placeholder probe would miss is exactly the one a
        // non-global regex + String#replace would introduce.
        templateEvaluate: new w.Template('#{a}-#{b}').evaluate({ a: 'x', b: 'y' }),
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
    functionBindThis: 42,
    templateEvaluate: 'x-y',
    classCreateWorks: 7,
    hashGet: 5,
    objectRange: '1,2,3',
    dollarW: 3,
    dollarA: 'x,y',
  });
  guard.assertClean();
});

// A third test used to live here re-checking a cold boot's console output
// and desktop landmarks. Once this file shares one boot across its tests
// (see fixtures/shared-boot.ts), a test called that would no longer be
// checking a *cold* load — it'd be checking state after the two tests above
// already ran. smoke.spec.ts asserts the same thing (#desktop, #taskBar,
// zero console errors) against a genuinely fresh boot; nothing lost.
