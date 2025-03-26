import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.jsx'],
            refresh: true,
        }),
        react(),
    ],
    resolve: {
        alias: {
            '@': '/resources'
        }
    },
    server: {
        hmr: { overlay: true },
        host: true
    },
    build: {
        timeout: 120000,
        outDir: 'public/build',
        assetsDir: '',
        manifest: true,
        minify: false,
        sourcemap: false,
        terserOptions: undefined,
        rollupOptions: {
            external: [],
            output: {
                manualChunks: {
                    vendor: ['lodash', 'axios']
                }
            }
        },
        chunkSizeWarningLimit: 1000,
    },
    optimizeDeps: {
        force: true,
        include: ['lodash', 'axios']
    },
});
