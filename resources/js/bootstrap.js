/**
 * Ce fichier initialise l'environnement JavaScript pour l'application.
 * Il configure les bibliothèques telles qu'Axios et les dépendances globales.
 */

// Importer lodash avec import ES6 standard au lieu de require
import _ from 'lodash';
window._ = _;

/**
 * Configuration d'Axios pour les requêtes HTTP
 */
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Permet à Axios d'utiliser les cookies dans les requêtes CSRF
 */
window.axios.defaults.withCredentials = true;

/**
 * Configuration d'Echo (décommentez si vous utilisez Laravel Echo)
 */
// import Echo from 'laravel-echo';
// import Pusher from 'pusher-js';
// window.Pusher = Pusher;
// 
// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
