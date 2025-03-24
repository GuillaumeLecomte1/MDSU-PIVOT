import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';
import fs from 'fs';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.jsx',
            ],
            refresh: true,
            publicDirectory: 'public',
        }),
        react(),
    ],
    resolve: {
        alias: {
            '/imagesAccueil': '/public/imagesAccueil',
            '/public': '/public',
        }
    },
    build: {
        manifest: true,
        outDir: 'public/build',
        assetsDir: 'assets',
        rollupOptions: {
            output: {
                entryFileNames: `assets/js/[name].js`,
                chunkFileNames: `assets/js/[name].js`,
                assetFileNames: `assets/[ext]/[name].[ext]`,
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        return 'vendor';
                    }
                }
            },
        },
    },
    server: {
        hmr: {
            host: 'localhost',
        },
    },
    base: process.env.APP_ENV === 'production' ? '/build/' : '',
});
