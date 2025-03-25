import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.jsx',
            ],
            refresh: true,
        }),
        react(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
            '/imagesAccueil': '/public/imagesAccueil',
        }
    },
    build: {
        chunkSizeWarningLimit: 1000,
        minify: false,
        sourcemap: false,
        rollupOptions: {
            output: {
                manualChunks: (id) => {
                    if (id.includes('node_modules')) {
                        const modules = ['react', 'react-dom', '@headlessui', '@heroicons', 'axios'];
                        const chunk = modules.find(m => id.includes(m));
                        return chunk ? `vendor-${chunk}` : 'vendor';
                    }
                }
            }
        },
    },
    server: {
        hmr: {
            host: 'localhost',
        },
    },
    define: {
        'process.env.NODE_OPTIONS': '"--max-old-space-size=4096"'
    },
    base: process.env.APP_ENV === 'production' ? '/build/' : '',
});
