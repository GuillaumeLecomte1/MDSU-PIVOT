import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';
import fs from 'fs';
import path from 'path';

export default defineConfig(({ command, mode }) => {
    const isProduction = mode === 'production';
    
    return {
        base: isProduction ? '/assets/' : '/',
        plugins: [
            laravel({
                input: [
                    'resources/js/app.jsx', 
                    'resources/js/app.minimal.jsx', 
                    'resources/css/app.css'
                ],
                refresh: true,
                buildDirectory: 'assets',
                https: true,
            }),
            react(),
            {
                name: 'copy-manifest',
                closeBundle() {
                    try {
                        const viteManifestPath = path.resolve(__dirname, 'public/assets/.vite/manifest.json');
                        const targetManifestPath = path.resolve(__dirname, 'public/assets/manifest.json');
                        
                        if (fs.existsSync(viteManifestPath)) {
                            console.log('Copie du manifeste Vite vers le répertoire racine de assets...');
                            fs.copyFileSync(viteManifestPath, targetManifestPath);
                            console.log('Manifeste copié avec succès!');
                            
                            // Créer un lien symbolique de build vers assets
                            const buildDir = path.resolve(__dirname, 'public/build');
                            const assetsDir = path.resolve(__dirname, 'public/assets');
                            
                            if (!fs.existsSync(buildDir)) {
                                try {
                                    if (process.platform === 'win32') {
                                        // Pour Windows, on crée un junction pour simuler un lien symbolique
                                        require('child_process').execSync(`mklink /J "${buildDir}" "${assetsDir}"`);
                                    } else {
                                        // Pour Unix/Linux
                                        fs.symlinkSync(assetsDir, buildDir, 'dir');
                                    }
                                    console.log('Lien symbolique créé de build vers assets');
                                } catch (linkError) {
                                    console.error('Erreur lors de la création du lien symbolique:', linkError);
                                }
                            }
                        } else {
                            console.log('Manifeste Vite non trouvé à:', viteManifestPath);
                            // Création d'un manifeste minimal si nécessaire
                            const minimalManifest = {
                                "resources/js/app.jsx": {
                                    "file": "js/app.js",
                                    "isEntry": true,
                                    "src": "resources/js/app.jsx"
                                },
                                "resources/css/app.css": {
                                    "file": "css/app.css",
                                    "isEntry": true,
                                    "src": "resources/css/app.css"
                                }
                            };
                            
                            // Assurez-vous que le répertoire existe
                            if (!fs.existsSync(path.dirname(targetManifestPath))) {
                                fs.mkdirSync(path.dirname(targetManifestPath), { recursive: true });
                            }
                            
                            fs.writeFileSync(targetManifestPath, JSON.stringify(minimalManifest, null, 2));
                            console.log('Manifeste minimal créé:', targetManifestPath);
                        }
                    } catch (error) {
                        console.error('Erreur lors de la copie du manifeste:', error);
                    }
                }
            }
        ],
        build: {
            outDir: 'public/assets',
            emptyOutDir: true,
            manifest: true,
            minify: isProduction ? 'esbuild' : false,
            cssMinify: isProduction,
            reportCompressedSize: false,
            chunkSizeWarningLimit: 2000,
            assetsInlineLimit: 4096,
            sourcemap: isProduction ? false : true,
            rollupOptions: {
                input: {
                    app: 'resources/js/app.jsx',
                    css: 'resources/css/app.css'
                },
                output: {
                    manualChunks: isProduction ? {
                        vendor: [
                            'react', 
                            'react-dom', 
                            '@inertiajs/react'
                        ],
                    } : undefined,
                    assetFileNames: 'css/[name]-[hash][extname]',
                    chunkFileNames: 'js/[name]-[hash].js',
                    entryFileNames: 'js/[name]-[hash].js',
                },
            },
            terserOptions: {
                compress: {
                    drop_console: isProduction,
                    drop_debugger: isProduction
                }
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
    };
});
