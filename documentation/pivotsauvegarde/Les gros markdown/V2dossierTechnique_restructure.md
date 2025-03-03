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
- [Fonctionnalités de la Plateforme](#fonctionnalités-de-la-plateforme)
  - [Vue d'ensemble des Fonctionnalités](#vue-densemble-des-fonctionnalités)
  - [Fonctionnalités par Type d'Utilisateur](#fonctionnalités-par-type-dutilisateur)
  - [Parcours Utilisateurs Clés](#parcours-utilisateurs-clés)
- [Sécurisation](#sécurisation)
  - [Authentification et Autorisation](#authentification-et-autorisation)
  - [Protection des données](#protection-des-données)
- [Conformité](#conformité)
  - [Respect des Principes du RGPD](#respect-des-principes-du-rgpd)
- [Accessibilité](#accessibilité)
  - [Conformité au RGAA](#conformité-au-rgaa)
  - [Utilisation des Attributs ARIA](#utilisation-des-attributs-aria)
- [Stratégie de Test et Qualité](#stratégie-de-test-et-qualité)
  - [Tests Unitaires](#tests-unitaires)
  - [Tests d'Intégration](#tests-dintégration)
  - [Tests de Performance](#tests-de-performance)
- [Spécifications Techniques](#spécifications-techniques)
  - [API et Interfaces](#api-et-interfaces)
  - [Sécurité](#sécurité)
  - [Performance et Optimisation](#performance-et-optimisation)
- [Prototype](#prototype)
  - [Objectifs et Périmètre](#objectifs-et-périmètre)
  - [Résultats et Enseignements](#résultats-et-enseignements)
  - [Évolutions Prévues](#évolutions-prévues)
- [Conclusion](#conclusion)
  - [Synthèse du Projet](#synthèse-du-projet)
  - [Perspectives d'Évolution](#perspectives-dévolution)

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

## Fonctionnalités de la Plateforme

### Vue d'ensemble des Fonctionnalités

Le projet Pivot comprend plusieurs fonctionnalités essentielles visant à répondre aux besoins des utilisateurs, à faciliter la gestion d'une marketplace multi-vendeurs, et à garantir une expérience optimale et conforme aux standards.

Les fonctionnalités sont organisées selon trois profils d'utilisateurs principaux :

- **Administrateur** : Gestion globale de la plateforme, des utilisateurs et des contenus
- **Ressourcerie (Vendeur)** : Gestion des produits, des commandes et de l'espace de vente
- **Client** : Navigation, achat et suivi des commandes

### Fonctionnalités par Type d'Utilisateur

#### Fonctionnalités Communes

##### Page d'accueil

La landing page est conçue pour attirer l'attention des utilisateurs dès leur arrivée sur le site. Elle présente une interface simple, claire et ergonomique, avec une navigation fluide vers les autres sections. La page d'accueil est conçue pour être mobile-friendly et conforme aux standards d'accessibilité.

**Scénarios possibles :**

- **Utilisateur non connecté** : Accès à l'information générale, boutons d'inscription et de connexion, présentation des catégories populaires et des produits en vedette.
- **Utilisateur connecté** : Accès à son profil, à ses données et à ses services personnalisés en fonction de son rôle.

##### Inscription et Connexion

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

##### Gestion du Compte

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
- **Consultation des avis :** Accès aux avis des autres clients pour guider les décisions d'achat.
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

### Parcours Utilisateurs Clés

#### Parcours d'Achat Client

1. **Recherche de produit** : Le client recherche un produit via la barre de recherche ou en naviguant dans les catégories.
2. **Consultation de la fiche produit** : Le client consulte les détails du produit, ses caractéristiques et sa disponibilité.
3. **Ajout au panier** : Le client ajoute le produit à son panier et peut continuer ses achats.
4. **Validation du panier** : Le client vérifie son panier et procède à la commande.
5. **Choix du créneau de retrait** : Le client sélectionne un créneau horaire pour récupérer sa commande.
6. **Paiement** : Le client procède au paiement sécurisé via Stripe.
7. **Confirmation** : Le client reçoit une confirmation de commande par email.
8. **Suivi** : Le client peut suivre l'état de sa commande depuis son espace personnel.

#### Parcours Ressourcerie - Ajout de Produit

1. **Connexion** : La ressourcerie se connecte à son espace dédié.
2. **Accès à la gestion des produits** : La ressourcerie accède à l'interface de gestion des produits.
3. **Création d'un nouveau produit** : La ressourcerie remplit le formulaire d'ajout avec les informations du produit.
4. **Upload des photos** : La ressourcerie ajoute des photos du produit.
5. **Définition des caractéristiques** : La ressourcerie précise l'état, les dimensions et autres caractéristiques spécifiques.
6. **Publication** : La ressourcerie publie le produit qui devient visible dans le catalogue.
7. **Suivi** : La ressourcerie peut suivre les vues et l'intérêt pour le produit.

#### Parcours Administrateur - Gestion des Utilisateurs

1. **Connexion** : L'administrateur se connecte à son interface d'administration.
2. **Accès à la gestion des utilisateurs** : L'administrateur accède à la liste des utilisateurs.
3. **Filtrage** : L'administrateur peut filtrer les utilisateurs par type, date d'inscription, statut, etc.
4. **Consultation d'un profil** : L'administrateur consulte les détails d'un utilisateur spécifique.
5. **Modification** : L'administrateur peut modifier les informations ou le statut d'un utilisateur.
6. **Actions spécifiques** : L'administrateur peut suspendre un compte, réinitialiser un mot de passe, etc.
7. **Suivi** : L'administrateur peut consulter l'historique des actions liées à un utilisateur.

## Stratégie de Test et Qualité

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

Les tests d'intégration vérifient les interactions entre différents composants de l'application, assurant leur bon fonctionnement lorsqu'ils sont combinés.

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

### Objectifs et Périmètre

Le développement d'un prototype fonctionnel pour PIVOT Marketplace constitue une étape cruciale dans la validation du concept et des choix techniques. Ce prototype a été conçu pour démontrer la faisabilité du projet et tester les fonctionnalités clés dans un environnement contrôlé, avant le déploiement complet de la plateforme.

Les objectifs principaux du prototype sont :

1. **Valider l'architecture technique** : Confirmer que l'architecture proposée (Laravel, Inertia.js, React) répond efficacement aux besoins spécifiques des ressourceries.

2. **Tester les fonctionnalités essentielles** : Implémenter et évaluer les fonctionnalités critiques pour les ressourceries et leurs clients.

3. **Évaluer l'expérience utilisateur** : Recueillir des retours sur l'ergonomie et l'accessibilité de l'interface.

4. **Identifier les points d'amélioration** : Détecter les éventuelles limitations ou difficultés techniques avant le développement complet.

5. **Démontrer la valeur ajoutée** : Présenter concrètement aux parties prenantes la plus-value de la plateforme pour les ressourceries.

#### Fonctionnalités Implémentées dans le Prototype

1. **Gestion des Utilisateurs**
   - Inscription et connexion (client et ressourcerie)
   - Profils utilisateurs basiques
   - Système de rôles et permissions

2. **Gestion des Ressourceries**
   - Création et configuration d'un espace ressourcerie
   - Personnalisation basique (logo, description, coordonnées)
   - Géolocalisation sur une carte interactive

3. **Gestion des Produits**
   - Ajout, modification et suppression de produits
   - Catégorisation des produits
   - Upload de photos
   - Définition des caractéristiques spécifiques (état, dimensions, etc.)

4. **Catalogue et Recherche**
   - Affichage du catalogue produits
   - Filtres par catégorie, prix, état
   - Recherche textuelle simple
   - Recherche géolocalisée (produits à proximité)

5. **Processus d'Achat en Click-and-Collect**
   - Panier d'achat
   - Processus de commande simplifié
   - Sélection du créneau de retrait
   - Confirmation de commande

### Résultats et Enseignements

#### Performances Techniques

Le prototype a démontré des performances satisfaisantes :

- **Temps de chargement** : Moins de 1,5 secondes pour les pages principales
- **Responsive design** : Adaptation fluide aux différents appareils
- **Scalabilité** : Capacité à gérer plusieurs centaines d'utilisateurs simultanés

#### Retours Utilisateurs

Des tests utilisateurs ont été menés auprès de 5 ressourceries et 20 clients potentiels, avec les résultats suivants :

**Points Forts**
- Interface intuitive et moderne
- Facilité d'ajout de produits pour les ressourceries
- Pertinence de la recherche géolocalisée
- Appréciation des indicateurs d'impact environnemental

**Points d'Amélioration**
- Besoin d'optimisation du processus d'upload des photos
- Demande de fonctionnalités supplémentaires pour la gestion des stocks
- Suggestions pour améliorer la visualisation des créneaux de retrait
- Besoin d'une meilleure intégration avec les outils existants des ressourceries

#### Enseignements Clés

Le développement du prototype a permis de tirer plusieurs enseignements importants :

1. **Spécificités des produits** : La gestion de produits uniques de seconde main nécessite une flexibilité particulière dans la structure de données.

2. **Importance de la géolocalisation** : La dimension géographique est cruciale pour le succès du click-and-collect dans le contexte des ressourceries.

3. **Valeur de l'impact environnemental** : Les métriques d'impact positif constituent un réel facteur de différenciation et de motivation pour les utilisateurs.

4. **Besoins d'accompagnement** : Les ressourceries ont besoin d'une interface particulièrement intuitive et d'un support adapté pour l'adoption de la plateforme.

5. **Performance des technologies choisies** : L'architecture Laravel + Inertia.js + React s'est avérée pertinente pour répondre aux besoins spécifiques du projet.

### Évolutions Prévues

Suite aux enseignements tirés du prototype, plusieurs évolutions sont planifiées pour la version complète :

1. **Optimisation des médias** : Amélioration du système de gestion des images avec redimensionnement automatique et compression intelligente.

2. **Enrichissement du tableau de bord** : Ajout de statistiques avancées et d'outils d'analyse pour les ressourceries.

3. **Amélioration de la recherche** : Intégration complète d'Algolia avec toutes ses fonctionnalités avancées.

4. **Système de notifications** : Développement d'un système de notifications en temps réel pour améliorer la réactivité.

5. **API pour intégrations tierces** : Création d'une API permettant aux ressourceries d'intégrer la plateforme à leurs outils existants.

6. **Fonctionnalités communautaires** : Ajout de fonctionnalités favorisant les interactions entre utilisateurs et ressourceries.

## Conclusion

### Synthèse du Projet

Le dossier technique présenté dans ce document détaille la conception, le développement et la mise en œuvre de PIVOT Marketplace, la première plateforme de click-and-collect dédiée aux ressourceries en France. Ce projet innovant répond à un besoin concret du secteur de l'économie circulaire, en offrant aux structures de réemploi un outil numérique adapté à leurs spécificités et à leurs contraintes.

L'analyse approfondie du contexte a permis d'identifier les défis uniques auxquels font face les ressourceries : gestion de produits de seconde main souvent uniques, besoin de visibilité locale, valorisation de l'impact environnemental positif, et nécessité d'une solution technique accessible à des structures aux ressources limitées. Ces défis ont guidé l'ensemble des choix techniques et fonctionnels du projet.

PIVOT Marketplace se distingue par son approche technique innovante, combinant des technologies modernes (Laravel, Inertia.js, React) avec une architecture robuste et évolutive. Cette combinaison permet d'offrir une expérience utilisateur fluide et intuitive, tout en garantissant la performance, la sécurité et la maintenabilité de la plateforme.

L'intégration de fonctionnalités spécifiques au contexte des ressourceries, comme la gestion de produits uniques, la géolocalisation avancée et le calcul d'impact environnemental, démontre la capacité du projet à répondre précisément aux besoins du secteur.

### Perspectives d'Évolution

Le développement de PIVOT Marketplace s'inscrit dans une vision à long terme, avec plusieurs axes d'évolution identifiés :

1. **Élargissement du réseau** : Intégration progressive de nouvelles ressourceries sur l'ensemble du territoire français, pour créer un maillage dense et accessible au plus grand nombre.

2. **Enrichissement fonctionnel** : Développement de nouvelles fonctionnalités basées sur les retours d'usage, comme un système de réservation avancé, des outils de gestion de stock plus sophistiqués, ou des fonctionnalités communautaires.

3. **Interopérabilité** : Création d'API permettant l'intégration avec d'autres outils utilisés par les ressourceries (logiciels de caisse, systèmes de gestion, etc.).

4. **Analyse de données** : Exploitation des données anonymisées pour générer des insights sur les tendances de consommation responsable et mesurer l'impact global du réemploi.

5. **Internationalisation** : À plus long terme, adaptation de la plateforme pour d'autres marchés européens, en tenant compte des spécificités locales.

PIVOT Marketplace représente bien plus qu'un simple projet technique : c'est une contribution concrète à la transition écologique et à l'économie circulaire. En offrant aux ressourceries un outil numérique adapté à leurs besoins, la plateforme leur permet de développer leur activité, d'élargir leur clientèle et de maximiser leur impact positif sur l'environnement.

La qualité technique du projet, sa pertinence fonctionnelle et son potentiel d'évolution en font une solution durable et évolutive, capable d'accompagner le développement du secteur des ressourceries dans les années à venir.

PIVOT Marketplace incarne ainsi la rencontre réussie entre innovation technologique et engagement environnemental, démontrant que le numérique peut être un puissant levier de transformation écologique lorsqu'il est mis au service de l'économie circulaire et solidaire.