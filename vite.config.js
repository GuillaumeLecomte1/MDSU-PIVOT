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
        minify: 'esbuild',
        cssMinify: true,
        reportCompressedSize: false,
        chunkSizeWarningLimit: 1000,
        assetsInlineLimit: 4096,
        sourcemap: false,
        rollupOptions: {
            input: {
                app: 'resources/js/app.jsx',
                css: 'resources/css/app.css'
            },
            output: {
                manualChunks: (id) => {
                    if (id.includes('node_modules')) {
                        if (id.includes('@inertiajs') || id.includes('react')) {
                            return 'vendor';
                        }
                        if (id.includes('chart.js')) {
                            return 'charts';
                        }
                        if (id.includes('leaflet')) {
                            return 'leaflet';
                        }
                        return 'vendor-other';
                    }
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
