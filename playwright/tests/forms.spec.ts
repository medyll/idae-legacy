/**
 * The « Modifier » tab of a record — `mdl/app/app/app_update.php`.
 *
 * Its `<form class="Form">` carries `onsubmit="ajaxFormValidation(this);return
 * false;"`. Submitting runs the whole Prototype form stack in one go
 * (engine/engine.js:263-311):
 *   - `form.readAttribute('auto_close')` → `$(form).makeLoading()`
 *     (Element#readAttribute/insert/identify — methods.js:323),
 *   - `Form.serialize($(form))` builds the POST body,
 *   - `new Ajax.Updater($('div_form_validation'), url, …)` posts it,
 *   - onComplete `$(form).hide().fire('dom:close')` (auto_close forms).
 *
 * Every one of those calls is on the shim's contract list, so this spec is the
 * form-side counterpart of prototype-surface.spec.ts: it fails the moment
 * `$F`/`Form.serialize`/`Ajax.Updater`/`Element#fire` drifts.
 */
import { test, expect } from './fixtures/test-base';
import { TABLE, TABLE_VALUE } from './fixtures/auth';
import { closeWindow, openChrome } from './fixtures/app';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for both tests below — see fixtures/shared-boot.ts. Both tests
// open the *same* update-tab window (same table/table_value → same window
// id, per fixtures/app.ts) — each must close it, or the next test's
// openChrome() call waits forever for a "new" window id that never appears.
const getPage = sharedPage();

/** Opens the update tab for the reference record. */
function openUpdate(page: Parameters<typeof openChrome>[0]) {
  return openChrome(page, 'app/app/app_update', `table=${TABLE}&table_value=${TABLE_VALUE}`);
}

test('forms: update tab renders a form with a readable field value ($F)', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const win = await openUpdate(page);
  const form = win.locator('form.Form');
  await expect(form).toBeVisible({ timeout: 30_000 });

  // Server-rendered contract: action + hidden routing fields.
  await expect(form.locator('input[name=F_action]')).toHaveValue('app_update');
  await expect(form.locator('input[name=table]')).toHaveValue(TABLE);
  await expect(form.locator('input[name=table_value]')).toHaveValue(TABLE_VALUE);

  // $F() is Prototype's field accessor — the shim must keep it working on
  // live elements, whatever the field name happens to be in this dataset.
  const field = form.locator('input[name^="vars["]:not([type=hidden])').first();
  await expect(field).toBeVisible();
  const handle = await field.elementHandle();
  const domValue = await field.inputValue();
  const viaF = await page.evaluate((el) => (window as any).$F(el), handle);
  expect(viaF).toEqual(domValue);

  await closeWindow(win);
  guard.assertClean();
});

test('forms: submitting posts the serialized form and auto-closes it', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const win = await openUpdate(page);
  const form = win.locator('form.Form');
  await expect(form).toBeVisible({ timeout: 30_000 });

  // ajaxFormValidation builds the body with Form.serialize and posts it to
  // the form's action (app/actions.php). Values are submitted unchanged —
  // this is an idempotent update of the reference record.
  const posted = page.waitForRequest(
    (req) => req.url().includes('app/actions.php') && req.method() === 'POST',
    { timeout: 30_000 }
  );
  await form.locator('input.valid_button[type=submit]').click();

  const req = await posted;
  const body = req.postData() || '';
  expect(body).toContain('F_action=app_update');
  expect(body).toContain(`table=${TABLE}`);
  expect(body).toContain(`table_value=${TABLE_VALUE}`);

  // auto_close contract: the response area exists on body and the form hides.
  await expect(page.locator('body > #div_form_validation')).toBeAttached();
  await expect(form).toBeHidden({ timeout: 30_000 });

  guard.assertClean();
});
