/**
 * Unified advanced socket app: fusion complète de appsocket.app.js et app_idaertys.app.js
 * - Gestion avancée des rooms, sessions, heartbeat, MongoDB, authorizations, etc.
 * - Toutes les routes HTTP et tous les événements socket des deux fichiers sont présents.
 *
 * Migrated code and structure from Lebrun Meddy 2009
 */
import express from 'express';
import http from 'http';
import { Server } from 'socket.io';
import { config } from './config/config.js';
import { db } from './db/mongo.js';
import { cronService } from './services/cron.js';
import { createRouter } from './web/routes.js';
import { registerHandlers } from './socket/handlers.js';
import { authMiddleware } from './socket/middleware.js';
import { setupFidelNamespace } from './socket/fidel.js';

async function bootstrap() {
    console.log('[BOOT] Starting Idae Node Server...');

    // 1. Connect to Database
    try {
        await db.connect();
    } catch (err) {
        console.error('[BOOT] Critical: Database connection failed. Exiting.');
        process.exit(1);
    }

    // 2. Setup Express & HTTP Server
    const app = express();
    const server = http.createServer(app);

    // Middleware
    app.use(express.urlencoded({ extended: true, limit: '50mb' }));
    app.use(express.json({ limit: '50mb' }));

    // 3. Setup Socket.IO
    const io = new Server(server, {
        cors: {
            origin: [
                "http://localhost:8080", "http://127.0.0.1:8080", "http://localhost",
                "http://localhost:80", "http://127.0.0.1:80", "http://127.0.0.1",
                /^http:\/\/.*\.lan(:\d+)?$/, // Allow .lan domains with optional port
                /^http:\/\/localhost(:\d+)?$/, // Allow localhost with any port
                /^http:\/\/127\.0\.0\.1(:\d+)?$/ // Allow 127.0.0.1 with any port
            ],
            methods: ["GET", "POST"],
            credentials: true
        },
        allowEIO3: true // Support legacy clients if needed (v2/v3)
    });

    // 4. Socket Internals
    io.use(authMiddleware);

    io.on('connection', (socket) => {
        console.log(`[SOCKET] New connection ${socket.id}`);

        socket.onAny((eventName, ...args) => {
           // console.log(`[SOCKET DEBUG] INCOMING: ${eventName}`, args);
        });

        registerHandlers(io, socket);
    });

    setupFidelNamespace(io);

    // 5. HTTP Routes (Inject IO to trigger socket events from Webhooks)
    app.use('/', createRouter(io));

    // Health check
    app.get('/', (req, res) => res.send('Idae Socket Server Running'));

    // 6. Start Cron
    cronService.start();

    // 7. Listen
    server.listen(config.port, () => {
        console.log(`[BOOT] Server listening on port ${config.port}`);
        console.log(`[BOOT] Environment: ${config.env}`);
    });
    
    // Graceful Shutdown
    process.on('SIGTERM', async () => {
        console.log('SIGTERM signal received: closing HTTP server');
        server.close(() => {
            console.log('HTTP server closed');
            process.exit(0);
        });
    });
}

bootstrap().catch(err => {
    console.error('[BOOT] Unhandled Error:', err);
    process.exit(1);
});
