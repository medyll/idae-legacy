/**
 * Migrated code and structure from Lebrun Meddy 2009
 */
import dotenv from 'dotenv';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

// Load .env from root of app_node (two levels up from src/config)
dotenv.config({ path: path.resolve(__dirname, '../../.env') });

export const config = {
    port: process.env.PORT || 3005,
    mongo: {
        host: process.env.MONGO_HOST || 'host.docker.internal',
        user: process.env.MONGO_USER || 'admin',
        pass: process.env.MONGO_PASS || 'gwetme2011',
        dbName: process.env.MONGO_DB || 'sitebase_sockets',
        url() {
            // If running against the local test mongo (no auth) or when credentials are not provided,
            // use an unauthenticated connection string to avoid authentication errors.
            if (!this.user || this.user === '' || this.host === 'mongo-test') {
                return `mongodb://${this.host}:27017/${this.dbName}`;
            }
            return `mongodb://${this.user}:${this.pass}@${this.host}:27017/${this.dbName}?authSource=admin`;
        }
    },
    env: process.env.NODE_ENV || 'development',
    // Map client-facing domains to internal Docker service names for PHP bridge calls
    phpBridgeHost: process.env.PHP_BRIDGE_HOST || ''
};

// Log config at startup
console.log('[CONFIG] Environment:', config.env);
console.log('[CONFIG] Port:', config.port);
console.log('[CONFIG] MongoDB Host:', config.mongo.host);
