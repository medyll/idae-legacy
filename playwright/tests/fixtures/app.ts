/**
 * Helpers for driving the desktop shell.
 *
 * The SPA navigates through `act_chrome_gui(file, vars)` (defined in
 * `javascript/engine/engine.js`): it AJAX-loads a module, wraps it in a
 * `.containerdisp` window appended to `#inBody`, and fills the nested
 * `.cf_module` placeholders in a second round of requests.
 *
 * Windows carry a derived id (`container` + a slug of file+vars), so specs
 * locate them by "the window that just appeared" rather than by a hardcoded id.
 */
import { expect, type Locator, type Page } from '@playwright/test';

/** Ids of the windows currently open. */
async function openWindowIds(page: Page): Promise<string[]> {
  return page.evaluate(() => [...document.querySelectorAll('.containerdisp')].map((e) => e.id));
}

/**
 * Calls `act_chrome_gui` and resolves with the window it opened, once every
 * `.cf_module` placeholder inside it has been filled.
 */
export async function openChrome(page: Page, file: string, vars = ''): Promise<Locator> {
  const before = await openWindowIds(page);

  await page.evaluate(
    ([f, v]) => (window as any).act_chrome_gui(f, v),
    [file, vars] as const
  );

  const id = await page.waitForFunction(
    (known: string[]) => {
      const fresh = [...document.querySelectorAll('.containerdisp')].map((e) => e.id).find((i) => !known.includes(i));
      return fresh || false;
    },
    before,
    { timeout: 60_000 }
  ).then((h) => h.jsonValue() as Promise<string>);

  // Window ids are slugs of file+vars, so they are always plain identifiers.
  const win = page.locator(`#${id}`);
  await waitForModules(page, id);
  return win;
}

/**
 * Waits until the module placeholders inside a window have received content.
 *
 * `.cf_module` divs are emptied then filled by a follow-up AJAX call; asserting
 * before they resolve produces flaky "element not found" failures.
 */
export async function waitForModules(page: Page, windowId: string, timeout = 60_000): Promise<void> {
  await page.waitForFunction(
    (id: string) => {
      const win = document.getElementById(id);
      if (!win) return false;
      const modules = [...win.querySelectorAll('.cf_module')];
      return modules.length > 0 && modules.every((m) => m.children.length > 0);
    },
    windowId,
    { timeout }
  );
}

/** Closes a window through its title-bar button and waits for it to leave the DOM. */
export async function closeWindow(win: Locator): Promise<void> {
  await win.locator('.handledisp .buttonclose').click();
  await expect(win).toHaveCount(0, { timeout: 15_000 });
}

/** Opens the record sheet for a table row. */
export function openRecord(page: Page, table: string, value: string): Promise<Locator> {
  return openChrome(page, 'app/app/app_fiche', `table=${table}&table_value=${value}`);
}

/** Opens the list view for a table. */
export function openList(page: Page, table: string): Promise<Locator> {
  return openChrome(page, 'app/app_liste/app_liste_gui', `table=${table}`);
}
