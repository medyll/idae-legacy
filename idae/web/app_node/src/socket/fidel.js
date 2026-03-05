/**
 * Migrated code and structure from Lebrun Meddy 2009
 */
import { db } from '../db/mongo.js';

export function setupFidelNamespace(io) {
    const fidel = io.of("/fidel");
    
    fidel.on("connection", function (socket) {
        // console.log("[FIDEL] Connection", socket.id);

        socket.on("disconnect", async function () {
            try {
                const collection = db.onLineSite;
                if (collection) {
                    await collection.deleteOne({ socket: socket.id });
                }
            } catch (err) {
                console.error("[FIDEL] Disconnect error:", err);
            }
        });

        socket.on("grantSite", async function (data) {
            data = data || {};
            const cleanData = {
                online: 1,
                ip: data.ip || "",
                ssid: data.ssid || "",
                host: data.host || "",
                uri: data.uri || "",
                time: Math.floor(Date.now() / 1000)
            };

            try {
                const collection = db.onLineSite;
                if (collection) {
                    await collection.updateOne(
                        { socket: socket.id },
                        { $set: cleanData },
                        { upsert: true }
                    );
                }
            } catch (err) {
                console.error("[FIDEL] grantSite error:", err);
            }
        });
    });
}
