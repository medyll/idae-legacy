/**
 * app/app_live_data.js — socket-driven live updates: throttled field
 * refreshes for rows that changed server-side, plus the tache/conge
 * re-parenting on the planning boards. Loaded unconditionally
 * (main_bag.js:48); its act_* handlers are what app_socket.js's receive_cmd
 * dispatches into.
 *
 * Driven here by calling the handlers directly against constructed
 * fixtures — reaching them for real means a second client mutating the
 * same record, which this suite has no way to stage.
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('live_data: act_upd_data refreshes subscribed fields and fires dom:data_reload', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const result = await page.evaluate(async () => {
    const row = document.createElement('div');
    row.setAttribute('data-table', 'probe');
    row.setAttribute('data-table_value', '5');
    row.setAttribute('data-uniqid', 'probe_uid');
    row.innerHTML = '<span data-field_name="nomProbe">old</span>';
    document.body.appendChild(row);

    let reloadMemo: any = null;
    row.addEventListener('dom:data_reload', (e: any) => { reloadMemo = e.memo; });

    // Only subscribed tables are processed.
    (window as any).data_subscribe['probe'] = 1;
    (window as any).act_upd_data({table: 'probe', table_value: '5', vars: {nomProbe: 'new'}}, true);

    const fieldText = (row.querySelector('[data-field_name=nomProbe]') as HTMLElement).innerHTML;
    // dom:data_reload is fired on a 100ms timer.
    await new Promise((r) => setTimeout(r, 200));

    const out = {fieldText, reloadMemo};
    delete (window as any).data_subscribe['probe'];
    row.remove();
    return out;
  });

  expect(result.fieldText).toBe('new');
  expect(result.reloadMemo).toEqual({nomProbe: 'new'});

  guard.assertClean();
});

test('live_data: act_upd_data ignores tables nobody subscribed to', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const untouched = await page.evaluate(() => {
    const row = document.createElement('div');
    row.setAttribute('data-table', 'unsubscribed_probe');
    row.setAttribute('data-table_value', '1');
    row.innerHTML = '<span data-field_name="x">old</span>';
    document.body.appendChild(row);

    (window as any).act_upd_data({table: 'unsubscribed_probe', table_value: '1', vars: {x: 'new'}}, true);

    const text = (row.querySelector('[data-field_name=x]') as HTMLElement).innerHTML;
    row.remove();
    return text;
  });

  expect(untouched).toBe('old');
  guard.assertClean();
});

test('live_data: act_close_mdl removes the matching scoped nodes', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const result = await page.evaluate(() => {
    const scoped = document.createElement('div');
    scoped.setAttribute('scope', 'idprobe');
    scoped.setAttribute('value', '9');
    document.body.appendChild(scoped);

    let closed = false;
    scoped.addEventListener('dom:close', () => { closed = true; });

    (window as any).act_close_mdl({vars: {table: 'probe', table_value: '9'}});

    return {closed, stillAttached: document.body.contains(scoped)};
  });

  expect(result.closed).toBe(true);
  expect(result.stillAttached).toBe(false);

  guard.assertClean();
});

test('live_data: deleted niceForm global is gone', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  // librairie/niceForm.js was never in any loader list — only the
  // same-named stylesheet is loaded — and the class was never
  // instantiated, so the file was deleted.
  const t = await page.evaluate(() => typeof (window as any).niceForm);
  expect(t).toBe('undefined');

  guard.assertClean();
});

test('live_data: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const warnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('app/app_live_data.js')) warnings.push(message.text());
  });

  await page.evaluate(() => {
    (window as any).IDAE_SHIM_WARN = 1;
    (window as any).__idaeShimInstallWarn();
  });

  await page.evaluate(async () => {
    const row = document.createElement('div');
    row.setAttribute('data-table', 'shimprobe');
    row.setAttribute('data-table_value', '2');
    row.setAttribute('data-uniqid', 'u');
    row.innerHTML = '<span data-field_name="f">a</span>';
    document.body.appendChild(row);

    (window as any).data_subscribe['shimprobe'] = 1;
    (window as any).act_upd_data({table: 'shimprobe', table_value: '2', vars: {f: 'b'}}, true);
    (window as any).act_add_data({table: 'shimprobe'});
    (window as any).act_update_mdl({mdl: 'app/probe', value: '2', html: 'x'});
    await new Promise((r) => setTimeout(r, 200));
    (window as any).act_close_mdl({vars: {table: 'shimprobe', table_value: '2'}});

    delete (window as any).data_subscribe['shimprobe'];
    if (row.parentNode) row.remove();
  });
  await page.waitForTimeout(300);

  expect(warnings).toEqual([]);
});
