/**
 * Migrated code and structure from Lebrun Meddy 2009
 */
import cookie from 'cookie';

export const authMiddleware = (socket, next) => {
    // console.log("[AUTH] Headers:", socket.handshake.headers);

    if (socket.handshake.headers.cookie) {
        const cookies = cookie.parse(socket.handshake.headers.cookie);
        if (!cookies.PHPSESSID || cookies.PHPSESSID === "undefined") {
            const err = new Error("No valid PHPSESSID cookie transmitted.");
            console.error("[AUTH] Failed:", err.message);
            return next(err);
        }
        
        // Attach PHPSESSID to socket for later use
        socket.PHPSESSID = cookies.PHPSESSID;
        socket.cookie_string = socket.handshake.headers.cookie;
        
        return next();
    } else {
        const err = new Error("No cookie transmitted.");
        console.error("[AUTH] Failed:", err.message);
        return next(err);
    }
};
