/**
 * Migrated code and structure from Lebrun Meddy 2009
 */
import { CronJob } from 'cron';
import axios from 'axios';
import { getHosts } from '../config/hosts.js';

const TIME_ZONE = 'Europe/Paris';

const CRON_PATTERNS = {
    'secondes_30': '30 * * * * *',
    'minute': '00 * * * * *',
    'minutes_5': '00 */5 * * * *',
    'minutes_10': '00 */10 * * * *',
    'minutes_15': '00 */15 * * * *',
    'midday': '00 00 12 * * *',
    'hourly_double': '00 00 */2 * * *',
    'hourly_mid': '00 30 * * * *',
    'hourly': '00 00 * * * *',
    'midnight': '00 00 00 * * *',
    'midnight_hour_1': '00 00 01 * * *',
    'midnight_hour_1_mid': '00 30 01 * * *',
    'midnight_hour_2': '00 00 02 * * *',
    'midnight_hour_2_mid': '00 30 02 * * *'
};

class CronService {
    constructor() {
        this.jobs = [];
    }

    start() {
        const hosts = getHosts();
        console.log('[CRON] Starting cron jobs for hosts:', hosts);

        for (const [type, pattern] of Object.entries(CRON_PATTERNS)) {
            const job = new CronJob(pattern, () => {
                this.launchJob(type, hosts);
            }, null, true, TIME_ZONE);
            
            this.jobs.push(job);
        }
    }

    async launchJob(type, hosts) {
        for (const host of hosts) {
            const url = `http://${host}/bin/cron/cron_dispatch.php?type_cron=${type}&host=${host}`;
            try {
                await axios.get(url, {
                    headers: { 'X-CRON': 'tactac' }
                });
                // console.log(`[CRON] Executed ${type} on ${host}`);
            } catch (err) {
                console.error(`[CRON] Error executing ${type} on ${host}: ${err.message}`);
            }
        }
    }
}

export const cronService = new CronService();
