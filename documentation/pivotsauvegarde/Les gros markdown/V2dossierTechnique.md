# Dossier Technique - Projet PIVOT Marketplace

## Table des matières

- [Introduction et Contexte](#introduction-et-contexte)
- [Modélisation](#modélisation)
  - [Modèle Conceptuel de Données (MCD)](#modèle-conceptuel-de-données-mcd)
  - [UML de cas d'utilisation](#uml-de-cas-dutilisation)
- [Architecture et Choix Technologiques](#architecture-et-choix-technologiques)
  - [Vue d'ensemble de l'Architecture](#vue-densemble-de-larchitecture)
  - [Stack Technologique et Justification des Choix](#stack-technologique-et-justification-des-choix)
  - [Alternatives Considérées](#alternatives-considérées)
  - [Couches Applicatives](#couches-applicatives)
  - [Contraintes Techniques](#contraintes-techniques)
  - [Environnement de Développement](#environnement-de-développement)
- [Fonctionnalités Développées](#fonctionnalités-développées)
  - [Description des Fonctionnalités](#description-des-fonctionnalités)
- [Sécurisation](#sécurisation)
  - [Authentification et Autorisation](#authentification-et-autorisation)
  - [Protection des données](#protection-des-données)
- [Conformité](#conformité)
  - [Respect des Principes du RGPD](#respect-des-principes-du-rgpd)
- [Accessibilité](#accessibilité)
  - [Conformité au RGAA](#conformité-au-rgaa)
  - [Utilisation des Attributs ARIA](#utilisation-des-attributs-aria)
- [Recettage](#recettage)
  - [Tests Unitaires](#tests-unitaires)
  - [Tests d'Intégration](#tests-dintégration)
  - [Tests de Performance](#tests-de-performance)
- [Spécifications Fonctionnelles](#spécifications-fonctionnelles)
- [Spécifications Techniques](#spécifications-techniques)
- [Prototype](#prototype)

## Introduction et Contexte

Dans un contexte économique où les plateformes de commerce en ligne connaissent une croissance exponentielle, notre projet Pivot vise à offrir une solution innovante et complète spécifiquement conçue pour les ressourceries. Ces structures, dédiées à la récupération, la valorisation et la revente de biens sur un territoire donné, jouent un rôle crucial dans la sensibilisation et l'éducation à l'environnement, contribuant ainsi à l'économie circulaire et à la réduction des déchets.

PIVOT est la première plateforme de click-and-collect dédiée aux ressourceries en France, permettant de donner une seconde vie aux produits dénichés tout en créant de nouvelles interactions sociales. Le projet s'articule autour d'une architecture modulaire et évolutive, permettant à chaque ressourcerie de configurer sa boutique en ligne selon ses besoins spécifiques.

### Origine et Objectifs du Projet

Le projet Pivot est né d'une observation simple : malgré la démocratisation du commerce en ligne, les ressourceries, acteurs essentiels de l'économie circulaire, ne disposent pas d'outils numériques adaptés à leurs besoins spécifiques. Notre solution vise à combler ce fossé en proposant une plateforme complète, flexible et accessible, spécifiquement conçue pour ce secteur niche.

Les ressourceries font face à plusieurs défis spécifiques :
- Gestion d'inventaires variés et uniques (objets de seconde main)
- Valorisation de l'aspect écologique et social des produits
- Nécessité de toucher un public plus large que leur zone géographique immédiate
- Besoin de solutions numériques adaptées à leurs ressources souvent limitées

### Public Cible

Le projet s'adresse à deux cibles distinctes mais complémentaires :

- **Les ressourceries (B2B)**, qui cherchent à étendre leur portée et à digitaliser leur offre sans investissement technique lourd.
- **Les consommateurs éco-responsables (B2C)**, qui souhaitent acheter des produits de seconde main tout en soutenant des structures locales à vocation sociale et environnementale.

### Périmètre Fonctionnel

Le projet se concentre dans un premier temps sur les fonctionnalités essentielles d'une marketplace adaptée aux ressourceries, avec une attention particulière portée à :

- **La gestion des produits de seconde main**, avec des caractéristiques spécifiques comme l'état, les dimensions, la provenance.
-~~**Le système de click-and-collect**, privilégiant le retrait en boutique pour renforcer le lien social et réduire l'empreinte carbone.~~
- ~~**La personnalisation de l'interface**, offrant la possibilité d'adapter l'apparence de la boutique en ligne à l'identité visuelle de chaque ressourcerie.~~
- ~~**La géolocalisation**, permettant aux utilisateurs de trouver facilement les ressourceries proches de chez eux.~~

### Attentes et Besoins Identifiés

Lors de l'analyse des besoins, plusieurs attentes clés ont été identifiées :

- **Une interface d'administration intuitive**, permettant une gestion simplifiée des produits uniques et des commandes.
- **Des outils de valorisation de l'impact environnemental**, mettant en avant la contribution à l'économie circulaire.
- **Une architecture évolutive**, capable de s'adapter à la croissance de la plateforme et à la diversification des services proposés par les ressourceries.

En tant que développeur web du projet, mon rôle est de concevoir et de réaliser l'ensemble de la plateforme. Cela inclut le développement du backend, la création d'une interface utilisateur intuitive, la mise en place des fonctionnalités techniques clés, la gestion des données, ainsi que l'intégration des bonnes pratiques en matière de sécurité (conformité RGPD) et d'accessibilité (normes RGAA, ARIA, etc.).

Ce dossier technique détaillera les différentes étapes de la réalisation du projet, des besoins initiaux aux choix techniques effectués, en passant par la modélisation des données et l'analyse des fonctionnalités clés.

## Modélisation

### Modèle Conceptuel de Données (MCD)

#### Introduction

La modélisation des données est une étape fondamentale dans la conception de notre plateforme Pivot pour ressourceries. Elle permet de structurer les informations et d'assurer une gestion efficace des interactions entre les différents acteurs (administrateurs, ressourceries, clients).

Le Modèle Conceptuel de Données (MCD) que nous avons élaboré repose sur plusieurs entités interconnectées, garantissant ainsi une organisation cohérente des informations et une évolutivité adaptée aux besoins spécifiques des ressourceries.

Le MCD repose sur huit entités principales : **Users**, **Roles**, **Products**, **Categories**, **Orders**, **OrderItems**, **Ressourceries**, et **Payments**.

Ces entités définissent la structure de la base de données et permettent d'assurer le bon fonctionnement de la marketplace en respectant les contraintes métiers propres au secteur des ressourceries.

#### Description des Entités

##### 1. Utilisateurs (Users)

L'entité **Users** regroupe les informations des différents profils de l'application :

- **Administrateurs** : gestionnaires de la plateforme avec des droits étendus.
- **Ressourceries** : structures de réemploi proposant leurs produits sur la marketplace.
- **Clients** : utilisateurs effectuant des achats sur la plateforme.

Les principaux attributs de cette entité incluent :
- Les informations générales : name, email, password, phone, address.
- Des données spécifiques : role, company_name, ape_code (pour les ressourceries).
- Des données de gestion : created_at, updated_at, email_verified_at.

Chaque utilisateur peut interagir avec plusieurs autres entités, notamment les produits, les commandes, et les ressourceries.

##### 2. Rôles (Roles)

L'entité **Roles** définit les différents niveaux d'accès et de permissions au sein de la plateforme :

- Un identifiant unique id.
- Un nom de rôle name (admin, ressourcerie, client).
- Une description description du rôle.

Cette entité permet une gestion fine des autorisations et des accès aux fonctionnalités de la plateforme, adaptée aux besoins spécifiques de chaque type d'utilisateur.

##### 3. Produits (Products)

L'entité **Products** représente les articles de seconde main disponibles à la vente sur la marketplace :

- Un identifiant unique id.
- Des informations descriptives : name, description, price, condition, dimensions, color, brand.
- Des références vers la ressourcerie ressourcerie_id et la catégorie category_id.
- Des métadonnées : created_at, updated_at, stock, is_available.

Cette structure permet une gestion complète du catalogue produits, avec des informations détaillées pour chaque article, particulièrement importantes pour des objets de seconde main dont l'état et les caractéristiques peuvent varier.

##### 4. Catégories (Categories)

L'entité **Categories** permet d'organiser les produits par thématiques :

- Un identifiant unique id.
- Un nom name et une description description.
- Une référence parent_id pour les sous-catégories.
- Des métadonnées : created_at, updated_at.

Cette hiérarchisation facilite la navigation et la recherche de produits pour les clients, permettant de regrouper les objets par type (mobilier, électroménager, vêtements, etc.).

##### 5. Commandes (Orders)

L'entité **Orders** gère les transactions effectuées sur la plateforme :

- Un identifiant unique id.
- Une référence vers le client user_id.
- Des informations de livraison ou de retrait : shipping_address, shipping_method.
- Des données financières : total_amount, status.
- Des métadonnées : created_at, updated_at, completed_at.

Cette structure permet un suivi précis des commandes et de leur état d'avancement, avec une attention particulière au mode de retrait (click-and-collect privilégié).

##### 6. Éléments de commande (OrderItems)

L'entité **OrderItems** détaille les produits inclus dans chaque commande :

- Un identifiant unique id.
- Des références vers la commande order_id et le produit product_id.
- Des informations quantitatives : quantity, unit_price.
- Des métadonnées : created_at, updated_at.

Cette relation permet de conserver l'historique précis des produits commandés, même en cas de modification ultérieure des fiches produits, ce qui est particulièrement important pour des articles uniques.

##### 7. Ressourceries (Ressourceries)

L'entité **Ressourceries** représente les structures de réemploi présentes sur la plateforme :

- Un identifiant unique id.
- Une référence vers l'utilisateur user_id.
- Des informations descriptives : name, description, logo_url.
- Des données de contact : email, phone, address, city, postal_code.
- Des informations géographiques : latitude, longitude.
- Des données légales : siret.
- Des métadonnées : created_at, updated_at, is_active.

Cette structure permet à chaque ressourcerie de personnaliser son espace et de gérer ses produits de manière autonome, tout en facilitant la recherche géographique pour les clients.

##### 8. Paiements (Payments)

L'entité **Payments** gère les transactions financières :

- Un identifiant unique id.
- Une référence vers la commande order_id.
- Des informations de paiement : amount, payment_method, transaction_id.
- Un statut status (pending, completed, failed).
- Des métadonnées : created_at, updated_at.

Cette entité assure le suivi des paiements et la sécurisation des transactions entre les clients et les ressourceries.

### UML de cas d'utilisation

#### Introduction

Le diagramme UML de cas d'utilisation est un outil fondamental pour la modélisation des interactions entre les utilisateurs et un système. Il permet de visualiser les différentes fonctionnalités accessibles aux acteurs et d'illustrer leur rôle dans l'application.

Dans le cadre du projet PIVOT, marketplace pour ressourceries, le diagramme de cas d'utilisation a été élaboré afin de représenter les interactions entre les utilisateurs principaux (administrateurs, ressourceries, clients) et les services proposés par la plateforme. Il met en évidence les différentes fonctionnalités disponibles, telles que la gestion des produits de seconde main, le processus de commande en click-and-collect et la gestion des espaces ressourceries.

Ce diagramme permet ainsi d'obtenir une vue d'ensemble du système et de faciliter la compréhension des fonctionnalités essentielles. Il constitue également une base solide pour la phase de développement, en assurant une cohérence entre les attentes des utilisateurs et la mise en œuvre technique.

#### Relations entre Acteurs et Cas d'Utilisation

- **Client** : Il peut s'inscrire, se connecter, parcourir les produits, effectuer des achats en click-and-collect, suivre ses commandes et gérer son profil.
- **Ressourcerie** : Elle peut gérer son espace, ajouter et modifier des produits de seconde main, suivre les commandes et les paiements.
- **Administrateur** : Il peut gérer l'ensemble de la plateforme, les utilisateurs, les catégories et superviser les transactions.
- **Système** : Il gère les paiements, les notifications et la génération de rapports.

#### Description détaillée des Cas d'Utilisation

##### 1. Inscription et Connexion

- Les utilisateurs peuvent s'inscrire sur la plateforme en choisissant leur rôle (client ou ressourcerie).
- Une fois inscrits, ils peuvent se connecter pour accéder à leur espace personnel.
- Les administrateurs sont créés directement dans le système.

##### 2. Gestion des Produits de Seconde Main

- Les ressourceries peuvent ajouter, modifier et supprimer leurs produits.
- Elles peuvent définir les prix, l'état, les dimensions et les descriptions détaillées.
- Les administrateurs peuvent modérer les produits et les catégories.

##### 3. Processus d'Achat en Click-and-Collect

- Les clients peuvent parcourir les produits par catégorie, localisation ou via la recherche.
- Ils peuvent ajouter des produits au panier et procéder au paiement.
- Le système gère la transaction et informe la ressourcerie concernée.
- Le client est notifié quand le produit est prêt à être retiré.

##### 4. Gestion des Commandes

- Les clients peuvent suivre l'état de leurs commandes.
- Les ressourceries reçoivent des notifications pour les nouvelles commandes.
- Les administrateurs peuvent superviser l'ensemble des transactions.

##### 5. Gestion des Espaces Ressourceries

- Les ressourceries peuvent personnaliser leur espace de vente.
- Elles peuvent définir leurs horaires d'ouverture et informations de contact.
- Les administrateurs peuvent valider ou suspendre les espaces ressourceries.

##### 6. Reporting et Analyse

- Les ressourceries ont accès à des statistiques sur leurs ventes.
- Les administrateurs peuvent générer des rapports globaux.
- Le système fournit des insights sur l'impact environnemental des achats (économie de CO2, déchets évités).

## Architecture et Choix Technologiques

### Vue d'ensemble de l'Architecture

L'architecture technique de Pivot pour ressourceries repose sur une structure moderne, évolutive et sécurisée, spécifiquement conçue pour répondre aux besoins particuliers des structures de réemploi. Notre choix s'est porté sur une architecture MVC (Modèle-Vue-Contrôleur) basée sur Laravel, complétée par une interface utilisateur réactive construite avec Inertia.js et React.

Cette architecture permet de combiner la robustesse d'un backend PHP avec la fluidité d'une interface utilisateur moderne, tout en maintenant une complexité technique adaptée au contexte des ressourceries qui n'ont généralement pas d'équipes techniques dédiées.

Le schéma ci-dessous illustre les principales composantes de l'architecture et leurs interactions :

```
┌─────────────────────────────────────────────────────────────────┐
│                        Client (Navigateur)                       │
└───────────────────────────────┬─────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                           Laravel (PHP)                          │
│                                                                 │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────────────┐  │
│  │   Routes    │───▶│ Controllers │───▶│      Services       │  │
│  └─────────────┘    └──────┬──────┘    └──────────┬──────────┘  │
│                            │                       │             │
│                            ▼                       ▼             │
│                    ┌─────────────┐        ┌─────────────────┐   │
│                    │   Inertia   │        │  Repositories   │   │
│                    └──────┬──────┘        └──────────┬──────┘   │
│                            │                         │           │
│                            ▼                         ▼           │
│                    ┌─────────────┐            ┌─────────────┐   │
│                    │    React    │            │   Models    │   │
│                    └─────────────┘            └──────┬──────┘   │
│                                                      │           │
│                                                      ▼           │
│                                               ┌─────────────┐   │
│                                               │  Database   │   │
│                                               └─────────────┘   │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                        Services Externes                         │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌─────────┐ │
│  │    Stripe   │  │   Algolia   │  │   Leaflet   │  │  Email  │ │
│  └─────────────┘  └─────────────┘  └─────────────┘  └─────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

### Stack Technologique et Justification des Choix

#### Backend

- **Laravel 10** : Framework PHP robuste et mature, offrant une base solide pour le développement d'applications web complexes. Laravel a été choisi pour sa documentation exhaustive, sa communauté active et ses fonctionnalités intégrées qui accélèrent le développement (ORM Eloquent, système d'authentification, validation des données, etc.).

- **MySQL 8** : Système de gestion de base de données relationnelle performant et fiable, parfaitement adapté pour stocker les données structurées de la marketplace (utilisateurs, produits, commandes, etc.). MySQL a été sélectionné pour sa fiabilité, sa performance et sa compatibilité avec Laravel. Étant une base de données relationnelle mature, MySQL est parfaitement adaptée à la gestion des relations complexes entre les différentes entités du projet (utilisateurs, produits, commandes), tout en garantissant l'intégrité des données et la cohérence des transactions.

- **PHP 8.1** : Version récente du langage PHP, offrant des performances améliorées et des fonctionnalités modernes (types de retour, propriétés typées, match expressions, etc.).

#### Frontend

- **Inertia.js** : Bibliothèque permettant de créer des applications monopages (SPA) sans avoir à construire une API. Inertia.js sert de pont entre Laravel et React, offrant une expérience de développement fluide et une navigation côté client sans rechargement de page. Cette approche permet de bénéficier des avantages d'une Single Page Application (SPA) sans les inconvénients habituels liés à la duplication de la logique de routing.

- **React 18** : Bibliothèque JavaScript pour la construction d'interfaces utilisateur interactives. React a été choisi pour sa flexibilité, ses performances et sa popularité, garantissant une interface utilisateur moderne et réactive. Sa popularité assure également une communauté active et de nombreuses ressources disponibles.

- **Tailwind CSS 3** : Framework CSS utilitaire permettant de construire rapidement des interfaces personnalisées sans quitter le HTML. Tailwind CSS facilite la création d'interfaces cohérentes et adaptatives, tout en offrant une grande flexibilité de design.

#### Outils et Services

- **Stripe** : Service de paiement en ligne sécurisé, permettant de gérer les transactions financières entre les clients et les ressourceries. Stripe a été choisi en raison de sa fiabilité, de sa sécurité et de sa facilité d'intégration. Stripe offre une API complète et bien documentée, qui permet de gérer les paiements, les abonnements et les commissions de manière efficace.

- **Laravel Sanctum** : Solution d'authentification légère pour les SPA, API et applications mobiles. Sanctum gère l'authentification des utilisateurs et la sécurisation des routes.

- **Laravel Breeze** : Package d'authentification minimaliste, fournissant une implémentation simple de toutes les fonctionnalités d'authentification de Laravel.

- **Laravel Socialite** : Package facilitant l'authentification via des fournisseurs OAuth (Google, Facebook, etc.), simplifiant le processus d'inscription pour les utilisateurs.

- **Algolia** : Service de recherche en temps réel, permettant aux utilisateurs de trouver rapidement des produits spécifiques parmi le catalogue des ressourceries.

- **Leaflet** : Bibliothèque JavaScript open-source pour les cartes interactives, utilisée pour la géolocalisation des ressourceries et la recherche de produits par proximité.

### Alternatives Considérées

#### Backend

- **Symfony** : Alternative solide à Laravel, mais jugée plus complexe et moins adaptée au contexte des ressourceries.
- **Express.js (Node.js)** : Considéré pour sa performance, mais écarté en raison de la familiarité de l'équipe avec PHP et de l'écosystème Laravel.

#### Frontend

- **Vue.js** : Alternative viable à React, mais React a été préféré pour sa popularité et son écosystème plus large.
- **Angular** : Jugé trop complexe et rigide pour les besoins du projet.
- **API REST + SPA séparée** : Approche traditionnelle écartée au profit d'Inertia.js pour simplifier le développement.

#### Base de Données

- **PostgreSQL** : Alternative sérieuse à MySQL, offrant des fonctionnalités avancées, mais MySQL a été choisi pour sa simplicité et sa compatibilité avec l'hébergement prévu.
- **MongoDB** : Base de données NoSQL considérée, mais jugée moins adaptée au modèle de données relationnel de la marketplace.

### Couches Applicatives

#### 1. Couche Présentation

La couche présentation est responsable de l'interface utilisateur et de l'expérience utilisateur. Elle est construite avec React, Inertia.js et Tailwind CSS.

Cette couche gère :
- L'affichage des données aux utilisateurs
- La capture des entrées utilisateur
- La validation côté client
- Les transitions et animations
- L'adaptation responsive pour différents appareils

#### 2. Couche Application

La couche application contient la logique métier de l'application et coordonne les interactions entre la couche présentation et la couche domaine. Elle comprend :

- **Controllers** : Gèrent les requêtes HTTP, valident les entrées et coordonnent les réponses.
- **Services** : Encapsulent la logique métier complexe et orchestrent les opérations entre différentes entités.
- **Middleware** : Filtrent et transforment les requêtes HTTP (authentification, autorisation, etc.).
- **Validators** : Assurent la validation des données entrantes selon des règles métier.

#### 3. Couche Domaine

La couche domaine contient les entités métier et les règles de gestion spécifiques aux ressourceries. Elle comprend :

- **Models** : Représentent les entités métier (Ressourcerie, Product, Order, etc.) avec leurs attributs et relations.
- **Repositories** : Abstraient l'accès aux données et encapsulent la logique de requête.
- **Events & Listeners** : Implémentent le pattern observer pour découpler les composants.
- **Value Objects** : Encapsulent des concepts métier complexes (Address, Money, etc.).

#### 4. Couche Infrastructure

La couche infrastructure fournit les services techniques nécessaires au fonctionnement de l'application :

- **Database** : Gestion de la persistance des données avec MySQL.
- **Cache** : Mise en cache des données fréquemment accédées pour améliorer les performances.
- **Queue** : Traitement asynchrone des tâches longues (envoi d'emails, génération de rapports).
- **Storage** : Gestion des fichiers (images des produits, logos des ressourceries).
- **External Services** : Intégration avec des services tiers (Stripe, Algolia, etc.).

### Contraintes Techniques

#### Performances

- **Temps de réponse** : Objectif de temps de réponse inférieur à 2 secondes pour les opérations courantes.
- **Optimisation des requêtes** : Utilisation d'index, de requêtes optimisées et de mise en cache pour améliorer les performances.
- **Chargement des pages** : Optimisation des assets (images, CSS, JavaScript) pour réduire le temps de chargement initial.
- **Pagination** : Mise en place de pagination pour les listes de produits et les résultats de recherche.

#### Sécurité

- **Authentification robuste** : Mise en œuvre de mécanismes d'authentification sécurisés (hachage des mots de passe, protection contre les attaques par force brute).
- **Autorisation granulaire** : Contrôle précis des accès aux ressources en fonction des rôles utilisateur.
- **Protection des données sensibles** : Chiffrement des données sensibles (informations de paiement, coordonnées personnelles).
- **Conformité RGPD** : Mise en place des mesures nécessaires pour assurer la conformité avec le Règlement Général sur la Protection des Données.
- **Sécurisation des API** : Protection contre les vulnérabilités courantes (CSRF, XSS, injection SQL, etc.).

#### Scalabilité

- **Architecture modulaire** : Conception permettant d'ajouter facilement de nouvelles fonctionnalités.
- **Optimisation des ressources** : Utilisation efficace des ressources serveur pour supporter la croissance du nombre d'utilisateurs.
- **Mise en cache** : Implémentation de stratégies de mise en cache pour réduire la charge sur la base de données.
- **Traitement asynchrone** : Utilisation de files d'attente pour les tâches intensives (génération de rapports, envoi d'emails en masse).

#### Compatibilité

- **Navigateurs** : Support des navigateurs modernes (Chrome, Firefox, Safari, Edge) dans leurs versions récentes.
- **Appareils** : Design responsive adapté aux ordinateurs, tablettes et smartphones.
- **Accessibilité** : Conformité aux normes WCAG 2.1 niveau AA pour garantir l'accessibilité à tous les utilisateurs.

### Environnement de Développement

#### Outils de Développement

- **IDE** : Visual Studio Code avec extensions PHP, Laravel, React et Tailwind CSS.
- **Contrôle de version** : Git avec GitHub pour la gestion du code source.
- **Gestion de dépendances** : Composer pour PHP, npm pour JavaScript.
- **Tests** : PHPUnit pour les tests backend, Jest pour les tests frontend.
- **Linting et formatting** : PHP_CodeSniffer, ESLint et Prettier pour maintenir la qualité du code.
- **Documentation** : PHPDoc et JSDoc pour la documentation du code.

#### Workflow de Développement

- **Méthodologie Agile** : Développement itératif avec sprints de deux semaines.
- **Revue de code** : Processus de pull request avec revue obligatoire avant fusion.
- **Intégration continue** : Tests automatisés exécutés à chaque push.
- **Déploiement continu** : Déploiement automatique sur l'environnement de staging après validation des tests.

## Fonctionnalités Développées

### Description des Fonctionnalités

Le projet Pivot comprend plusieurs fonctionnalités essentielles visant à répondre aux besoins des utilisateurs, à faciliter la gestion d'une marketplace multi-vendeurs, et à garantir une expérience optimale et conforme aux standards.

Les fonctionnalités sont divisées en 3 types d'accès :

- Administrateur
- Vendeur
- Client

#### Page d'accueil

La landing page est conçue pour attirer l'attention des utilisateurs dès leur arrivée sur le site. Elle présente une interface simple, claire et ergonomique, avec une navigation fluide vers les autres sections. La page d'accueil est conçue pour être mobile-friendly et conforme aux standards d'accessibilité.

**Scénarios possibles :**

- **Utilisateur non connecté** : Accès à l'information générale, boutons d'inscription et de connexion, présentation des catégories populaires et des produits en vedette.
- **Utilisateur connecté** : Accès à son profil, à ses données et à ses services personnalisés en fonction de son rôle.

#### Inscription et Connexion

Les utilisateurs doivent pouvoir s'inscrire de manière simple en fournissant leurs informations de base. Lors de la création d'un compte, ils doivent également choisir le type d'utilisateur qu'ils souhaitent être : **Client** ou **Ressourcerie**.

Ce choix est essentiel car il conditionne l'accès à des fonctionnalités spécifiques adaptées à leur profil.

Le processus d'inscription est sécurisé, utilisant l'authentification par **Laravel Sanctum** pour garantir la confidentialité des données. Cela assure une gestion fiable des sessions et protège les informations sensibles des utilisateurs.

**Scénarios possibles :**

- **Nouvel utilisateur** :
  1. Remplissage du formulaire d'inscription avec des informations de base (nom, adresse e-mail, mot de passe).
  2. Sélection du type d'utilisateur : **Client** ou **Ressourcerie**.
  3. Validation des données avec vérification des champs obligatoires.
  4. Enregistrement sécurisé des informations dans la base de données.
  5. Redirection vers l'interface correspondant au type d'utilisateur choisi.
- **Utilisateur existant** :
  - Connexion via un formulaire sécurisé en saisissant son e-mail et son mot de passe.
  - Gestion des erreurs telles que les identifiants incorrects ou les comptes inactifs.
  - Authentification par Laravel Sanctum pour une session sécurisée et rapide.

#### Gestion du Compte

L'utilisateur peut gérer ses informations personnelles via un espace dédié, avec des champs spécifiques en fonction de son type d'utilisateur (Client ou Ressourcerie).

**Fonctionnalités clés :**

- **Modification des données personnelles :**
  - **Pour les Clients :** Possibilité de mettre à jour des informations de base telles que le nom, l'adresse e-mail, le mot de passe, l'adresse de livraison, etc.
  - **Pour les Ressourceries :** En plus des informations de base, les ressourceries peuvent renseigner des détails professionnels essentiels :
    - **Informations de la boutique :** Nom, description, logo.
    - **Coordonnées professionnelles :** Adresse, téléphone, email de contact.
    - **Informations bancaires :** Pour recevoir les paiements des ventes.
- **Sécurité des données :** Protection des informations grâce à des mécanismes d'authentification sécurisés.

#### Utilisateur de type Client

##### Parcours d'Achat

Le parcours d'achat est l'une des fonctionnalités centrales pour les clients. Il permet de naviguer dans le catalogue, de sélectionner des produits et de finaliser une commande.

**Fonctionnalités clés :**

- **Navigation dans le catalogue :** Recherche par mots-clés, filtrage par catégories, prix, ressourceries, etc.
- **Fiche produit détaillée :** Informations complètes, photos, avis clients, disponibilité.
- **Panier d'achat :** Ajout/suppression de produits, modification des quantités, calcul automatique du total.
- **Processus de commande :** Saisie des informations de livraison, choix du mode de paiement, récapitulatif avant validation.
- **Paiement sécurisé :** Intégration avec Stripe pour des transactions sécurisées.

##### Suivi des Commandes

Les clients peuvent suivre l'état de leurs commandes et accéder à l'historique de leurs achats.

**Fonctionnalités clés :**

- **Tableau de bord des commandes :** Vue d'ensemble des commandes en cours et passées.
- **Détail des commandes :** Informations complètes sur chaque commande (produits, prix, statut, etc.).
- **Suivi en temps réel :** Mise à jour du statut de la commande (en préparation, expédiée, livrée).
- **Factures et reçus :** Téléchargement des documents relatifs aux achats.

```
🚨 ⚠️ [Pas encore dispo]⚠️
##### Gestion des Avis 

Les clients peuvent laisser des avis sur les produits achetés, contribuant ainsi à la communauté de la marketplace.

**Fonctionnalités clés :**

- **Dépôt d'avis :** Notation et commentaire sur les produits achetés.
- **Modification/suppression d'avis :** Possibilité de mettre à jour ou retirer ses propres avis.
- **Consultation des avis :** Accès aux avis des autres clients pour guider les décisions d'achat.~~
```

#### Utilisateur de type Ressourcerie

##### Gestion de la Boutique

Les ressourceries peuvent créer et personnaliser leur espace de vente sur la marketplace.

**Fonctionnalités clés :**

- **Création de boutique :** Configuration initiale avec nom, description, logo, etc.
 ```
🚨 ⚠️ [Pas encore dispo]⚠️
- **Personnalisation :** Adaptation de l'apparence selon l'identité visuelle de la ressourcerie.
- **Paramètres de la boutique :** Définition des politiques de livraison, de retour, etc.
 ```
- **Statistiques :** Suivi des performances (vues, ventes, taux de conversion).

##### Gestion des Produits

Les ressourceries peuvent gérer leur catalogue de produits de manière autonome.

**Fonctionnalités clés :**

- **Ajout de produits :** Création de fiches produits avec informations détaillées, photos, prix, etc.
- **Modification/suppression :** Mise à jour ou retrait de produits du catalogue.
- **Gestion des stocks :** Suivi des quantités disponibles, alertes de stock bas.
 ```
🚨 ⚠️ [Pas encore dispo]⚠️
- **Promotions :** Création d'offres spéciales, réductions temporaires, etc.
```

##### Gestion des Commandes

Les ressourceries peuvent suivre et traiter les commandes concernant leurs produits.

**Fonctionnalités clés :**

- **Tableau de bord des commandes :** Vue d'ensemble des commandes à traiter.
- **Traitement des commandes :** Changement de statut, préparation, expédition.
 ```
🚨 ⚠️ [Pas encore dispo]⚠️
- **Communication avec les clients :** Échange de messages concernant les commandes.
- **Gestion des retours :** Traitement des demandes de retour ou d'échange.
```

##### Gestion des Paiements

Les ressourceries peuvent suivre leurs revenus et gérer leurs informations financières.

**Fonctionnalités clés :**

- **Tableau de bord financier :** Vue d'ensemble des ventes, commissions, revenus nets.
- **Historique des transactions :** Détail des paiements reçus et des commissions prélevées.
- **Configuration des paiements :** Gestion des informations bancaires pour recevoir les paiements.
 ```
🚨 ⚠️ [Pas encore dispo]⚠️
- **Factures et documents comptables :** Génération et téléchargement des documents nécessaires.
```

#### Utilisateur de type Administrateur

##### Gestion de la Plateforme

Les administrateurs disposent d'un tableau de bord complet pour gérer l'ensemble de la marketplace.

**Fonctionnalités clés :**

- **Vue d'ensemble :** Statistiques globales, activité récente, alertes.
- **Gestion des utilisateurs :** Création, modification, suspension de comptes.
- **Gestion des catégories :** Structuration du catalogue avec catégories et sous-catégories.
- **Paramètres système** : Configuration des paramètres globaux de la plateforme.
 ```
🚨 ⚠️ [Pas encore dispo]⚠️
##### Modération des Contenus

Les administrateurs peuvent contrôler et modérer les contenus publiés sur la plateforme.

**Fonctionnalités clés :**

- **Validation des boutiques :** Approbation des nouvelles boutiques avant leur mise en ligne.
- **Modération des produits :** Vérification de la conformité des produits aux règles de la plateforme.
- **Modération des avis :** Contrôle des avis clients pour éviter les contenus inappropriés.
- **Gestion des signalements :** Traitement des signalements émis par les utilisateurs.
```

##### Gestion des Transactions

Les administrateurs peuvent superviser l'ensemble des transactions financières de la plateforme.

**Fonctionnalités clés :**

- **Suivi des commandes :** Vue globale de toutes les commandes en cours.
- **Gestion des paiements :** Supervision des transactions, remboursements, litiges.
```
🚨 ⚠️ [Pas encore dispo]⚠️
- **Configuration des commissions :** Définition des taux de commission par catégorie ou ressourcerie.
- **Rapports financiers :** Génération de rapports détaillés sur les performances financières.
```

## Sécurisation

### Introduction à la Sécurité

La sécurité constitue un pilier fondamental de la plateforme Pivot pour ressourceries. En tant que plateforme traitant des données personnelles, des informations de paiement et gérant des transactions commerciales, PIVOT doit garantir un niveau de sécurité optimal pour protéger à la fois les ressourceries et leurs clients.

Cette section détaille l'approche globale en matière de sécurité, les mesures techniques et organisationnelles mises en place, ainsi que les stratégies de conformité réglementaire, particulièrement adaptées au contexte spécifique des ressourceries.

### Analyse des Risques

Une analyse approfondie des risques a été réalisée pour identifier les menaces potentielles spécifiques à une plateforme de marketplace pour ressourceries :

#### Risques Identifiés

1. **Vol de données personnelles** : Risque d'accès non autorisé aux informations des utilisateurs (clients et ressourceries).

2. **Fraude aux paiements** : Risque de transactions frauduleuses ou de détournement de paiements.

3. **Usurpation d'identité** : Risque de création de faux comptes ressourceries ou d'usurpation de comptes existants.

4. **Attaques par déni de service** : Risque d'indisponibilité de la plateforme suite à des attaques DDoS.

5. **Manipulation des données produits** : Risque de modification non autorisée des informations produits ou des prix.

6. **Injection de code malveillant** : Risque d'exploitation de vulnérabilités pour injecter du code malveillant.

7. **Fuite de données géographiques** : Risque lié à l'exposition des données de localisation des ressourceries et des clients.

#### Évaluation et Priorisation

Chaque risque a été évalué selon sa probabilité d'occurrence et son impact potentiel, permettant d'établir la matrice de risques suivante :

| Risque | Probabilité | Impact | Niveau de Risque |
|--------|-------------|--------|------------------|
| Vol de données personnelles | Moyenne | Élevé | Critique |
| Fraude aux paiements | Moyenne | Élevé | Critique |
| Usurpation d'identité | Moyenne | Élevé | Critique |
| Attaques par déni de service | Faible | Moyen | Modéré |
| Manipulation des données produits | Moyenne | Moyen | Élevé |
| Injection de code malveillant | Faible | Élevé | Élevé |
| Fuite de données géographiques | Moyenne | Moyen | Élevé |

Cette analyse a guidé la priorisation des mesures de sécurité à mettre en œuvre, avec une attention particulière portée aux risques évalués comme "Critiques".

### Mesures de Sécurité Techniques pour le futur de l'application

#### Sécurité de l'Infrastructure

##### Protection du Réseau

- **Pare-feu applicatif (WAF)** : Mise en place d'un pare-feu applicatif pour filtrer le trafic malveillant.
- **Protection DDoS** : Utilisation de services de protection contre les attaques par déni de service.
- **VPN pour l'administration** : Accès administratif uniquement via VPN avec authentification forte.
- **Segmentation réseau** : Séparation des environnements de production, staging et développement.
- **Surveillance du trafic** : Analyse en temps réel des patterns de trafic pour détecter les comportements anormaux.

##### Sécurisation des Serveurs

- **Hardening des serveurs** : Configuration sécurisée des serveurs selon les bonnes pratiques de l'industrie.
- **Mises à jour régulières** : Application systématique des correctifs de sécurité.
- **Principe du moindre privilège** : Attribution des droits minimaux nécessaires pour chaque service.
- **Chiffrement des disques** : Protection des données au repos par chiffrement.
- **Surveillance et alerting** : Détection des comportements anormaux et alertes en temps réel.

##### Sécurité des Bases de Données

- **Chiffrement des données sensibles** : Protection des informations personnelles et financières.
- **Isolation des bases** : Séparation des données critiques dans des bases dédiées.
- **Sauvegardes chiffrées** : Protection des backups par chiffrement.
- **Audit des accès** : Journalisation de toutes les opérations sur les données sensibles.
- **Requêtes paramétrées** : Prévention des injections SQL par l'utilisation systématique de requêtes préparées.

#### Sécurité des Applications

##### Protection contre les Vulnérabilités Web

- **Validation des entrées** : Contrôle strict de toutes les données entrantes.
- **Échappement des sorties** : Protection contre les attaques XSS.
- **Protection CSRF** : Tokens CSRF sur tous les formulaires et requêtes sensibles.
- **En-têtes de sécurité** : Configuration des en-têtes HTTP de sécurité (Content-Security-Policy, X-Frame-Options, etc.).
- **Gestion des sessions** : Sécurisation des cookies et rotation des identifiants de session.

##### Sécurité du Code

- **Analyse statique** : Utilisation d'outils d'analyse statique (PHPStan niveau 5) pour détecter les vulnérabilités.
- **Revue de code** : Processus de revue par les pairs avant toute mise en production.
- **Dépendances sécurisées** : Vérification régulière des vulnérabilités dans les bibliothèques tierces.
- **Tests de pénétration** : Réalisation périodique de tests d'intrusion par des experts externes.
- **Bug bounty** : Programme de récompense pour la découverte de vulnérabilités.

##### Sécurité des API

- **Authentification par token** : Utilisation de JWT ou tokens d'API pour sécuriser les accès.
- **Rate limiting** : Limitation du nombre de requêtes pour prévenir les abus.
- **Validation des schémas** : Contrôle strict de la structure des données échangées.
- **Journalisation des appels** : Traçabilité complète des interactions avec les API.
- **Versionnement** : Gestion claire des versions d'API pour les évolutions sécurisées.

#### Sécurité des Données

##### Protection des Données Personnelles

- **Minimisation des données** : Collecte limitée aux informations strictement nécessaires.
- **Pseudonymisation** : Séparation des identifiants directs des données d'usage lorsque possible.
- **Chiffrement de bout en bout** : Protection des communications sensibles.
- **Durées de conservation limitées** : Suppression automatique des données après la période nécessaire.
- **Contrôles d'accès stricts** : Limitation de l'accès aux données personnelles selon le principe du besoin d'en connaître.

##### Sécurité des Paiements

- **Intégration PCI-DSS** : Conformité avec les normes de sécurité de l'industrie des cartes de paiement.
- **Tokenisation** : Aucun stockage direct des données de carte bancaire.
- **Délégation à Stripe** : Utilisation de Stripe pour le traitement sécurisé des paiements.
- **Authentification 3D Secure** : Vérification supplémentaire pour les transactions à risque.
- **Surveillance des transactions** : Détection des patterns suspects et des tentatives de fraude.

##### Protection des Données Géographiques

- **Anonymisation des données de localisation** : Précision limitée pour les affichages publics.
- **Consentement explicite** : Demande d'autorisation claire pour l'utilisation des données de géolocalisation.
- **Contrôle utilisateur** : Possibilité pour les utilisateurs de masquer leur localisation précise.
- **Sécurisation des API cartographiques** : Restriction des clés API pour les services de cartographie.

### Authentification et Autorisation

#### Système d'Authentification

##### Mécanismes d'Authentification

- **Authentification multi-facteurs** : Option d'activation de la 2FA pour les comptes sensibles (administrateurs, ressourceries).
- **Politiques de mots de passe robustes** : Exigences de complexité et rotation périodique.
- **Verrouillage de compte** : Blocage temporaire après plusieurs tentatives échouées.
- **Détection des connexions suspectes** : Alertes en cas de connexion depuis un nouvel appareil ou une nouvelle localisation.
- **Sessions sécurisées** : Expiration automatique après inactivité et invalidation à la déconnexion.

##### Processus de Vérification pour les Ressourceries

- **Vérification d'identité renforcée** : Processus spécifique pour valider l'authenticité des ressourceries.
- **Validation du SIRET** : Vérification automatique auprès des registres officiels.
- **Confirmation par contact direct** : Appel téléphonique ou courrier postal pour les nouvelles inscriptions.
- **Documents justificatifs** : Demande de pièces administratives pour confirmer le statut de ressourcerie.
- **Période probatoire** : Limitations initiales jusqu'à validation complète du compte.

#### Gestion des Autorisations

##### Modèle de Contrôle d'Accès

- **RBAC (Role-Based Access Control)** : Contrôle d'accès basé sur les rôles (Administrateur, Ressourcerie, Client).
- **Permissions granulaires** : Définition précise des droits pour chaque fonctionnalité.
- **Séparation des privilèges** : Distribution des responsabilités administratives pour limiter les risques.
- **Principe du moindre privilège** : Attribution des droits minimaux nécessaires.
- **Révocation immédiate** : Suppression instantanée des accès en cas de compromission.

##### Sécurisation des Fonctionnalités Sensibles

- **Double validation** : Confirmation supplémentaire pour les opérations critiques.
- **Journalisation des actions** : Traçabilité complète des opérations sensibles.
- **Notifications de sécurité** : Alertes en cas d'actions importantes (modification de profil, changement de mot de passe).
- **Verrouillage temporel** : Délai imposé entre certaines actions sensibles.
- **Vérification contextuelle** : Contrôles supplémentaires basés sur le contexte (appareil, localisation, comportement).

### Conformité et Réglementation

#### Conformité RGPD

##### Principes Fondamentaux

- **Licéité, loyauté et transparence** : Traitement des données conforme aux attentes légitimes des utilisateurs.
- **Limitation des finalités** : Utilisation des données uniquement pour les objectifs annoncés.
- **Minimisation des données** : Collecte limitée aux informations strictement nécessaires.
- **Exactitude** : Mécanismes permettant de maintenir les données à jour.
- **Limitation de conservation** : Suppression des données après la durée nécessaire.
- **Intégrité et confidentialité** : Protection technique et organisationnelle des données.
- **Responsabilité** : Documentation et démonstration de la conformité.

##### Mise en Œuvre Pratique

- **Registre des traitements** : Documentation détaillée de tous les traitements de données.
- **Analyses d'impact (PIA)** : Évaluation des risques pour les traitements sensibles.
- **Politique de confidentialité claire** : Information transparente sur l'utilisation des données.
- **Gestion des consentements** : Recueil et traçabilité des consentements explicites.
- **Procédures d'exercice des droits** : Mécanismes permettant aux utilisateurs d'exercer leurs droits (accès, rectification, effacement, etc.).
- **Notification des violations** : Processus de gestion et de notification des fuites de données.
- **DPO désigné** : Nomination d'un Délégué à la Protection des Données.

#### Autres Réglementations

- **Directive e-Commerce** : Conformité avec les obligations d'information précontractuelle.
- **Loi pour la Confiance dans l'Économie Numérique** : Respect des obligations d'identification des éditeurs.
- **Réglementation sur les cookies** : Gestion conforme des cookies et traceurs.
- **Droit de la consommation** : Respect des droits des consommateurs (rétractation, garanties, etc.).
- **Réglementation sur l'accessibilité** : Conformité avec les normes d'accessibilité RGAA.

### Gestion des Incidents de Sécurité

#### Plan de Réponse aux Incidents

- **Équipe de réponse** : Constitution d'une équipe dédiée avec des rôles et responsabilités clairs.
- **Procédures documentées** : Processus détaillés pour chaque type d'incident.
- **Chaîne d'escalade** : Définition claire des niveaux d'escalade selon la gravité.
- **Communication de crise** : Stratégie de communication interne et externe.
- **Coordination avec les autorités** : Procédures de notification à la CNIL et autres autorités compétentes.

#### Processus de Gestion

1. **Détection** : Systèmes de surveillance pour identifier rapidement les incidents.
2. **Évaluation** : Analyse de l'impact et de l'étendue de l'incident.
3. **Confinement** : Mesures immédiates pour limiter la propagation.
4. **Éradication** : Suppression de la menace et correction des vulnérabilités.
5. **Récupération** : Restauration des systèmes et données affectés.
6. **Retour d'expérience** : Analyse post-incident et amélioration des processus.

#### Continuité d'Activité

- **Plan de continuité** : Stratégies pour maintenir les fonctions critiques en cas d'incident majeur.
- **Sauvegardes régulières** : Politique de backup avec tests de restauration périodiques.
- **Sites de repli** : Infrastructure redondante pour assurer la disponibilité.
- **Procédures de failover** : Basculement automatique vers les systèmes de secours.
- **Tests réguliers** : Exercices de simulation pour valider l'efficacité du plan.

### Formation et Sensibilisation

#### Programme de Sensibilisation

- **Formation initiale** : Sensibilisation obligatoire pour tous les nouveaux utilisateurs ressourceries.
- **Mises à jour régulières** : Sessions périodiques sur les nouvelles menaces et bonnes pratiques.
- **Ressources pédagogiques** : Guide de sécurité adapté au contexte des ressourceries.
- **Alertes de sécurité** : Communication proactive sur les menaces émergentes.
- **Tests de phishing** : Simulations pour évaluer la vigilance des utilisateurs.

#### Contenu Spécifique pour les Ressourceries

- **Sécurisation des comptes** : Bonnes pratiques pour la gestion des mots de passe et l'authentification.
- **Détection des fraudes** : Formation à l'identification des tentatives de fraude.
- **Protection des données clients** : Sensibilisation aux enjeux de confidentialité.
- **Sécurité des terminaux** : Recommandations pour la sécurisation des appareils utilisés.
- **Procédures d'urgence** : Actions à entreprendre en cas de suspicion de compromission.

### Audit et Amélioration Continue

#### Processus d'Audit

- **Audits internes** : Revues périodiques des mesures de sécurité.
- **Audits externes** : Évaluations par des tiers indépendants.
- **Tests de pénétration** : Simulations d'attaques pour identifier les vulnérabilités.
- **Scans de vulnérabilités** : Analyses automatisées régulières.
- **Revue de code** : Inspection du code source pour détecter les failles potentielles.

#### Cycle d'Amélioration

- **Veille sécurité** : Suivi des nouvelles menaces et vulnérabilités.
- **Analyse des incidents** : Retours d'expérience après chaque incident.
- **Benchmarking** : Comparaison avec les meilleures pratiques du secteur.
- **Mise à jour des politiques** : Révision régulière des procédures de sécurité.
- **Adaptation technologique** : Évolution des solutions de sécurité selon l'état de l'art.

### Conclusion sur la Sécurité

La sécurité de la plateforme Pivot pour ressourceries repose sur une approche globale, combinant mesures techniques, procédures organisationnelles et sensibilisation des utilisateurs. Cette stratégie multicouche permet de protéger efficacement les données sensibles des ressourceries et de leurs clients, tout en assurant la conformité avec les réglementations en vigueur.

L'attention particulière portée aux spécificités du secteur des ressourceries, notamment la gestion de produits uniques de seconde main et l'importance de la dimension géographique, a guidé l'élaboration de mesures de sécurité adaptées à ce contexte particulier.

La sécurité étant un processus continu, Pivot s'engage dans une démarche d'amélioration permanente, avec des évaluations régulières et une adaptation constante aux nouvelles menaces et aux évolutions réglementaires. Cette vigilance constante garantit un niveau de protection optimal pour l'ensemble des acteurs de la plateforme.

## Accessibilité

### Conformité au RGAA

L'accessibilité numérique est un enjeu essentiel dans le développement de notre plateforme Pivot. Elle vise à garantir que toute personne, y compris celles en situation de handicap, puisse naviguer, comprendre et interagir avec le site ou l'application.

Dans ce cadre, nous nous appuyons sur plusieurs standards et bonnes pratiques, notamment le **Référentiel Général d'Amélioration de l'Accessibilité (RGAA)** et les **attributs ARIA (Accessible Rich Internet Applications)**.

Le **RGAA** est le référentiel officiel en France qui fixe les règles d'accessibilité à respecter pour les sites et applications. Il repose sur les **principes des WCAG (Web Content Accessibility Guidelines)** et s'articule autour de quatre grands axes :

1. **Perceptible** : Rendre les informations et composants de l'interface visibles et compréhensibles par tous (ex : alternatives textuelles pour les images, sous-titrage des vidéos).
2. **Utilisable** : Assurer que les fonctionnalités sont accessibles avec différents modes d'interaction (clavier, lecteur d'écran, etc.).
3. **Compréhensible** : Rendre l'interface intuitive, avec des messages clairs et des instructions adaptées.
4. **Robuste** : Garantir la compatibilité avec les technologies d'assistance et les navigateurs.

**Actions mises en place :**

- **Structure sémantique HTML5** : Utilisation appropriée des balises sémantiques (header, nav, main, section, article, footer) pour une meilleure compréhension de la structure par les technologies d'assistance.
- **Alternatives textuelles** : Ajout d'attributs alt descriptifs pour toutes les images et icônes informatives.
- **Navigation au clavier** : Implémentation d'une navigation complète au clavier avec focus visible et ordre logique des éléments.
- **Contraste de couleurs** : Respect des ratios de contraste minimaux (4.5:1 pour le texte normal, 3:1 pour le texte de grande taille) pour assurer la lisibilité.
- **Formulaires accessibles** : Association explicite des labels avec les champs de formulaire, messages d'erreur clairs et liés aux champs concernés.
- **Responsive design** : Adaptation de l'interface à différentes tailles d'écran et possibilité de zoom jusqu'à 200% sans perte de contenu.
- **Titres hiérarchisés** : Organisation logique des titres (h1-h6) pour faciliter la navigation et la compréhension de la structure du contenu.

### Utilisation des Attributs ARIA

Les **attributs ARIA (Accessible Rich Internet Applications)** permettent d'améliorer l'accessibilité des composants interactifs complexes (ex : menus déroulants, carrousels, pop-ups) qui ne peuvent pas être rendus accessibles uniquement avec le HTML standard.

**Implémentations spécifiques :**

- **Landmarks ARIA** : Utilisation des rôles landmark (banner, navigation, main, contentinfo) pour définir les zones principales de la page.
- **aria-label et aria-labelledby** : Attribution de noms accessibles aux éléments qui n'ont pas de texte visible ou qui nécessitent une description plus précise.
- **aria-expanded** : Indication de l'état d'expansion des menus déroulants et accordéons.
- **aria-controls** : Association explicite entre un bouton et le contenu qu'il contrôle.
- **aria-live** : Notification des mises à jour dynamiques du contenu (ex : messages de confirmation, alertes).
- **aria-hidden** : Masquage des éléments purement décoratifs ou redondants pour les technologies d'assistance.
- **aria-invalid et aria-describedby** : Indication des erreurs de validation dans les formulaires et association avec les messages d'erreur.

**Exemples concrets d'implémentation :**

```html
<!-- Menu de navigation avec landmarks ARIA -->
<nav aria-label="Menu principal">
  <ul role="menubar">
    <li role="none"><a role="menuitem" href="/">Accueil</a></li>
    <li role="none">
      <button role="menuitem" aria-haspopup="true" aria-expanded="false">Catégories</button>
      <ul role="menu" aria-label="Sous-menu catégories">
        <!-- Items du sous-menu -->
      </ul>
    </li>
  </ul>
</nav>

<!-- Formulaire accessible -->
<form>
  <div>
    <label for="email">Adresse e-mail</label>
    <input type="email" id="email" aria-required="true" aria-describedby="email-error">
    <div id="email-error" class="error" aria-live="polite"></div>
  </div>
  <button type="submit">S'inscrire</button>
</form>
```

**Tests d'accessibilité :**

Pour garantir la conformité de notre plateforme aux standards d'accessibilité, nous avons mis en place une stratégie de tests comprenant :

- **Audits automatisés** : Utilisation d'outils comme Lighthouse, Axe, et Wave pour détecter les problèmes d'accessibilité les plus courants.
- **Tests manuels** : Vérification de la navigation au clavier, des contrastes, de la structure sémantique et des fonctionnalités interactives.
- **Tests avec technologies d'assistance** : Validation avec des lecteurs d'écran (NVDA, JAWS, VoiceOver) pour s'assurer que le contenu est correctement vocalisé.
- **Tests utilisateurs** : Sessions de test avec des personnes en situation de handicap pour identifier les problèmes d'usage réels.

## Recettage

### Tests Unitaires

Les tests unitaires constituent la première ligne de défense pour garantir la qualité et la fiabilité du code. Ils permettent de vérifier le bon fonctionnement de chaque composant de manière isolée.

**Stratégie de tests unitaires :**

- **Framework de test** : PHPUnit pour le backend Laravel et Jest pour le frontend React.
- **Couverture de code** : Objectif de couverture minimum de 80% pour les classes critiques.
- **Mocking** : Utilisation de mocks et de stubs pour isoler les dépendances externes.
- **Tests de modèles** : Validation des relations Eloquent, des accesseurs/mutateurs et des règles de validation.
- **Tests de services** : Vérification de la logique métier encapsulée dans les services.
- **Tests de contrôleurs** : Validation des réponses HTTP et du traitement des requêtes.
- **Tests de composants React** : Vérification du rendu et du comportement des composants UI.

**Exemple de test unitaire pour un modèle :**

```php
public function test_product_belongs_to_store()
{
    $product = Product::factory()->create();
    $this->assertInstanceOf(Store::class, $product->store);
}

public function test_product_has_valid_price()
{
    $product = Product::factory()->create(['price' => -10]);
    $this->assertFalse($product->isValid());
    
    $product->price = 100;
    $this->assertTrue($product->isValid());
}
```

**Exemple de test unitaire pour un composant React :**

```javascript
test('ProductCard displays product information correctly', () => {
  const product = {
    id: 1,
    name: 'Test Product',
    price: 99.99,
    image: 'test.jpg'
  };
  
  const { getByText, getByAltText } = render(<ProductCard product={product} />);
  
  expect(getByText('Test Product')).toBeInTheDocument();
  expect(getByText('99,99 €')).toBeInTheDocument();
  expect(getByAltText('Test Product')).toHaveAttribute('src', expect.stringContaining('test.jpg'));
});
```

### Tests d'Intégration

Les tests d'intégration vérifient que les différents composants du système fonctionnent correctement ensemble. Ils sont essentiels pour détecter les problèmes qui pourraient survenir lors de l'interaction entre les différentes parties de l'application.

**Stratégie de tests d'intégration :**

- **Tests API** : Validation des endpoints API, des formats de réponse et des codes HTTP.
- **Tests de flux** : Vérification des parcours utilisateur complets (ex : inscription, achat, gestion de produits).
- **Tests de base de données** : Validation des migrations, des requêtes complexes et des transactions.
- **Tests d'authentification** : Vérification des mécanismes de connexion, d'inscription et de gestion des permissions.
- **Tests de formulaires** : Validation du traitement des données soumises et des règles de validation.

**Exemple de test d'intégration pour une API :**

```php
public function test_create_product_api()
{
    $user = User::factory()->create(['role' => 'vendor']);
    $store = Store::factory()->create(['user_id' => $user->id]);
    
    $response = $this->actingAs($user)
        ->postJson('/api/products', [
            'name' => 'New Product',
            'description' => 'Product description',
            'price' => 199.99,
            'category_id' => 1,
            'store_id' => $store->id
        ]);
    
    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => ['id', 'name', 'price', 'description', 'category_id', 'store_id']
        ]);
    
    $this->assertDatabaseHas('products', ['name' => 'New Product']);
}
```

### Tests de Performance

Les tests de performance sont cruciaux pour garantir que l'application reste réactive et stable, même sous charge. Ils permettent d'identifier les goulots d'étranglement et d'optimiser les ressources.

**Stratégie de tests de performance :**

- **Tests de charge** : Simulation d'un grand nombre d'utilisateurs simultanés pour évaluer la capacité du système.
- **Tests de stress** : Évaluation des limites du système en augmentant progressivement la charge jusqu'au point de rupture.
- **Tests d'endurance** : Vérification du comportement du système sur une longue période d'utilisation continue.
- **Profilage de base de données** : Identification des requêtes lentes et optimisation des index.
- **Mesures de temps de réponse** : Évaluation des temps de chargement des pages et des API.

**Outils utilisés :**

- **JMeter** : Pour les tests de charge et de stress.
- **Blackfire** : Pour le profilage PHP et l'identification des problèmes de performance.
- **Laravel Telescope** : Pour le monitoring des requêtes, des jobs et des événements.
- **Lighthouse** : Pour l'analyse des performances frontend.

**Résultats et optimisations :**

Suite aux tests de performance, plusieurs optimisations ont été mises en place :

- **Mise en cache** : Implémentation de stratégies de cache pour les données fréquemment accédées.
- **Eager loading** : Optimisation des requêtes Eloquent pour éviter le problème N+1.
- **Pagination** : Limitation du nombre de résultats retournés pour les listes volumineuses.
- **Queues** : Déport des tâches lourdes (génération de rapports, envoi d'emails en masse) dans des jobs asynchrones.
- **Optimisation des assets** : Minification et bundling des fichiers CSS et JavaScript, utilisation de lazy loading pour les images.
- **CDN** : Utilisation d'un CDN pour la distribution des assets statiques.

Ces optimisations ont permis d'améliorer significativement les performances de la plateforme, avec des temps de réponse moyens inférieurs à 200ms pour les requêtes API et un score Lighthouse supérieur à 90 pour les performances frontend. 

## Spécifications Fonctionnelles

### Description des Fonctionnalités

Le projet Pivot comprend plusieurs fonctionnalités essentielles visant à répondre aux besoins des utilisateurs, à faciliter la gestion d'une marketplace multi-vendeurs, et à garantir une expérience optimale et conforme aux standards.

Les fonctionnalités sont divisées en 3 types d'accès :

- Administrateur
- Vendeur
- Client

#### Page d'accueil

La landing page est conçue pour attirer l'attention des utilisateurs dès leur arrivée sur le site. Elle présente une interface simple, claire et ergonomique, avec une navigation fluide vers les autres sections. La page d'accueil est conçue pour être mobile-friendly et conforme aux standards d'accessibilité.

**Scénarios possibles :**

- **Utilisateur non connecté** : Accès à l'information générale, boutons d'inscription et de connexion, présentation des catégories populaires et des produits en vedette.
- **Utilisateur connecté** : Accès à son profil, à ses données et à ses services personnalisés en fonction de son rôle.

#### Inscription et Connexion

Les utilisateurs doivent pouvoir s'inscrire de manière simple en fournissant leurs informations de base. Lors de la création d'un compte, ils doivent également choisir le type d'utilisateur qu'ils souhaitent être : **Client** ou **Ressourcerie**.

Ce choix est essentiel car il conditionne l'accès à des fonctionnalités spécifiques adaptées à leur profil.

Le processus d'inscription est sécurisé, utilisant l'authentification par **Laravel Sanctum** pour garantir la confidentialité des données. Cela assure une gestion fiable des sessions et protège les informations sensibles des utilisateurs.

**Scénarios possibles :**

- **Nouvel utilisateur** :
  1. Remplissage du formulaire d'inscription avec des informations de base (nom, adresse e-mail, mot de passe).
  2. Sélection du type d'utilisateur : **Client** ou **Ressourcerie**.
  3. Validation des données avec vérification des champs obligatoires.
  4. Enregistrement sécurisé des informations dans la base de données.
  5. Redirection vers l'interface correspondant au type d'utilisateur choisi.
- **Utilisateur existant** :
  - Connexion via un formulaire sécurisé en saisissant son e-mail et son mot de passe.
  - Gestion des erreurs telles que les identifiants incorrects ou les comptes inactifs.
  - Authentification par Laravel Sanctum pour une session sécurisée et rapide.

#### Gestion du Compte

L'utilisateur peut gérer ses informations personnelles via un espace dédié, avec des champs spécifiques en fonction de son type d'utilisateur (Client ou Ressourcerie).

**Fonctionnalités clés :**

- **Modification des données personnelles :**
  - **Pour les Clients :** Possibilité de mettre à jour des informations de base telles que le nom, l'adresse e-mail, le mot de passe, l'adresse de livraison, etc.
  - **Pour les Ressourceries :** En plus des informations de base, les ressourceries peuvent renseigner des détails professionnels essentiels :
    - **Informations de la boutique :** Nom, description, logo.
    - **Coordonnées professionnelles :** Adresse, téléphone, email de contact.
    - **Informations bancaires :** Pour recevoir les paiements des ventes.
- **Sécurité des données :** Protection des informations grâce à des mécanismes d'authentification sécurisés.

#### Utilisateur de type Client

##### Parcours d'Achat

Le parcours d'achat est l'une des fonctionnalités centrales pour les clients. Il permet de naviguer dans le catalogue, de sélectionner des produits et de finaliser une commande.

**Fonctionnalités clés :**

- **Navigation dans le catalogue :** Recherche par mots-clés, filtrage par catégories, prix, ressourceries, etc.
- **Fiche produit détaillée :** Informations complètes, photos, avis clients, disponibilité.
- **Panier d'achat :** Ajout/suppression de produits, modification des quantités, calcul automatique du total.
- **Processus de commande :** Saisie des informations de livraison, choix du mode de paiement, récapitulatif avant validation.
- **Paiement sécurisé :** Intégration avec Stripe pour des transactions sécurisées.

##### Suivi des Commandes

Les clients peuvent suivre l'état de leurs commandes et accéder à l'historique de leurs achats.

**Fonctionnalités clés :**

- **Tableau de bord des commandes :** Vue d'ensemble des commandes en cours et passées.
- **Détail des commandes :** Informations complètes sur chaque commande (produits, prix, statut, etc.).
- **Suivi en temps réel :** Mise à jour du statut de la commande (en préparation, expédiée, livrée).
- **Factures et reçus :** Téléchargement des documents relatifs aux achats.

```
🚨 ⚠️ [Pas encore dispo]⚠️
##### Gestion des Avis 

Les clients peuvent laisser des avis sur les produits achetés, contribuant ainsi à la communauté de la marketplace.

**Fonctionnalités clés :**

- **Dépôt d'avis :** Notation et commentaire sur les produits achetés.
- **Modification/suppression d'avis :** Possibilité de mettre à jour ou retirer ses propres avis.
- **Consultation des avis :** Accès aux avis des autres clients pour guider les décisions d'achat.~~
```

#### Utilisateur de type Ressourcerie

##### Gestion de la Boutique

Les ressourceries peuvent créer et personnaliser leur espace de vente sur la marketplace.

**Fonctionnalités clés :**

- **Création de boutique :** Configuration initiale avec nom, description, logo, etc.
 ```
🚨 ⚠️ [Pas encore dispo]⚠️
- **Personnalisation :** Adaptation de l'apparence selon l'identité visuelle de la ressourcerie.
- **Paramètres de la boutique :** Définition des politiques de livraison, de retour, etc.
 ```
- **Statistiques :** Suivi des performances (vues, ventes, taux de conversion).

##### Gestion des Produits

Les ressourceries peuvent gérer leur catalogue de produits de manière autonome.

**Fonctionnalités clés :**

- **Ajout de produits :** Création de fiches produits avec informations détaillées, photos, prix, etc.
- **Modification/suppression :** Mise à jour ou retrait de produits du catalogue.
- **Gestion des stocks :** Suivi des quantités disponibles, alertes de stock bas.
 ```
🚨 ⚠️ [Pas encore dispo]⚠️
- **Promotions :** Création d'offres spéciales, réductions temporaires, etc.
```

##### Gestion des Commandes

Les ressourceries peuvent suivre et traiter les commandes concernant leurs produits.

**Fonctionnalités clés :**

- **Tableau de bord des commandes :** Vue d'ensemble des commandes à traiter.
- **Traitement des commandes :** Changement de statut, préparation, expédition.
 ```
🚨 ⚠️ [Pas encore dispo]⚠️
- **Communication avec les clients :** Échange de messages concernant les commandes.
- **Gestion des retours :** Traitement des demandes de retour ou d'échange.
```

##### Gestion des Paiements

Les ressourceries peuvent suivre leurs revenus et gérer leurs informations financières.

**Fonctionnalités clés :**

- **Tableau de bord financier :** Vue d'ensemble des ventes, commissions, revenus nets.
- **Historique des transactions :** Détail des paiements reçus et des commissions prélevées.
- **Configuration des paiements :** Gestion des informations bancaires pour recevoir les paiements.
 ```
🚨 ⚠️ [Pas encore dispo]⚠️
- **Factures et documents comptables :** Génération et téléchargement des documents nécessaires.
```

#### Utilisateur de type Administrateur

##### Gestion de la Plateforme

Les administrateurs disposent d'un tableau de bord complet pour gérer l'ensemble de la marketplace.

**Fonctionnalités clés :**

- **Vue d'ensemble :** Statistiques globales, activité récente, alertes.
- **Gestion des utilisateurs :** Création, modification, suspension de comptes.
- **Gestion des catégories :** Structuration du catalogue avec catégories et sous-catégories.
- **Paramètres système** : Configuration des paramètres globaux de la plateforme.
 ```
🚨 ⚠️ [Pas encore dispo]⚠️
##### Modération des Contenus

Les administrateurs peuvent contrôler et modérer les contenus publiés sur la plateforme.

**Fonctionnalités clés :**

- **Validation des boutiques :** Approbation des nouvelles boutiques avant leur mise en ligne.
- **Modération des produits :** Vérification de la conformité des produits aux règles de la plateforme.
- **Modération des avis :** Contrôle des avis clients pour éviter les contenus inappropriés.
- **Gestion des signalements :** Traitement des signalements émis par les utilisateurs.
```

##### Gestion des Transactions

Les administrateurs peuvent superviser l'ensemble des transactions financières de la plateforme.

**Fonctionnalités clés :**

- **Suivi des commandes :** Vue globale de toutes les commandes en cours.
- **Gestion des paiements :** Supervision des transactions, remboursements, litiges.
```
🚨 ⚠️ [Pas encore dispo]⚠️
- **Configuration des commissions :** Définition des taux de commission par catégorie ou ressourcerie.
- **Rapports financiers :** Génération de rapports détaillés sur les performances financières.
```

## Spécifications Techniques

### API et Interfaces

#### API Internes

Les API internes sont utilisées par l'application frontend pour communiquer avec le backend. Elles sont implémentées via Inertia.js, qui permet une communication transparente entre Laravel et React.

##### Endpoints Principaux

1. **Authentification**
   - `POST /login` : Authentification utilisateur
   - `POST /register` : Création de compte
   - `POST /logout` : Déconnexion
   - `POST /forgot-password` : Demande de réinitialisation de mot de passe
   - `POST /reset-password` : Réinitialisation de mot de passe

2. **Utilisateurs**
   - `GET /user/profile` : Récupération du profil utilisateur
   - `PUT /user/profile` : Mise à jour du profil
   - `GET /user/orders` : Liste des commandes de l'utilisateur
   - `GET /user/favorites` : Liste des produits favoris

3. **Ressourceries**
   - `GET /ressourceries` : Liste des ressourceries
   - `GET /ressourceries/{id}` : Détails d'une ressourcerie
   - `GET /ressourceries/nearby` : Ressourceries à proximité
   - `POST /ressourceries` : Création d'une ressourcerie (admin/ressourcerie)
   - `PUT /ressourceries/{id}` : Mise à jour d'une ressourcerie (admin/ressourcerie)

4. **Produits**
   - `GET /products` : Liste des produits avec filtres
   - `GET /products/{id}` : Détails d'un produit
   - `GET /products/search` : Recherche de produits
   - `POST /products` : Ajout d'un produit (ressourcerie)
   - `PUT /products/{id}` : Mise à jour d'un produit (ressourcerie)
   - `DELETE /products/{id}` : Suppression d'un produit (ressourcerie)

5. **Catégories**
   - `GET /categories` : Liste des catégories
   - `GET /categories/{id}/products` : Produits d'une catégorie

6. **Panier et Commandes**
   - `GET /cart` : Contenu du panier
   - `POST /cart/add` : Ajout au panier
   - `PUT /cart/update` : Mise à jour du panier
   - `DELETE /cart/remove` : Suppression d'un élément du panier
   - `POST /orders` : Création d'une commande
   - `GET /orders/{id}` : Détails d'une commande
   - `PUT /orders/{id}/status` : Mise à jour du statut (ressourcerie/admin)

7. **Paiements**
   - `POST /payments/intent` : Création d'une intention de paiement
   - `POST /payments/confirm` : Confirmation d'un paiement
   - `GET /payments/{id}` : Détails d'un paiement

8. **Administration**
   - `GET /admin/dashboard` : Tableau de bord administrateur
   - `GET /admin/users` : Gestion des utilisateurs
   - `GET /admin/ressourceries` : Gestion des ressourceries
   - `GET /admin/orders` : Gestion des commandes
   - `GET /admin/reports` : Rapports et statistiques

#### Intégrations Externes

##### Stripe (Paiements)

L'intégration avec Stripe permet de gérer les paiements en ligne de manière sécurisée :

- **Création d'intention de paiement** : Génération d'une intention de paiement côté serveur
- **Confirmation de paiement** : Traitement de la confirmation après paiement réussi
- **Webhooks** : Gestion des événements Stripe (paiement réussi, échec, remboursement)
- **Gestion des remboursements** : Processus de remboursement en cas d'annulation

##### Algolia (Recherche)

L'intégration avec Algolia offre une expérience de recherche rapide et pertinente :

- **Indexation des produits** : Synchronisation automatique du catalogue
- **Recherche instantanée** : Résultats en temps réel pendant la saisie
- **Filtres avancés** : Recherche par catégorie, prix, état, ressourcerie, etc.
- **Géolocalisation** : Recherche de produits par proximité géographique

##### Leaflet (Cartographie)

L'intégration avec Leaflet permet d'afficher des cartes interactives :

- **Affichage des ressourceries** : Visualisation des ressourceries sur une carte
- **Calcul d'itinéraires** : Directions vers les points de retrait
- **Filtrage géographique** : Recherche de produits dans un rayon défini
- **Clustering** : Regroupement des marqueurs pour une meilleure lisibilité

##### Services de Mailing

L'intégration avec des services d'emailing (Mailgun, SendGrid) permet d'envoyer des notifications :

- **Emails transactionnels** : Confirmation de commande, notification de préparation, etc.
- **Emails marketing** : Newsletters, promotions, événements des ressourceries
- **Alertes système** : Notifications administratives et techniques

### Sécurité

#### Authentification et Autorisation

##### Système d'Authentification

- **Laravel Sanctum** : Gestion de l'authentification avec support des tokens API
- **Protection contre les attaques** : Limitation des tentatives de connexion, protection CSRF
- **Sessions sécurisées** : Cookies HTTP-only, expiration automatique
- **Authentification à deux facteurs** : Option pour renforcer la sécurité des comptes

##### Gestion des Permissions

- **Système de rôles** : Administrateur, Ressourcerie, Client
- **Permissions granulaires** : Contrôle précis des accès aux fonctionnalités
- **Middleware d'autorisation** : Vérification des permissions à chaque requête
- **Policies Laravel** : Définition des règles d'autorisation par ressource

#### Protection des Données

##### Sécurisation des Données Sensibles

- **Chiffrement des données** : Protection des informations sensibles (coordonnées, paiements)
- **Hachage des mots de passe** : Utilisation de l'algorithme Bcrypt
- **Masquage des attributs sensibles** : Protection contre les fuites accidentelles
- **Validation stricte** : Contrôle rigoureux des entrées utilisateur

##### Conformité RGPD

- **Consentement explicite** : Recueil du consentement pour la collecte de données
- **Droit à l'oubli** : Mécanisme de suppression des données personnelles
- **Portabilité des données** : Export des données utilisateur au format standard
- **Politique de confidentialité** : Documentation claire des pratiques de traitement des données
- **Journalisation des accès** : Traçabilité des accès aux données sensibles

#### Sécurité des Transactions

- **Protocole HTTPS** : Communication chiffrée entre client et serveur
- **Conformité PCI DSS** : Respect des normes pour le traitement des paiements
- **Tokenisation des paiements** : Aucune donnée de carte bancaire stockée sur les serveurs
- **Signatures numériques** : Vérification de l'intégrité des transactions
- **Journalisation des transactions** : Traçabilité complète des opérations financières

#### Protection contre les Attaques Courantes

- **Injection SQL** : Utilisation de requêtes préparées et de l'ORM Eloquent
- **XSS (Cross-Site Scripting)** : Échappement automatique des sorties
- **CSRF (Cross-Site Request Forgery)** : Tokens CSRF sur tous les formulaires
- **Clickjacking** : En-têtes X-Frame-Options appropriés
- **CORS (Cross-Origin Resource Sharing)** : Configuration restrictive des origines autorisées

### Performance et Optimisation

#### Stratégies de Cache

- **Cache de requêtes** : Mise en cache des requêtes fréquentes et coûteuses
- **Cache de page** : Mise en cache des pages statiques ou semi-dynamiques
- **Cache d'application** : Stockage des données de configuration et des résultats de calculs complexes
- **Cache de session** : Optimisation du stockage des sessions utilisateur
- **Cache distribué** : Utilisation de Redis pour le cache partagé entre instances

#### Optimisation des Requêtes

- **Eager Loading** : Chargement anticipé des relations pour éviter le problème N+1
- **Indexation** : Création d'index sur les colonnes fréquemment utilisées dans les requêtes
- **Pagination** : Limitation du nombre de résultats par page
- **Requêtes optimisées** : Utilisation de requêtes SQL efficaces et ciblées
- **Query Builder** : Construction de requêtes complexes de manière optimisée

#### Optimisation Frontend

- **Bundling et Minification** : Réduction de la taille des assets JavaScript et CSS
- **Lazy Loading** : Chargement différé des images et composants non critiques
- **Code Splitting** : Division du code JavaScript en chunks chargés à la demande
- **Optimisation des images** : Compression, redimensionnement et formats modernes (WebP)
- **Mise en cache côté client** : Utilisation appropriée des en-têtes de cache HTTP

#### Scalabilité

- **Architecture stateless** : Conception permettant la répartition de charge entre serveurs
- **Files d'attente** : Traitement asynchrone des tâches intensives (emails, rapports)
- **Microservices** : Isolation de certaines fonctionnalités critiques (paiement, recherche)
- **Réplication de base de données** : Séparation lecture/écriture pour les charges importantes
- **Auto-scaling** : Configuration permettant l'ajout dynamique de ressources en cas de pic de charge

## Prototype

### Description

Le prototype est une représentation simplifiée de la plateforme Pivot pour ressourceries, conçue pour tester les fonctionnalités principales et valider les concepts avant de les implémenter en production.

### Fonctionnalités Testées

- **Authentification** : Test de l'inscription, de la connexion et de la gestion des profils.
- **Gestion des produits** : Ajout, modification et suppression de produits.
- **Gestion des commandes** : Création, suivi et gestion des commandes.
- **Gestion des paiements** : Intégration avec Stripe pour les transactions sécurisées.
- **Accessibilité** : Test des fonctionnalités d'accessibilité pour s'assurer que la plateforme est utilisable par tous.

### Technologies Utilisées

- **React** : Pour le frontend.
- **Laravel** : Pour le backend.
- **Stripe** : Pour les paiements.
- **Tailwind CSS** : Pour le style.
- **Inertia.js** : Pour la navigation sans rechargement de page.

### Déploiement

Le prototype est déployé sur un serveur de développement pour permettre aux développeurs de tester les fonctionnalités avant de les intégrer dans la version finale.

### Résultats

Les tests réalisés lors de la phase de prototype ont permis de valider les concepts et d'identifier les points de renforcement nécessaires avant de passer à l'implémentation en production.

### Conclusion

Le prototype a fourni une base solide pour la mise en œuvre de la plateforme Pivot pour ressourceries, permettant de tester les fonctionnalités principales et d'obtenir des retours utiles avant de passer à l'implémentation en production.

## Conclusion

### Synthèse du Projet PIVOT Marketplace

Le dossier technique présenté dans ce document détaille la conception, le développement et la mise en œuvre de PIVOT Marketplace, la première plateforme de click-and-collect dédiée aux ressourceries en France. Ce projet innovant répond à un besoin concret du secteur de l'économie circulaire, en offrant aux structures de réemploi un outil numérique adapté à leurs spécificités et à leurs contraintes.

L'analyse approfondie du contexte a permis d'identifier les défis uniques auxquels font face les ressourceries : gestion de produits de seconde main souvent uniques, besoin de visibilité locale, valorisation de l'impact environnemental positif, et nécessité d'une solution technique accessible à des structures aux ressources limitées. Ces défis ont guidé l'ensemble des choix techniques et fonctionnels du projet.

### Points Forts du Projet

#### Innovation Technique au Service de l'Économie Circulaire

PIVOT Marketplace se distingue par son approche technique innovante, combinant des technologies modernes (Laravel, Inertia.js, React) avec une architecture robuste et évolutive. Cette combinaison permet d'offrir une expérience utilisateur fluide et intuitive, tout en garantissant la performance, la sécurité et la maintenabilité de la plateforme.

L'intégration de fonctionnalités spécifiques au contexte des ressourceries, comme la gestion de produits uniques, la géolocalisation avancée et le calcul d'impact environnemental, démontre la capacité du projet à répondre précisément aux besoins du secteur.

#### Approche Centrée Utilisateur

La conception de PIVOT Marketplace a été guidée par une compréhension approfondie des besoins des deux publics cibles : les ressourceries et leurs clients. Cette approche centrée utilisateur se traduit par des interfaces intuitives, des parcours d'utilisation simplifiés et des fonctionnalités adaptées aux pratiques réelles du terrain.

Les tests utilisateurs réalisés lors de la phase de prototype ont confirmé la pertinence de cette approche, avec des retours positifs sur la facilité d'utilisation et l'adéquation aux besoins quotidiens des ressourceries.

#### Impact Social et Environnemental

Au-delà de ses qualités techniques, PIVOT Marketplace se distingue par sa contribution positive à la transition écologique et à l'économie sociale et solidaire. En facilitant l'accès aux produits de seconde main, la plateforme favorise la réduction des déchets, l'allongement de la durée de vie des objets et la sensibilisation du grand public aux enjeux de la consommation responsable.

La mise en valeur de l'impact environnemental des achats (CO2 évité, déchets détournés) constitue un levier de sensibilisation puissant, transformant chaque transaction en un geste concret pour la planète.

### Perspectives d'Évolution

Le développement de PIVOT Marketplace s'inscrit dans une vision à long terme, avec plusieurs axes d'évolution identifiés :

1. **Élargissement du réseau** : Intégration progressive de nouvelles ressourceries sur l'ensemble du territoire français, pour créer un maillage dense et accessible au plus grand nombre.

2. **Enrichissement fonctionnel** : Développement de nouvelles fonctionnalités basées sur les retours d'usage, comme un système de réservation avancé, des outils de gestion de stock plus sophistiqués, ou des fonctionnalités communautaires.

3. **Interopérabilité** : Création d'API permettant l'intégration avec d'autres outils utilisés par les ressourceries (logiciels de caisse, systèmes de gestion, etc.).

4. **Analyse de données** : Exploitation des données anonymisées pour générer des insights sur les tendances de consommation responsable et mesurer l'impact global du réemploi.

5. **Internationalisation** : À plus long terme, adaptation de la plateforme pour d'autres marchés européens, en tenant compte des spécificités locales.

### Mot de la Fin

PIVOT Marketplace représente bien plus qu'un simple projet technique : c'est une contribution concrète à la transition écologique et à l'économie circulaire. En offrant aux ressourceries un outil numérique adapté à leurs besoins, la plateforme leur permet de développer leur activité, d'élargir leur clientèle et de maximiser leur impact positif sur l'environnement.

La qualité technique du projet, sa pertinence fonctionnelle et son potentiel d'évolution en font une solution durable et évolutive, capable d'accompagner le développement du secteur des ressourceries dans les années à venir.

PIVOT Marketplace incarne ainsi la rencontre réussie entre innovation technologique et engagement environnemental, démontrant que le numérique peut être un puissant levier de transformation écologique lorsqu'il est mis au service de l'économie circulaire et solidaire. 