Debug & smoke-test helpers (agent-friendly)

Purpose

Central commands and notes to quickly restart services, tail logs, and run smoke checks during development.

Quick commands (PowerShell, run from repository root)

- Restart only the socket service:
  .\docker-restart.ps1 socket

- Tail socket logs (will read mounted ./logs/socket.log if present, else falls back to container logs):
  .\docker-logs.ps1 socket

- Tail Apache/PHP logs:
  .\docker-logs.ps1 apache

- Combined logs (opens two tails):
  .\docker-logs.ps1 all

Health checks

- App health (inside container):
  curl -f http://localhost:8080/idae/web/check_mongo.php

- Socket health:
  curl -f http://localhost:3005/health

Session cookie notes

- conf.lan.inc.php sets development-friendly session cookie settings (SameSite=Lax, cookie_domain adjusted for localhost). For production, ensure session.cookie_secure=1 and SameSite=None if needed for cross-site cookies.

Playwright (optional)

- Recommended: run Playwright from host, pointing to http://host.docker.internal:8080. Create a simple test to login, open a grid page, and perform a CRUD action.
- If desired, agent can create a sample Playwright test and run it locally (requires Playwright installed on host).

Node engine warnings

- Current socket container uses Node v18; some npm packages warn for Node >=20. Upgrading to node:20-slim is optional and may require rebuilding containers.

Files written/modified by agent

- docker-compose.yml (mounted ./logs into socket, set LOG_DIR=/logs)
- docker-restart.ps1 (added 'socket' option)
- conf.lan.inc.php (dev-friendly session cookie settings)
- docker-logs.ps1 (added socket/apache/all modes)

If you want the agent to proceed with Playwright test creation or Node upgrade, reply "launch-playwright" or "upgrade-node". Otherwise the current environment is ready for interactive debugging.