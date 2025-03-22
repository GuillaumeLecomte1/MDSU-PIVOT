<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts and Styles -->
        @routes
        <link href="/build/app.css" rel="stylesheet">
        <script src="/build/app.js" defer></script>
        
        <!-- Essayer de charger les scripts Inertia.js à partir du CDN si disponible -->
        <script src="https://unpkg.com/@inertiajs/inertia@0.11.1/dist/index.global.js" defer></script>
        <script src="https://unpkg.com/@inertiajs/inertia-vue3@0.6.0/dist/index.global.js" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/vue@3.2.47/dist/vue.global.prod.js" defer></script>

        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia

        <div id="app-loading" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255,255,255,0.9); z-index: 9999; display: flex; justify-content: center; align-items: center;">
            <div style="text-align: center; padding: 1rem; background-color: white; border-radius: 0.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 1.2rem; font-weight: bold; margin-bottom: 0.5rem;">Chargement...</div>
                <div>Veuillez patienter pendant le chargement de l'application.</div>
            </div>
        </div>
        
        <script>
            // Supprime l'indicateur de chargement après 3 secondes
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    var loadingElement = document.getElementById('app-loading');
                    if (loadingElement) {
                        loadingElement.style.display = 'none';
                    }
                }, 3000);
            });
        </script>
    </body>
</html> 