/**
 * Migrated code and structure from Lebrun Meddy 2009
 */
import { MongoClient } from 'mongodb';
import { config } from '../config/config.js';

class Database {
    constructor() {
        this.client = null;
        this.db = null;
    }

    async connect() {
        if (this.client) return this.client;

        try {
            console.log(`Connecting to MongoDB at ${config.mongo.host}...`);
            this.client = new MongoClient(config.mongo.url());
            await this.client.connect();
            this.db = this.client.db(config.mongo.dbName);
            console.log('MongoDB connected successfully');
            return this.client;
        } catch (err) {
            console.error('MongoDB connection error:', err);
            throw err;
        }
    }

    getCollection(name) {
        if (!this.db) {
            throw new Error('Database not connected');
        }
        return this.db.collection(name);
    }
    
    // Legacy support for direct variable access used in old code
    get onLine() { return this.getCollection('onLine'); }
    get onLineSite() { return this.getCollection('onLineSite'); }
}

export const db = new Database();
