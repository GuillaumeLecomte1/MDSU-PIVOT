<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @php
        // Fonction pour gérer correctement les assets en production et développement
        function asset_path($path) {
            if (app()->environment('production')) {
                // Environnement de production - utiliser des fallbacks si nécessaire
                $manifestPath = public_path('build/manifest.json');
                $buildDir = 'build/';
                
                if (!file_exists($manifestPath)) {
                    $manifestPath = public_path('assets/manifest.json');
                    $buildDir = 'assets/';
                }
                
                if (file_exists($manifestPath)) {
                    $manifest = json_decode(file_get_contents($manifestPath), true);
                    $key = ltrim($path, '/');
                    
                    if (isset($manifest[$key]['file'])) {
                        return '/' . $buildDir . $manifest[$key]['file'];
                    }
                }
                
                // Fallback pour les assets CSS/JS sans manifest
                if (strpos($path, '.css') !== false) {
                    return '/build/assets/css/app.css';
                } else if (strpos($path, '.js') !== false) {
                    return '/build/assets/js/app.js';
                }
                
                return $path;
            } else {
                // Environnement de développement - utiliser Vite
                return Vite::asset($path);
            }
        }
        @endphp

        @routes

        @if(app()->environment('local'))
            @viteReactRefresh
            @vite(['resources/css/app.css', 'resources/js/app.jsx'])
        @else
            <link rel="stylesheet" href="{{ asset_path('resources/css/app.css') }}">
            <script src="{{ asset_path('resources/js/app.jsx') }}" defer></script>
        @endif

        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia

        {{-- Fallback si le chargement de l'application Inertia échoue --}}
        <div id="app-fallback" style="display: none; padding: 2rem; text-align: center;">
            <h1 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 1rem;">Application PIVOT</h1>
            <p style="margin-bottom: 1rem;">L'application Inertia.js n'a pas pu se charger correctement.</p>
            <a href="/login" style="display: inline-block; padding: 0.5rem 1rem; background-color: #4F46E5; color: white; border-radius: 0.25rem; text-decoration: none;">
                Accéder à la page de connexion
            </a>
        </div>

        <script>
            // Afficher le fallback si l'application Inertia ne se charge pas après 5 secondes
            setTimeout(function() {
                if (!window.app) {
                    document.getElementById('app-fallback').style.display = 'block';
                }
            }, 5000);
        </script>
    </body>
</html> 