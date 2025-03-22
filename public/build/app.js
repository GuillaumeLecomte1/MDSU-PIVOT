// Script JavaScript pour l'application Laravel avec Inertia.js et React
console.log('Application chargée - Pivot');

// Attendre que le document soit chargé
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si les dépendances sont chargées
    if (typeof Inertia !== 'undefined' && typeof React !== 'undefined' && typeof ReactDOM !== 'undefined' && typeof InertiaReact !== 'undefined') {
        console.log('Toutes les dépendances sont chargées, tentative d\'initialisation d\'Inertia');
        try {
            // Essayer d'initialiser Inertia avec React
            const app = document.getElementById('app');
            if (app) {
                // Configuration de base pour Inertia
                Inertia.on('navigate', (event) => {
                    console.log('Navigation Inertia vers:', event.detail.page.url);
                });
            }
        } catch (error) {
            console.error('Erreur lors de l\'initialisation d\'Inertia:', error);
        }
    } else {
        console.warn('Dépendances manquantes pour Inertia.js:', {
            'Inertia': typeof Inertia !== 'undefined',
            'React': typeof React !== 'undefined',
            'ReactDOM': typeof ReactDOM !== 'undefined',
            'InertiaReact': typeof InertiaReact !== 'undefined'
        });
    }
    
    console.log('Document chargé et prêt');
}); 