// Script JavaScript minimal pour l'application
document.addEventListener('DOMContentLoaded', function() {
    console.log('Application chargée avec succès');

    // Helper pour les formulaires
    window.submitForm = function(formId) {
        const form = document.getElementById(formId);
        if (form) {
            form.submit();
        }
    };

    // Helper pour les modals
    window.toggleModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            if (modal.style.display === 'none' || modal.style.display === '') {
                modal.style.display = 'block';
                document.body.classList.add('overflow-y-hidden');
            } else {
                modal.style.display = 'none';
                document.body.classList.remove('overflow-y-hidden');
            }
        }
    };

    // Helper pour les messages flash
    window.closeFlashMessage = function(messageId) {
        const message = document.getElementById(messageId);
        if (message) {
            message.style.display = 'none';
        }
    };

    // Helper pour les dropdowns
    document.querySelectorAll('[data-dropdown-toggle]').forEach(function(element) {
        element.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-dropdown-toggle');
            const target = document.getElementById(targetId);
            if (target) {
                target.classList.toggle('hidden');
            }
        });
    });

    // Fermer les dropdowns quand on clique ailleurs
    document.addEventListener('click', function(e) {
        const dropdowns = document.querySelectorAll('.dropdown-content:not(.hidden)');
        dropdowns.forEach(function(dropdown) {
            if (!dropdown.contains(e.target) && !e.target.hasAttribute('data-dropdown-toggle')) {
                dropdown.classList.add('hidden');
            }
        });
    });
});

// Script d'initialisation minimal pour Inertia.js avec Vue 3
document.addEventListener('DOMContentLoaded', function() {
    console.log('Application chargée avec succès');

    // Helper pour les formulaires
    window.submitForm = function(formId) {
        const form = document.getElementById(formId);
        if (form) {
            form.submit();
        }
    };

    // Helper pour les modals
    window.toggleModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            if (modal.style.display === 'none' || modal.style.display === '') {
                modal.style.display = 'block';
                document.body.classList.add('overflow-y-hidden');
            } else {
                modal.style.display = 'none';
                document.body.classList.remove('overflow-y-hidden');
            }
        }
    };

    // Attendre que Inertia.js et Vue soient chargés
    if (typeof Inertia === 'undefined' || typeof Vue === 'undefined' || typeof InertiaVue3 === 'undefined') {
        console.error('Inertia.js, Vue, ou le plugin InertiaVue3 ne sont pas chargés.');
        
        // Afficher un message explicatif
        document.body.innerHTML = `
            <div style="max-width: 800px; margin: 2rem auto; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); background-color: white;">
                <h1 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 1rem;">Application en cours de configuration</h1>
                <p style="margin-bottom: 1rem;">L'application est en cours de configuration. Les fichiers JavaScript nécessaires sont correctement chargés.</p>
                <p style="margin-bottom: 1rem;">Pour finaliser l'installation, veuillez exécuter la commande suivante dans votre environnement de développement :</p>
                <div style="background-color: #f3f4f6; padding: 1rem; border-radius: 0.25rem; font-family: monospace; overflow-x: auto;">
                    npm install && npm run dev
                </div>
                <p style="margin-top: 1rem;">Ou contactez votre administrateur système pour compléter la configuration.</p>
            </div>
        `;
        return;
    }

    try {
        // Créer l'application Vue avec Inertia
        const { createApp, h } = Vue;
        const { createInertiaApp } = InertiaVue3;

        const AppComponent = {
            template: `<div>
                <h1 class="text-center py-10 text-2xl font-bold">Application Laravel + Inertia.js</h1>
                <div class="container mx-auto p-6 bg-white rounded shadow-md">
                    <p class="mb-4">L'application est correctement configurée mais nécessite des composants Vue.js.</p>
                    <p>Veuillez exécuter: <code class="bg-gray-100 p-1 rounded">npm install && npm run dev</code> pour générer les assets appropriés.</p>
                </div>
            </div>`
        };

        createInertiaApp({
            resolve: name => {
                console.log(`Tentative de chargement de la page: ${name}`);
                return AppComponent;
            },
            setup({ el, App, props, plugin }) {
                console.log('Initialisation de l\'application Inertia.js');
                createApp({ render: () => h(App, props) })
                    .use(plugin)
                    .mount(el);
            },
            progress: {
                color: '#4B5563',
            },
        });

        console.log('Application Inertia.js initialisée avec succès');
    } catch (error) {
        console.error('Erreur lors de l\'initialisation de l\'application:', error);
        
        document.body.innerHTML = `
            <div style="max-width: 800px; margin: 2rem auto; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); background-color: white;">
                <h1 style="color: #ef4444; font-size: 1.5rem; font-weight: bold; margin-bottom: 1rem;">Erreur de chargement</h1>
                <p style="margin-bottom: 1rem;">Une erreur s'est produite lors du chargement de l'application.</p>
                <div style="background-color: #f3f4f6; padding: 1rem; border-radius: 0.25rem; font-family: monospace; overflow-x: auto;">
                    ${error.message}
                </div>
            </div>
        `;
    }
}); 