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
    baseURL: process.env.BASE_URL || 'http://localhost:8080',
    headless: true,
    viewport: { width: 1400, height: 900 },
    ignoreHTTPSErrors: true,
    // Written by global-setup.ts, which runs before any test.
    storageState: 'tests/.auth/state.json',
    trace: 'retain-on-failure',
  },
  testDir: './tests',
  globalSetup: './global-setup.ts',
  // All specs share one storageState, hence one PHPSESSID. Running contexts
  // in parallel means concurrent server-side requests on the same PHP
  // session — this has taken down the socket bridge under load. One worker
  // until specs get per-test sessions.
  workers: 1,
  // A cold SPA boot loads ~60 scripts sequentially through bag.js, with a
  // cache-buster on every entry. 10-20s before the app is usable is normal.
  timeout: 120000,
  expect: { timeout: 15000 },
});
