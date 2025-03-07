# Schémas de Base de Données

## Introduction

Ce document présente les schémas de base de données du POC PIVOT Marketplace. Il détaille la structure des tables, les relations entre elles, les contraintes d'intégrité et les index mis en place pour optimiser les performances.

> **Note :** Ces schémas sont l'implémentation concrète du [Modèle Conceptuel de Données](./README.md) dans MySQL.

## Structure de la base de données

### Vue d'ensemble

La base de données du POC PIVOT est composée de 8 tables principales, correspondant aux entités du modèle conceptuel, ainsi que de quelques tables additionnelles pour la gestion des relations many-to-many et des fonctionnalités spécifiques.

```mermaid
erDiagram
    users ||--o{ orders : places
    users }|--|| roles : has
    ressourceries ||--o{ products : offers
    ressourceries ||--o{ orders : receives
    products }|--|| categories : belongs_to
    orders ||--o{ order_items : contains
    orders ||--o{ payments : has
    order_items }|--|| products : references
    
    users {
        bigint id PK
        string name
        string email UK
        string password
        string remember_token
        timestamp email_verified_at
        bigint role_id FK
        timestamps timestamps
    }
    
    roles {
        bigint id PK
        string name UK
        string description
        timestamps timestamps
    }
    
    ressourceries {
        bigint id PK
        string name
        string description
        string address
        string city
        string postal_code
        string phone
        string email
        decimal latitude
        decimal longitude
        string opening_hours
        string logo_path
        timestamps timestamps
    }
    
    products {
        bigint id PK
        bigint ressourcery_id FK
        bigint category_id FK
        string name
        text description
        decimal price
        integer quantity
        string status
        string condition
        decimal weight
        string dimensions
        json images
        decimal environmental_impact
        timestamps timestamps
        index idx_ressourcery
        index idx_category
        index idx_status
    }
    
    categories {
        bigint id PK
        string name UK
        string description
        string icon
        timestamps timestamps
    }
    
    orders {
        bigint id PK
        bigint user_id FK
        bigint ressourcery_id FK
        string status
        decimal total_amount
        timestamp pickup_date
        string payment_method
        string payment_status
        timestamps timestamps
        index idx_user
        index idx_ressourcery
        index idx_status
    }
    
    order_items {
        bigint id PK
        bigint order_id FK
        bigint product_id FK
        integer quantity
        decimal price
        timestamps timestamps
        index idx_order
        index idx_product
    }
    
    payments {
        bigint id PK
        bigint order_id FK
        string transaction_id UK
        decimal amount
        string status
        string provider
        timestamp payment_date
        timestamps timestamps
        index idx_order
        index idx_status
    }
```

## Détail des tables

### Table `users`

Stocke les informations des utilisateurs du système.

| Colonne | Type | Contraintes | Description |
|---------|------|-------------|-------------|
| id | bigint | PK, AUTO_INCREMENT | Identifiant unique |
| name | varchar(255) | NOT NULL | Nom complet de l'utilisateur |
| email | varchar(255) | NOT NULL, UNIQUE | Adresse email (identifiant de connexion) |
| password | varchar(255) | NOT NULL | Mot de passe hashé |
| remember_token | varchar(100) | NULL | Token pour la fonctionnalité "Se souvenir de moi" |
| email_verified_at | timestamp | NULL | Date de vérification de l'email |
| role_id | bigint | FK | Référence à la table roles |
| created_at | timestamp | NULL | Date de création |
| updated_at | timestamp | NULL | Date de dernière modification |

**Index :**
- PRIMARY KEY (`id`)
- UNIQUE KEY `users_email_unique` (`email`)
- KEY `users_role_id_foreign` (`role_id`)

**Contraintes :**
- FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)

### Table `roles`

Définit les différents rôles et leurs permissions.

| Colonne | Type | Contraintes | Description |
|---------|------|-------------|-------------|
| id | bigint | PK, AUTO_INCREMENT | Identifiant unique |
| name | varchar(255) | NOT NULL, UNIQUE | Nom du rôle |
| description | text | NULL | Description des responsabilités |
| created_at | timestamp | NULL | Date de création |
| updated_at | timestamp | NULL | Date de dernière modification |

**Index :**
- PRIMARY KEY (`id`)
- UNIQUE KEY `roles_name_unique` (`name`)

### Table `ressourceries`

Stocke les informations des ressourceries partenaires.

| Colonne | Type | Contraintes | Description |
|---------|------|-------------|-------------|
| id | bigint | PK, AUTO_INCREMENT | Identifiant unique |
| name | varchar(255) | NOT NULL | Nom de la ressourcerie |
| description | text | NULL | Description de la ressourcerie |
| address | varchar(255) | NOT NULL | Adresse physique |
| city | varchar(100) | NOT NULL | Ville |
| postal_code | varchar(20) | NOT NULL | Code postal |
| phone | varchar(20) | NULL | Numéro de téléphone |
| email | varchar(255) | NOT NULL | Adresse email de contact |
| latitude | decimal(10,8) | NULL | Latitude pour géolocalisation |
| longitude | decimal(11,8) | NULL | Longitude pour géolocalisation |
| opening_hours | json | NULL | Horaires d'ouverture (format JSON) |
| logo_path | varchar(255) | NULL | Chemin vers le logo |
| created_at | timestamp | NULL | Date de création |
| updated_at | timestamp | NULL | Date de dernière modification |

**Index :**
- PRIMARY KEY (`id`)
- KEY `ressourceries_city_postal_code_index` (`city`, `postal_code`)
- SPATIAL KEY `ressourceries_location` (`latitude`, `longitude`)

### Table `products`

Stocke les informations des produits proposés par les ressourceries.

| Colonne | Type | Contraintes | Description |
|---------|------|-------------|-------------|
| id | bigint | PK, AUTO_INCREMENT | Identifiant unique |
| ressourcery_id | bigint | FK, NOT NULL | Référence à la ressourcerie |
| category_id | bigint | FK, NOT NULL | Référence à la catégorie |
| name | varchar(255) | NOT NULL | Nom du produit |
| description | text | NULL | Description détaillée |
| price | decimal(8,2) | NOT NULL | Prix en euros |
| quantity | int | NOT NULL, DEFAULT 1 | Quantité disponible |
| status | varchar(20) | NOT NULL, DEFAULT 'available' | Statut (disponible, réservé, vendu) |
| condition | varchar(50) | NOT NULL | État du produit |
| weight | decimal(8,2) | NULL | Poids en kg |
| dimensions | varchar(100) | NULL | Dimensions (format LxlxH) |
| images | json | NULL | Chemins vers les images (format JSON) |
| environmental_impact | decimal(8,2) | DEFAULT 0 | Impact environnemental calculé |
| created_at | timestamp | NULL | Date de création |
| updated_at | timestamp | NULL | Date de dernière modification |

**Index :**
- PRIMARY KEY (`id`)
- KEY `products_ressourcery_id_foreign` (`ressourcery_id`)
- KEY `products_category_id_foreign` (`category_id`)
- KEY `products_status_index` (`status`)
- FULLTEXT KEY `products_search` (`name`, `description`)

**Contraintes :**
- FOREIGN KEY (`ressourcery_id`) REFERENCES `ressourceries` (`id`) ON DELETE CASCADE
- FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)

### Table `categories`

Stocke les catégories de produits.

| Colonne | Type | Contraintes | Description |
|---------|------|-------------|-------------|
| id | bigint | PK, AUTO_INCREMENT | Identifiant unique |
| name | varchar(255) | NOT NULL, UNIQUE | Nom de la catégorie |
| description | text | NULL | Description de la catégorie |
| icon | varchar(255) | NULL | Icône représentative |
| created_at | timestamp | NULL | Date de création |
| updated_at | timestamp | NULL | Date de dernière modification |

**Index :**
- PRIMARY KEY (`id`)
- UNIQUE KEY `categories_name_unique` (`name`)

### Table `orders`

Stocke les commandes passées par les utilisateurs.

| Colonne | Type | Contraintes | Description |
|---------|------|-------------|-------------|
| id | bigint | PK, AUTO_INCREMENT | Identifiant unique |
| user_id | bigint | FK, NOT NULL | Référence à l'utilisateur |
| ressourcery_id | bigint | FK, NOT NULL | Référence à la ressourcerie |
| status | varchar(20) | NOT NULL, DEFAULT 'pending' | Statut de la commande |
| total_amount | decimal(10,2) | NOT NULL | Montant total |
| pickup_date | timestamp | NULL | Date de retrait prévue |
| payment_method | varchar(50) | NULL | Méthode de paiement |
| payment_status | varchar(20) | DEFAULT 'pending' | Statut du paiement |
| created_at | timestamp | NULL | Date de création |
| updated_at | timestamp | NULL | Date de dernière modification |

**Index :**
- PRIMARY KEY (`id`)
- KEY `orders_user_id_foreign` (`user_id`)
- KEY `orders_ressourcery_id_foreign` (`ressourcery_id`)
- KEY `orders_status_index` (`status`)
- KEY `orders_pickup_date_index` (`pickup_date`)

**Contraintes :**
- FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
- FOREIGN KEY (`ressourcery_id`) REFERENCES `ressourceries` (`id`)

### Table `order_items`

Stocke les éléments individuels des commandes.

| Colonne | Type | Contraintes | Description |
|---------|------|-------------|-------------|
| id | bigint | PK, AUTO_INCREMENT | Identifiant unique |
| order_id | bigint | FK, NOT NULL | Référence à la commande |
| product_id | bigint | FK, NOT NULL | Référence au produit |
| quantity | int | NOT NULL, DEFAULT 1 | Quantité commandée |
| price | decimal(8,2) | NOT NULL | Prix unitaire au moment de la commande |
| created_at | timestamp | NULL | Date de création |
| updated_at | timestamp | NULL | Date de dernière modification |

**Index :**
- PRIMARY KEY (`id`)
- KEY `order_items_order_id_foreign` (`order_id`)
- KEY `order_items_product_id_foreign` (`product_id`)

**Contraintes :**
- FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
- FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)

### Table `payments`

Stocke les informations de paiement.

| Colonne | Type | Contraintes | Description |
|---------|------|-------------|-------------|
| id | bigint | PK, AUTO_INCREMENT | Identifiant unique |
| order_id | bigint | FK, NOT NULL | Référence à la commande |
| transaction_id | varchar(255) | UNIQUE | Identifiant de transaction externe |
| amount | decimal(10,2) | NOT NULL | Montant payé |
| status | varchar(20) | NOT NULL, DEFAULT 'pending' | Statut du paiement |
| provider | varchar(50) | NULL | Fournisseur de paiement |
| payment_date | timestamp | NULL | Date du paiement |
| created_at | timestamp | NULL | Date de création |
| updated_at | timestamp | NULL | Date de dernière modification |

**Index :**
- PRIMARY KEY (`id`)
- KEY `payments_order_id_foreign` (`order_id`)
- UNIQUE KEY `payments_transaction_id_unique` (`transaction_id`)
- KEY `payments_status_index` (`status`)

**Contraintes :**
- FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE

## Migrations Laravel

### Exemple de migration pour la table `products`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ressourcery_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->integer('quantity')->default(1);
            $table->string('status', 20)->default('available');
            $table->string('condition', 50);
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('dimensions', 100)->nullable();
            $table->json('images')->nullable();
            $table->decimal('environmental_impact', 8, 2)->default(0);
            $table->timestamps();
            
            // Index pour optimiser les recherches
            $table->index('status');
            $table->fullText(['name', 'description'], 'products_search');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

### Exemple de migration pour la table `orders`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('ressourcery_id')->constrained();
            $table->string('status', 20)->default('pending');
            $table->decimal('total_amount', 10, 2);
            $table->timestamp('pickup_date')->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_status', 20)->default('pending');
            $table->timestamps();
            
            // Index pour optimiser les recherches
            $table->index('status');
            $table->index('pickup_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
```

## Optimisations de la base de données

### Index

Des index ont été mis en place sur les colonnes fréquemment utilisées dans les requêtes pour optimiser les performances :

1. **Index de clés étrangères** : Sur toutes les colonnes de clés étrangères pour accélérer les jointures.
2. **Index de statut** : Sur les colonnes de statut (`status`) des tables `products`, `orders` et `payments` pour accélérer les filtres par statut.
3. **Index de recherche full-text** : Sur les colonnes `name` et `description` de la table `products` pour optimiser les recherches textuelles.
4. **Index spatial** : Sur les coordonnées géographiques des ressourceries pour optimiser les recherches par proximité.

### Contraintes d'intégrité

Des contraintes d'intégrité référentielle ont été mises en place pour garantir la cohérence des données :

1. **Contraintes de clé étrangère** : Pour assurer que les références entre tables sont valides.
2. **Contraintes ON DELETE CASCADE** : Pour les relations parent-enfant où la suppression du parent doit entraîner la suppression des enfants (ex: ressourcery -> products, order -> order_items).
3. **Contraintes UNIQUE** : Sur les colonnes qui doivent contenir des valeurs uniques (ex: email des utilisateurs, transaction_id des paiements).

## Évolutions futures

Le schéma de base de données actuel a été conçu pour permettre des évolutions futures, notamment :

1. **Table de favoris** : Pour permettre aux utilisateurs de marquer des produits ou des ressourceries comme favoris.
2. **Table d'avis** : Pour les avis et notations des produits et ressourceries.
3. **Table de promotions** : Pour gérer les offres spéciales et les codes promotionnels.
4. **Table de facteurs d'impact environnemental** : Pour stocker les facteurs utilisés dans le calcul de l'impact environnemental.
5. **Tables de traduction** : Pour l'internationalisation des contenus.

## Conclusion

Le schéma de base de données du POC PIVOT Marketplace a été conçu pour être robuste, performant et évolutif. Les choix de structure, d'index et de contraintes ont été faits pour optimiser les performances tout en garantissant l'intégrité des données.

La mise en œuvre via les migrations Laravel permet une gestion versionnée de la structure de la base de données, facilitant ainsi les évolutions futures et le déploiement sur différents environnements.

---

*Dernière mise à jour : Février 2025*

[Retour à l'index](../README.md)
