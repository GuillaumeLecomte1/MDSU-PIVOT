import { defineConfig } from 'vitepress'

export default defineConfig({
  title: "Documentation Marketplace",
  description: "Documentation technique du système de marketplace",
  themeConfig: {
    nav: [
      { text: 'Accueil', link: '/' },
      { text: 'Permissions', link: '/permissions/' }
    ],
    sidebar: [
      {
        text: 'Guide',
        items: [
          { text: 'Système de Permissions', link: '/permissions/' },
          { text: 'Gestion des Images', link: '/images/' },
          { text: 'Gestion des Produits', link: '/products/' }
        ]
      }
    ],
    socialLinks: [
      { icon: 'github', link: 'https://github.com/yourusername/marketplace' }
    ]
  },
  markdown: {
    lineNumbers: true,
  },
  vite: {
    optimizeDeps: {
      exclude: ['@vueuse/core']
    }
  }
}) 