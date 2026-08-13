/**
 * app/app_conge.js — drag & drop for the leave (congé) planning board:
 * `[data-dragconge]` items dropped onto `[data-dropzone=conge]` slots.
 * Same delegated shape as app_planning.js, but the drop repositions the
 * dragged node with clonePosition instead of re-parenting it, and persists
 * nothing (the ajaxValidation call is commented out in the source).
 *
 * Delegated at document.body and loaded unconditionally by main_bag.js, so
 * these handlers are live on every boot — driven here against a
 * constructed fixture rather than the real congé board, which would need
 * leave-request fixture data this suite doesn't otherwise carry.
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('app_conge: drop writes datedebut and repositions the dragged node over the slot', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const result = await page.evaluate(() => {
    const dragged = document.createElement('div');
    dragged.setAttribute('data-dragconge', '');
    dragged.setAttribute('data-idconge', '77');
    Object.assign(dragged.style, {position: 'absolute', left: '0px', top: '0px', width: '30px', height: '15px'});
    document.body.appendChild(dragged);

    const slot = document.createElement('div');
    slot.setAttribute('data-dropzone', 'conge');
    slot.setAttribute('heuredebut', '08:00');
    slot.setAttribute('datedebut', '2026-08-11');
    Object.assign(slot.style, {position: 'absolute', left: '300px', top: '220px', width: '90px', height: '40px'});
    document.body.appendChild(slot);

    const dt = new DataTransfer();
    dragged.dispatchEvent(new DragEvent('dragstart', {bubbles: true, cancelable: true, dataTransfer: dt}));
    const dragid = dt.getData('dragid');
    const opacityWhileDragging = dragged.style.opacity;

    // dragover paints the slot, dragleave clears it.
    slot.dispatchEvent(new DragEvent('dragover', {bubbles: true, cancelable: true, dataTransfer: dt}));
    const bgOnDragOver = slot.style.background;
    slot.dispatchEvent(new DragEvent('dragleave', {bubbles: true, cancelable: true, dataTransfer: dt}));
    const bgOnDragLeave = slot.style.background;

    slot.dispatchEvent(new DragEvent('drop', {bubbles: true, cancelable: true, dataTransfer: dt}));
    const datedebut = dragged.getAttribute('datedebut');
    // clonePosition with setWidth/setHeight false: position adopted, size kept.
    const left = dragged.style.left;
    const top = dragged.style.top;
    const width = dragged.style.width;

    dragged.dispatchEvent(new DragEvent('dragend', {bubbles: true, cancelable: true, dataTransfer: dt}));
    const opacityAfterDragEnd = dragged.style.opacity;

    dragged.remove();
    slot.remove();

    return {dragid, opacityWhileDragging, bgOnDragOver, bgOnDragLeave, datedebut, left, top, width, opacityAfterDragEnd};
  });

  expect(result.dragid).toBeTruthy();
  expect(result.opacityWhileDragging).toBe('0.4');
  expect(result.bgOnDragOver).toContain('rgb(255, 204, 51)'); // #FC3 expanded to #FFCC33 by the browser
  expect(result.bgOnDragLeave).toBe('');
  expect(result.datedebut).toBe('2026-08-11');
  // Moved onto the slot's own page position, size untouched.
  expect(result.left).toBe('300px');
  expect(result.top).toBe('220px');
  expect(result.width).toBe('30px');
  expect(result.opacityAfterDragEnd).toBe('1');

  guard.assertClean();
});

test('app_conge: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const warnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('app/app_conge.js')) warnings.push(message.text());
  });

  await page.evaluate(() => {
  });

  await page.evaluate(() => {
    const dragged = document.createElement('div');
    dragged.setAttribute('data-dragconge', '');
    dragged.setAttribute('data-idconge', '77');
    document.body.appendChild(dragged);
    const slot = document.createElement('div');
    slot.setAttribute('data-dropzone', 'conge');
    slot.setAttribute('datedebut', '2026-08-11');
    document.body.appendChild(slot);

    const dt = new DataTransfer();
    dragged.dispatchEvent(new DragEvent('dragstart', {bubbles: true, cancelable: true, dataTransfer: dt}));
    slot.dispatchEvent(new DragEvent('dragover', {bubbles: true, cancelable: true, dataTransfer: dt}));
    slot.dispatchEvent(new DragEvent('dragleave', {bubbles: true, cancelable: true, dataTransfer: dt}));
    slot.dispatchEvent(new DragEvent('drop', {bubbles: true, cancelable: true, dataTransfer: dt}));
    dragged.dispatchEvent(new DragEvent('dragend', {bubbles: true, cancelable: true, dataTransfer: dt}));

    dragged.remove();
    slot.remove();
  });
  await page.waitForTimeout(300);

  expect(warnings).toEqual([]);
});
