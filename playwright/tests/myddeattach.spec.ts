/**
 * librairie/myddeAttach.js — drag-and-drop / file-input upload widget.
 * Real, widely instantiated: app_wallpaper.php, app_img_upload.php,
 * mail_send.php, document_liste_drop.php, app_fiche_document.php among
 * others (`new myddeAttach(...)` — confirmed by grep across mdl/, hors
 * vendor/).
 *
 * Driven here against a constructed fixture rather than any one of those
 * real screens — the drag/drop + upload flow only needs the DOM shape the
 * constructor expects (an element, optionally a form with an action), not
 * anything specific to a given module.
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('myddeAttach: dragenter builds the drop zone, dragend hides it when nothing was dropped', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const result = await page.evaluate(() => {
    const el = document.createElement('div');
    document.body.appendChild(el);

    const attach = new (window as any).myddeAttach(el, {});

    // dragenter -> makeDropArea(): builds .zone as a child of `el`.
    el.dispatchEvent(new DragEvent('dragenter', {bubbles: true, cancelable: true}));
    const zoneExists = !!attach.zone;
    const zoneIsChildOfEl = attach.zone ? el.contains(attach.zone) : false;
    const zoneVisibleAfterEnter = attach.zone ? attach.zone.style.display !== 'none' : false;

    // dragend with nothing dropped -> hides the zone again (dragEnd checks
    // `this.dropped`, never set true here).
    document.body.dispatchEvent(new DragEvent('dragend', {bubbles: true, cancelable: true}));
    const zoneHiddenAfterEnd = attach.zone ? attach.zone.style.display === 'none' : false;

    el.remove();
    if (attach.zone && attach.zone.parentNode) attach.zone.parentNode.removeChild(attach.zone);

    return {zoneExists, zoneIsChildOfEl, zoneVisibleAfterEnter, zoneHiddenAfterEnd};
  });

  expect(result.zoneExists).toBe(true);
  expect(result.zoneIsChildOfEl).toBe(true);
  expect(result.zoneVisibleAfterEnter).toBe(true);
  expect(result.zoneHiddenAfterEnd).toBe(true);

  guard.assertClean();
});

test('myddeAttach: drop reads the form action attribute and dispatches FileSelectHandler', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const result = await page.evaluate(() => {
    const form = document.createElement('form');
    form.id = 'myddeattach_probe_form';
    form.setAttribute('action', 'actions.php?probe=1');
    document.body.appendChild(form);

    const el = document.createElement('div');
    document.body.appendChild(el);

    const attach = new (window as any).myddeAttach(el, {form: 'myddeattach_probe_form'});
    const actionFromForm = attach.options.action;

    // A drop with an empty FileList — real files aren't constructible from
    // script, but FileSelectHandler only reads .length/.files here, and
    // the point is proving the delegated 'drop' listener actually reaches
    // FileSelectHandler (dropped flips true) rather than never firing.
    const dt = new DataTransfer();
    el.dispatchEvent(new DragEvent('drop', {bubbles: true, cancelable: true, dataTransfer: dt}));
    const droppedFlag = attach.dropped;

    el.remove();
    form.remove();
    if (attach.zone && attach.zone.parentNode) attach.zone.parentNode.removeChild(attach.zone);

    return {actionFromForm, droppedFlag};
  });

  expect(result.actionFromForm).toBe('actions.php?probe=1');
  expect(result.droppedFlag).toBe(true);

  guard.assertClean();
});

test('myddeAttach: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const warnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('librairie/myddeAttach.js')) warnings.push(message.text());
  });

  await page.evaluate(() => {
    (window as any).IDAE_SHIM_WARN = 1;
    (window as any).__idaeShimInstallWarn();
  });

  await page.evaluate(() => {
    const el = document.createElement('div');
    document.body.appendChild(el);
    const attach = new (window as any).myddeAttach(el, {});

    el.dispatchEvent(new DragEvent('dragenter', {bubbles: true, cancelable: true}));
    el.dispatchEvent(new DragEvent('dragover', {bubbles: true, cancelable: true}));
    document.body.dispatchEvent(new DragEvent('dragend', {bubbles: true, cancelable: true}));
    el.dispatchEvent(new DragEvent('drop', {bubbles: true, cancelable: true, dataTransfer: new DataTransfer()}));

    el.remove();
    if (attach.zone && attach.zone.parentNode) attach.zone.parentNode.removeChild(attach.zone);
  });
  await page.waitForTimeout(300);

  expect(warnings).toEqual([]);
});
