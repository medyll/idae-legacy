/**
 * The « Modifier » tab of a record — `mdl/app/app/app_update.php`.
 *
 * Its `<form class="Form">` carries `onsubmit="ajaxFormValidation(this);return
 * false;"`. Submitting runs `engine/engine.js`'s form stack end to end:
 *   - `form.getAttribute('auto_close')` → `form.makeLoading()` (native,
 *     engine/methods.js),
 *   - `engine_formSerialize(form)` builds the POST body (file-local, mirrors
 *     Prototype's Form.serialize — always excludes submit buttons, unlike
 *     FormData, which has no notion of "which button" outside a submit
 *     event and would include every one's value),
 *   - `engine_ajaxUpdater(...)` posts it — native fetch, still stripping
 *     `<script>` before the innerHTML assignment and deferred-eval'ing the
 *     original response when `options.evalScripts` is set, same contract
 *     app_socket.js's sk_update needed (see that file's BE_PLAN.md entry) —
 *     this form's response can itself carry one,
 *   - onComplete hides the form and fires `dom:close` (auto_close forms).
 *
 * `$F()` (still shim-provided; that surface hasn't migrated) reads the live
 * DOM regardless of which side built it, so this spec doubles as proof the
 * native rewrite renders a form indistinguishable from the shim's.
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

test('forms: update tab renders a form with a readable native field value', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const win = await openUpdate(page);
  const form = win.locator('form.Form');
  await expect(form).toBeVisible({ timeout: 30_000 });

  // Server-rendered contract: action + hidden routing fields.
  await expect(form.locator('input[name=F_action]')).toHaveValue('app_update');
  await expect(form.locator('input[name=table]')).toHaveValue(TABLE);
  await expect(form.locator('input[name=table_value]')).toHaveValue(TABLE_VALUE);

  // Read the live control through the native value property, whatever the
  // field name happens to be in this dataset.
  const field = form.locator('input[name^="vars["]:not([type=hidden])').first();
  await expect(field).toBeVisible();
  const domValue = await field.inputValue();
  const nativeValue = await field.evaluate((el: HTMLInputElement) => el.value);
  expect(nativeValue).toEqual(domValue);

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

test('forms: engine.js\'s form/navigation stack does not call compatibility shims', async () => {
  const page = getPage();
  const engineWarnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('engine/engine.js')) engineWarnings.push(message.text());
  });

  await page.evaluate(() => {
  });

  // act_chrome_gui (opening the tab) and ajaxFormValidation/Real (submitting
  // it) are engine.js's two busiest paths — this exercises both.
  const win = await openUpdate(page);
  const form = win.locator('form.Form');
  await expect(form).toBeVisible({ timeout: 30_000 });
  await form.locator('input.valid_button[type=submit]').click();
  await expect(form).toBeHidden({ timeout: 30_000 });

  expect(engineWarnings).toEqual([]);
});
