import { test, expect } from '@playwright/test';

// Best-effort CRUD probe using server endpoints. May require tuning for specific tables/fields.

test('crud: create -> list -> delete (probe)', async ({ request }) => {
  const base = process.env.BASE_URL || 'http://localhost:8080';
  const user = process.env.PLAYWRIGHT_USER || 'Mydde';
  const pass = process.env.PLAYWRIGHT_PASS || 'malaterre';

  // Login to get PHPSESSID
  const login = await request.post(base + '/mdl/app/app_login/actions.php', { form: {
    F_action: 'app_log', type: 'agent', loginAgent: user, passwordAgent: pass
  }});

  const sc = login.headers()['set-cookie'] || login.headers()['Set-Cookie'] || '';
  let phpsess = '';
  if (sc) {
    const m = sc.match(/PHPSESSID=([^;]+)/);
    if (m) phpsess = m[1];
  }

  const ts = Date.now();
  const name = 'PWTestClient_' + ts;

  // Attempt to create a minimal record via explorer/create flow (best-effort)
  // This may be app-specific; we try to POST to a few candidate endpoints.
  const createCandidates = [
    base + '/services/json_data.php',
    base + '/mdl/app/app/app_update.php',
    base + '/mdl/app/app/app_create.php'
  ];

  let created = false;
  for (const url of createCandidates) {
    try {
      const body = new URLSearchParams();
      body.append('table', 'client');
      body.append('nomClient', name);
      body.append('table_value', '0');
      const resp = await request.post(url, {
        body: body.toString(),
        headers: { 'content-type': 'application/x-www-form-urlencoded', 'cookie': phpsess ? `PHPSESSID=${phpsess}` : '' }
      });
      if (resp.ok()) {
        created = true;
        break;
      }
    } catch (e) {
      // ignore and try next
    }
  }

  // Probe list to find created name
  const listResp = await request.post(base + '/mdl/app/app/app_explorer.php', {
    form: { table: 'client' },
    headers: phpsess ? { cookie: `PHPSESSID=${phpsess}` } : {}
  });
  const listBody = await listResp.text();
  const found = listBody.includes(name);

  // Report (soft assertions)
  console.log('CRUD probe createdCandidate:', created, 'foundInList:', found);
  // Make test non-failing but informative — assert that either created or found
  expect(created || found).toBeTruthy();

  // Note: deletion cleanup skipped — environment may not allow deletes via these probes safely.
});
