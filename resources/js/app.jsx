import './bootstrap';
import '../css/app.css';

import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import axios from 'axios';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Pivot';

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

// Détecter si on est en production ou en développement
const isProduction = import.meta.env.PROD;

// Adapter les chemins d'assets si nécessaire
if (isProduction) {
    // Récupérer le tag link et ajuster le chemin si nécessaire
    document.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
        const href = link.getAttribute('href');
        if (href && (href.includes('/build/') || href.includes('/assets/'))) {
            const fixedHref = href.replace('/build/assets/', '/assets/');
            if (fixedHref !== href) {
                link.setAttribute('href', fixedHref);
            }
        }
    });
    
    // Récupérer les tags script et ajuster le chemin si nécessaire
    document.querySelectorAll('script[src]').forEach(script => {
        const src = script.getAttribute('src');
        if (src && (src.includes('/build/') || src.includes('/assets/'))) {
            const fixedSrc = src.replace('/build/assets/', '/assets/');
            if (fixedSrc !== src) {
                script.setAttribute('src', fixedSrc);
            }
        }
    });
}

// Afficher des informations de débogage
console.log('Inertia Setup - Auth:', window.auth || {});
console.log('Inertia Setup - User:', window.auth?.user || null);
console.log('Inertia Setup - Permissions:', window.auth?.permissions || {});

// Fonction pour résoudre les problèmes d'assets en production
function fixAssetUrl(url) {
    // Vérifier si l'URL contient déjà /build/ ou /assets/
    if (url && typeof url === 'string' && !url.startsWith('/build/') && !url.startsWith('/assets/')) {
        // En production, ajouter /build/ devant les chemins d'assets
        if (import.meta.env.PROD) {
            return `/build/${url.startsWith('/') ? url.substring(1) : url}`;
        }
    }
    return url;
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.jsx`, import.meta.glob('./Pages/**/*.jsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);
        
        // Ajouter une fonction d'assistance pour les assets aux props globales
        props.fixAssetUrl = fixAssetUrl;
        
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

        root.render(<App {...props} />);
    },
    progress: {
        color: '#4B5563',
    },
});
