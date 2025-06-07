import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

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
});
