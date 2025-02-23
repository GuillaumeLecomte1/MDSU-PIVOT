---
title: Base de données
description: Documentation de la structure et de l'organisation de la base de données
---

# Base de données

## Structure générale

La base de données de la Marketplace utilise MySQL/MariaDB et suit une architecture relationnelle optimisée.

## 📊 Tables principales

### Users

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'ressourcerie', 'client') NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Ressourceries

```sql
CREATE TABLE ressourceries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    address TEXT NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    phone VARCHAR(20),
    email VARCHAR(255),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Products

```sql
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ressourcerie_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT UNSIGNED NOT NULL DEFAULT 1,
    status ENUM('available', 'reserved', 'sold') NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (ressourcerie_id) REFERENCES ressourceries(id)
);
```

[Voir toutes les tables](./structure)

## 🔄 Relations

### One-to-Many
- User -> Ressourcerie
- Ressourcerie -> Products
- User -> Orders

### Many-to-Many
- Products <-> Categories
- Users <-> Roles

[Détails des relations](./relations)

## 📈 Optimisation

### Indexes

```sql
-- Users
CREATE INDEX users_email_index ON users(email);

-- Products
CREATE INDEX products_ressourcerie_id_index ON products(ressourcerie_id);
CREATE INDEX products_status_index ON products(status);

-- Ressourceries
CREATE INDEX ressourceries_coordinates_index ON ressourceries(latitude, longitude);
```

### Performances

- Utilisation d'indexes appropriés
- Optimisation des requêtes
- Cache des requêtes fréquentes
- Pagination des résultats

[Guide d'optimisation](./optimization)

## 🔄 Migrations

Les migrations sont gérées via Laravel :

```bash
php artisan migrate        # Exécuter les migrations
php artisan migrate:fresh # Recréer la base de données
php artisan db:seed      # Remplir avec des données de test
```

[Détails des migrations](./migrations)

## 💾 Sauvegarde

### Configuration

```env
BACKUP_ENABLED=true
BACKUP_PATH=/backups
BACKUP_FREQUENCY=daily
```

### Commandes

```bash
# Sauvegarde manuelle
php artisan backup:run

# Restauration
php artisan backup:restore
```

::: tip Maintenance
Effectuez régulièrement des sauvegardes et vérifiez leur intégrité.
:::

::: warning Important
Certaines tables contiennent des données sensibles. Assurez-vous de respecter le RGPD.
::: 