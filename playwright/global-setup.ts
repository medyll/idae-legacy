/**
 * Logs in once and stores the session cookie, so specs skip the login form.
 * The bag.js boot still runs per page load — only the credential round-trip is
 * saved.
 */
import { request } from '@playwright/test';
import { mkdirSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { apiLogin, STORAGE_STATE } from './tests/fixtures/auth';

export default async function globalSetup() {
  const statePath = resolve(__dirname, STORAGE_STATE);
  mkdirSync(dirname(statePath), { recursive: true });

  const ctx = await request.newContext();
  await apiLogin(ctx);
  await ctx.storageState({ path: statePath });
  await ctx.dispose();
}
