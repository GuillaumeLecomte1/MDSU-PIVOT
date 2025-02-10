import './bootstrap';
import '../css/app.css';

import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import axios from 'axios';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob('./Pages/**/*.jsx'),
        ),
    setup({ el, App, props }) {
        // Configure Axios avec le CSRF token
        if (props.csrf_token) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = props.csrf_token;
            // Met à jour le meta tag CSRF
            document.querySelector('meta[name="csrf-token"]')?.setAttribute('content', props.csrf_token);
        }
        axios.defaults.withCredentials = true;

        const root = createRoot(el);
        root.render(<App {...props} />);
    },
    progress: {
        color: '#4B5563',
    },
});
