import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY || 'akusukangoding123',
    wsHost: 'ryaze.my.id',
    wsPort: 443,
    wssPort: 443,
    forceTLS: true,
    enabledTransports: ['ws', 'wss'],
});
