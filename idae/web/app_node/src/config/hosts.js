/**
 * Migrated code and structure from Lebrun Meddy 2009
 */
import { config } from './config.js';

// Default hosts from legacy code
const DEFAULT_HOSTS = [
    'tactac.idae.preprod.lan',
    'maw.idae.preprod.lan',
    'leasys.idae.preprod.lan',
    'idae.io.idae.preprod.lan',
    'blog.idae.preprod.lan'
];

export function getHosts() {
    if (process.env.APP_HOSTS) {
        return process.env.APP_HOSTS.split(',').map(h => h.trim());
    }
    return DEFAULT_HOSTS;
}
