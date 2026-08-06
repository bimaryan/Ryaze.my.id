import Pusher from 'pusher-js';
import { Buffer } from 'node:buffer';
import dotenv from 'dotenv';
import fs from 'fs';

// Load .env from current dir if exists
if (fs.existsSync('.env')) {
    dotenv.config();
}

// Configuration
const REVERB_HOST = process.env.VITE_REVERB_HOST || '127.0.0.1';
const REVERB_PORT = process.env.VITE_REVERB_PORT || 8080;
const REVERB_SCHEME = process.env.VITE_REVERB_SCHEME || 'http';
const PUSHER_APP_KEY = process.env.VITE_REVERB_APP_KEY || 'local_key';
const SERVER_URL = process.env.APP_URL || 'http://localhost';

const SUBDOMAIN = process.argv[2];
const LOCAL_TARGET = process.argv[3] || 'http://localhost:8000';

if (!SUBDOMAIN) {
    console.error("Usage: node tunnel-client.js <subdomain> [local_target_url]");
    console.error("Example: node tunnel-client.js mysite http://localhost:8000");
    process.exit(1);
}

console.log(`Starting tunnel for subdomain: ${SUBDOMAIN}`);
console.log(`Forwarding to: ${LOCAL_TARGET}`);
console.log(`Connecting to Reverb at ${REVERB_SCHEME}://${REVERB_HOST}:${REVERB_PORT}...`);

const pusher = new Pusher(PUSHER_APP_KEY, {
    wsHost: REVERB_HOST,
    wsPort: REVERB_PORT,
    wssPort: REVERB_PORT,
    forceTLS: REVERB_SCHEME === 'https',
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
});

pusher.connection.bind('connected', () => {
    console.log('Connected to Reverb WebSocket server!');
    console.log(`Your tunnel is live at: ${SERVER_URL}/t/${SUBDOMAIN}/`);
});

pusher.connection.bind('error', (err) => {
    console.error('WebSocket Error:', err);
});

const channel = pusher.subscribe(`tunnel.${SUBDOMAIN}`);

channel.bind('App\\Events\\TunnelRequestReceived', async (data) => {
    console.log(`\n[->] Request Received: ${data.method} ${data.url}`);
    
    try {
        const localUrl = new URL(data.url, LOCAL_TARGET).href;
        
        const fetchOptions = {
            method: data.method,
            headers: data.headers,
        };

        if (['POST', 'PUT', 'PATCH'].includes(data.method) && data.body) {
            fetchOptions.body = Buffer.from(data.body, 'base64');
        }

        // Fetch from local server
        const startTime = Date.now();
        const response = await fetch(localUrl, fetchOptions);
        
        const responseHeaders = {};
        response.headers.forEach((value, key) => {
            responseHeaders[key] = value;
        });

        // Get response body as buffer and encode to base64
        const arrayBuffer = await response.arrayBuffer();
        const responseBodyBase64 = Buffer.from(arrayBuffer).toString('base64');
        const duration = Date.now() - startTime;

        console.log(`[<-] Response: ${response.status} (${duration}ms)`);

        // Send response back to main server
        const submitUrl = `${SERVER_URL}/api/tunnel/response`;
        const submitResponse = await fetch(submitUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                request_id: data.requestId,
                status: response.status,
                headers: responseHeaders,
                body: responseBodyBase64,
            }),
        });

        if (!submitResponse.ok) {
            console.error('Failed to submit response to server:', await submitResponse.text());
        }
    } catch (err) {
        console.error('Error proxying request:', err.message);
        
        // Submit error back to server
        const submitUrl = `${SERVER_URL}/api/tunnel/response`;
        await fetch(submitUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                request_id: data.requestId,
                status: 502,
                headers: { 'Content-Type': 'application/json' },
                body: Buffer.from(JSON.stringify({ error: 'Bad Gateway: ' + err.message })).toString('base64'),
            }),
        }).catch(e => console.error("Could not send error back:", e.message));
    }
});
