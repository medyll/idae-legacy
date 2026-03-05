/**
 * Migrated code and structure from Lebrun Meddy 2009
 */
import express from 'express';
import { phpBridge } from '../services/phpBridge.js';
import qs from 'qs';

export const createRouter = (io) => {
    const router = express.Router();

    // Middleware to parse body is handled in main app (express.urlencoded)

    router.post('/postScope', (req, res) => {
        const data = req.body;
        // console.log('[HTTP] /postScope', data);

        let reloadVars = { scope: data.scope, value: data.value };
        if (data.vars) reloadVars.vars = typeof data.vars === 'object' ? qs.stringify(data.vars) : data.vars;

        if (data.scope && data.value) {
            io.emit('reloadScope', reloadVars);
        }
        res.type('html').send('');
    });

    router.post('/run', async (req, res) => {
        const data = req.body;
        // console.log('[HTTP] /run', data.mdl); // Minimal log

        const DOCUMENTDOMAIN = data.DOCUMENTDOMAIN;
        if (!DOCUMENTDOMAIN || !data.mdl) { 
             return res.status(400).send('Missing params'); 
        };

        const url = `http://${DOCUMENTDOMAIN}/${data.mdl}.php`;
        
        // Fire and forget logic from original code?
        // Original: request.post(..., generic_callback)
        // We will execute it.
        try {
            await phpBridge.post(url, data);
        } catch (e) {}

        // Original sends no specific response? (it was req.on('end'))
        // Actually original didn't respond! It just closed? 
        // Wait, looking at original code: `res.writeHead?` No, /run didn't have res.write... 
        // Logic: case "/run": req.on("end", function() { request.post(...) }); 
        // It seems it didn't explicitly send response, or default node response?
        // We should send 200 OK.
        if (!res.headersSent) res.sendStatus(200);
    });

    router.post('/runModule', async (req, res) => {
        const data = req.body;
        const mdl = data.mdl;
        const DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || "app.destinationsreve.com";
        const url = `http://${DOCUMENTDOMAIN}/mdl/${mdl}.php`;

        try {
            const result = await phpBridge.post(url, data);
            io.emit("act_run", { body: result.data });
        } catch (e) {
            console.error('/runModule failed', e.message);
        }
        res.type('html').send('');
    });

    router.post('/runForgetModule', async (req, res) => {
        const data = req.body;
        const mdl = data.mdl;
        const SESSID = data.SESSID || "";
        const PHPSESSID = data.PHPSESSID || "";
        const DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || "app.destinationsreve.com";
        const url = `http://${DOCUMENTDOMAIN}/mdl/${mdl}.php?SESSID=${SESSID}&PHPSESSID=${PHPSESSID}`;
        
        // Manual body construction as in legacy
        const body = `SESSID=${SESSID}&PHPSESSID=${PHPSESSID}&${qs.stringify(data.vars || {})}`;

        try {
            const result = await phpBridge.post(url, data, body);
            // console.log(result.data); // Legacy did console.log
            io.emit("act_run", { body: result.data });
            res.type('html').send("done runForgetModule");
        } catch (e) {
             res.type('html').send("error runForgetModule");
        }
    });

    router.post('/postReload', (req, res) => {
        const data = req.body;
        res.type('html').send('');

        const DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || "";
        let reloadVars = { module: data.module, value: data.value || "" };
        
        if (data.cmd && data.vars) {
            if (data.OWN) {
                // Legacy: io.sockets.to(data.OWN)
                io.to(data.OWN).emit("receive_cmd", data);
            } else {
                io.to(DOCUMENTDOMAIN).emit("receive_cmd", data);
            }
        }

        if (data.vars && typeof data.vars === 'object') {
            reloadVars.vars = qs.stringify(data.vars);
        }

        if (data.module && data.value) {
            io.emit("reloadModule", reloadVars);
        }
    });

    return router;
};
