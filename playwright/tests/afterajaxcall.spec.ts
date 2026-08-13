/**
 * afterAjaxCall — wires the class-based click handlers
 * (`cancelClose`/`cancelClean`/`cancelRemove`/`cancelHide`/`cancelButton`/
 * `cancelToggle`/`cancelFade`) a server-rendered fragment carries.
 * `engine/afterAjaxCall.js`, called on every AJAX-loaded fragment from
 * app_socket.js's socketModule handler and app_datatable.js.
 *
 * `mdl/app/app/app_fiche.php:147` renders a real `.cancelClose` button
 * ("Fermer") on every record sheet — the one screen in this dataset proven
 * to carry it, used here rather than any of the other templates that also
 * reference these classes (app_create.php, app_delete.php, ...), which need
 * screens this test account may not reach.
 */
import { test, expect } from './fixtures/test-base';
import { TABLE, TABLE_VALUE } from './fixtures/auth';
import { openRecord } from './fixtures/app';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('afterAjaxCall: a .cancelClose button fires dom:close on its containing form', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const win = await openRecord(page, TABLE, TABLE_VALUE);
  const closeButton = win.locator('.cancelClose').first();
  await expect(closeButton).toBeVisible();

  // isClicked's dom:close fires on aac_up(element) — the button's own
  // parent, not the whole record window — after a 350ms setTimeout. Listen
  // before clicking so the delayed dispatch isn't missed.
  const closeSeen = page.evaluate(() => new Promise<boolean>((resolve) => {
    const btn = document.querySelector('.cancelClose');
    const parent = btn?.parentElement;
    if (!parent) return resolve(false);
    parent.addEventListener('dom:close', () => resolve(true), { once: true });
    setTimeout(() => resolve(false), 5_000);
  }));

  await closeButton.click();
  expect(await closeSeen).toBe(true);

  guard.assertClean();
});

test('afterAjaxCall: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const ajaxCallWarnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('engine/afterAjaxCall.js')) ajaxCallWarnings.push(message.text());
  });

  await page.evaluate(() => {
  });

  const win = await openRecord(page, TABLE, TABLE_VALUE);
  await expect(win.locator('.cancelClose').first()).toBeVisible();
  // Also focus a text input inside the fragment — exercises the
  // Event.element + activate() path afterAjaxCall wires separately.
  const textInput = win.locator('input[type=text]').first();
  if (await textInput.count()) await textInput.focus();

  expect(ajaxCallWarnings).toEqual([]);
});
