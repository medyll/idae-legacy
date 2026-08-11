/**
 * Positive control for the IDAE_SHIM_WARN instrument.
 *
 * Every other spec's "does not call compatibility shims" guard ends with
 * `expect(warnings).toEqual([])`. That passes when the migrated file is
 * clean — and equally when the instrument records nothing at all. This
 * spec is what makes those guards mean something: it proves the instrument
 * fires.
 *
 * Modified: 2026-08-11 — rewritten to arm *after* boot and to trigger the
 * call deliberately. The original armed IDAE_SHIM_WARN via addInitScript
 * (so before the shims loaded) and collected every warning of a whole cold
 * boot into an unbounded array. installWarnWraps() wraps
 * Function.prototype (hence every .bind()), Array.prototype and
 * String.prototype, and each wrapped call throws an Error to capture a
 * stack, so an instrumented boot emits them continuously: that collector
 * exhausted Node's 4GB heap in ~134s ("FATAL ERROR: Ineffective
 * mark-compacts near heap limit") and, short of dying, stalled full-suite
 * runs for 17+ minutes on this one file. Capping the collector was not
 * enough — an instrumented boot alone still overran the 60s test budget
 * when run in sequence.
 *
 * Arming after boot and calling one shim-owned method proves exactly the
 * same thing in a fraction of a second, and leaves the boot untouched.
 */
import { test, expect } from './fixtures/test-base';
import { sharedPage } from './fixtures/shared-boot';

const getPage = sharedPage();

test('IDAE_SHIM_WARN logs shimmed calls', async () => {
  const page = getPage();
  const warns: string[] = [];

  const CAP = 20;
  const onConsole = (m: { type(): string; text(): string }) => {
    if (warns.length >= CAP) return;
    if (m.type() === 'warning' && m.text().includes('[idae-shim]')) warns.push(m.text());
  };
  page.on('console', onConsole as any);

  // Arm, make one deliberate shim-owned call, disarm immediately. Leaving
  // it armed would degrade the shared page for every spec that follows.
  await page.evaluate(() => {
    (window as any).IDAE_SHIM_WARN = 1;
    (window as any).__idaeShimInstallWarn();
    try { (document.body as any).hasClassName('idae-shim-probe'); } catch (e) { /* reported below */ }
    (window as any).IDAE_SHIM_WARN = 0;
  });

  await page.waitForTimeout(200);
  page.off('console', onConsole as any);

  console.log('WARNS ' + warns.length + ' sample: ' + (warns[0] || 'NONE').split('\n')[0]);
  expect(
    warns.length,
    'the shim instrument recorded nothing for a deliberate shimmed call — every ' +
    '"does not call compatibility shims" guard in this suite is vacuous',
  ).toBeGreaterThan(0);
});
