import './bootstrap';
import '../css/app.css';

import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import axios from 'axios';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Forcer HTTPS pour toutes les requêtes
if (window.location.protocol === 'https:') {
    // Intercepter toutes les requêtes fetch pour forcer HTTPS
    const originalFetch = window.fetch;
    window.fetch = function(url, options) {
        if (typeof url === 'string' && url.startsWith('http:')) {
            url = url.replace('http:', 'https:');
        }
        return originalFetch(url, options);
    };
}

// Afficher des informations de débogage
console.log('Inertia Setup - Auth:', window.auth || {});
console.log('Inertia Setup - User:', window.auth?.user || null);
console.log('Inertia Setup - Permissions:', window.auth?.permissions || {});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob('./Pages/**/*.jsx'),
        ),
    setup({ el, App, props }) {
        // Debug logs
        const pageProps = props.initialPage.props;
        console.log('Inertia Setup - Auth:', pageProps.auth);
        console.log('Inertia Setup - User:', pageProps.auth?.user);
        console.log('Inertia Setup - Permissions:', pageProps.auth?.permissions);
        
        // Configure Axios avec le CSRF token
        if (pageProps.csrf_token) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = pageProps.csrf_token;
            document.querySelector('meta[name="csrf-token"]')?.setAttribute('content', pageProps.csrf_token);
        }
        axios.defaults.withCredentials = true;

        const root = createRoot(el);
        root.render(<App {...props} />);
    },
    progress: {
        color: '#4B5563',
    },
});
