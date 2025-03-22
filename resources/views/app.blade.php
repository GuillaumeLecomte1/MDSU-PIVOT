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
        
        <!-- CDNs pour React et Inertia -->
        <script src="https://unpkg.com/react@18/umd/react.production.min.js" crossorigin></script>
        <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js" crossorigin></script>
        <script src="https://unpkg.com/@inertiajs/inertia@0.11.1/dist/index.global.js"></script>
        <script src="https://unpkg.com/@inertiajs/inertia-react@0.8.1/dist/index.global.js"></script>
        <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
        
        <!-- Application script -->
        <script src="/build/app.js" defer></script>

        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia

        <div id="app-loading" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255,255,255,0.9); z-index: 9999; display: flex; justify-content: center; align-items: center;">
            <div style="text-align: center; padding: 1rem; background-color: white; border-radius: 0.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 1.2rem; font-weight: bold; margin-bottom: 0.5rem;">Chargement...</div>
                <div>Veuillez patienter pendant le chargement de l'application.</div>
            </div>
        </div>
        
        <!-- Minimal React component if Inertia fails -->
        <div id="fallback-app" style="display: none; max-width: 800px; margin: 2rem auto; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); background-color: white;"></div>
        
        <script type="text/babel">
            // Supprime l'indicateur de chargement après 3 secondes
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    var loadingElement = document.getElementById('app-loading');
                    if (loadingElement) {
                        loadingElement.style.display = 'none';
                    }
                    
                    // Si Inertia n'a pas encore rendu l'application, afficher un composant de secours
                    if (!document.querySelector('[data-page]')) {
                        const fallbackElement = document.getElementById('fallback-app');
                        if (fallbackElement) {
                            fallbackElement.style.display = 'block';
                            // Rendu React basique
                            const FallbackComponent = () => {
                                return (
                                    <div>
                                        <h1 style={{fontSize: '1.5rem', fontWeight: 'bold', marginBottom: '1rem'}}>Application PIVOT</h1>
                                        <p style={{marginBottom: '1rem'}}>L'application Inertia.js n'a pas pu se charger correctement.</p>
                                        <a href="/login" style={{display: 'inline-block', padding: '0.5rem 1rem', backgroundColor: '#4F46E5', color: 'white', borderRadius: '0.25rem', textDecoration: 'none'}}>
                                            Accéder à la page de connexion
                                        </a>
                                    </div>
                                );
                            };
                            
                            ReactDOM.render(<FallbackComponent />, fallbackElement);
                        }
                    }
                }, 3000);
            });
        </script>
    </body>
</html> 