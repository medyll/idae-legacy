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
    viewport: { width: 1280, height: 720 },
    ignoreHTTPSErrors: true,
  },
  testDir: './tests',
  timeout: 30000,
});
