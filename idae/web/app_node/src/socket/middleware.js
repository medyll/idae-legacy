/**
 * Migrated code and structure from Lebrun Meddy 2009
 */
import cookie from 'cookie';
import { config } from '../config/config.js';

export const authMiddleware = (socket, next) => {
    console.log("[AUTH] Headers:", {
        cookie: socket.handshake.headers.cookie,
        host: socket.handshake.headers.host,
        origin: socket.handshake.headers.origin,
        referer: socket.handshake.headers.referer
    });

    if (socket.handshake.headers.cookie) {
        const cookies = cookie.parse(socket.handshake.headers.cookie);
        console.log("[AUTH] Parsed cookies:", cookies);
        
        if (!cookies.PHPSESSID || cookies.PHPSESSID === "undefined") {
            // En mode développement, être moins strict
            if (config.env === 'development') {
                console.warn("[AUTH] No PHPSESSID in dev mode - allowing connection");
                socket.PHPSESSID = 'dev-session-' + Date.now();
                socket.cookie_string = socket.handshake.headers.cookie;
                return next();
            }
            
            const err = new Error("No valid PHPSESSID cookie transmitted.");
            console.error("[AUTH] Failed:", err.message, cookies);
            return next(err);
        }
        
        // Attach PHPSESSID to socket for later use
        socket.PHPSESSID = cookies.PHPSESSID;
        socket.cookie_string = socket.handshake.headers.cookie;
        console.log("[AUTH] Success:", socket.PHPSESSID);
        
        return next();
    } else {
        // En mode développement, permettre connexion sans cookies pour les tests
        if (config.env === 'development') {
            console.warn("[AUTH] No cookies in dev mode - allowing test connection");
            socket.PHPSESSID = 'dev-session-no-cookies-' + Date.now();
            socket.cookie_string = '';
            return next();
        }
        
        const err = new Error("No cookie transmitted.");
        console.error("[AUTH] Failed:", err.message);
        console.error("[AUTH] All headers:", socket.handshake.headers);
        return next(err);
    }
};
