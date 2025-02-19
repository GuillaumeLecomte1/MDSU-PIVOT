# Documentation Marketplace Pivot

::: tip Bienvenue
Bienvenue dans la documentation technique de la Marketplace. Cette documentation est destinée aux développeurs et administrateurs du système.
:::

## 📚 Sections principales

<div class="features">
  <div class="feature">
    <h3>🔐 Système de Permissions</h3>
    <p>Gestion des rôles et des permissions des utilisateurs.</p>
    <a href="/permissions/">Voir la documentation</a>
  </div>
  
  <div class="feature">
    <h3>🖼️ Gestion des Images</h3>
    <p>Système de gestion et d'optimisation des images.</p>
    <a href="/images/">Voir la documentation</a>
  </div>
  
  <div class="feature">
    <h3>📦 Gestion des Produits</h3>
    <p>Système de gestion des produits et du catalogue.</p>
    <a href="/products/">Voir la documentation</a>
  </div>
</div>

## 🚀 Démarrage rapide

Pour lancer le serveur de documentation en local :

```bash
npm run docs:dev
```

Le serveur sera accessible à l'adresse : [http://localhost:5173/](http://localhost:5173/)

## 🔧 Technologies utilisées

- Laravel 10
- React + Inertia.js
- Tailwind CSS
- VitePress pour la documentation

<style>
.features {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
  margin: 2rem 0;
}

.feature {
  padding: 1.5rem;
  border-radius: 8px;
  background: var(--vp-c-bg-soft);
  transition: all 0.3s ease;
}

.feature:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.feature h3 {
  margin-top: 0;
  font-size: 1.2rem;
}

.feature a {
  display: inline-block;
  margin-top: 1rem;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  background: var(--vp-c-brand);
  color: white;
  text-decoration: none;
  transition: background 0.3s ease;
}

.feature a:hover {
  background: var(--vp-c-brand-dark);
}
</style> 