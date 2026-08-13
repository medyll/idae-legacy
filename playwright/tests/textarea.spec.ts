/**
 * librairie/textarea.js — after this migration the file holds two things:
 * the `nl2br` global (which deliberately shadows app_php.js's differently-
 * behaving one; see the file header) and `resizeInput`, an auto-sizing
 * text input used by app_insertionQ.js:419 and myddeDatalist.js:179.
 *
 * `ResizingTextArea` used to live here too and was never instantiated
 * anywhere; it was removed in the same commit.
 *
 * datalist.spec.ts already exercises resizeInput indirectly (every
 * datalist input gets one). This file pins the two behaviours that would
 * otherwise fail silently: which nl2br wins, and that the measuring span
 * is actually attached and re-measured on input.
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('textarea: nl2br is this file\'s version, not app_php.js\'s', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const out = await page.evaluate(() => (window as any).nl2br('a\nb'));

  // This file's version keeps the newline and inserts <br> before it.
  // app_php.js's would have produced 'a<br />b' — main_bag.js loads
  // app_php.js first (line 27) and this file later (line 75), so this one
  // wins, and app_socket.js's live-data updates depend on it.
  expect(out).toBe('a<br>\nb');

  guard.assertClean();
});

test('textarea: resizeInput attaches a measuring span and resizes on keydown', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const result = await page.evaluate(() => {
    const holder = document.createElement('div');
    document.body.appendChild(holder);
    const input = document.createElement('input');
    input.type = 'text';
    input.value = 'short';
    holder.appendChild(input);

    new (window as any).resizeInput(input);

    const span = holder.querySelector('span');
    const spanAttached = !!span && span.parentElement === holder;
    // The constructor measures before appending, so the first width is 0px
    // (documented quirk); minWidth is what actually keeps it usable.
    const initialWidth = input.style.width;
    const minWidth = input.style.minWidth;

    // A keydown re-measures, this time with the span in the document.
    input.value = 'a considerably longer value than before';
    input.dispatchEvent(new KeyboardEvent('keydown', {bubbles: true}));
    const widthAfterTyping = parseInt(input.style.width, 10);
    const spanText = span ? span.innerHTML : null;

    holder.remove();
    return {spanAttached, initialWidth, minWidth, widthAfterTyping, spanText};
  });

  expect(result.spanAttached).toBe(true);
  expect(result.initialWidth).toBe('0px');
  expect(result.minWidth).toBe('80px');
  expect(result.widthAfterTyping).toBeGreaterThan(0);
  expect(result.spanText).toBe('a considerably longer value than before');

  guard.assertClean();
});

test('textarea: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const warnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('librairie/textarea.js')) warnings.push(message.text());
  });

  await page.evaluate(() => {
  });

  await page.evaluate(() => {
    const holder = document.createElement('div');
    document.body.appendChild(holder);
    const input = document.createElement('input');
    input.type = 'text';
    input.value = 'probe';
    holder.appendChild(input);
    new (window as any).resizeInput(input);
    input.value = 'probe longer';
    input.dispatchEvent(new KeyboardEvent('keydown', {bubbles: true}));
    (window as any).nl2br('x\ny');
    holder.remove();
  });
  await page.waitForTimeout(300);

  expect(warnings).toEqual([]);
});
