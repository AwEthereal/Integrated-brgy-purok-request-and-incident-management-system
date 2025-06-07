import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Initialize Pusher
window.Pusher = Pusher;

// Initialize Echo with WebSockets
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY || 'local',
    wsHost: import.meta.env.VITE_PUSHER_HOST || window.location.hostname,
    wsPort: import.meta.env.VITE_PUSHER_PORT || 6001,
    forceTLS: false,
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
    encrypted: false
});

// Log connection status
window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('Connected to WebSockets server');
});

window.Echo.connector.pusher.connection.bind('disconnected', () => {
    console.log('Disconnected from WebSockets server');
});

window.Echo.connector.pusher.connection.bind('error', (error) => {
    console.error('WebSocket error:', error);
});
