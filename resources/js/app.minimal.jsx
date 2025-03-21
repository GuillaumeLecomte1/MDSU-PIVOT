import React from 'react';
import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import axios from 'axios';

console.log('Fallback minimal app loaded');

const appName = 'Marketplace Fallback';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        // Version simplifiée qui essaie de charger les pages avec le moins de dépendances possible
        const pages = import.meta.glob('./Pages/**/*.jsx');
        return pages[`./Pages/${name}.jsx`]();
    },
    setup({ el, App, props }) {
        // Configure Axios avec le CSRF token si disponible
        const pageProps = props.initialPage.props;
        if (pageProps.csrf_token) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = pageProps.csrf_token;
        }
        axios.defaults.withCredentials = true;

        const root = createRoot(el);
        root.render(<App {...props} />);
    },
    progress: {
        color: '#4B5563',
    },
}); 