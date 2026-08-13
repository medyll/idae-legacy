/**
 * librairie/tableGui.js — sizes a grid's cells to an even split of its
 * parent, then fades them in. One live caller,
 * app_planning_mens.php:164: `new tableGui($('tablePlanningMensuel'),
 * {numRow: N, onlyClass: 'caseMois'})`. The only other call site
 * (mdlCalendrierListYear.php:45) is commented out.
 *
 * Built as a fixture rather than opened through the monthly planning
 * screen, which needs its own task/leave data; the class only needs a
 * parent of known height and some `.onlyClass` cells.
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('tableGui: splits the parent height across rows and reveals the grid', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  const result = await page.evaluate(async () => {
    const parent = document.createElement('div');
    parent.id = 'tg_probe_parent';
    Object.assign(parent.style, {position: 'absolute', left: '0px', top: '0px', width: '600px', height: '300px'});
    document.body.appendChild(parent);

    const grid = document.createElement('div');
    grid.id = 'tg_probe_grid';
    // Three cells carrying the class the caller scopes on.
    grid.innerHTML =
      '<div class="tg_probe_cell"></div>' +
      '<div class="tg_probe_cell"></div>' +
      '<div class="tg_probe_cell"></div>';
    parent.appendChild(grid);

    new (window as any).tableGui(grid, {numRow: 3, onlyClass: 'tg_probe_cell'});

    const cells = Array.from(grid.querySelectorAll('.tg_probe_cell')) as HTMLElement[];
    const heights = cells.map((c) => c.style.height);

    // appearElement animates opacity from 0; let it settle.
    await new Promise((r) => setTimeout(r, 400));
    const gridOpacity = window.getComputedStyle(grid).opacity;

    parent.remove();
    return {heights, gridOpacity};
  });

  // 300px parent / numRow 3 => 100px per cell.
  expect(result.heights).toEqual(['100px', '100px', '100px']);
  // The grid starts at opacity 0 and is faded back in by appearElement.
  expect(Number(result.gridOpacity)).toBeGreaterThan(0);

  guard.assertClean();
});

test('tableGui: native implementation does not call compatibility shims', async () => {
  const page = getPage();
  const warnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('librairie/tableGui.js')) warnings.push(message.text());
  });

  await page.evaluate(() => {
  });

  await page.evaluate(async () => {
    const parent = document.createElement('div');
    Object.assign(parent.style, {position: 'absolute', width: '600px', height: '300px'});
    document.body.appendChild(parent);
    const grid = document.createElement('div');
    grid.innerHTML = '<div class="tg_shimprobe_cell"></div><div class="tg_shimprobe_cell"></div>';
    parent.appendChild(grid);

    const gui = new (window as any).tableGui(grid, {numRow: 2, numCol: 2, onlyClass: 'tg_shimprobe_cell'});
    gui.reBuild();
    await new Promise((r) => setTimeout(r, 200));
    parent.remove();
  });
  await page.waitForTimeout(300);

  expect(warnings).toEqual([]);
});
