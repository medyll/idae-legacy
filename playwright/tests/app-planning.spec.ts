/**
 * app/app_planning.js — drag & drop for the planning/calendar view
 * ([data-dragtache] items dropped onto [data-droptache] zones), plus the
 * dom:resizetache handler that re-lays-out overlapping tasks in a slot.
 * Real feature: [data-droptache] markup ships in several planning
 * templates (app_planning_quoti.php, _hebdo.php, _mens.php,
 * calendrier_day.php).
 *
 * Delegated at document.body, loaded unconditionally by main_bag.js —
 * driven here against a constructed fixture rather than the real
 * templates, since reaching a specific day/slot through the real
 * calendar UI would mean fixture data this suite doesn't otherwise need.
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('app_planning: drop moves the dragged node and writes its attributes', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const result = await page.evaluate(() => {
    // ajaxValidation posts to actions.php for real — stub it so this test
    // stays hermetic (drop's own DOM/attribute work is what's under test).
    const original = (window as any).ajaxValidation;
    (window as any).ajaxValidation = () => {};

    const dragged = document.createElement('div');
    dragged.setAttribute('data-dragtache', '');
    dragged.setAttribute('data-table_value', '42');
    document.body.appendChild(dragged);

    const dropzone = document.createElement('div');
    dropzone.setAttribute('data-droptache', 'slot');
    dropzone.setAttribute('heuredebut', '09:00');
    dropzone.setAttribute('dropvalue', '2026-08-10');
    document.body.appendChild(dropzone);

    // dragstart wires dragid onto the DataTransfer via identify().
    const dt = new DataTransfer();
    dragged.dispatchEvent(new DragEvent('dragstart', {bubbles: true, cancelable: true, dataTransfer: dt}));
    const dragid = dt.getData('dragid');

    dropzone.dispatchEvent(new DragEvent('drop', {bubbles: true, cancelable: true, dataTransfer: dt}));

    const movedInsideDropzone = dropzone.contains(dragged);
    const heuredebut = dragged.getAttribute('heuredebut');
    const datedebut = dragged.getAttribute('datedebut');

    dropzone.remove();
    dragged.remove();
    (window as any).ajaxValidation = original;

    return {dragid, movedInsideDropzone, heuredebut, datedebut};
  });

  expect(result.dragid).toBeTruthy();
  expect(result.movedInsideDropzone).toBe(true);
  expect(result.heuredebut).toBe('09:00');
  expect(result.datedebut).toBe('2026-08-10');

  guard.assertClean();
});

test('app_planning: dom:resizetache spreads same-slot tasks and stacks z-index by top', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const result = await page.evaluate(() => {
    const zone = document.createElement('div');
    zone.setAttribute('data-droptache', 'slot');
    document.body.appendChild(zone);

    // Two tasks at the same heureDebut share the width; a third at a
    // different heureDebut is a group of its own and keeps full width.
    const makeTask = (heure: string, top: number) => {
      const t = document.createElement('div');
      t.className = 'dyntache';
      t.setAttribute('data-heureDebut', heure);
      Object.defineProperty(t, 'offsetTop', {value: top, configurable: true});
      zone.appendChild(t);
      return t;
    };
    const a = makeTask('09:00', 20);
    const b = makeTask('09:00', 10);
    const c = makeTask('10:00', 5);
    Object.defineProperty(zone, 'offsetWidth', {value: 200, configurable: true});

    zone.dispatchEvent(new CustomEvent('dom:resizetache', {bubbles: true, cancelable: true}));

    return {
      aWidth: a.style.width, aMargin: a.style.marginLeft, aZ: a.style.zIndex,
      bWidth: b.style.width, bMargin: b.style.marginLeft, bZ: b.style.zIndex,
      cWidth: c.style.width, cZ: c.style.zIndex,
    };
  });

  // a and b split the 200px slot; a is first in DOM order (aa=0, margin 0), b second (margin = width).
  expect(result.aWidth).toBe('100px');
  expect(result.bWidth).toBe('100px');
  expect(result.aMargin).toBe('0px');
  expect(result.bMargin).toBe('100px');
  // c is alone in its heureDebut group, keeps the full slot width.
  expect(result.cWidth).toBe('200px');
  // z-index stacks by offsetTop ascending: c(5) < b(10) < a(20).
  expect(Number(result.cZ)).toBeLessThan(Number(result.bZ));
  expect(Number(result.bZ)).toBeLessThan(Number(result.aZ));

  guard.assertClean();
});

test('app_planning: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const warnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('app/app_planning.js')) warnings.push(message.text());
  });

  await page.evaluate(() => {
  });

  await page.evaluate(() => {
    const original = (window as any).ajaxValidation;
    (window as any).ajaxValidation = () => {};

    const dragged = document.createElement('div');
    dragged.setAttribute('data-dragtache', '');
    document.body.appendChild(dragged);
    const dropzone = document.createElement('div');
    dropzone.setAttribute('data-droptache', 'slot');
    document.body.appendChild(dropzone);

    const dt = new DataTransfer();
    dragged.dispatchEvent(new DragEvent('dragstart', {bubbles: true, cancelable: true, dataTransfer: dt}));
    dragged.dispatchEvent(new DragEvent('dragend', {bubbles: true, cancelable: true, dataTransfer: dt}));
    dropzone.dispatchEvent(new DragEvent('dragover', {bubbles: true, cancelable: true, dataTransfer: dt}));
    dropzone.dispatchEvent(new DragEvent('dragleave', {bubbles: true, cancelable: true, dataTransfer: dt}));
    dropzone.dispatchEvent(new DragEvent('drop', {bubbles: true, cancelable: true, dataTransfer: dt}));
    dropzone.dispatchEvent(new CustomEvent('dom:resizetache', {bubbles: true, cancelable: true}));

    dropzone.remove();
    (window as any).ajaxValidation = original;
  });
  await page.waitForTimeout(300);

  expect(warnings).toEqual([]);
});
