import { test, expect } from './fixtures/test-base';
import { openApp } from './fixtures/auth';

test('IDAE_SHIM_WARN logs shimmed calls', async ({ page }) => {
  const warns: string[] = [];
  page.on('console', (m) => { if (m.type() === 'warning' && m.text().includes('[idae-shim]')) warns.push(m.text()); });
  await page.addInitScript(() => { (window as any).IDAE_SHIM_WARN = 1; });
  await openApp(page);
  console.log('WARNS ' + warns.length + ' sample: ' + (warns[0] || 'NONE').split('\n')[0]);
  expect(warns.length).toBeGreaterThan(0);
});
