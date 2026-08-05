/**
 * Console guard — the primary regression detector for the PrototypeJS → idae-be
 * migration.
 *
 * The legacy SPA has no JS unit tests. What it does have is a clean console: a
 * cold boot plus a login currently produces zero errors. Any missing `$`,
 * `Element.xxx is not a function`, or broken `Class.create` chain surfaces here
 * long before it shows up as a visibly wrong screen.
 *
 * Usage:
 *   const guard = watchConsole(page);
 *   ...
 *   guard.assertClean();
 */
import { expect, type Page } from '@playwright/test';

/**
 * Messages that are noise in this environment rather than regressions.
 * Keep this list short and justified — every entry is a blind spot.
 */
const IGNORED = [
  /favicon\.ico/i,
  /net::ERR_(ABORTED|CONNECTION_REFUSED)/i, // socket server may be down in CI
  /WebSocket connection to .* failed/i,
];

export interface ConsoleGuard {
  /** Every error captured so far, in order. */
  readonly errors: string[];
  /** Fails the test if any non-ignored console error or page error was seen. */
  assertClean(): void;
}

export function watchConsole(page: Page): ConsoleGuard {
  const errors: string[] = [];

  const record = (text: string) => {
    if (IGNORED.some((re) => re.test(text))) return;
    errors.push(text);
  };

  page.on('console', (msg) => {
    if (msg.type() === 'error') record(`console.error: ${msg.text()}`);
  });
  page.on('pageerror', (err) => record(`pageerror: ${err.message}`));

  return {
    get errors() {
      return errors;
    },
    assertClean() {
      expect(errors, `unexpected console errors:\n${errors.join('\n')}`).toEqual([]);
    },
  };
}
