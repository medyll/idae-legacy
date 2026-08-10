/**
 * sortableTable — click-to-sort table headers (`librairie/sorttable.js`,
 * instantiated by app_insertionQ.js's `table.act_sort` watcher on every
 * list — the class is stripped right after, which is why it never shows up
 * in a selector, only `[isSortable]` does).
 *
 * Real list windows (app_datatable.js's BuildTbl) instantiate it on a
 * `<thead>` they then hide themselves (`this.thead.hidden = true`,
 * app_datatable.js:639) — the visible column header users actually see and
 * click lives in a separate floating zone with its own logic, unrelated to
 * this file. That means the reachable list screens in this suite exercise
 * `sortableTable`'s *construction* (no shim warnings, no thrown errors) but
 * never its actual click-to-sort behaviour — nothing here can click a
 * hidden header and call that a real interaction.
 *
 * So this spec covers two different things on purpose: construction on a
 * real screen (below), and the sort algorithm itself against a table built
 * ad hoc — the only way to exercise the part of this file real users on
 * `table.table_groupe` never reach, without guessing at which other
 * screen (app_scheme_grille.php, skelbuilder_liste_*, ...) still shows a
 * live one and how to reach it through this session's test account.
 */
import { test, expect } from './fixtures/test-base';
import { TABLE } from './fixtures/auth';
import { closeWindow, openList } from './fixtures/app';
import { sharedPage } from './fixtures/shared-boot';
import { watchConsole } from './helpers/console-guard';

// One boot for all tests below — see fixtures/shared-boot.ts.
const getPage = sharedPage();

test('sorttable: constructs on a real list without shim warnings or errors', async () => {
  const page = getPage();
  const sortWarnings: string[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'warning' || !message.text().includes('[idae-shim]')) return;
    const directCaller = message.text().split('\n').find((line) =>
      line.includes('javascript/') && !line.includes('vendor/idae-be-shim/'),
    );
    if (directCaller?.includes('librairie/sorttable.js')) sortWarnings.push(message.text());
  });
  await page.evaluate(() => {
    (window as any).IDAE_SHIM_WARN = 1;
    (window as any).__idaeShimInstallWarn();
  });

  const win = await openList(page, TABLE);
  const guard = watchConsole(page);
  await expect(win.locator('tbody.div_tbody tr')).not.toHaveCount(0, { timeout: 30_000 });

  // makeHeader() ran: every <td> in the (hidden) thead row became a
  // .sortheader link, and initialize() marked the table isSortable.
  await expect(win.locator('table[isSortable] .sortheader')).not.toHaveCount(0);

  await closeWindow(win);
  guard.assertClean();
  expect(sortWarnings).toEqual([]);
});

test('sorttable: clicking a header reorders rows and flips the sort arrow', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  // A minimal table shaped like what makeHeader() expects: a <thead><tr> of
  // <td>s (this app's tables use <td> in the header row, not <th> — see
  // frag_table in app_datatable.js) and a <tbody> of data rows. Visible and
  // in the DOM, unlike the one BuildTbl hides — this is the only way to
  // click a real header and see rows actually move.
  await page.evaluate(() => {
    const table = document.createElement('table');
    table.id = 'test_sort_table';
    table.innerHTML = `
      <thead><tr><td>Nom</td><td>Age</td></tr></thead>
      <tbody>
        <tr><td>Charlie</td><td>40</td></tr>
        <tr><td>Alice</td><td>30</td></tr>
        <tr><td>Bob</td><td>25</td></tr>
      </tbody>`;
    document.body.appendChild(table);
    new (window as any).sortableTable(table);
  });

  const table = page.locator('#test_sort_table');
  const rows = table.locator('tbody tr');
  await expect(rows).toHaveCount(3);

  const header = table.locator('.sortheader', { hasText: 'Nom' });
  await expect(header).toBeVisible();

  await header.click();
  await page.waitForTimeout(50);

  await expect(rows.nth(0)).toContainText('Alice');
  await expect(rows.nth(1)).toContainText('Bob');
  await expect(rows.nth(2)).toContainText('Charlie');

  // addArrows(): the clicked column's header cell is marked and carries an
  // arrow glyph.
  // makeHeader() marks every header <td> with sortDir on construction; only
  // the *clicked* one (Nom, cell 0) should end up carrying the sorted class.
  const nomHeaderTd = table.locator('thead td').first();
  await expect(nomHeaderTd).toHaveAttribute('sortdir', /up|down/);
  await expect(nomHeaderTd.locator('.sortheaderSorted')).toHaveCount(1);
  // addArrows() fills the arrow span with an <i class="fa ..."> icon, not
  // text — assert on the icon, not on visible text content.
  await expect(nomHeaderTd.locator('.sortarrow i.fa')).toHaveCount(1);

  // Click again: same column, reversed.
  await header.click();
  await page.waitForTimeout(50);
  await expect(rows.nth(0)).toContainText('Charlie');
  await expect(rows.nth(2)).toContainText('Alice');

  await page.evaluate(() => document.getElementById('test_sort_table')?.remove());
  guard.assertClean();
});

test('sorttable: sorts numerically, not lexically, for a numeric column', async () => {
  const page = getPage();
  const guard = watchConsole(page);

  // Row order deliberately does not start with the minimum value: activeSort()
  // decides ascending-vs-descending by comparing the first row's content
  // before and after sorting, so a fixture where row 0 already holds the min
  // (e.g. 9,100,20) makes an ascending sort look like a no-op and flips it to
  // descending — a real property of this algorithm (same heuristic
  // Prototype's version used), not something this migration should paper
  // over by picking fixture data that happens to dodge it silently.
  await page.evaluate(() => {
    const table = document.createElement('table');
    table.id = 'test_sort_table_numeric';
    table.innerHTML = `
      <thead><tr><td>Nom</td><td>Age</td></tr></thead>
      <tbody>
        <tr><td>A</td><td>40</td></tr>
        <tr><td>B</td><td>100</td></tr>
        <tr><td>C</td><td>20</td></tr>
      </tbody>`;
    document.body.appendChild(table);
    new (window as any).sortableTable(table);
  });

  const table = page.locator('#test_sort_table_numeric');
  const rows = table.locator('tbody tr');
  // Lexical order would put "100" before "20" before "40"; numeric sort
  // (getSortType()'s 'numeric' branch) must not.
  await table.locator('.sortheader', { hasText: 'Age' }).click();
  await page.waitForTimeout(50);

  await expect(rows.nth(0)).toContainText('20');
  await expect(rows.nth(1)).toContainText('40');
  await expect(rows.nth(2)).toContainText('100');

  await page.evaluate(() => document.getElementById('test_sort_table_numeric')?.remove());
  guard.assertClean();
});
