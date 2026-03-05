# Idae Socket Server (Modernized)

This is the refactored Node.js server for Idae Legacy, providing Socket.IO real-time features and PHP-Node bridging.

## Architecture

Refactored from a monolithic `idae_server.js` to a modular ESM structure (Node 18+).

### Structure
- `src/main.js`: Application entry point.
- `src/config/`: Configuration and Host lists.
- `src/db/`: MongoDB Connection (Driver v6).
- `src/services/`: Services (Cron, PHPBridge, SessionManager).
- `src/socket/`: Socket.IO Setup, Middleware, and Event Handlers.
- `src/web/`: Express Routes (Webhooks from PHP).

## Installation

1.  Navigate to this directory:
    ```bash
    cd idae/web/app_node
    ```
2.  Install dependencies:
    ```bash
    npm install
    ```
3.  Configure `.env` (created automatically, adjust if needed):
    ```ini
    MONGO_HOST=host.docker.internal
    MONGO_USER=admin
    MONGO_PASS=gwetme2011
    PORT=3005
    ```

## Running

**Development:**
```bash
npm run dev
# Uses nodemon to restart on changes
```

**Production:**
```bash
npm start
# Runs 'node src/main.js'
```

## Legacy Notes
The old `idae_server.js` has been renamed to `idae_server.legacy.js` for reference.
Legacy scripts like `start_socket.js` or `appmodules/` are deprecated in favor of this structure.
