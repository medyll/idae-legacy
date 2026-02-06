/**
 * Migrated code and structure from Lebrun Meddy 2009
 */
import { db } from '../db/mongo.js';
import { sessionManager } from '../services/sessionManager.js';
import { phpBridge } from '../services/phpBridge.js';
import qs from 'qs';

/**
 * Main Socket Event Handlers
 */
export function registerHandlers(io, socket) {

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
                console.error("Disconnect DB update error", e);
            }
            sessionManager.removeBySession(socket.id);
        }
    });

    // --- GrantIn (Login) ---
    socket.on("grantIn", function (data, fn) {
        // console.log("socket on grantin ", data);
        
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
        const DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || "app.destinationsreve.com";
        const mdl = data.mdl || "";
        const SESSID = data.SESSID || "";
        
        if (DOCUMENTDOMAIN) socket.join(DOCUMENTDOMAIN);

        const url = `http://${DOCUMENTDOMAIN}/mdl/${mdl}.php?SESSID=${SESSID}`;
        
        try {
            const result = await phpBridge.post(url, data); // post uses data.vars
            socket.emit("loadModule", {
                body: result.data,
                vars: data.vars || "",
                mdl: mdl,
                title: data.title || ""
            });
        } catch (e) {
            // Error handled in bridge
        }
    });

    // socketModule
    socket.on("socketModule", async function (data, fun) {
        const DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || "appgem.destinationsreve.com";
        if (DOCUMENTDOMAIN) socket.join(DOCUMENTDOMAIN);

        const url = `http://${DOCUMENTDOMAIN}/mdl/${data.file}.php`;
        
        try {
            // The legacy code sent `data.vars` as body directly? 
            // `body: data.vars` in legacy request.post
            // We'll pass raw body
            const body = typeof data.vars === 'string' ? data.vars : qs.stringify(data.vars);
            const result = await phpBridge.post(url, data, body);
            
            socket.emit("socketModule", { body: result.data, out: data });
            if (fun) fun({ body: result.data, data: data });
        } catch (e) {}
    });

    // upd_data
    socket.on("upd_data", async function (data) {
        const DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || "app.destinationsreve.com";
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
            const DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || "app.destinationsreve.com";
            const directory = data.directory || "services";
            const extension = data.extension || "php";
            const url = `http://${DOCUMENTDOMAIN}/${directory}/${data.mdl}.${extension}`;

            const result = await phpBridge.get(url, data);
            if (fn) fn(result, options);
        } catch (e) {
            if (fn) fn({ error: true, message: e.message }, options);
        }
    });
    
    // runModule
    socket.on("runModule", async function (data) {
         const DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || "appgem.destinationsreve.com";
         const url = `http://${DOCUMENTDOMAIN}/${data.file}.php`;
         
         const body = typeof data.vars === 'string' ? data.vars : qs.stringify(data.vars);
         await phpBridge.post(url, data, body);
    });
}
