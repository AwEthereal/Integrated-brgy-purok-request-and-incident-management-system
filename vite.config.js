import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { readFileSync } from 'fs';
import { resolve } from 'path';

// Load environment variables from .env
const env = {};
try {
    const envPath = resolve(process.cwd(), '.env');
    const envFile = readFileSync(envPath, 'utf-8');
    envFile.split('\n').forEach(line => {
        const match = line.match(/^([^=]+)=(.*)$/);
        if (match) {
            const [, key, value] = match;
            env[key] = value.replace(/['"]/g, '');
        }
    });
} catch (e) {
    console.warn('No .env file found or error reading .env file');
}

// Define Reverb environment variables
const reverbEnvVars = {
    VITE_REVERB_APP_ID: env.REVERB_APP_ID || 'kalawag_brgy_system',
    VITE_REVERB_APP_KEY: env.REVERB_APP_KEY || 'kalawag_brgy_key',
    VITE_REVERB_APP_SECRET: env.REVERB_APP_SECRET || 'kalawag_brgy_secret',
    VITE_REVERB_HOST: env.REVERB_HOST || '127.0.0.1',
    VITE_REVERB_PORT: env.REVERB_PORT || '8080',
    VITE_REVERB_SCHEME: env.REVERB_SCHEME || 'http',
};

console.log('Vite Config - Environment Variables:', reverbEnvVars);

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/incident-report.js',
                'resources/js/purok-notifications.js'
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    define: {
        'process.env': {
            ...reverbEnvVars,
            VITE_REVERB_APP_KEY: JSON.stringify(process.env.VITE_REVERB_APP_KEY || 'kalawag_brgy_key'),
            VITE_REVERB_HOST: JSON.stringify(process.env.VITE_REVERB_HOST || '127.0.0.1'),
            VITE_REVERB_PORT: JSON.stringify(process.env.VITE_REVERB_PORT || '8080'),
            VITE_REVERB_SCHEME: JSON.stringify(process.env.VITE_REVERB_SCHEME || 'http'),
        },
    },
    server: {
        hmr: {
            host: 'localhost',
            protocol: 'ws',
        },
    },
    build: {
        rollupOptions: {
            external: ['pusher-js'],
        },
    },
});
