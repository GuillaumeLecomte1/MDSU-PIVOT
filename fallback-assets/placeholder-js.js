/* Placeholder JavaScript file when Vite build fails */
document.addEventListener('DOMContentLoaded', function() {
  console.log('Application running in fallback mode due to Vite build failure');
  
  // Add a warning banner to notify users
  const body = document.body;
  const banner = document.createElement('div');
  banner.className = 'build-error container';
  banner.innerHTML = `
    <h1>Application en mode secours</h1>
    <p>L'application fonctionne actuellement en mode dégradé suite à un problème technique. Certaines fonctionnalités peuvent ne pas être disponibles.</p>
    <p>Veuillez contacter l'administrateur système si ce message persiste.</p>
  `;
  
  if (body.firstChild) {
    body.insertBefore(banner, body.firstChild);
  } else {
    body.appendChild(banner);
  }
}); 