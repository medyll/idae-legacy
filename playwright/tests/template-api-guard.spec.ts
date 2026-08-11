/**
 * The templates' half of the Prototype contract.
 *
 * Three times in three days the same bug shipped: an API was removed (or never
 * installed), no JavaScript called it, so every check we had said "safe" — and
 * the PHP templates called it anyway, from inline onclick/onsubmit attributes,
 * failing only when a user clicked.
 *
 *   - Form.serializeElements  — 8 call sites, missing since the Phase 3/4 swap
 *   - $(form).serialize()     — 13 call sites, same
 *   - Effect.Appear/Highlight — 3 call sites, dead the moment shim-effects.js
 *                               was deleted on 2026-08-09
 *
 * None of our tools looked at templates. The migration greps scanned
 * javascript/. IDAE_SHIM_WARN only sees what a test session actually clicks.
 * prototype-surface.spec.ts is a hand-written list derived from a JS-only
 * inventory — it asserted Ajax.Request (zero callers) while missing
 * Form.serialize (31 callers), and it actively signed off on dropping Effect.
 *
 * So this spec does not hardcode a list. It *derives* one: scan the templates
 * on disk, pull out every name that belongs to Prototype's API surface, then
 * ask the live page whether each one exists. Delete a shim that a template
 * still needs and this fails, without anyone having remembered to update it.
 *
 * Deliberately a presence check, not a behaviour check — the same division of
 * labour as prototype-surface.spec.ts. It answers "would this call throw
 * ReferenceError/TypeError", which is the failure mode all three bugs had.
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';
import { readdirSync, readFileSync, statSync } from 'node:fs';
import { join, resolve, sep } from 'node:path';

const getPage = sharedPage();

const WEB_ROOT = resolve(process.cwd(), '../idae/web');
const TEMPLATE_EXT = ['.php', '.latte', '.tpl'];

/**
 * Skipped subtrees. `vendor/` and `adodb/` are server-side PHP libraries with
 * no inline JavaScript — including them turns every PHP method call into a
 * false positive (1,449 hits on `Builder` alone). `flotr/` bundles its own
 * Prototype 1.6 and is self-contained; CLAUDE.md says leave it be.
 */
const SKIP_DIRS = new Set(['vendor', 'adodb', 'flotr', 'node_modules', '.git', 'nbproject']);

/** Prototype globals reached as `Name.member` or `new Name.member(...)`. */
const NAMESPACES = ['Effect', 'Ajax', 'Insertion', 'Position', 'Form', 'Field',
  'Element', 'Event', 'Class', 'Try', 'Draggable', 'Sortable', 'Builder',
  'Autocompleter', 'PeriodicalExecuter', 'Template', 'Hash', 'ObjectRange', 'Abstract'];

/**
 * Prototype's instance-method vocabulary. A closed set on purpose: matching
 * every `.foo(` in a PHP file would drown the signal in server-side calls.
 * A name here is probed against Element, Array, String, Function and Number,
 * and passes if any of them provides it — so a collision with an unrelated
 * PHP method name is harmless, it just gets checked and found present.
 */
const METHODS = [
  // Element
  'visible', 'toggle', 'hide', 'show', 'remove', 'update', 'replace', 'insert', 'wrap',
  'ancestors', 'descendants', 'firstDescendant', 'immediateDescendants', 'previousSiblings',
  'nextSiblings', 'siblings', 'match', 'up', 'down', 'previous', 'next', 'select', 'adjacent',
  'identify', 'readAttribute', 'writeAttribute', 'getHeight', 'getWidth', 'classNames',
  'hasClassName', 'addClassName', 'removeClassName', 'toggleClassName', 'cleanWhitespace',
  'empty', 'descendantOf', 'scrollTo', 'getStyle', 'getOpacity', 'setStyle', 'setOpacity',
  'getDimensions', 'makePositioned', 'undoPositioned', 'makeClipping', 'undoClipping',
  'cumulativeOffset', 'positionedOffset', 'absolutize', 'relativize', 'cumulativeScrollOffset',
  'getOffsetParent', 'viewportOffset', 'clonePosition', 'purge', 'observe', 'stopObserving',
  'fire', 'childElements', 'getValue', 'setValue', 'activate', 'present', 'serialize',
  'focusFirstElement', 'getInputs',
  // Enumerable / Array
  'each', 'eachSlice', 'all', 'any', 'collect', 'detect', 'findAll', 'inGroupsOf', 'inject',
  'invoke', 'partition', 'pluck', 'reject', 'sortBy', 'toArray', 'zip', 'size', 'first', 'last',
  'compact', 'flatten', 'without', 'uniq', 'intersect', 'clone',
  // String
  'gsub', 'sub', 'scan', 'truncate', 'strip', 'stripTags', 'stripScripts', 'extractScripts',
  'evalScripts', 'escapeHTML', 'unescapeHTML', 'toQueryParams', 'camelize', 'capitalize',
  'underscore', 'dasherize', 'evalJSON', 'isJSON', 'blank', 'interpolate', 'succ', 'times',
  // Function
  'bindAsEventListener', 'curry', 'delay', 'defer', 'methodize', 'argumentNames',
  // Number
  'toColorPart', 'toPaddedString',
  // Scriptaculous element effects. Included even though shim-effects.js is
  // gone, precisely *because* it is gone: these names must now come up missing
  // if a template still calls one. Leaving them out is what let three
  // Effect.Appear/Highlight calls (ed8b761) and three `.fade()` calls survive
  // the shim's deletion unnoticed.
  'fade', 'appear', 'blindUp', 'blindDown', 'slideUp', 'slideDown', 'pulsate',
  'highlight', 'morph', 'shake', 'puff', 'grow', 'shrink', 'switchOff',
  'dropOut', 'squish', 'fold',
];

function walk(dir: string, out: string[] = []): string[] {
  for (const entry of readdirSync(dir)) {
    if (SKIP_DIRS.has(entry)) continue;
    const full = join(dir, entry);
    let st;
    try { st = statSync(full); } catch { continue; }
    if (st.isDirectory()) walk(full, out);
    else if (TEMPLATE_EXT.some((e) => entry.endsWith(e))) out.push(full);
  }
  return out;
}

type Found = { namespaced: Set<string>; methods: Set<string>; where: Map<string, string> };

function scanTemplates(): Found {
  const namespaced = new Set<string>();
  const methods = new Set<string>();
  const where = new Map<string, string>();
  const nsRe = new RegExp(`\\b(${NAMESPACES.join('|')})\\.([A-Za-z_]\\w*)`, 'g');
  const methodSet = new Set(METHODS);

  for (const file of walk(WEB_ROOT)) {
    let text: string;
    try { text = readFileSync(file, 'utf8'); } catch { continue; }
    const rel = file.slice(WEB_ROOT.length + 1).split(sep).join('/');

    // Strip line comments so a commented-out call (page_body.latte's
    // `//new Effect.ScrollTo('body')`) does not keep a dead API alive here.
    const code = text.replace(/^\s*(\/\/|\*|#).*$/gm, '');

    for (const m of code.matchAll(nsRe)) {
      const key = `${m[1]}.${m[2]}`;
      if (!namespaced.has(key)) { namespaced.add(key); where.set(key, rel); }
    }
    for (const m of code.matchAll(/\.([A-Za-z_]\w*)\s*\(/g)) {
      if (!methodSet.has(m[1])) continue;
      if (!methods.has(m[1])) { methods.add(m[1]); where.set('.' + m[1] + '()', rel); }
    }
  }
  return { namespaced, methods, where };
}

test('template API guard: every Prototype symbol the templates call exists at runtime', async () => {
  const page = getPage();
  const found = scanTemplates();

  expect(found.methods.size, 'template scan found nothing — the walk is broken').toBeGreaterThan(10);

  const missing = await page.evaluate(
    ([namespaced, methods]) => {
      const w = window as any;
      const out: { namespaced: string[]; methods: string[] } = { namespaced: [], methods: [] };

      for (const key of namespaced) {
        const [ns, member] = key.split('.');
        if (w[ns] == null || w[ns][member] === undefined) out.namespaced.push(key);
      }

      const probe = document.createElement('div');
      probe.innerHTML = '<span>x</span>';
      document.body.appendChild(probe);
      const el = typeof w.$ === 'function' ? w.$(probe) : probe;
      // A name passes if any Prototype-extended host provides it. The scan
      // cannot tell which receiver a `.foo(` in a template belongs to, and it
      // does not need to — all it must rule out is "nothing provides it".
      const hosts: any[] = [el, Array.prototype, String.prototype, Function.prototype, Number.prototype];
      for (const name of methods) {
        if (!hosts.some((h) => typeof h?.[name] === 'function')) out.methods.push(name);
      }
      if (probe.parentNode) probe.parentNode.removeChild(probe);
      return out;
    },
    [[...found.namespaced], [...found.methods]] as const
  );

  const report = [...missing.namespaced, ...missing.methods.map((m) => '.' + m + '()')]
    .map((k) => `  ${k}  (e.g. ${found.where.get(k) ?? found.where.get('.' + k.replace(/^\./, '')) ?? '?'})`)
    .join('\n');

  expect(
    missing.namespaced.length + missing.methods.length,
    `templates call APIs that do not exist at runtime:\n${report}\n\n` +
      'Either restore the API, or migrate the template call sites off it.'
  ).toBe(0);
});
