import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';
import fs from 'fs';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.jsx', 'resources/css/app.css'],
            refresh: true,
            buildDirectory: '',
            https: true,
        }),
        react(),
        {
            name: 'copy-manifest',
            closeBundle() {
                const viteManifestPath = path.resolve(__dirname, 'public/build/.vite/manifest.json');
                const targetManifestPath = path.resolve(__dirname, 'public/build/manifest.json');
                
                if (fs.existsSync(viteManifestPath)) {
                    console.log('Copie du manifeste Vite vers le répertoire racine de build...');
                    fs.copyFileSync(viteManifestPath, targetManifestPath);
                    console.log('Manifeste copié avec succès!');
                } else {
                    console.log('Manifeste Vite non trouvé à:', viteManifestPath);
                }
            }
        }
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
