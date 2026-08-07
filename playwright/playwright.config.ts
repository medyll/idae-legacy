import { defineConfig } from '@playwright/test';
import { existsSync, readFileSync } from 'node:fs';
import { resolve } from 'node:path';

const testingEnvPath = resolve(__dirname, '..', '.env.testing');

if (existsSync(testingEnvPath)) {
  for (const line of readFileSync(testingEnvPath, 'utf8').split(/\r?\n/)) {
    const match = line.match(/^\s*([A-Z_][A-Z0-9_]*)\s*=\s*(.*)\s*$/);
    if (!match || match[1] in process.env) continue;

    const [, key, rawValue] = match;
    process.env[key] = rawValue.replace(/^(['"])(.*)\1$/, '$2');
  }
}

process.env.BASE_URL ??= process.env.TEST_BASE_URL;
process.env.PLAYWRIGHT_USER ??= process.env.TEST_LOGIN;
process.env.PLAYWRIGHT_PASS ??= process.env.TEST_PASSWORD;

export default defineConfig({
  use: {
    // 127.0.0.1 rather than localhost — see the BASE comment in
    // tests/fixtures/auth.ts (IPv6-first resolution stalls ~21s per fresh
    // Node-side connection).
    baseURL: process.env.BASE_URL || 'http://127.0.0.1:8080',
    headless: true,
    viewport: { width: 1400, height: 900 },
    ignoreHTTPSErrors: true,
    // storageState is supplied per-worker by fixtures/test-base.ts, not set
    // here — see that file for why (one shared session forced workers: 1
    // and repeatedly saturated the socket bridge).
    trace: 'retain-on-failure',
  },
  testDir: './tests',
  // Bridge probe before any worker starts: fails in ~15s with an actionable
  // message when Apache/phpBridge is wedged, instead of letting every spec
  // burn its 2x120s boot retries. See global-setup.ts and HANG_TEST.md.
  globalSetup: './global-setup.ts',
  // No globalSetup: login is now a worker-scoped fixture
  // (fixtures/test-base.ts), so it runs once per worker, lazily, instead of
  // once for the whole run before any worker starts.
  //
  // Tried 4 workers twice after fixing the Hyper-V memory ceiling (switched
  // Docker Desktop to the WSL2 backend, 15.5GB dynamic vs. a hard static
  // 1.9GB) and a port-8080 conflict (see docker-compose.yml). Both attempts
  // still failed 19-21 of 23 specs — cold AND warm containers, plenty of
  // free CPU/memory, Apache's MaxRequestWorkers=150 nowhere near saturated.
  // Root cause not identified (candidates: WSL2's NAT/port-forward layer
  // under bursty concurrent connections, or MongoDB — on the host, not
  // containerized — under 4x concurrent PHP sessions). Not worth more blind
  // 8-9min retries to find out. workers: 1 is the only configuration proven
  // reliable (20/20 green, see BE_PLAN.md Phase 1). The real lever for
  // suite speed here is fewer full boots per run (share one page/context
  // across the tests in a spec file via test.describe + beforeAll), not
  // concurrency — noted as unimplemented in BE_PLAN.md.
  workers: 1,
  // Measured directly (Chromium, real network, 2026-08-08) after the
  // per-file cache-busting fix (f4f090a): cold boot to login-form-visible
  // ~11.3s (106 requests), warm (bag.js IndexedDB cache hit) ~8.9s (16
  // requests). The old 180s budget dated from before that fix and was never
  // re-measured — do not widen this back out without a fresh measurement to
  // justify it. Each worker boots twice back-to-back (the worker-scoped
  // login in fixtures/test-base.ts, then the shared boot in
  // fixtures/shared-boot.ts's beforeAll) before any test body runs; 45s
  // covers 2x the measured cold-boot ceiling plus the test body itself.
  timeout: 45000,
  expect: { timeout: 15000 },
  // main_bag.js's boot is genuinely flaky under the current WSL2 backend —
  // reproduced outside Playwright entirely (plain Chromium via the Playwright
  // API): the app's socket.io client sometimes disconnects and reconnects
  // mid-boot, and schemeLoad() doesn't recover from that, hanging until
  // timeout. Not a bug in this suite's fixtures — a real, standard case for
  // retries.
  retries: 2,
});
