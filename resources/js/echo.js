import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

const pusherKey = document.querySelector('meta[name="pusher-key"]')?.getAttribute('content')
    || import.meta.env.VITE_PUSHER_APP_KEY
    || 'e777bf9f25b68e56f962';

const pusherCluster = document.querySelector('meta[name="pusher-cluster"]')?.getAttribute('content')
    || import.meta.env.VITE_PUSHER_APP_CLUSTER
    || 'mt1';

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: pusherKey,
    cluster: pusherCluster,
    forceTLS: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
    },
});

