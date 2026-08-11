/**
 * Form serialization contract.
 *
 * Unlike the phase-5 migration specs, this one is *not* trying to prove the
 * shim is unused — `Form` is one of the shims that stays. It exists because
 * two of its APIs were silently missing between the Phase 3/4 swap and
 * 2026-08-11, and nothing caught it:
 *
 *   - `Form.serializeElements(collection)` — the static. Eight call sites on
 *     the produit_tarif_gamme update screens.
 *   - `$(form).serialize()` — the element method. Thirteen call sites.
 *
 * Both are reached exclusively from inline `onclick`/`onsubmit` attributes in
 * PHP templates, so no JS-level grep or runtime probe of the loaded modules
 * saw them, and every one threw "… is not a function" when a user clicked.
 * These assertions are the regression guard: they are cheap, they run against
 * a synthetic form rather than a real screen, and they fail loudly if a future
 * shim reshuffle drops either API again.
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

const getPage = sharedPage();

/** Builds a form covering every branch of the serializer, returns the probes. */
async function serializeProbe(page: any) {
  return page.evaluate(() => {
    const w = window as any;
    const f = document.createElement('form');
    f.id = 'pw_form_serialize_probe';
    f.innerHTML =
      '<input name="a" value="1">' +
      '<input name="b" value="deux trois">' +
      '<input name="skipme" value="x" disabled>' +
      '<input type="checkbox" name="c" value="on" checked>' +
      '<input type="checkbox" name="unchecked" value="nope">' +
      '<select name="d" multiple><option value="p" selected><option value="q" selected><option value="r"></select>' +
      '<input type="submit" name="go" value="send">';
    document.body.appendChild(f);
    const $f = w.$(f);
    try {
      return {
        formSerialize: $f.serialize(),
        staticSerialize: w.Form.serializeElements(w.Form.getElements(f)),
        // The produit_tarif_gamme shape: an arbitrary subset of fields,
        // which is the whole reason the static exists separately.
        subset: w.Form.serializeElements($f.select('input[name=a], input[name=b]')),
        // A single field keeps Prototype's Field#serialize meaning, not the
        // form one — the shim's addMethods is untyped, so shim-form.js
        // dispatches on tagName to preserve both.
        fieldSerialize: w.$(f.querySelector('input[name=b]')).serialize(),
      };
    } finally {
      if (f.parentNode) f.parentNode.removeChild(f);
    }
  });
}

test('form serialization: both APIs exist and agree', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const r = await serializeProbe(page);

  // Disabled fields and the submit button are excluded; a multiple-select
  // emits one pair per selected option; values are percent-encoded.
  expect(r.formSerialize).toBe('a=1&b=deux%20trois&c=on&d=p&d=q');

  // Form#serialize is defined *as* serializeElements(getElements(form)), so
  // the two can never drift apart. Asserted rather than assumed.
  expect(r.staticSerialize).toBe(r.formSerialize);

  expect(r.subset).toBe('a=1&b=deux%20trois');
  expect(r.fieldSerialize).toBe('b=deux%20trois');

  guard.assertClean();
});
