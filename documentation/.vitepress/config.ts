import { defineConfig } from 'vitepress'

export default defineConfig({
  title: "Marketplace Documentation",
  description: "Documentation complète du projet Marketplace",
  lang: 'fr-FR',
  lastUpdated: true,
  ignoreDeadLinks: true,

  head: [
    ['link', { rel: 'icon', href: '/favicon.ico' }],
    ['meta', { name: 'theme-color', content: '#3eaf7c' }],
    ['meta', { name: 'apple-mobile-web-app-capable', content: 'yes' }],
    ['meta', { name: 'apple-mobile-web-app-status-bar-style', content: 'black' }]
  ],

  themeConfig: {
    siteTitle: 'Marketplace Docs',
    logo: '/logo.png',

    nav: [
      { text: 'Accueil', link: '/' },
      {
        text: 'Guide',
        items: [
          { text: 'Introduction', link: '/guide/introduction' },
          { text: 'Installation', link: '/guide/installation' },
          { text: 'Déploiement', link: '/guide/deployment' },
          { text: 'Configuration', link: '/guide/configuration' }
        ]
      },
      {
        text: 'Développement',
        items: [
          { text: 'Architecture', link: '/development/architecture' },
          { text: 'Base de données', link: '/development/database' },
          { text: 'API', link: '/development/api' },
          { text: 'Tests', link: '/development/testing' }
        ]
      },
      {
        text: 'Fonctionnalités',
        items: [
          { text: 'Vue d\'ensemble', link: '/features/' },
          { text: '🏪 Ressourceries', link: '/features/ressourceries' },
          { text: '📦 Produits', link: '/features/products' },
          { text: '🖼️ Gestion des Images', link: '/features/images' },
          { text: '🔐 Permissions', link: '/features/permissions' },
          { text: '💳 Paiements', link: '/features/payments' }
        ]
      }
    ],

    sidebar: {
      '/guide/': [
        {
          text: 'Commencer',
          items: [
            { text: 'Introduction', link: '/guide/introduction' },
            { text: 'Installation', link: '/guide/installation' },
            { text: 'Démarrage rapide', link: '/guide/quickstart' }
          ]
        },
        {
          text: 'Déploiement',
          items: [
            { text: 'Prérequis', link: '/guide/deployment-prerequisites' },
            { text: 'Configuration serveur', link: '/guide/server-setup' },
            { text: 'Déploiement', link: '/guide/deployment' },
            { text: 'SSL et Sécurité', link: '/guide/security' }
          ]
        }
      ],
      '/development/': [
        {
          text: 'Architecture',
          items: [
            { text: 'Vue d\'ensemble', link: '/development/architecture' },
            { text: 'Structure du projet', link: '/development/project-structure' },
            { text: 'Design Patterns', link: '/development/patterns' }
          ]
        },
        {
          text: 'Base de données',
          items: [
            { text: 'Structure', link: '/development/database' },
            { text: 'Migrations', link: '/development/migrations' },
            { text: 'Seeders', link: '/development/seeders' }
          ]
        },
        {
          text: 'API',
          items: [
            { text: 'Introduction', link: '/development/api' },
            { text: 'Authentication', link: '/development/api-auth' },
            { text: 'Endpoints', link: '/development/api-endpoints' }
          ]
        }
      ],
      '/features/': [
        {
          text: 'Fonctionnalités Principales',
          items: [
            { text: 'Vue d\'ensemble', link: '/features/' },
            { text: '🏪 Ressourceries', link: '/features/ressourceries' },
            { text: '📦 Produits', link: '/features/products' }
          ]
        },
        {
          text: '🖼️ Système de Gestion des Images',
          items: [
            { text: 'Introduction', link: '/features/images/' },
            { text: 'Upload et Traitement', link: '/features/images/upload' },
            { text: 'Optimisation', link: '/features/images/optimization' },
            { text: 'Stockage et CDN', link: '/features/images/storage' },
            { text: 'Manipulation d\'images', link: '/features/images/manipulation' },
            { text: 'Bonnes pratiques', link: '/features/images/best-practices' }
          ]
        },
        {
          text: '🔐 Gestion des Permissions',
          items: [
            { text: 'Système de Rôles', link: '/features/permissions/' },
            { text: 'Permissions Utilisateurs', link: '/features/permissions/users' },
            { text: 'Permissions Ressourceries', link: '/features/permissions/stores' },
            { text: 'Permissions Produits', link: '/features/permissions/products' },
            { text: 'Middleware de Sécurité', link: '/features/permissions/middleware' },
            { text: 'Audit et Logs', link: '/features/permissions/audit' }
          ]
        },
        {
          text: '💳 Système de Paiement',
          items: [
            { text: 'Configuration Stripe', link: '/features/payments/' },
            { text: 'Processus de paiement', link: '/features/payments/process' },
            { text: 'Webhooks', link: '/features/payments/webhooks' },
            { text: 'Remboursements', link: '/features/payments/refunds' }
          ]
        },
        {
          text: '📍 Géolocalisation',
          items: [
            { text: 'Configuration', link: '/features/geolocation/' },
            { text: 'Recherche proximité', link: '/features/geolocation/search' },
            { text: 'Calcul distances', link: '/features/geolocation/distance' }
          ]
        },
        {
          text: '⚙️ Administration',
          items: [
            { text: 'Dashboard', link: '/features/dashboard' },
            { text: 'Gestion utilisateurs', link: '/features/user-management' },
            { text: 'Statistiques', link: '/features/statistics' },
            { text: 'Logs système', link: '/features/system-logs' }
          ]
        },
        {
          text: '🔄 Intégrations',
          items: [
            { text: 'API Externes', link: '/features/integrations/' },
            { text: 'Webhooks', link: '/features/integrations/webhooks' },
            { text: 'Services tiers', link: '/features/integrations/third-party' }
          ]
        }
      ]
    },

    footer: {
      message: 'Documentation générée avec VitePress',
      copyright: `Copyright © ${new Date().getFullYear()} Marketplace`
    },

    search: {
      provider: 'local',
      options: {
        locales: {
          root: {
            translations: {
              button: {
                buttonText: 'Rechercher',
                buttonAriaLabel: 'Rechercher'
              },
              modal: {
                noResultsText: 'Aucun résultat pour',
                resetButtonTitle: 'Réinitialiser',
                footer: {
                  selectText: 'pour sélectionner',
                  navigateText: 'pour naviguer'
                }
              }
            }
          }
        }
      }
    },

    socialLinks: [
      { icon: 'github', link: 'https://github.com/votre-repo' }
    ],

    outline: {
      level: [2, 3],
      label: 'Sur cette page'
    },

    docFooter: {
      prev: 'Page précédente',
      next: 'Page suivante'
    },

    // Configuration pour la gestion des pages non trouvées
    notFound: {
      title: 'Page en construction',
      quote: 'Cette section de la documentation est en cours de développement.',
      linkText: 'Retour à l\'accueil',
      linkLabel: 'Retourner à la page d\'accueil',
      code: ''
    }
  }
}) 