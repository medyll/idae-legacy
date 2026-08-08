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
  // Bridge probe before any worker starts: fails in ~10s with an actionable
  // message when Apache/phpBridge is wedged, instead of letting every spec
  // burn its boot retries. See global-setup.ts and HANG_TEST.md.
  globalSetup: './global-setup.ts',
  // No globalSetup for auth: login is a worker-scoped fixture
  // (fixtures/test-base.ts), so it runs once per worker, lazily, instead of
  // once for the whole run before any worker starts.
  //
  // Tried 4 workers twice after fixing the Hyper-V memory ceiling (switched
  // Docker Desktop to the WSL2 backend, 15.5GB dynamic vs. a hard static
  // 1.9GB) and a port-8080 conflict (see docker-compose.yml). Both attempts
  // still failed 19-21 of 23 specs — cold AND warm containers, plenty of
  // free CPU/memory, Apache's MaxRequestWorkers=150 nowhere near saturated.
  // Root cause not identified then (candidates: WSL2's NAT/port-forward layer
  // under bursty concurrent connections, or MongoDB — on the host, not
  // containerized — under concurrent PHP sessions).
  //
  // 2026-08-08: retried at workers: 2 (half the failed concurrency) now that
  // the swap is live, cache-busting is fixed (f4f090a), and timeouts are
  // retuned to measured reality. Same failure signature reproduced: 3 specs
  // (forms, explorer, insertionq) exhausted all retries on "beforeAll hook
  // timeout ... waiting for locator('#desktop')" — 4 simultaneous boots (2
  // workers × 2 boots each) contending for something, cascading into every
  // downstream test failing instantly once the shared page never came up.
  // Deliberately isolated whether this is the idae-be swap itself: logged in
  // through a single uncontended browser tab (no Playwright, no concurrency)
  // against the same live container — #desktop rendered, 87 schemes loaded,
  // real data in every panel, zero console errors. The swap is clean in
  // normal use; the failure is concurrency contention, not a rendering
  // regression. workers: 1 remains the only configuration proven reliable.
  workers: 1,
  // Measured directly (Chromium, real network, 2026-08-08) after the
  // per-file cache-busting fix (f4f090a): cold boot to login-form-visible
  // ~11.3s (106 requests), warm (bag.js IndexedDB cache hit) ~8.9s (16
  // requests). Each worker boots twice back-to-back (the worker-scoped UI
  // login in fixtures/test-base.ts, then the shared boot in
  // fixtures/shared-boot.ts's beforeAll) before any test body runs. First
  // workers:2 attempt at 45s failed 2 specs on "beforeAll hook timeout of
  // 45000ms exceeded" waiting on #desktop — legitimate boot contention
  // under 2 concurrent workers (4 simultaneous boots), not a fixture bug.
  // 60s (2026-08-08): still low relative to the 180s this used to be, but
  // gives real headroom above the 45s ceiling that just failed instead of
  // guessing again. Do not widen further without a fresh measurement.
  timeout: 60000,
  expect: { timeout: 15000 },
  // main_bag.js's boot is genuinely flaky under the current WSL2 backend —
  // reproduced outside Playwright entirely (plain Chromium via the Playwright
  // API): the app's socket.io client sometimes disconnects and reconnects
  // mid-boot, and schemeLoad() doesn't recover from that, hanging until
  // timeout. Not a bug in this suite's fixtures — a real, standard case for
  // retries.
  retries: 2,
});
