/**
 * app/app_quickfind.js — client-side filter over a list: typing in a
 * `[data-quickFind]` input hides every `tag` node whose text doesn't
 * contain the query. Instantiated by app_insertionQ.js:244; real, used on
 * app_dispatch_inner, app_fiche_maxi_liste, app_scheme_field_type,
 * app_scheme_has_field, app_user_pref_scheme and appsite_scheme_values.
 *
 * Not to be confused with the global `quickFind(value, where, tag, spy)`
 * in engine/engine.js, which older templates call from inline onkeyup —
 * same idea, unrelated code, not touched here.
 *
 * Built as a fixture rather than opened through one of those screens: the
 * class only needs an input plus a container of taggable nodes, and the
 * real screens each need their own scheme/dispatch data.
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('quickfind: typing hides non-matching rows and restores them when cleared', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const result = await page.evaluate(async () => {
    const wrap = document.createElement('div');
    wrap.id = 'qf_probe_wrap';
    document.body.appendChild(wrap);

    const input = document.createElement('input');
    input.type = 'text';
    wrap.appendChild(input);

    const where = document.createElement('div');
    where.id = 'qf_probe_where';
    where.innerHTML =
      '<div class="qf_row">alpha widget</div>' +
      '<div class="qf_row">beta <b>gadget</b></div>' +
      '<div class="qf_row">gamma thing</div>';
    wrap.appendChild(where);

    const qf = new (window as any).QuickFind(input, {where: 'qf_probe_where', tag: '.qf_row'});

    // The search icon is inserted right after the input by the constructor.
    const iconAfterInput = !!(input.nextElementSibling && input.nextElementSibling.hasAttribute('search_icon'));

    const rows = () => Array.from(where.querySelectorAll('.qf_row')) as HTMLElement[];
    const visible = () => rows().filter((r) => r.style.display !== 'none').map((r) => r.textContent);

    // perform_search is debounced 500ms behind keyup, and the show/hide is
    // itself deferred another 10ms (Prototype's Function#defer) — call it
    // directly and wait out the defer rather than racing two timers.
    input.value = 'gadget';
    qf.perform_search();
    await new Promise((r) => setTimeout(r, 60));
    const afterFilter = visible();

    input.value = '';
    qf.perform_search();
    await new Promise((r) => setTimeout(r, 60));
    const afterClear = visible();

    wrap.remove();
    return {iconAfterInput, afterFilter, afterClear};
  });

  expect(result.iconAfterInput).toBe(true);
  // Only the row containing "gadget" survives — and it matches through a
  // nested <b>, proving the text is compared after stripTags.
  expect(result.afterFilter).toEqual(['beta gadget']);
  expect(result.afterClear).toHaveLength(3);

  guard.assertClean();
});

test('quickfind: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const warnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('app/app_quickfind.js')) warnings.push(message.text());
  });

  await page.evaluate(() => {
  });

  await page.evaluate(async () => {
    const wrap = document.createElement('div');
    document.body.appendChild(wrap);
    const input = document.createElement('input');
    input.type = 'text';
    wrap.appendChild(input);
    const where = document.createElement('div');
    where.id = 'qf_shimprobe_where';
    where.innerHTML = '<div class="qf_row">alpha</div><div class="qf_row">beta</div>';
    wrap.appendChild(where);

    const qf = new (window as any).QuickFind(input, {where: 'qf_shimprobe_where', tag: '.qf_row'});
    input.value = 'alpha';
    qf.perform_search();
    await new Promise((r) => setTimeout(r, 60));
    input.value = '';
    qf.perform_search();
    await new Promise((r) => setTimeout(r, 60));
    wrap.remove();
  });
  await page.waitForTimeout(300);

  expect(warnings).toEqual([]);
});
