import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.jsx', 'resources/css/app.css'],
            refresh: true,
        }),
        react(),
    ],
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: {
                app: 'resources/js/app.jsx',
                css: 'resources/css/app.css'
            },
            output: {
                manualChunks: {
                    vendor: [
                        '@inertiajs/react',
                        'react',
                        'react-dom',
                    ],
                    charts: ['chart.js'],
                    leaflet: ['leaflet'],
                },
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name.match(/\.(png|jpe?g|gif|svg|webp)$/)) {
                        return 'assets/images/[name]-[hash][extname]';
                    }
                    if (assetInfo.name.match(/\.css$/)) {
                        return 'assets/css/[name]-[hash][extname]';
                    }
                    return 'assets/[name]-[hash][extname]';
                },
                chunkFileNames: 'assets/js/[name]-[hash].js',
                entryFileNames: 'assets/js/[name]-[hash].js',
            },
        },
    },
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    server: {
        hmr: {
            host: '127.0.0.1',
        },
        host: '127.0.0.1',
        port: 5173,
    },
    optimizeDeps: {
        include: ['@inertiajs/react'],
    },
});
