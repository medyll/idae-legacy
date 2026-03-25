Playwright smoke test (Idae Legacy)

Purpose:
- Quick smoke test: login -> open grid -> open first item -> perform a simple read.

Prerequisites (on host):
1. Node.js installed (>=16). Recommended: latest LTS or use nvm.
2. From repository root run:
   cd playwright
   npm install
   npx playwright install --with-deps
3. Set environment variables for credentials (or edit test to hardcode dev creds):
   On Windows PowerShell: $env:PLAYWRIGHT_USER='devuser'; $env:PLAYWRIGHT_PASS='devpass'
   On Unix: export PLAYWRIGHT_USER=devuser; export PLAYWRIGHT_PASS=devpass

Run:
  cd playwright
  npm test

Notes:
- Tests target http://host.docker.internal:8080 by default. If running non-docker, set BASE_URL env var or edit playwright.config.ts.
- Adjust selectors in tests/smoke.spec.ts to match the app's login form and grid.
- Running Playwright requires network access to the Docker-hosted app; use host.docker.internal for Linux/Windows Docker Desktop.
