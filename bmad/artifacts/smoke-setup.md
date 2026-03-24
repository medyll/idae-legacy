Smoke test setup and instructions

What was done:
- Playwright test scaffold added at /playwright (playwright.config.ts, tests/smoke.spec.ts, package.json, README).
- Docker: ./logs mounted into socket container; LOG_DIR set; socket container Node version upgraded to node:20-slim.
- Helpers: docker-restart.ps1 socket, docker-logs.ps1 (apache/socket/all), DEBUG_AGENT.md created.

How to run smoke tests (host):
1. cd playwright
2. npm install
3. npx playwright install --with-deps
4. Set environment variables for credentials:
   - Windows PowerShell: $env:PLAYWRIGHT_USER='devuser'; $env:PLAYWRIGHT_PASS='devpass'
   - Unix: export PLAYWRIGHT_USER=devuser; export PLAYWRIGHT_PASS=devpass
5. npm test

Notes:
- Tests target http://host.docker.internal:8080 by default. Adjust BASE_URL in playwright.config.ts if needed.
- If Playwright cannot run on host, run tests from a machine with network access to Docker host.
- If any step fails, check logs: .\docker-logs.ps1 socket and .\docker-logs.ps1 apache
