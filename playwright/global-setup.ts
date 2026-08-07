/**
 * Bridge probe — fails the run in ~10s when Apache/phpBridge is wedged,
 * instead of burning minutes of boot retries per spec file.
 *
 * The wedge (see HANG_TEST.md): phpBridge→Apache calls used to wait forever;
 * when Apache stops answering, every get_data promise never settles and the
 * SPA boot hangs silently. phpBridge now has a 30s axios timeout
 * (83ef31c), but a wedged Apache still means every spec fails slowly —
 * better to refuse the run upfront with an actionable message.
 *
 * This hits the exact endpoint the bridge calls for the boot's first
 * get_data (services/json_scheme.php), through the same published port.
 * It deliberately does NOT restart anything itself: idae-socket is the
 * daily dev stack, not a test fixture — the restart decision stays human.
 */
import { request } from '@playwright/test';

const PROBE_TIMEOUT_MS = 10_000;

export default async function globalSetup(): Promise<void> {
  const base = process.env.BASE_URL || 'http://127.0.0.1:8080';
  const url = `${base}/services/json_scheme.php?piece=scheme`;

  const ctx = await request.newContext({ timeout: PROBE_TIMEOUT_MS });
  try {
    const resp = await ctx.get(url);
    if (!resp.ok()) {
      throw new Error(`HTTP ${resp.status()}`);
    }
  } catch (e) {
    throw new Error(
      `[bridge-probe] ${url} did not answer within ${PROBE_TIMEOUT_MS / 1000}s (${(e as Error).message}).\n` +
      `Apache or the phpBridge path is probably wedged — run \`docker restart idae-socket\`, ` +
      `wait for the container to be healthy, then re-run the suite.\n` +
      `Context: HANG_TEST.md.`
    );
  } finally {
    await ctx.dispose();
  }
}
