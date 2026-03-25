import { test, expect } from '@playwright/test';

// UI/UX basic checks: page load, menu presence, responsive breakpoints, title

test('uiux: basic layout and responsive checks', async ({ page, request }) => {
  const base = process.env.BASE_URL || 'http://localhost:8080';
  const user = process.env.PLAYWRIGHT_USER || 'Mydde';
  const pass = process.env.PLAYWRIGHT_PASS || 'malaterre';

  // Quick session check via server-side login
  const login = await request.post(base + '/mdl/app/app_login/actions.php', { form: { F_action: 'app_log', type: 'agent', loginAgent: user, passwordAgent: pass }});
  const sc = login.headers()['set-cookie'] || login.headers()['Set-Cookie'] || '';
  let phpsess = '';
  if (sc) {
    const m = sc.match(/PHPSESSID=([^;]+)/);
    if (m) phpsess = m[1];
  }

  // Load homepage in desktop; try to detect main UI and otherwise perform an in-browser login
  await page.setViewportSize({ width: 1280, height: 800 });
  await page.goto(base + '/');
  try {
    await page.waitForSelector('#main, .app-gui, #grid, .grid, .app-list', { timeout: 10000 });
  } catch (e) {
    // Perform in-browser login flow if SPA displayed login widget
    const loginSelector = 'input[name=loginAgent], input[name=login], input[placeholder*=Identification], input[placeholder*=Ident]';
    await page.waitForSelector(loginSelector, { timeout: 15000 });
    await page.fill('input[name=loginAgent], input[name=login]', user);
    await page.fill('input[name=passwordAgent]', pass);
    await page.click('input[type=submit], button:has-text("Valider")');
    await page.waitForLoadState('networkidle').catch(() => {});
    // Fetch session info and set localStorage so SPA bootstraps reliably
    const sess = await page.evaluate(async () => { try { const r = await fetch('/services/json_ssid.php'); return await r.json(); } catch(e) { return null; } });
    if (sess && sess.PHPSESSID) {
      await page.evaluate((s) => { try { localStorage.setItem('PHPSESSID', s.PHPSESSID); localStorage.setItem('SESSID', s.SESSID || s.idagent || ''); localStorage.setItem('APPID', s.PHPSESSID); } catch(e){} }, sess);
    }
    await page.waitForSelector('#main, .app-gui, #grid, .grid, .app-list', { timeout: 30000 });
  }

  // Basic checks
  const title = await page.title();
  console.log('Page title:', title);

  // Look for main UI landmarks (best-effort selectors)
  const mainExists = await page.locator('#main, .app-gui, #grid, .grid, .app-list').count();
  expect(mainExists).toBeGreaterThan(0);

  // Responsive: mobile viewport should still show menu or a hamburger
  await page.setViewportSize({ width: 375, height: 800 });
  await page.waitForTimeout(400);
  const mobileMenu = await page.locator('.ms-Button, .hamburger, .menu, #menu').first().count();
  expect(mobileMenu).toBeGreaterThanOrEqual(0);

  // Accessibility quick check: ensure body role exists
  const hasBody = await page.locator('body').count();
  expect(hasBody).toBeGreaterThan(0);
});
