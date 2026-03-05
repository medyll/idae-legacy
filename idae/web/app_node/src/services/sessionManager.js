/**
 * Migrated code and structure from Lebrun Meddy 2009
 */
class SessionManager {
    constructor() {
        this.sessions = new Map();
    }

    add(sess) {
        if (sess && sess.sessionId) {
            this.sessions.set(sess.sessionId, sess);
        }
    }

    getBySession(sessionId) {
        return this.sessions.get(sessionId) || null;
    }

    removeBySession(sessionId) {
        this.sessions.delete(sessionId);
    }
}

export const sessionManager = new SessionManager();
