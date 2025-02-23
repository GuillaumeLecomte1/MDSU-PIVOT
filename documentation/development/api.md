---
title: API Documentation
description: Documentation complète de l'API de la Marketplace
---

# Documentation API

## Introduction

L'API de la Marketplace permet d'interagir programmatiquement avec la plateforme. Elle suit les principes REST et utilise JSON pour les échanges de données.

## 🔑 Authentication

L'API utilise Laravel Sanctum pour l'authentification. Deux méthodes sont disponibles :

- Token Bearer pour les applications
- Cookie Session pour le SPA

[En savoir plus sur l'Authentication](./auth)

## 📡 Endpoints

### Ressourceries

```http
GET /api/v1/ressourceries
POST /api/v1/ressourceries
GET /api/v1/ressourceries/{id}
PUT /api/v1/ressourceries/{id}
DELETE /api/v1/ressourceries/{id}
```

### Produits

```http
GET /api/v1/products
POST /api/v1/products
GET /api/v1/products/{id}
PUT /api/v1/products/{id}
DELETE /api/v1/products/{id}
```

[Liste complète des Endpoints](./endpoints)

## 🔄 Webhooks

L'API fournit des webhooks pour :

- Notifications de commandes
- Mises à jour de stock
- Événements de paiement
- Changements de statut

[En savoir plus sur les Webhooks](./webhooks)

## 📊 Pagination

L'API utilise une pagination standard :

```json
{
  "data": [],
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "...",
    "per_page": 15,
    "to": 10,
    "total": 10
  }
}
```

## 🚦 Rate Limiting

- 60 requêtes par minute pour les endpoints publics
- 120 requêtes par minute pour les endpoints authentifiés

## 📝 Formats de Réponse

### Succès

```json
{
  "success": true,
  "data": {},
  "message": "Opération réussie"
}
```

### Erreur

```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Description de l'erreur"
  }
}
```

::: warning Note
Assurez-vous de gérer correctement les erreurs et les codes HTTP retournés par l'API.
:::

::: tip Versioning
L'API est versionnée. Utilisez toujours le préfixe `/api/v1/` pour assurer la compatibilité.
::: 