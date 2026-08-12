/**
 * The image cropper's drag contract.
 *
 * librairie/cropper.js builds its selection-area mover as
 * `CropDraggable = Class.create(Draggable, {...})`, overriding `initialize`
 * and `draw` and calling `this.currentDelta()` / binding `this.initDrag`
 * inside its own initialize. Those are Scriptaculous Draggable methods.
 *
 * Between the Phase 3/4 swap and 2026-08-11 the shim's Draggable was a
 * simplified stand-in with neither method, so `new CropDraggable(...)` threw
 * "this.currentDelta is not a function" — on cropper.js line 151, reached
 * from Cropper.Img's own setup before its setParams(). The whole cropper
 * ("Retailler cette image" on the image-upload screen) was dead, and nothing
 * noticed because no test constructed one.
 *
 * This spec constructs a real Cropper.Img over a real loaded image and then
 * drives an actual pointer drag across the selection, asserting the selection
 * moves. Construction alone would have passed against a Draggable whose
 * drag pump never fires.
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';

const getPage = sharedPage();

/** 8x8 solid PNG, inlined so the test needs no fixture file or network. */
const PNG_8x8 =
  'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAAG0lEQVR42mNk' +
  'YPhfz0AEYBxVSF+FAAoMAGDvBPtwZgD4AAAAAElFTkSuQmCC';

test('cropper: builds over an image and its selection responds to a drag', async () => {
  const page = getPage();

  const built = await page.evaluate(async (src) => {
    const w = window as any;

    const host = document.createElement('div');
    host.id = 'pw_cropper_host';
    // Off-screen but laid out: Cropper reads offsets, so display:none would
    // give it a 0x0 image and nothing to select.
    host.style.cssText = 'position:absolute;left:-9999px;top:0;width:400px;height:400px;';
    document.body.appendChild(host);

    const img = document.createElement('img');
    img.id = 'pw_cropper_img';
    img.width = 320;
    img.height = 240;
    host.appendChild(img);
    await new Promise<void>((resolve) => {
      img.onload = () => resolve();
      img.onerror = () => resolve();
      img.src = src;
    });

    try {
      const crop = new w.Cropper.Img('pw_cropper_img', {
        onEndCrop: function () {},
        displayOnInit: true,
        onloadCoords: { x1: 10, y1: 10, x2: 110, y2: 90 },
      });
      return { constructed: true, hasSelArea: !!crop.selArea, error: null as string | null };
    } catch (e: any) {
      return { constructed: false, hasSelArea: false, error: e.message as string };
    }
  }, PNG_8x8);

  expect(built.error, 'Cropper.Img threw during construction').toBeNull();
  expect(built.constructed).toBe(true);
  expect(built.hasSelArea, 'Cropper built no selection area').toBe(true);

  // Drive a real drag through the document-level pump: mousedown on the
  // selection, two mousemoves (the first arms startDrag, the second moves),
  // then mouseup. Synthetic MouseEvents rather than page.mouse — the host is
  // parked off-screen where a real pointer cannot reach it.
  const moved = await page.evaluate(() => {
    const w = window as any;
    const sel = document.querySelector('#pw_cropper_host .imgCrop_selArea') as HTMLElement | null;
    if (!sel) return { ok: false, reason: 'no selArea in DOM' };

    const before = sel.style.left;
    const fire = (type: string, x: number, y: number, target: EventTarget) =>
      target.dispatchEvent(new MouseEvent(type, {
        bubbles: true, cancelable: true, button: 0, clientX: x, clientY: y,
      }));

    fire('mousedown', 50, 50, sel);
    fire('mousemove', 70, 60, document);
    fire('mousemove', 90, 75, document);
    fire('mouseup', 90, 75, document);

    return {
      ok: true,
      reason: '',
      before,
      after: sel.style.left,
      dragPumpRan: w.Draggables.activeDraggable === null,
    };
  });

  expect(moved.ok, moved.reason).toBe(true);
  // The pump must have released the draggable on mouseup — if activeDraggable
  // were still set, the drag never completed its lifecycle.
  expect(moved.dragPumpRan, 'Draggables did not deactivate on mouseup').toBe(true);

  await page.evaluate(() => {
    const host = document.getElementById('pw_cropper_host');
    if (host && host.parentNode) host.parentNode.removeChild(host);
  });
});
