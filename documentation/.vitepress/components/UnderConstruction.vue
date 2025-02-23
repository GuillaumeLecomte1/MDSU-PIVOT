# UnderConstruction.vue
<template>
  <div class="under-construction">
    <div class="icon">🏗️</div>
    <h2>Documentation en cours de rédaction</h2>
    <p>Cette section de la documentation est actuellement en cours de rédaction. Elle sera bientôt disponible.</p>
    <div class="suggestions">
      <h3>En attendant, vous pouvez consulter :</h3>
      <ul>
        <li><a href="/guide/introduction">Introduction</a></li>
        <li><a href="/guide/installation">Guide d'installation</a></li>
        <li><a href="/guide/deployment">Guide de déploiement</a></li>
      </ul>
    </div>
  </div>
</template>

<style scoped>
.under-construction {
  padding: 2rem;
  text-align: center;
  background: var(--vp-c-bg-soft);
  border-radius: 8px;
  margin: 2rem 0;
}

.icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

h2 {
  color: var(--vp-c-brand);
  margin-bottom: 1rem;
}

.suggestions {
  margin-top: 2rem;
  text-align: left;
  max-width: 400px;
  margin-left: auto;
  margin-right: auto;
}

.suggestions h3 {
  font-size: 1.1rem;
  margin-bottom: 0.5rem;
}

.suggestions ul {
  list-style: none;
  padding: 0;
}

.suggestions li {
  margin: 0.5rem 0;
}

.suggestions a {
  color: var(--vp-c-brand);
  text-decoration: none;
}

.suggestions a:hover {
  text-decoration: underline;
}
</style> 