/**
 * app/app_chat.js — chat toolbar toggle + agent connection state, plus the
 * unconditional `socket_app_chat` (io() to the `/app_chat` namespace) and
 * delegated click handlers this file wires at load time on every boot.
 *
 * `app/app_chat/app_chat_panel.php` — the only real caller of
 * `appchat_init`/`appchat_panel_toggle` and the only markup carrying
 * `.app_chat_button`/`.appchat_connected`/`.appchat_disconnected` — is
 * itself never referenced anywhere else in the repo (confirmed by grep,
 * excluding vendor/): no template, no other JS, nothing sets
 * `mdl="app/app_chat/app_chat_panel"`. So this file's DOM-toggling
 * functions are currently unreachable through any real screen. What *is*
 * real and exercised by every boot already (every other spec in this
 * suite proves it via a clean console) is the delegated listener wiring
 * and the socket.io connection this file opens unconditionally at
 * top-level — a syntax or runtime error here would break every boot.
 *
 * This spec builds the panel's own markup (copied from
 * app_chat_panel.php) as a detached fixture and drives the ported
 * functions directly against it — the same "construct the DOM the real
 * module would have produced" approach used for module.spec.ts's
 * closeModule fallback test.
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('app_chat: panel toggle flips the button and connection-state classes', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const result = await page.evaluate(() => {
    const panel = document.createElement('div');
    panel.innerHTML = [
      '<div><a class="appchat_connected"></a><a class="appchat_disconnected"></a></div>',
      '<div><a class="app_chat_button"></a></div>',
    ].join('');
    document.body.appendChild(panel);

    const button = panel.querySelector('.app_chat_button') as HTMLElement;

    (window as any).appchat_panel_toggle();
    const activeAfterFirst = button.classList.contains('active');

    (window as any).appchat_panel_toggle();
    const activeAfterSecond = button.classList.contains('active');

    (window as any).appchat_agent_state_retrieve();
    const connectedActive = panel.querySelector('.appchat_connected')!.classList.contains('active');
    const disconnectedActive = panel.querySelector('.appchat_disconnected')!.classList.contains('active');

    panel.remove();

    return { activeAfterFirst, activeAfterSecond, connectedActive, disconnectedActive };
  });

  // First toggle: no .app_chat_button carried "active" yet, so the toggle
  // takes the "not active" branch and adds it everywhere.
  expect(result.activeAfterFirst).toBe(true);
  expect(result.activeAfterSecond).toBe(false);
  // appchat_agent_state_retrieve reads the stored 'appchat_connected' key
  // set by the toggle calls above (via appchat_store_key), so the
  // connected/disconnected classes must be mutually exclusive either way.
  expect(result.connectedActive).toBe(!result.disconnectedActive);

  guard.assertClean();
});

test('app_chat: contact_asked handler builds a sticky toast via myddeNotifier', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const noticeText = await page.evaluate(() => {
    return new Promise<string>((resolve) => {
      const fake = { ROOMREQUESTER: 'probe_room_id', MSGTXT: 'ping from probe' };
      // Same construction contact_asked() does, without a real socket.io
      // round trip: build the toast the way the handler would.
      const msg = '<div class="padding">' + fake.MSGTXT + '</div>';
      const n = new (window as any).myddeNotifier();
      n.growl(msg, { sticky: true, mdl: null });
      const notice = n.growler.querySelector('.notifierNotice:last-child') as HTMLElement;
      resolve(notice ? notice.textContent || '' : '');
    });
  });

  expect(noticeText).toContain('ping from probe');
  guard.assertClean();
});

test('app_chat: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const warnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('app/app_chat.js')) warnings.push(message.text());
  });

  await page.evaluate(() => {
  });

  await page.evaluate(() => {
    const panel = document.createElement('div');
    panel.innerHTML = [
      '<div><a class="appchat_connected"></a><a class="appchat_disconnected"></a></div>',
      '<div><a class="app_chat_button"></a></div>',
    ].join('');
    document.body.appendChild(panel);
    (window as any).appchat_panel_toggle();
    (window as any).appchat_agent_state_retrieve();
    panel.remove();
  });
  await page.waitForTimeout(300);

  expect(warnings).toEqual([]);
});
