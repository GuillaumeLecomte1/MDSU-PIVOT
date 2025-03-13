import _ from 'lodash';
import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window._ = _;
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Initialiser Pusher seulement si la clé est disponible
const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER;

if (pusherKey && pusherKey !== 'your_app_key' && pusherKey !== '${PUSHER_APP_KEY}') {
    window.Pusher = Pusher;
    
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: pusherCluster,
        forceTLS: true
    });
    
    console.log('Pusher initialized with key:', pusherKey);
} else {
    // Créer un Echo factice pour éviter les erreurs
    window.Echo = {
        channel: () => ({
            listen: () => {}
        }),
        private: () => ({
            listen: () => {}
        }),
        join: () => ({
            listen: () => {},
            here: () => {},
            joining: () => {},
            leaving: () => {}
        })
    };
    
    console.log('Pusher not initialized: Missing or invalid app key');
}
