Playwright smoke test (Idae Legacy)

Purpose:
- Quick smoke test: login -> open grid -> open first item -> perform a simple read.

Prerequisites (on host):
1. Node.js installed (>=16). Recommended: latest LTS or use nvm.
2. From repository root run:
   cd playwright
   npm install
   npx playwright install --with-deps
3. Create the local testing environment once from the repository root:
   Copy-Item .env.testing.example .env.testing
   Then set the real TEST_LOGIN and TEST_PASSWORD values in .env.testing.

The Playwright configuration loads .env.testing automatically. The local file is ignored by Git;
.env.testing.example contains placeholders only. Explicit BASE_URL, PLAYWRIGHT_USER and PLAYWRIGHT_PASS
environment variables still take precedence when provided.

Run:
  cd playwright
  npm test

Notes:
- Tests target TEST_BASE_URL from .env.testing (http://localhost:8080 in the example).
- Adjust selectors in tests/smoke.spec.ts to match the app's login form and grid.
- Running Playwright requires network access to the Docker-hosted app; use host.docker.internal for Linux/Windows Docker Desktop.
