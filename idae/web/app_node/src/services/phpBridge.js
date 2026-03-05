/**
 * Migrated code and structure from Lebrun Meddy 2009
 */
import axios from 'axios';
import qs from 'qs';

/**
 * Wrapper for PHP Legacy calls
 */
export const phpBridge = {
    /**
     * Build Headers for PHP request
     */
    buildHeaders(data) {
        const PHPSESSID = data.PHPSESSID || "";
        return {
            "Cookie": "PHPSESSID=" + PHPSESSID + "; path=/",
            "content-type": "application/x-www-form-urlencoded"
        };
    },

    /**
     * Call PHP Script via POST
     */
    async post(url, data, bodyOverride = null) {
        try {
            const body = bodyOverride || qs.stringify(data.vars || {});
            const headers = this.buildHeaders(data);
            
            // Console log for debug
            // console.log(`[PHP-BRIDGE] POST ${url}`, { headers, bodyLength: body.length });

            const response = await axios.post(url, body, {
                headers: headers,
                // Handle redirects if needed, though usually PHP API won't redirect
                maxRedirects: 0,
                validateStatus: function (status) {
                    return status >= 200 && status < 500; // Resolve even if 400/500 to handle PHP errors gracefully
                },
            });
            return { data: response.data, status: response.status };
        } catch (error) {
            // Handle Redirects (3xx) manually if needed, or just return empty/error
             if (error.response && error.response.status >= 300 && error.response.status < 400) {
                 console.log(`[PHP-BRIDGE] Redirect detected from ${url} -> ${error.response.headers.location}`);
                 return { data: "", status: error.response.status, location: error.response.headers.location };
             }
            console.error(`[PHP-BRIDGE] Error posting to ${url}:`, error.message);
            throw error;
        }
    },

    /**
     * Call PHP Script via GET
     */
    async get(url, data) {
         try {
            const headers = this.buildHeaders(data);
            const response = await axios.get(url, {
                headers: headers,
                params: data.vars || {},
                maxRedirects: 0,
                validateStatus: status => status < 500
            });
            return response.data;
        } catch (error) {
            if (error.response && error.response.status >= 300 && error.response.status < 400) {
                 console.log(`[PHP-BRIDGE] Redirect detected from ${url} -> ${error.response.headers.location}`);
                 // Return empty JSON or indication of redirect? 
                 // For get_data('json_ssid'), return default structure?
                 return { error: 'redirect', location: error.response.headers.location };
             }
            console.error(`[PHP-BRIDGE] Error getting ${url}:`, error.message);
            throw error;
        }       
    }
};
