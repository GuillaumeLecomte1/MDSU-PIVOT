# Spécifications Techniques

## Vue d'Ensemble

Ce document présente les spécifications techniques détaillées du POC PIVOT Marketplace. Il consolide l'ensemble des aspects techniques du projet, incluant l'architecture, les exigences techniques, les contraintes de performance et les détails d'intégration entre les différentes technologies utilisées.

## Architecture Technique

### Architecture Globale

Le POC PIVOT est construit selon une architecture moderne de type monolithique avec séparation claire des responsabilités :

```
┌─────────────────────────────────────────────────────────────┐
│                     Client (Navigateur)                     │
└───────────────────────────────┬─────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────┐
│                        HTTPS / TLS 1.3                      │
└───────────────────────────────┬─────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────┐
│                      Serveur Web (Nginx)                    │
└───────────────────────────────┬─────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────┐
│                     Application Laravel                     │
├─────────────────┬─────────────┬──────────────┬──────────────┤
│  Controllers    │   Models    │   Services   │    Events    │
├─────────────────┴─────────────┴──────────────┴──────────────┤
│                        Inertia.js                           │
├─────────────────────────────────────────────────────────────┤
│                      React Components                       │
└───────────────────────────────┬─────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────┐
│                     Base de données MySQL                   │
└─────────────────────────────────────────────────────────────┘
```

### Couches Applicatives

1. **Couche Présentation**
   - Interface utilisateur React
   - Composants réutilisables
   - Gestion d'état côté client
   - Validation des formulaires

2. **Couche Middleware**
   - Inertia.js pour la communication entre Laravel et React
   - Gestion des requêtes et réponses
   - Middleware d'authentification et d'autorisation

3. **Couche Application**
   - Contrôleurs Laravel
   - Services métier
   - Gestion des événements
   - Files d'attente pour les tâches asynchrones

4. **Couche Domaine**
   - Modèles Eloquent
   - Règles métier
   - Validation des données

5. **Couche Persistance**
   - Migrations de base de données
   - Requêtes SQL optimisées
   - Transactions

### Flux de Données

```
┌──────────────┐    ┌───────────────┐    ┌────────────────┐    ┌────────────────┐
│  Utilisateur │    │ React/Inertia │    │ Laravel        │    │ Base de données│
│  (Browser)   │    │ (Frontend)    │    │ (Backend)      │    │ (MySQL)        │
└──────┬───────┘    └───────┬───────┘    └────────┬───────┘    └────────┬───────┘
       │                    │                     │                     │
       │ Requête HTTP       │                     │                     │
       │ ─────────────────► │                     │                     │
       │                    │                     │                     │
       │                    │ Inertia Request     │                     │
       │                    │ ─────────────────►  │                     │
       │                    │                     │                     │
       │                    │                     │ Requête SQL         │
       │                    │                     │ ─────────────────►  │
       │                    │                     │                     │
       │                    │                     │ ◄───────────────────│
       │                    │                     │ Résultats SQL       │
       │                    │                     │                     │
       │                    │ ◄─────────────────  │                     │
       │                    │ Inertia Response    │                     │
       │                    │ (JSON + Props)      │                     │
       │                    │                     │                     │
       │ ◄───────────────── │                     │                     │
       │ Rendu React        │                     │                     │
       │                    │                     │                     │
```

## Stack Technologique Détaillée

### Backend

- **Framework** : Laravel 10.x
  - **PHP** : Version 8.1+
  - **Composer** : Gestion des dépendances
  - **Artisan** : CLI pour les tâches automatisées
  - **Eloquent ORM** : Mapping objet-relationnel
  - **Blade** : Moteur de templates (utilisé pour les emails)
  - **Laravel Sanctum** : Authentification API
  - **Laravel Queues** : Gestion des tâches asynchrones

### Frontend

- **Framework** : React 18.x
  - **Inertia.js** : Pont entre Laravel et React
  - **TypeScript** : Typage statique
  - **React Hook Form** : Gestion des formulaires
  - **React Query** : Gestion des requêtes et du cache
  - **Tailwind CSS** : Framework CSS utilitaire
  - **Headless UI** : Composants accessibles sans style
  - **Leaflet** : Bibliothèque de cartographie

### Base de données

- **SGBD** : MySQL 8.0
  - **Migrations** : Gestion du schéma de base de données
  - **Seeders** : Données initiales
  - **Indexes** : Optimisation des requêtes fréquentes

### Infrastructure

- **Serveur Web** : Nginx
- **Environnement d'exécution** : PHP-FPM
- **Sécurité** : TLS 1.3, HTTPS
- **Cache** : Redis (sessions, cache applicatif)
- **Stockage** : Système de fichiers local (POC)

### Outils de Développement

- **Versioning** : Git avec GitHub
- **CI/CD** : GitHub Actions
- **Tests** : PHPUnit, Jest, Cypress
- **Linting** : ESLint, PHP_CodeSniffer
- **Analyse statique** : PHPStan (niveau 5)

## Exigences Techniques Précises

### Exigences Système

- **Serveur** :
  - CPU : 2 cœurs minimum
  - RAM : 4 Go minimum
  - Stockage : 20 Go minimum (SSD recommandé)
  - Bande passante : 10 Mbps minimum

- **Client** :
  - Navigateurs supportés : Chrome 90+, Firefox 90+, Safari 14+, Edge 90+
  - JavaScript activé
  - Cookies activés pour l'authentification

### Exigences Logicielles

- **Environnement de production** :
  - PHP 8.1+ avec extensions (BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML)
  - Composer 2.x
  - MySQL 8.0+
  - Node.js 16+ (pour la compilation des assets)
  - NPM 8+ ou Yarn 1.22+

- **Environnement de développement** :
  - Mêmes exigences que la production
  - Git 2.x+
  - Docker (optionnel, pour le développement local)

### Exigences de Sécurité

- **Authentification** :
  - Mots de passe : Minimum 8 caractères, combinaison de lettres, chiffres et caractères spéciaux
  - Sessions : Expiration après 2 heures d'inactivité
  - Verrouillage de compte : Après 5 tentatives échouées

- **Autorisation** :
  - RBAC (Role-Based Access Control)
  - Vérification des permissions à chaque action sensible

- **Protection des données** :
  - Chiffrement des données sensibles en base de données
  - Transmission sécurisée via HTTPS
  - Validation stricte des entrées utilisateur

## Contraintes de Performance et Scalabilité

### Objectifs de Performance

- **Temps de réponse** :
  - Pages statiques : < 200ms
  - Pages dynamiques : < 500ms
  - Recherche : < 800ms
  - Upload d'images : < 3s

- **Débit** :
  - Support de 50 utilisateurs simultanés (POC)
  - Capacité à traiter 100 requêtes/minute

- **Disponibilité** :
  - Objectif de 99% de disponibilité pour le POC
  - Maintenance planifiée en dehors des heures de pointe

### Optimisations Implémentées

- **Base de données** :
  - Indexes sur les colonnes fréquemment recherchées
  - Requêtes optimisées avec eager loading pour éviter le problème N+1
  - Pagination des résultats de recherche

- **Frontend** :
  - Lazy loading des images
  - Code splitting pour réduire la taille des bundles JavaScript
  - Mise en cache des composants React statiques

- **Backend** :
  - Mise en cache des requêtes fréquentes
  - Traitement asynchrone des tâches longues (emails, calculs d'impact)
  - Compression des réponses HTTP

### Limites Actuelles et Évolutivité

Le POC actuel présente certaines limitations en termes de performance et de scalabilité :

- **Stockage des médias** : Système de fichiers local, à migrer vers un service de stockage objet pour la production
- **Base de données** : Pas de réplication ou de sharding dans le POC
- **Mise en cache** : Implémentation basique, à améliorer pour la production
- **Monitoring** : Limité aux logs applicatifs, sans APM complet

Pour l'évolution vers une version production, les améliorations suivantes sont prévues :

- **Infrastructure** :
  - Migration vers une architecture conteneurisée (Docker)
  - Mise en place d'un équilibreur de charge
  - Réplication de la base de données
  - CDN pour les assets statiques

- **Optimisations** :
  - Implémentation d'un cache distribué
  - Optimisation des requêtes SQL complexes
  - Mise en place d'un système de queue plus robuste
  - Implémentation d'un monitoring complet

## Détails d'Intégration Entre les Technologies

### Inertia.js + Laravel + React

L'intégration entre Laravel et React via Inertia.js constitue l'épine dorsale technique du projet :

```
┌─────────────────────────────────────────────────────────────┐
│                     Laravel (Backend)                       │
│                                                             │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐     │
│  │ Controllers │───►│  Services   │───►│   Models    │     │
│  └─────────────┘    └─────────────┘    └─────────────┘     │
│          │                                                  │
│          ▼                                                  │
│  ┌─────────────┐                                            │
│  │  Inertia    │                                            │
│  │  Responses  │                                            │
│  └─────────────┘                                            │
└──────────┬──────────────────────────────────────────────────┘
           │
           │ JSON + Props
           ▼
┌──────────┴──────────────────────────────────────────────────┐
│                      Inertia.js (Bridge)                    │
└──────────┬──────────────────────────────────────────────────┘
           │
           │ Props
           ▼
┌──────────┴──────────────────────────────────────────────────┐
│                       React (Frontend)                      │
│                                                             │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐     │
│  │    Pages    │───►│ Components  │───►│    Hooks    │     │
│  └─────────────┘    └─────────────┘    └─────────────┘     │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

**Flux de données** :

1. L'utilisateur effectue une action (clic, soumission de formulaire)
2. Inertia.js intercepte la requête et l'envoie au backend Laravel
3. Le contrôleur Laravel traite la requête, interagit avec les modèles et services
4. Le contrôleur renvoie une réponse Inertia avec les données (props)
5. Inertia.js reçoit la réponse et met à jour les props du composant React
6. React re-render le composant avec les nouvelles données
7. L'interface utilisateur est mise à jour sans rechargement complet de la page

### Intégration avec les Services Externes

Le POC intègre plusieurs services et bibliothèques externes :

- **Leaflet** pour la cartographie :
  - Intégration via React-Leaflet
  - Utilisation de composants React dédiés
  - Chargement asynchrone des tuiles cartographiques

- **Système d'emails** :
  - Laravel Mail avec Mailgun (ou SMTP)
  - Templates Blade pour le contenu des emails
  - File d'attente pour l'envoi asynchrone

- **Stockage des médias** :
  - Utilisation du système de fichiers de Laravel
  - Intervention Image pour le redimensionnement
  - Validation et sanitisation des fichiers uploadés

## Modèle de Données Détaillé

### Diagramme Entité-Relation (MCD)

```
┌───────────────┐       ┌───────────────┐       ┌───────────────┐
│     Users     │       │     Roles     │       │ Ressourceries │
├───────────────┤       ├───────────────┤       ├───────────────┤
│ id            │       │ id            │       │ id            │
│ name          │       │ name          │       │ user_id       │
│ email         │◄──┐   │ description   │       │ name          │
│ password      │   │   └───────┬───────┘       │ description   │
│ phone         │   │           │               │ address       │
│ address       │   │           │               │ city          │
│ role_id       │───┘           │               │ postal_code   │
│ company_name  │               │               │ latitude      │
│ created_at    │               │               │ longitude     │
│ updated_at    │               │               │ phone         │
│ email_verified│               │               │ email         │
└───────┬───────┘               │               │ website       │
        │                       │               │ logo          │
        │                       │               │ opening_hours │
        │                       │               │ created_at    │
        │                       │               │ updated_at    │
        │                       │               └───────┬───────┘
        │                       │                       │
        │                       │                       │
┌───────┴───────┐     ┌─────────┴─────────┐   ┌────────┴────────┐
│    Orders     │     │     Products      │   │   Categories    │
├───────────────┤     ├───────────────────┤   ├─────────────────┤
│ id            │     │ id                │   │ id              │
│ user_id       │     │ name              │   │ name            │
│ status        │     │ description       │   │ description     │
│ total_amount  │     │ price             │   │ parent_id       │
│ pickup_date   │     │ condition         │   │ created_at      │
│ pickup_time   │     │ dimensions        │   │ updated_at      │
│ created_at    │     │ color             │   └─────────┬───────┘
│ updated_at    │     │ brand             │             │
└───────┬───────┘     │ ressourcerie_id   │◄────────────┘
        │             │ category_id       │
        │             │ stock             │
        │             │ is_available      │
        │             │ created_at        │
        │             │ updated_at        │
┌───────┴───────┐     └─────────┬─────────┘
│  OrderItems   │               │
├───────────────┤               │
│ id            │               │
│ order_id      │               │
│ product_id    │◄──────────────┘
│ quantity      │
│ price         │
│ created_at    │
│ updated_at    │
└───────────────┘
```

### Relations Clés

- **Users - Roles** : Relation Many-to-One (Plusieurs utilisateurs peuvent avoir un même rôle)
- **Users - Orders** : Relation One-to-Many (Un utilisateur peut avoir plusieurs commandes)
- **Users - Ressourceries** : Relation One-to-One (Un utilisateur de type ressourcerie est lié à une ressourcerie)
- **Ressourceries - Products** : Relation One-to-Many (Une ressourcerie peut proposer plusieurs produits)
- **Categories - Products** : Relation One-to-Many (Une catégorie peut contenir plusieurs produits)
- **Categories - Categories** : Relation récursive One-to-Many (Une catégorie peut avoir plusieurs sous-catégories)
- **Orders - OrderItems** : Relation One-to-Many (Une commande contient plusieurs éléments)
- **Products - OrderItems** : Relation One-to-Many (Un produit peut figurer dans plusieurs éléments de commande)

## Flux Utilisateurs

### Parcours Client

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  Inscription│     │  Recherche  │     │   Panier    │     │ Confirmation │
│  Connexion  │────►│  Navigation │────►│   d'achat   │────►│  Commande   │
└─────────────┘     └─────────────┘     └─────────────┘     └─────────────┘
                           │                                        │
                           ▼                                        │
                    ┌─────────────┐                                 │
                    │   Fiche     │                                 │
                    │   Produit   │◄────────────────────────────────┘
                    └─────────────┘
```

### Parcours Ressourcerie

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  Inscription│     │  Tableau de │     │  Gestion    │     │  Suivi des  │
│  Validation │────►│    bord     │────►│  Produits   │────►│  Commandes  │
└─────────────┘     └─────────────┘     └─────────────┘     └─────────────┘
                           │                   ▲                   │
                           │                   │                   │
                           ▼                   │                   ▼
                    ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
                    │  Gestion du │     │  Ajout de   │     │ Statistiques│
                    │   Profil    │     │  Produits   │     │   Basiques  │
                    └─────────────┘     └─────────────┘     └─────────────┘
```

### Parcours Administrateur

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  Connexion  │     │  Tableau de │     │  Gestion    │     │  Gestion    │
│  Admin      │────►│    bord     │────►│ Utilisateurs│────►│ Ressourceries│
└─────────────┘     └─────────────┘     └─────────────┘     └─────────────┘
                           │                                       │
                           ▼                                       ▼
                    ┌─────────────┐                        ┌─────────────┐
                    │  Rapports   │                        │  Validation │
                    │  Statistiques│                       │ Inscriptions│
                    └─────────────┘                        └─────────────┘
```

## Interfaces Principales

### Maquettes des Écrans Clés

Les interfaces principales du POC PIVOT suivent une approche de design responsive avec Tailwind CSS :

1. **Page d'accueil** : Présentation de la plateforme, recherche, catégories populaires
2. **Catalogue produits** : Affichage en grille/liste, filtres, tri
3. **Fiche produit** : Photos, description, caractéristiques, bouton d'ajout au panier
4. **Panier** : Récapitulatif des produits, sélection du créneau de retrait
5. **Tableau de bord ressourcerie** : Statistiques, commandes récentes, produits populaires
6. **Interface d'ajout de produit** : Formulaire avec upload de photos, caractéristiques
7. **Gestion des commandes** : Liste des commandes, filtres par statut, détails

### Composants UI Réutilisables

Le frontend est construit autour de composants React réutilisables :

- **Boutons** : Primaire, secondaire, danger, avec différentes tailles
- **Formulaires** : Champs texte, select, checkbox, upload de fichiers
- **Cartes produit** : Affichage compact des informations produit
- **Pagination** : Navigation entre les pages de résultats
- **Modales** : Confirmations, formulaires en overlay
- **Notifications** : Alertes de succès, erreur, information
- **Navigation** : Barre de navigation, menu latéral, fil d'Ariane

## Stratégie de Déploiement

### Environnements

Le POC PIVOT dispose de trois environnements distincts :

1. **Développement** : Environnement local pour les développeurs
   - Configuration : `.env.development`
   - Base de données : Instance locale
   - Serveur : Serveur de développement Laravel (artisan serve)

2. **Test** : Environnement de validation
   - Configuration : `.env.testing`
   - Base de données : Instance dédiée aux tests
   - Serveur : Instance de staging

3. **Production** : Environnement de démonstration du POC
   - Configuration : `.env.production`
   - Base de données : Instance de production
   - Serveur : Hébergement mutualisé (POC)

### Processus de Déploiement

Le déploiement du POC suit un processus simplifié :

1. **Préparation** :
   - Compilation des assets (npm run production)
   - Optimisation de l'autoloader Composer
   - Génération du cache de configuration

2. **Déploiement** :
   - Transfert des fichiers vers le serveur
   - Exécution des migrations de base de données
   - Vidage des caches

3. **Vérification** :
   - Tests de smoke pour vérifier les fonctionnalités critiques
   - Vérification des logs pour détecter d'éventuelles erreurs

### Monitoring et Maintenance

Pour le POC, un monitoring basique est mis en place :

- **Logs applicatifs** : Enregistrement des erreurs et événements importants
- **Surveillance des performances** : Temps de réponse, utilisation des ressources
- **Sauvegardes** : Sauvegarde quotidienne de la base de données

## Conclusion

Les spécifications techniques du POC PIVOT Marketplace définissent une architecture moderne et évolutive, adaptée aux besoins spécifiques des ressourceries. Bien que certaines limitations existent en raison de la nature du POC, les fondations techniques sont solides et permettront une évolution vers une version production complète.

Les choix technologiques (Laravel, Inertia.js, React, MySQL) offrent un bon équilibre entre rapidité de développement, performance et maintenabilité, tout en permettant une expérience utilisateur fluide et réactive.

Ces spécifications serviront de référence pour les développements futurs et l'évolution du projet PIVOT. 