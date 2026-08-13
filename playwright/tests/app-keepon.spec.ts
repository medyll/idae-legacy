/**
 * app/app_keepon.js — "keep on" presence/glue socket channel. Loaded
 * unconditionally by main_bag.js on every boot, so its top-level code
 * (socket_keep_on = io(...) to the /keep_on namespace, three delegated
 * click handlers) already runs on every test in the suite. Real,
 * boot-rendered markup: `#socket_keep_on_status` ships in
 * app_gui_main.php (the desktop shell itself).
 *
 * `app/app_keepon/app_keepon_panel.php` — the only source of
 * `.keepon_connected`/`.keepon_disconnected` markup — is, like
 * app_chat_panel.php, never referenced by any template (confirmed by
 * grep). Clicking those buttons throws "keepon_connect_agent is not
 * defined" today — a pre-existing bug (keepon_connect_agent/
 * keepon_disconnect_agent are called but never defined anywhere in the
 * repo), carried forward unchanged, not introduced by this migration.
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('app_keepon: kp_show/kp_hide/kp_update drive the real boot-rendered status element', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const result = await page.evaluate(() => {
    const status = document.getElementById('socket_keep_on_status');
    if (!status) return {found: false};

    const initiallyHidden = status.style.display === 'none';
    (window as any).kp_show(status);
    const shown = status.style.display !== 'none';
    (window as any).kp_update(status, '<i class="fa fa-wifi textrouge"></i> - 2');
    const updatedText = status.textContent;
    (window as any).kp_hide(status);
    const hiddenAgain = status.style.display === 'none';

    // Restore app_gui_main.php's own initial state.
    if (initiallyHidden) status.style.display = 'none';

    return {found: true, initiallyHidden, shown, updatedText, hiddenAgain};
  });

  expect(result.found).toBe(true);
  expect(result.initiallyHidden).toBe(true);
  expect(result.shown).toBe(true);
  expect(result.updatedText).toContain('- 2');
  expect(result.hiddenAgain).toBe(true);

  guard.assertClean();
});

test('app_keepon: the glue_reserve delegate resolves data-appid via kp_up and emits', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const emitted = await page.evaluate(() => {
    const log = document.createElement('div');
    log.id = 'socket_keep_on_log';
    log.innerHTML = '<div data-appid="probe_appid_123"><a class="glue_reserve"></a></div>';
    document.body.appendChild(log);

    localStorage.setItem('PHPSESSID', 'probe_sess');
    localStorage.setItem('IDAGENT', '7');

    let captured: any = null;
    const originalEmit = (window as any).socket_keep_on.emit;
    (window as any).socket_keep_on.emit = function (event: string, payload: any) {
      if (event === 'glue_reserve') captured = payload;
      return originalEmit.apply(this, arguments);
    };

    (log.querySelector('.glue_reserve') as HTMLElement).click();

    (window as any).socket_keep_on.emit = originalEmit;
    log.remove();

    return captured;
  });

  expect(emitted).toMatchObject({RESERVEDID: 'probe_appid_123', IDAGENT: '7'});

  guard.assertClean();
});

test('app_keepon: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const warnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('app/app_keepon.js')) warnings.push(message.text());
  });

  await page.evaluate(() => {
  });

  await page.evaluate(() => {
    const status = document.getElementById('socket_keep_on_status');
    if (status) {
      (window as any).kp_show(status);
      (window as any).kp_update(status, 'probe');
      (window as any).kp_hide(status);
    }
    const log = document.createElement('div');
    log.id = 'socket_keep_on_log';
    log.innerHTML = '<div data-appid="x"><a class="glue_reserve"></a></div>';
    document.body.appendChild(log);
    (log.querySelector('.glue_reserve') as HTMLElement).click();
    log.remove();
  });
  await page.waitForTimeout(300);

  expect(warnings).toEqual([]);
});
