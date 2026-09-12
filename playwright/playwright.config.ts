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

// Never let "localhost" through, whatever .env.testing says.
//
// The app derives its socket.io host from `document.domain` (app_socket.js),
// so browsing to localhost:8080 makes the browser open ws://localhost:3005 —
// and on Windows "localhost" resolves to ::1 first while Docker's WSL2
// port-forward only binds IPv4. HTTP survives that via Happy Eyeballs; the
// WebSocket does not reliably: under concurrent boots it dies mid-handshake
// ("WebSocket is closed before the connection is established"), socket.io
// reconnects with a fresh sid, and schemeLoad() never resolves — the app
// hangs with APPSCHEMES empty, no login form and no #desktop. That is the
// whole "4 workers mystery" this suite has been pinned at workers: 1 for.
// Measured 2026-08-09: 4 concurrent boots on localhost → 1/4 reach the UI;
// on 127.0.0.1 → 4/4, and 8/8 at 8 concurrent boots.
if (process.env.BASE_URL) {
  process.env.BASE_URL = process.env.BASE_URL.replace(/\/\/localhost(?=[:/]|$)/, '//127.0.0.1');
}
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
  // Was pinned at 1 for months. Every attempt at 2 or 4 workers failed the
  // same way — specs dying in `beforeAll` on #desktop, or in the worker login
  // fixture on input[name=loginAgent] — and it was written off as unexplained
  // "concurrency contention" (WSL2 NAT, host MongoDB, Apache).
  //
  // It was none of those. It was the hostname. `.env.testing` sets
  // TEST_BASE_URL=http://localhost:8080, which won over this file's
  // 127.0.0.1 default, and the app derives its socket.io host from
  // `document.domain` (app_socket.js) — so the browser opened
  // ws://localhost:3005, hit Windows' IPv6-first resolution against a
  // WSL2 port-forward that only binds IPv4, and the WebSocket died
  // mid-handshake under concurrent boots. socket.io reconnected with a new
  // sid, the in-flight get_data acks were answered to the dead one, and
  // schemeLoad() waited forever: APPSCHEMES empty, no login form, no
  // #desktop. Not contention — a hung boot that looked like a slow one.
  //
  // Both halves are fixed: BASE_URL is normalized off localhost above, and
  // get_data re-emits in-flight requests when the socket reconnects instead
  // of hanging (javascript/app/app.js). Measured 2026-08-09, concurrent cold
  // boots to a usable UI: localhost 1/4 before the fixes, 4/4 after;
  // 127.0.0.1 4/4 and 8/8.
  //
  // 2, not 4 — and for throughput, not correctness. Both pass 23/23 on a
  // healthy stack, but 4 is *slower*: 2.3min vs 1.7min (measured 2026-08-09,
  // back to back, same containers). Each worker boots twice (worker login +
  // shared boot), and a boot is ~10s of one core parsing and eval'ing ~90
  // scripts, so past 2 workers the boots just contend. Under any extra
  // machine load, 4 degrades further into `beforeAll` timeouts — no console
  // errors, just boots missing the 60s hook budget. Revisit only if the boot
  // stops being a synchronous script-eval storm.
  //
  // If a run suddenly collapses into boot timeouts everywhere, suspect the
  // stack before this number: a wedged phpBridge produces exactly that
  // signature. `docker restart idae-legacy` (HANG_TEST.md).
  workers: 2,
  // Measured directly (Chromium, real network, 2026-08-08) after the
  // per-file cache-busting fix (f4f090a): cold boot to login-form-visible
  // ~11.3s (106 requests), warm (bag.js IndexedDB cache hit) ~8.9s (16
  // requests). Each worker boots twice back-to-back (the worker-scoped UI
  // login in fixtures/test-base.ts, then the shared boot in
  // fixtures/shared-boot.ts's beforeAll) before any test body runs. 8
  // concurrent cold boots (the 4-worker worst case) measured 9-12.5s each on
  // 2026-08-09, so 60s keeps ~5x headroom. Do not widen without a fresh
  // measurement — a boot that needs more than this is hung, not slow.
  timeout: 60000,
  expect: { timeout: 15000 },
  // Kept for the residual case the get_data recovery softens rather than
  // removes: a WebSocket dropped mid-boot still costs a 10s re-emit round,
  // and three of those exhaust the test timeout. The hang itself is gone, and
  // the last full run was 23/23 with zero flaky — so a retry firing at all is
  // now a signal worth reading, not background noise.
  retries: 2,
});
