import _ from 'lodash';
import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window._ = _;

// Configurer axios pour utiliser HTTPS
const appUrl = window.location.origin;
window.axios = axios;
window.axios.defaults.baseURL = appUrl;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

// Forcer HTTPS pour toutes les requêtes
if (window.location.protocol === 'https:') {
    window.axios.interceptors.request.use(config => {
        // Convertir les URLs HTTP en HTTPS
        if (config.url && config.url.startsWith('http:')) {
            config.url = config.url.replace('http:', 'https:');
        }
        return config;
    });
}

// Initialiser Pusher seulement si la clé est disponible et valide
const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER;

// Vérifier si la clé Pusher est valide (ni null, ni undefined, ni 'null', ni vide, ni valeur par défaut)
const isPusherKeyValid = pusherKey && 
                         pusherKey !== 'null' && 
                         pusherKey !== 'your_app_key' && 
                         pusherKey !== '${PUSHER_APP_KEY}' &&
                         pusherKey.trim() !== '';

if (isPusherKeyValid) {
    window.Pusher = Pusher;
    
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: pusherCluster || 'eu',
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

// Ajouter un gestionnaire d'erreurs global pour axios
window.axios.interceptors.response.use(
    response => response,
    error => {
        console.error('Axios error:', error);
        return Promise.reject(error);
    }
);
