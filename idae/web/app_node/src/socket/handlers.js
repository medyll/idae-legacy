/**
 * Migrated code and structure from Lebrun Meddy 2009
 */
import { config } from '../config/config.js';
import logger from '../config/logger.js';
import { db } from '../db/mongo.js';
import { sessionManager } from '../services/sessionManager.js';
import { phpBridge } from '../services/phpBridge.js';
import qs from 'qs';

/**
 * Main Socket Event Handlers
 */
export function registerHandlers(io, socket) {

    // Sanitize DOCUMENTDOMAIN from client (strip trailing slashes, paths, query strings)
    // When running in Docker, rewrite to internal service name so PHP bridge can reach Apache
    function cleanDomain(domain) {
        if (!domain) return domain;
        // Remove trailing slash(es)
        domain = domain.replace(/\/+$/, '');
        // Remove any path/query that might have leaked in (e.g. localhost:8080/index.php?retry=1)
        const slashIdx = domain.indexOf('/');
        if (slashIdx !== -1) {
            domain = domain.substring(0, slashIdx);
        }
        // Rewrite to internal Docker host if configured
        if (config.phpBridgeHost) {
            domain = config.phpBridgeHost;
        }
        return domain;
    }

    // --- Join Rooms logic ---
    if (socket.PHPSESSID) {
        socket.join(socket.PHPSESSID);
    }
    
    if (socket.handshake && socket.handshake.headers && socket.handshake.headers.host) {
        socket.join(socket.handshake.headers.host);
    }

    // --- Heartbeat ---
    const sender = setInterval(function () {
        socket.emit("message", Date.now());
        socket.emit("heartbeat_app", Date.now());
    }, 10000);

    // --- Disconnect ---
    socket.on("disconnect", async function () {
        clearInterval(sender);
        
        const session = sessionManager.getBySession(socket.id);
        if (session && session.SESSID) {
            try {
                // Ensure SESSID is treated as integer if possible, or string based on legacy 'eval' usage
                // Refactoring note: legacy used `eval(session.SESSID)`. We assume it's an ID.
                const idagent = parseInt(session.SESSID, 10); 
                
                if (!isNaN(idagent)) {
                     await db.onLine.updateOne(
                        { idagent: idagent },
                        { $set: { online: 0 } },
                        { upsert: true }
                    );
                }
            } catch (e) {
                logger.error("Disconnect DB update error", e);
            }
            sessionManager.removeBySession(socket.id);
        }
    });

    // --- GrantIn (Login) ---
    socket.on("grantIn", function (data, fn) {
        logger.info("[SOCKET] grantIn received:", data);
        
        const sess = {
            sessionId: socket.id,
            DOCUMENTDOMAIN: data.DOCUMENTDOMAIN,
            SESSID: data.SESSID,
            PHPSESSID: data.PHPSESSID
        };

        if (data.DOCUMENTDOMAIN) {
            socket.join(data.DOCUMENTDOMAIN);
            // Verify if io.sockets.to(...).send() is supported in v4, usually emit('message')
            io.to(data.DOCUMENTDOMAIN).emit("message", "user connected"); 
        }

        sessionManager.add(sess);

        if (data.SESSID) {
            if (fn) fn("woot");
            socket.emit("notify", data);
        }
    });

    // --- Dispatcher ---
    socket.on("dispatch_cmd", function (data) {
        io.emit(data.cmd, { vars: data });
        if (data.dispatch_to) {
            io.to(data.dispatch_to).emit("receive_cmd", { cmd: data.cmd, vars: data.dispatch_vars });
        } else if (data.DOCUMENTDOMAIN) {
            io.to(data.DOCUMENTDOMAIN).emit("receive_cmd", { cmd: data.cmd, vars: data.dispatch_vars });
        }
    });

    socket.on("reloadModule", function (data) {
        socket.broadcast.emit("reloadModule", data);
    });

    socket.on("reloadScope", function (data) {
        if (!data || !data.scope) return;
        
        let reloadVars = { scope: data.scope, value: data.value };
        if (data.vars) reloadVars.vars = qs.stringify(data.vars);
        
        if (data.scope && data.value) {
            io.emit("reloadScope", reloadVars);
        }
    });

    // --- PHP Calls ---

    // loadModule
    socket.on("loadModule", async function (data, func) {
        const DOCUMENTDOMAIN = cleanDomain(data.DOCUMENTDOMAIN) || "app.destinationsreve.com";
        const mdl = data.mdl || "";
        const SESSID = data.SESSID || "";
        
        logger.info(`[loadModule] DOMAIN=${DOCUMENTDOMAIN} mdl=${mdl} SESSID=${SESSID} PHPSESSID=${data.PHPSESSID || 'none'} vars=${data.vars || ''}`);
        if (DOCUMENTDOMAIN) socket.join(DOCUMENTDOMAIN);

        const url = `http://${DOCUMENTDOMAIN}/mdl/${mdl}.php?SESSID=${SESSID}`;
        logger.info(`[loadModule] -> ${url}`);
        
        try {
            const result = await phpBridge.post(url, data);
            logger.info(`[loadModule] <- status=${result.status} size=${result.data ? result.data.length : 0}`);
            socket.emit("loadModule", {
                body: result.data,
                vars: data.vars || "",
                mdl: mdl,
                title: data.title || ""
            });
        } catch (e) {
            logger.error(`[loadModule] ERROR: ${e.message}`);
        }
    });

    // socketModule
    socket.on("socketModule", async function (data, fun) {
        const DOCUMENTDOMAIN = cleanDomain(data.DOCUMENTDOMAIN) || "appgem.destinationsreve.com";
        logger.info(`[socketModule] DOMAIN=${DOCUMENTDOMAIN} file=${data.file} element=${data.element || 'none'} PHPSESSID=${data.PHPSESSID || 'none'} SESSID=${data.SESSID || 'none'} vars=${data.vars || ''}`);
        if (DOCUMENTDOMAIN) socket.join(DOCUMENTDOMAIN);

        const url = `http://${DOCUMENTDOMAIN}/mdl/${data.file}.php`;
        
        try {
            const body = typeof data.vars === 'string' ? data.vars : qs.stringify(data.vars);
            logger.info(`[socketModule] -> ${url} body=${body.substring(0, 200)}`);
            const result = await phpBridge.post(url, data, body);
            const preview = typeof result.data === 'string' ? result.data.substring(0, 150) : JSON.stringify(result.data).substring(0, 150);
            logger.info(`[socketModule] <- status=${result.status} size=${result.data ? result.data.length : 0} preview=${preview}`);
            
            socket.emit("socketModule", { body: result.data, out: data });
            if (fun) fun({ body: result.data, data: data });
        } catch (e) {
            logger.error(`[socketModule] ERROR: ${e.message}`);
        }
    });

    // upd_data
    socket.on("upd_data", async function (data) {
        const DOCUMENTDOMAIN = cleanDomain(data.DOCUMENTDOMAIN) || "app.destinationsreve.com";
        const url = `http://${DOCUMENTDOMAIN}/services/json_data_table_row.php`;
        
        try {
            const result = await phpBridge.post(url, data);
            io.emit("upd_data", {
                body: result.data,
                vars: data.vars || "",
                mdl: data.mdl || "",
                title: data.title || ""
            });
        } catch (e) {}
    });

    // get_data
    socket.on("get_data", async function (data, options, fn) {
        try {
            const DOCUMENTDOMAIN = cleanDomain(data.DOCUMENTDOMAIN) || "app.destinationsreve.com";
            const directory = data.directory || "services";
            const extension = data.extension || "php";
            const url = `http://${DOCUMENTDOMAIN}/${directory}/${data.mdl}.${extension}`;

            logger.info(`[get_data] DOMAIN=${DOCUMENTDOMAIN} mdl=${data.mdl} PHPSESSID=${data.PHPSESSID || 'none'} SESSID=${data.SESSID || 'none'} vars=${JSON.stringify(data.vars || {})}`);
            logger.info(`[get_data] -> ${url}`);
            const result = await phpBridge.get(url, data);
            
            // Legacy client expects a JSON string, but Axios automatically parses JSON.
            // We must stringify it back if it's an object, so the client can JSON.parse it.
            let responseData = result;
            if (typeof responseData === 'object') {
                responseData = JSON.stringify(responseData);
            }

            const preview = responseData ? responseData.substring(0, 200) : 'empty';
            logger.info(`[get_data] <- size=${responseData ? responseData.length : 0} preview=${preview}`);

            if (fn) fn(responseData, options);
        } catch (e) {
            logger.error(`[get_data] ERROR: ${e.message}`);
            if (fn) fn({ error: true, message: e.message }, options);
        }
    });
    
    // runModule
    socket.on("runModule", async function (data) {
         const DOCUMENTDOMAIN = cleanDomain(data.DOCUMENTDOMAIN) || "appgem.destinationsreve.com";
         const url = `http://${DOCUMENTDOMAIN}/${data.file}.php`;
         
         logger.info(`[runModule] DOMAIN=${DOCUMENTDOMAIN} file=${data.file} PHPSESSID=${data.PHPSESSID || 'none'} vars=${data.vars || ''}`);
         logger.info(`[runModule] -> ${url}`);
         const body = typeof data.vars === 'string' ? data.vars : qs.stringify(data.vars);
         try {
             await phpBridge.post(url, data, body);
             logger.info(`[runModule] <- done`);
         } catch (e) {
             logger.error(`[runModule] ERROR: ${e.message}`);
         }
    });
}
