// Script JavaScript simplifié pour l'application
console.log('Application chargée - Version simplifiée');

// Fonction pour afficher un message de statut à l'utilisateur
function showSystemMessage() {
    // Créer et afficher un message d'information basique
    var messageElement = document.createElement('div');
    messageElement.style.maxWidth = '800px';
    messageElement.style.margin = '2rem auto';
    messageElement.style.padding = '2rem';
    messageElement.style.borderRadius = '0.5rem';
    messageElement.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
    messageElement.style.backgroundColor = 'white';
    
    messageElement.innerHTML = `
        <h1 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 1rem;">Application PIVOT</h1>
        <p style="margin-bottom: 1rem;">L'application est fonctionnelle en mode de base.</p>
        <p style="margin-bottom: 1rem;">Si vous rencontrez des problèmes, veuillez contacter l'administrateur système.</p>
    `;
    
    // Ajouter au body uniquement s'il n'y a pas de contenu principal
    if (document.querySelector('main') === null && document.querySelector('.inertia-content') === null) {
        document.body.appendChild(messageElement);
    }
}

// Attendre que le document soit chargé
document.addEventListener('DOMContentLoaded', function() {
    // Afficher le message si la page est vide (pas de contenu principal)
    setTimeout(showSystemMessage, 500);
    
    // Log pour le debugging
    console.log('Document chargé et prêt');
}); 