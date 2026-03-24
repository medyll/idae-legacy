import { test, expect } from '@playwright/test';

test('smoke: login and open grid', async ({ page, request }) => {
  // Use known dev admin credentials from conf_install_go
  const user = process.env.PLAYWRIGHT_USER || 'Mydde';
  const pass = process.env.PLAYWRIGHT_PASS || 'malaterre';

  // Use BASE_URL env or default to localhost
  const base = process.env.BASE_URL || 'http://localhost:8080';

  // Perform HTTP login to obtain PHPSESSID
  const loginUrl = base + '/mdl/app/app_login/actions.php';
  const loginResp = await request.post(loginUrl, { form: {
    F_action: 'app_log',
    type: 'agent',
    loginAgent: user,
    passwordAgent: pass
  }});

  // Extract PHPSESSID from set-cookie header
  const sc = loginResp.headers()['set-cookie'] || loginResp.headers()['Set-Cookie'] || '';
  let phpsess = '';
  if (sc) {
    const m = sc.match(/PHPSESSID=([^;]+)/);
    if (m) phpsess = m[1];
  }

  // If we have a PHPSESSID, set it in the browser context so subsequent page loads are authenticated
  if (phpsess) {
    await page.context().addCookies([{
      name: 'PHPSESSID',
      value: phpsess,
      domain: 'localhost',
      path: '/',
      httpOnly: true,
      secure: false
    }]);
  }

  // Verify session via JSON endpoint
  await page.goto(base + '/services/json_ssid.php');
  const body = await page.locator('body').innerText();
  const sess = JSON.parse(body || '{}');
  expect(sess).toBeTruthy();
  // If idagent is present and >0, login succeeded
  expect(typeof sess.idagent).toBe('number');

  // Optionally check modules (simple smoke)
  const explorer = await request.post(base + '/mdl/app/app/app_explorer.php', { form: { table: 'client' } });
  expect(explorer.ok()).toBeTruthy();
});
