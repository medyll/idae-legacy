/**
 * librairie/myddeSelection.js — rubber-band drag selection.
 *
 * Instantiated by myddeExplorer.js:711 (act_drag_selection_zone), but only
 * on lists whose markup carries `expl_drag_selection_zone` — which, across
 * the whole repo, is exactly two screens: app_document/document_liste.php
 * and app_prod/app_prod.php. The generic list used by the rest of this
 * suite never builds one, so the class is constructed directly here
 * against a purpose-built fixture (same approach as app-chat.spec.ts /
 * app-keepon.spec.ts / myddeattach.spec.ts) rather than dragging the suite
 * into document/prod fixture data.
 *
 * The press has to land on the zone element itself — onMouseDown bails
 * unless `event.target === this.element` — so events are dispatched
 * directly rather than aimed with a real cursor.
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

/** Builds a zone + two absolutely-positioned selectable items, returns cleanup-able ids. */
const FIXTURE = () => {
  const zone = document.createElement('div');
  zone.id = 'ms_probe_zone';
  Object.assign(zone.style, {position: 'absolute', left: '0px', top: '0px', width: '400px', height: '300px'});
  document.body.appendChild(zone);

  // `near` sits inside the drag rectangle (10,10 -> 210,150), `far` well outside it.
  const near = document.createElement('div');
  near.className = 'ms_probe_item';
  near.setAttribute('data-probe', 'near');
  Object.assign(near.style, {position: 'absolute', left: '20px', top: '20px', width: '40px', height: '20px'});
  document.body.appendChild(near);

  const far = document.createElement('div');
  far.className = 'ms_probe_item';
  far.setAttribute('data-probe', 'far');
  Object.assign(far.style, {position: 'absolute', left: '600px', top: '500px', width: '40px', height: '20px'});
  document.body.appendChild(far);

  return {zone, near, far};
};

const CLEANUP = () => {
  ['ms_probe_zone'].forEach((id) => document.getElementById(id)?.remove());
  document.querySelectorAll('.ms_probe_item').forEach((n) => n.remove());
  document.getElementById('drag_selection')?.remove();
};

test('myddeSelection: drag builds #drag_selection, sizes it, and removes it on mouseup', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const result = await page.evaluate(({fixtureSrc, cleanupSrc}) => {
    const build = new Function('return (' + fixtureSrc + ')()') as () => any;
    const clean = new Function('return (' + cleanupSrc + ')()') as () => void;
    const {zone} = build();

    new (window as any).myddeSelection(zone, {only: '.ms_probe_item'});

    zone.dispatchEvent(new MouseEvent('mousedown', {bubbles: true, cancelable: true, clientX: 10, clientY: 10}));
    const div = document.getElementById('drag_selection');
    const createdOnMouseDown = !!div;
    const startPos = div ? (div as any).startPos : null;
    const positioned = div ? div.style.position : null;
    const opacity = div ? div.style.opacity : null;

    document.dispatchEvent(new MouseEvent('mousemove', {bubbles: true, cancelable: true, clientX: 210, clientY: 150}));
    const sizedWidth = div ? div.style.width : null;
    const sizedHeight = div ? div.style.height : null;

    document.dispatchEvent(new MouseEvent('mouseup', {bubbles: true, cancelable: true}));
    const removedOnMouseUp = !document.getElementById('drag_selection');

    clean();
    return {createdOnMouseDown, startPos, positioned, opacity, sizedWidth, sizedHeight, removedOnMouseUp};
  }, {fixtureSrc: FIXTURE.toString(), cleanupSrc: CLEANUP.toString()});

  expect(result.createdOnMouseDown).toBe(true);
  expect(result.positioned).toBe('absolute');
  expect(result.opacity).toBe('0.6');
  expect(result.startPos).toEqual([10, 10]);
  // Dragged 200px right / 140px down from the press point.
  expect(result.sizedWidth).toBe('200px');
  expect(result.sizedHeight).toBe('140px');
  expect(result.removedOnMouseUp).toBe(true);

  guard.assertClean();
});

test('myddeSelection: checkSelect marks overlapping items selected and clears the rest', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const result = await page.evaluate(({fixtureSrc, cleanupSrc}) => {
    const build = new Function('return (' + fixtureSrc + ')()') as () => any;
    const clean = new Function('return (' + cleanupSrc + ')()') as () => void;
    const {zone, near, far} = build();

    new (window as any).myddeSelection(zone, {only: '.ms_probe_item'});

    zone.dispatchEvent(new MouseEvent('mousedown', {bubbles: true, cancelable: true, clientX: 10, clientY: 10}));
    document.dispatchEvent(new MouseEvent('mousemove', {bubbles: true, cancelable: true, clientX: 210, clientY: 150}));

    const nearSelected = near.classList.contains('selected');
    const farSelected = far.classList.contains('selected');

    document.dispatchEvent(new MouseEvent('mouseup', {bubbles: true, cancelable: true}));
    clean();
    return {nearSelected, farSelected};
  }, {fixtureSrc: FIXTURE.toString(), cleanupSrc: CLEANUP.toString()});

  expect(result.nearSelected).toBe(true);
  expect(result.farSelected).toBe(false);

  guard.assertClean();
});

test('myddeSelection: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const warnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('librairie/myddeSelection.js')) warnings.push(message.text());
  });

  await page.evaluate(() => {
    (window as any).IDAE_SHIM_WARN = 1;
    (window as any).__idaeShimInstallWarn();
  });

  await page.evaluate(({fixtureSrc, cleanupSrc}) => {
    const build = new Function('return (' + fixtureSrc + ')()') as () => any;
    const clean = new Function('return (' + cleanupSrc + ')()') as () => void;
    const {zone} = build();
    new (window as any).myddeSelection(zone, {only: '.ms_probe_item'});
    zone.dispatchEvent(new MouseEvent('mousedown', {bubbles: true, cancelable: true, clientX: 10, clientY: 10}));
    document.dispatchEvent(new MouseEvent('mousemove', {bubbles: true, cancelable: true, clientX: 210, clientY: 150}));
    document.dispatchEvent(new MouseEvent('mouseup', {bubbles: true, cancelable: true}));
    clean();
  }, {fixtureSrc: FIXTURE.toString(), cleanupSrc: CLEANUP.toString()});
  await page.waitForTimeout(400);

  expect(warnings).toEqual([]);
});
