Playwright test run — 2026-03-25T13:54:32Z

Summary: Playwright exited with errors. Test runner reported "Playwright Test did not expect test() to be called here." Likely causes: test files are being imported by config, duplicate @playwright/test versions, or config file importing tests.

Raw error excerpt:

Error: Playwright Test did not expect test() to be called here.
Most common reasons include:
- You are calling test() in a configuration file.
- You are calling test() in a file that is imported by the configuration file.
- You have two different versions of @playwright/test. This usually happens
  when one of the dependencies in your package.json depends on @playwright/test.

Trace (example locations):
- playwright/tests/crud.spec.ts:5
- playwright/tests/smoke.spec.ts:3
- playwright/tests/uiux.spec.ts:5

Actionable next steps:
- Verify playwright.config.ts (ensure it does not import test files directly).
- Run `npm ls @playwright/test` to detect duplicate versions.
- Re-run tests with DEBUG=pw:test to get more details.

Attached output captured from initial run is saved here. Tester assigned to open an artifact and notify Developer to investigate.
