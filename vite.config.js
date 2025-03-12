import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.jsx', 'resources/css/app.css'],
            refresh: true,
            buildDirectory: 'build',
        }),
        react(),
    ],
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: true,
        minify: 'esbuild',
        cssMinify: true,
        reportCompressedSize: false,
        chunkSizeWarningLimit: 2000,
        assetsInlineLimit: 4096,
        sourcemap: false,
        rollupOptions: {
            input: {
                app: 'resources/js/app.jsx',
                css: 'resources/css/app.css'
            },
            output: {
                manualChunks: {
                    vendor: [
                        'react', 
                        'react-dom', 
                        '@inertiajs/react'
                    ],
                },
                assetFileNames: 'assets/[name]-[hash][extname]',
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
