import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.jsx',
            refresh: true,
        }),
        react(),
    ],
    build: {
        outDir: 'public/build',
        assetsDir: '',
        manifest: true,
        rollupOptions: {
            output: {
                manualChunks: undefined,
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name.match(/\.(png|jpe?g|gif|svg|webp)$/)) {
                        return 'assets/images/[name]-[hash][extname]';
                    }
                    return 'assets/[name]-[hash][extname]';
                },
            },
        },
    },
    resolve: {
        alias: {
            '@': '/resources/js',
            'public': '/public',
        },
    },
    publicDir: 'public',
    server: {
        hmr: {
            host: '127.0.0.1',
        },
        host: '127.0.0.1',
        port: 5173,
    },
    build: {
        chunkSizeWarningLimit: 1600,
        assetsInlineLimit: 4096,
    },
});
