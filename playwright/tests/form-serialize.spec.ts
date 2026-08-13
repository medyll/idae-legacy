/**
 * Form serialization contract.
 *
 * serializeFields replaces Form.serialize, Form.serializeElements and the
 * element-level serialize extension without changing their query-string
 * contract. It accepts forms, arbitrary containers, field collections and
 * single fields, while leaving HTMLElement.prototype untouched.
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

const getPage = sharedPage();

/** Builds a form covering every branch of the serializer, returns the probes. */
async function serializeProbe(page: any) {
  return page.evaluate(() => {
    const w = window as any;
    const container = document.createElement('div');
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
    container.appendChild(f);
    document.body.appendChild(container);
    try {
      return {
        formSerialize: w.serializeFields(f),
        containerSerialize: w.serializeFields(container),
        subset: w.serializeFields(f.querySelectorAll('input[name=a], input[name=b]')),
        fieldSerialize: w.serializeFields(f.querySelector('input[name=b]')),
        legacyForm: typeof w.Form,
        legacyMethod: typeof (f as any).serialize,
      };
    } finally {
      if (container.parentNode) container.parentNode.removeChild(container);
    }
  });
}

test('form serialization: native helper preserves the contract', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const r = await serializeProbe(page);

  // Disabled fields and the submit button are excluded; a multiple-select
  // emits one pair per selected option; values are percent-encoded.
  expect(r.formSerialize).toBe('a=1&b=deux%20trois&c=on&d=p&d=q');

  expect(r.containerSerialize).toBe(r.formSerialize);

  expect(r.subset).toBe('a=1&b=deux%20trois');
  expect(r.fieldSerialize).toBe('b=deux%20trois');
  expect(r.legacyForm).toBe('undefined');
  expect(r.legacyMethod).toBe('undefined');

  guard.assertClean();
});
