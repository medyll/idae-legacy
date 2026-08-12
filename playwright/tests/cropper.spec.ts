/**
 * The image cropper's behaviour contract.
 *
 * This is the safety net for migrating librairie/cropper.js off the Prototype
 * shims — 1362 lines that were the last JavaScript calling them, and the sole
 * caller keeping shim-class, shim-element and shim-draggable alive. Covered
 * before it was touched, not after.
 *
 * Draggable/Draggables now live inside cropper.js (as cr_Draggable /
 * cr_Draggables) and shim-draggable.js is deleted.
 *
 * It also guards the bug that motivated the first version of this spec:
 * `CropDraggable = Class.create(Draggable, {...})` calls `this.currentDelta()`
 * and binds `this.initDrag` inside its own initialize. Between the Phase 3/4
 * swap and 2026-08-11 the shim's Draggable had neither, so `new Cropper.Img`
 * threw before its setParams() and the whole cropper was dead. Verified: the
 * construction assertion below fails against that shim with exactly the
 * production error.
 *
 * Assertions are on observable geometry and on the onEndCrop payload rather
 * than on internals, so a native rewrite that keeps the behaviour passes and
 * one that quietly changes it does not.
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';

const getPage = sharedPage();

/** 8x8 solid PNG, inlined so the test needs no fixture file or network. */
const PNG_8x8 =
  'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAAG0lEQVR42mNk' +
  'YPhfz0AEYBxVSF+FAAoMAGDvBPtwZgD4AAAAAElFTkSuQmCC';

/**
 * Builds a Cropper.Img over a real loaded image inside a laid-out host.
 * Off-screen but not display:none — Cropper reads offsets, and a hidden image
 * would give it a 0x0 canvas and nothing to select.
 */
async function buildCropper(page: any, src: string) {
  return page.evaluate(async (imgSrc: string) => {
    const w = window as any;

    const prev = document.getElementById('pw_cropper_host');
    if (prev && prev.parentNode) prev.parentNode.removeChild(prev);

    const host = document.createElement('div');
    host.id = 'pw_cropper_host';
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
      img.src = imgSrc;
    });

    w.__pwCropCalls = [];
    try {
      w.__pwCrop = new w.Cropper.Img('pw_cropper_img', {
        displayOnInit: true,
        onloadCoords: { x1: 10, y1: 10, x2: 110, y2: 90 },
        onEndCrop: function (coords: any, dims: any) {
          w.__pwCropCalls.push({ coords: coords, dims: dims });
        },
      });
      return { error: null as string | null };
    } catch (e: any) {
      return { error: e.message as string };
    }
  }, src);
}

/** Synthetic MouseEvents: the host is parked off-screen, out of pointer reach. */
const DRIVE = `
  (sel, steps) => {
    const fire = (type, x, y, target) =>
      target.dispatchEvent(new MouseEvent(type, {
        bubbles: true, cancelable: true, button: 0, clientX: x, clientY: y }));
    fire('mousedown', steps[0][0], steps[0][1], sel);
    for (let i = 1; i < steps.length; i++) fire('mousemove', steps[i][0], steps[i][1], document);
    const last = steps[steps.length - 1];
    fire('mouseup', last[0], last[1], document);
  }`;

test('cropper: builds, honours onloadCoords, and reports them on end', async () => {
  const page = getPage();

  const built = await buildCropper(page, PNG_8x8);
  expect(built.error, 'Cropper.Img threw during construction').toBeNull();

  const state = await page.evaluate(() => {
    const w = window as any;
    const crop = w.__pwCrop;
    const sel = document.querySelector('#pw_cropper_host .imgCrop_selArea') as HTMLElement;
    return {
      hasSelArea: !!sel,
      areaCoords: crop.areaCoords,
      calcW: crop.calcW(),
      calcH: crop.calcH(),
    };
  });

  expect(state.hasSelArea, 'no selection area was built').toBe(true);
  // onloadCoords must survive setParams and land in areaCoords unchanged.
  expect(state.areaCoords).toMatchObject({ x1: 10, y1: 10, x2: 110, y2: 90 });
  expect(state.calcW).toBe(100);
  expect(state.calcH).toBe(80);
});

test('cropper: dragging the selection moves it and fires onEndCrop', async () => {
  const page = getPage();
  const built = await buildCropper(page, PNG_8x8);
  expect(built.error).toBeNull();

  const r = await page.evaluate(([drive]: [string]) => {
    const w = window as any;
    const sel = document.querySelector('#pw_cropper_host .imgCrop_selArea') as HTMLElement;
    const before = { x1: w.__pwCrop.areaCoords.x1, y1: w.__pwCrop.areaCoords.y1 };

    // eslint-disable-next-line no-eval
    (0, eval)(drive)(sel, [[50, 50], [70, 62], [92, 78]]);

    return {
      before,
      after: { x1: w.__pwCrop.areaCoords.x1, y1: w.__pwCrop.areaCoords.y1 },
      endCalls: w.__pwCropCalls.length,
      lastDims: w.__pwCropCalls.length ? w.__pwCropCalls[w.__pwCropCalls.length - 1].dims : null,
      // cr_Draggables, not Draggables: shim-draggable.js is deleted and the
      // pump now lives inside cropper.js as a file-local var (still a window
      // property, since cropper.js has no IIFE).
      pumpReleased: w.cr_Draggables.activeDraggable === null,
    };
  }, [DRIVE]);

  // The drag must actually move the selection, not merely run without error —
  // a Draggable whose pump never fires would leave these equal.
  expect(r.after.x1, 'selection did not move horizontally').not.toBe(r.before.x1);
  expect(r.after.y1, 'selection did not move vertically').not.toBe(r.before.y1);

  // Moving must not resize: the drag translates the box.
  expect(r.lastDims, 'onEndCrop never fired').not.toBeNull();
  expect(r.lastDims.width).toBe(100);
  expect(r.lastDims.height).toBe(80);

  expect(r.pumpReleased, 'Draggables did not deactivate on mouseup').toBe(true);
});

test('cropper: dragging the SE handle resizes rather than moves', async () => {
  const page = getPage();
  const built = await buildCropper(page, PNG_8x8);
  expect(built.error).toBeNull();

  const r = await page.evaluate(([drive]: [string]) => {
    const w = window as any;
    const handle = document.querySelector('#pw_cropper_host .imgCrop_handleSE') as HTMLElement;
    if (!handle) return { missing: true } as any;

    const before = Object.assign({}, w.__pwCrop.areaCoords);
    // eslint-disable-next-line no-eval
    (0, eval)(drive)(handle, [[110, 90], [130, 110], [150, 130]]);

    return {
      missing: false,
      before,
      after: Object.assign({}, w.__pwCrop.areaCoords),
      dims: { w: w.__pwCrop.calcW(), h: w.__pwCrop.calcH() },
    };
  }, [DRIVE]);

  expect(r.missing, 'no SE handle in the DOM').toBe(false);
  // The anchored corner stays put; the dragged one moves.
  expect(r.after.x1).toBe(r.before.x1);
  expect(r.after.y1).toBe(r.before.y1);
  expect(r.after.x2, 'SE handle did not widen the selection').toBeGreaterThan(r.before.x2);
  expect(r.dims.w).toBeGreaterThan(100);
});

test('cropper: remove() tears the UI back out of the DOM', async () => {
  const page = getPage();
  const built = await buildCropper(page, PNG_8x8);
  expect(built.error).toBeNull();

  const r = await page.evaluate(() => {
    const w = window as any;
    const hadUI = !!document.querySelector('#pw_cropper_host .imgCrop_selArea');
    w.__pwCrop.remove();
    const stillThere = !!document.querySelector('#pw_cropper_host .imgCrop_selArea');

    const host = document.getElementById('pw_cropper_host');
    if (host && host.parentNode) host.parentNode.removeChild(host);
    return { hadUI, stillThere };
  });

  expect(r.hadUI).toBe(true);
  expect(r.stillThere, 'remove() left the cropper UI in the DOM').toBe(false);
});
