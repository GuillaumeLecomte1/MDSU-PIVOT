// Version minimale sans imports complexes
(function() {
  console.log('Fallback app loaded successfully');
  
  // Fonction pour créer une page minimale
  function createMinimalPage(container, pageName) {
    // Créer un élément pour afficher que l'application fonctionne
    const appElement = document.createElement('div');
    appElement.style.fontFamily = 'Arial, sans-serif';
    appElement.style.padding = '20px';
    appElement.style.maxWidth = '800px';
    appElement.style.margin = '0 auto';
    
    // Ajouter un en-tête
    const header = document.createElement('header');
    header.style.marginBottom = '20px';
    header.style.padding = '10px 0';
    header.style.borderBottom = '1px solid #eaeaea';
    
    const title = document.createElement('h1');
    title.textContent = 'Marketplace';
    title.style.color = '#333';
    title.style.margin = '0';
    
    header.appendChild(title);
    appElement.appendChild(header);
    
    // Ajouter un contenu principal
    const main = document.createElement('main');
    
    const message = document.createElement('div');
    message.style.padding = '20px';
    message.style.backgroundColor = '#f8f9fa';
    message.style.borderRadius = '4px';
    message.style.marginBottom = '20px';
    
    const heading = document.createElement('h2');
    heading.textContent = 'Application en mode léger';
    heading.style.color = '#555';
    message.appendChild(heading);
    
    const description = document.createElement('p');
    description.textContent = 'L\'application fonctionne actuellement en mode léger. Contactez l\'administrateur pour plus d\'informations.';
    description.style.lineHeight = '1.5';
    message.appendChild(description);
    
    const pageInfo = document.createElement('p');
    pageInfo.textContent = `Page demandée: ${pageName || 'Accueil'}`;
    pageInfo.style.color = '#666';
    message.appendChild(pageInfo);
    
    main.appendChild(message);
    appElement.appendChild(main);
    
    // Ajouter un pied de page
    const footer = document.createElement('footer');
    footer.style.marginTop = '30px';
    footer.style.padding = '10px 0';
    footer.style.borderTop = '1px solid #eaeaea';
    footer.style.textAlign = 'center';
    footer.style.color = '#666';
    footer.textContent = '© ' + new Date().getFullYear() + ' Marketplace';
    
    appElement.appendChild(footer);
    
    // Ajouter au conteneur
    container.innerHTML = '';
    container.appendChild(appElement);
  }
  
  // Attendre que le DOM soit chargé
  document.addEventListener('DOMContentLoaded', function() {
    // Trouver l'élément racine pour l'application
    const appElement = document.getElementById('app');
    
    if (appElement) {
      // Récupérer la page demandée depuis l'URL
      const currentPath = window.location.pathname;
      const pageName = currentPath.split('/').filter(Boolean).pop() || 'Accueil';
      
      // Créer la page
      createMinimalPage(appElement, pageName);
    } else {
      console.error('Élément racine #app non trouvé');
    }
  });
})(); 