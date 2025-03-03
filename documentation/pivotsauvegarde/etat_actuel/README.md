# État Actuel du POC PIVOT

## Vue d'Ensemble

Le Proof of Concept (POC) de PIVOT représente la première phase de développement du projet. Il s'agit d'une version fonctionnelle mais limitée de la plateforme, permettant de valider les concepts fondamentaux et de recueillir des retours utilisateurs.

Actuellement, le POC est déployé auprès d'un panel de 5 ressourceries partenaires qui l'utilisent en conditions réelles, permettant ainsi de tester les fonctionnalités et d'identifier les axes d'amélioration.

## Fonctionnalités Actuellement Implémentées

### 1. Système d'Authentification

- **Inscription et connexion** des utilisateurs avec différents rôles (administrateur, ressourcerie, client)
- **Gestion des profils** avec informations de base (nom, email, téléphone, adresse)
- **Système de vérification d'email** pour sécuriser les inscriptions
- **Gestion des rôles et permissions** adaptée aux besoins spécifiques des ressourceries

### 2. Gestion des Produits

- **Ajout de produits** avec caractéristiques spécifiques aux objets de seconde main (état, dimensions, couleur, marque, etc.)
- **Upload de photos** pour illustrer les produits (limité à 3 photos par produit dans le POC)
- **Catégorisation** des produits selon une arborescence adaptée aux ressourceries
- **Gestion basique des stocks** (disponible/indisponible/vendu)
- **Édition et suppression** des produits

### 3. Recherche et Navigation

- **Catalogue produits** avec affichage en grille ou en liste
- **Recherche par mots-clés** dans les titres et descriptions des produits
- **Filtrage par catégorie** et par ressourcerie
- **Géolocalisation des ressourceries** sur une carte interactive
- **Fiches produits détaillées** avec toutes les informations pertinentes

### 4. Processus de Click-and-Collect

- **Panier d'achat** permettant d'ajouter/supprimer des produits
- **Sélection du créneau de retrait** sur un calendrier simplifié
- **Récapitulatif de commande** avant validation
- **Confirmation par email** après réservation
- **Suivi basique des commandes** (en attente, confirmée, retirée)

### 5. Calcul d'Impact Environnemental

- **Estimation simplifiée** de l'impact positif des achats de seconde main
- **Affichage des métriques environnementales** (CO2 évité, déchets détournés)
- **Compteur personnel** d'impact pour chaque client
- **Compteur global** pour la plateforme

## Tests et Validation

Le POC a été testé auprès d'un panel de 5 ressourceries partenaires, permettant de :

- **Valider l'architecture technique** et son adéquation avec les besoins des ressourceries
- **Recueillir des retours utilisateurs** sur l'expérience globale et les fonctionnalités spécifiques
- **Identifier les points forts** de la plateforme à conserver et développer
- **Repérer les axes d'amélioration** pour les futures versions

Les premiers retours sont globalement positifs, avec une appréciation particulière pour l'interface intuitive et la valorisation de l'impact environnemental.

## Limites Actuelles

Le POC présente certaines limitations inhérentes à sa nature de prototype :

1. **Gestion des photos limitée** : Maximum 3 photos par produit, sans optimisation automatique des images
2. **Absence d'intégration** avec les outils existants des ressourceries (logiciels de caisse, etc.)
3. **Statistiques simplifiées** : Tableaux de bord basiques sans analyses avancées
4. **Interface utilisateur perfectible** : Certains parcours utilisateurs peuvent être optimisés
5. **Fonctionnalités communautaires absentes** : Pas de système d'avis ou d'interactions entre utilisateurs

Ces limitations ont été identifiées et documentées pour être adressées dans les prochaines phases de développement, en fonction des priorités établies avec les ressourceries partenaires. 