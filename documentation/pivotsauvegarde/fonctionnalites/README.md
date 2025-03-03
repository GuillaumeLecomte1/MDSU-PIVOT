# Fonctionnalités Développées

## Vue d'Ensemble

Le POC PIVOT Marketplace implémente plusieurs fonctionnalités clés qui répondent aux besoins essentiels des ressourceries et de leurs clients. Cette section détaille les fonctionnalités actuellement disponibles dans le prototype, leur niveau d'implémentation et les choix techniques associés.

## 1. Système d'Authentification et Gestion des Utilisateurs

### Fonctionnalités implémentées

- **Inscription et connexion** : Système complet permettant aux utilisateurs de créer un compte et de s'authentifier.
- **Vérification d'email** : Processus de vérification d'adresse email pour sécuriser les comptes.
- **Gestion des profils** : Interface permettant aux utilisateurs de modifier leurs informations personnelles.
- **Système de rôles** : Trois rôles distincts avec des permissions différentes :
  - Administrateur : Accès complet à toutes les fonctionnalités
  - Ressourcerie : Gestion de ses produits et commandes
  - Client : Navigation, achat et suivi de commandes

### Implémentation technique

- Utilisation du système d'authentification de Laravel avec personnalisation pour les besoins spécifiques.
- Stockage sécurisé des mots de passe avec hachage bcrypt.
- Middleware d'authentification pour protéger les routes sensibles.
- Sessions sécurisées avec expiration automatique.

## 2. Gestion des Produits

### Fonctionnalités implémentées

- **Ajout de produits** : Interface permettant aux ressourceries d'ajouter des produits avec leurs caractéristiques spécifiques.
- **Gestion des photos** : Possibilité d'ajouter jusqu'à 3 photos par produit avec prévisualisation.
- **Catégorisation** : Système hiérarchique de catégories et sous-catégories.
- **Gestion des stocks** : Suivi basique de la disponibilité des produits.
- **Modification et suppression** : Outils pour mettre à jour ou retirer des produits du catalogue.

### Implémentation technique

- Formulaires de saisie avec validation côté client et serveur.
- Stockage optimisé des images avec redimensionnement automatique.
- Utilisation d'Eloquent ORM pour les relations entre produits et catégories.
- Soft deletes pour conserver l'historique des produits supprimés.

## 3. Recherche et Navigation

### Fonctionnalités implémentées

- **Catalogue de produits** : Affichage paginé des produits disponibles.
- **Recherche par mots-clés** : Moteur de recherche textuelle dans les titres et descriptions.
- **Filtrage** : Possibilité de filtrer par catégorie, prix, état et ressourcerie.
- **Géolocalisation** : Carte interactive des ressourceries partenaires.
- **Fiches produits** : Pages détaillées pour chaque article avec caractéristiques et photos.

### Implémentation technique

- Requêtes SQL optimisées pour les recherches avec indexation.
- Utilisation de Leaflet pour l'affichage cartographique.
- Composants React réutilisables pour l'interface utilisateur.
- Pagination côté serveur pour optimiser les performances.

## 4. Processus de Click-and-Collect

### Fonctionnalités implémentées

- **Panier d'achat** : Ajout/suppression de produits et calcul du total.
- **Sélection de créneau** : Choix de la date et de l'heure de retrait.
- **Récapitulatif de commande** : Vérification des informations avant confirmation.
- **Confirmation par email** : Envoi automatique d'un récapitulatif de commande.
- **Suivi de commande** : Interface simple pour suivre l'état des commandes.

### Implémentation technique

- Gestion du panier en session avec persistance en base de données.
- Système de réservation de créneaux avec vérification de disponibilité.
- Utilisation de files d'attente (queues) pour l'envoi d'emails asynchrones.
- Statuts de commande avec transitions d'état contrôlées.

## 5. Calcul d'Impact Environnemental

### Fonctionnalités implémentées

- **Estimation simplifiée** : Calcul approximatif de l'impact environnemental des achats d'occasion.
- **Métriques affichées** : CO2 évité, eau économisée, déchets non produits.
- **Compteurs personnels** : Suivi de l'impact cumulé des achats d'un utilisateur.
- **Impact global** : Affichage des économies réalisées par l'ensemble de la plateforme.

### Implémentation technique

- Algorithme basé sur des coefficients moyens par catégorie de produit.
- Stockage des métriques dans la base de données pour agrégation.
- Mise à jour en temps réel des compteurs lors de la finalisation des commandes.

## 6. Interface d'Administration

### Fonctionnalités implémentées

- **Tableau de bord** : Vue d'ensemble des statistiques clés.
- **Gestion des utilisateurs** : Création, modification et désactivation de comptes.
- **Supervision des ressourceries** : Suivi des activités et validation des inscriptions.
- **Rapports basiques** : Génération de rapports sur les ventes et les produits.

### Implémentation technique

- Interface administrateur sécurisée avec contrôle d'accès strict.
- Utilisation de composants React pour les tableaux de bord interactifs.
- Requêtes agrégées pour la génération de statistiques.
- Export de données au format CSV pour les rapports.

## 7. Espace Ressourcerie

### Fonctionnalités implémentées

- **Profil personnalisable** : Informations de contact, description, logo et horaires.
- **Gestion du catalogue** : Interface dédiée pour gérer les produits.
- **Suivi des commandes** : Tableau de bord des commandes à préparer et historique.
- **Statistiques basiques** : Aperçu des ventes et des produits les plus populaires.

### Implémentation technique

- Espaces cloisonnés avec accès limité aux données propres à chaque ressourcerie.
- Formulaires spécifiques pour la gestion du profil ressourcerie.
- Filtres de visualisation des commandes par statut.
- Calculs statistiques optimisés pour ne pas surcharger la base de données.

## Limites Actuelles et Évolutions Prévues

### Limites identifiées

- **Gestion des photos** : Nombre limité de photos et absence de zoom/galerie avancée.
- **Paiement** : Absence d'intégration avec des systèmes de paiement en ligne.
- **Statistiques** : Analyses limitées sans visualisations avancées.
- **Notifications** : Système basique par email uniquement.
- **Mobile** : Interface responsive mais sans application native.

### Évolutions envisagées

- Intégration d'un système de paiement en ligne (Stripe).
- Amélioration de la gestion des médias (plus de photos, vidéos).
- Développement de fonctionnalités communautaires (avis, notation).
- Système avancé de notifications (email, SMS, push).
- Application mobile native ou PWA.
- Intégration avec les outils existants des ressourceries.

Cette documentation des fonctionnalités reflète l'état actuel du POC PIVOT et sert de base pour planifier les développements futurs en fonction des retours utilisateurs et des priorités identifiées. 