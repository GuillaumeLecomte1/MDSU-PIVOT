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
                    vendor: ['lodash', 'axios'],
                    markdown: ['react-markdown', 'react-syntax-highlighter', 'remark-gfm'],
                    react: ['react', 'react-dom']
                }
            }
        },
        chunkSizeWarningLimit: 1000,
        commonjsOptions: {
            include: [/node_modules/],
            transformMixedEsModules: true
        }
    },
    optimizeDeps: {
        force: true,
        include: [
            'lodash',
            'axios',
            'react-markdown',
            'react-syntax-highlighter',
            'remark-gfm',
            'react',
            'react-dom'
        ],
        exclude: ['@inertiajs/react']
    },
    esbuild: {
        jsxInject: `import React from 'react'`,
        jsxDev: false
    }
});
