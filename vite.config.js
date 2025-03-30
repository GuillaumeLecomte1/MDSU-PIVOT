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
            publicDirectory: 'public',
        }),
        react(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
            '~': '/resources',
            '@assets': '/resources/assets',
        }
    },
    build: {
        chunkSizeWarningLimit: 1000,
        minify: false,
        sourcemap: false,
        assetsDir: 'assets',
        rollupOptions: {
            output: {
                manualChunks: (id) => {
                    if (id.includes('node_modules')) {
                        const modules = ['react', 'react-dom', '@headlessui', '@heroicons', 'axios'];
                        const chunk = modules.find(m => id.includes(m));
                        return chunk ? `vendor-${chunk}` : 'vendor';
                    }
                },
                assetFileNames: (assetInfo) => {
                    let extType = assetInfo.name.split('.').at(1);
                    if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(extType)) {
                        extType = 'img';
                    }
                    return `assets/[name]-[hash][extname]`;
                },
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
