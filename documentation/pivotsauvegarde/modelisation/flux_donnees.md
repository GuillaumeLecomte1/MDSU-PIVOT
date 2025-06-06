# Diagrammes de Flux de Données

## Introduction

Ce document présente les différents flux de données au sein du POC PIVOT Marketplace. Les diagrammes illustrent comment les données circulent entre les différentes parties du système, les acteurs impliqués et les processus métier sous-jacents.

> **Note :** Ces diagrammes complètent le [Modèle Conceptuel de Données](./README.md) et aident à comprendre les interactions dynamiques entre les composants du système.

## Flux de données principaux

### Vue d'ensemble du système

Le diagramme ci-dessous présente une vue d'ensemble des flux de données dans le système PIVOT :

```mermaid
flowchart TD
    Client[Client] <--> |Recherche/Commande| Frontend[Interface Utilisateur]
    Ressourcerie[Ressourcerie] <--> |Gestion Produits/Commandes| Frontend
    Admin[Administrateur] <--> |Administration| Frontend
    
    Frontend <--> |API Requests| Backend[Backend Laravel]
    Backend <--> |Requêtes SQL| DB[(Base de données MySQL)]
    Backend <--> |Géolocalisation| GeoAPI[API de Géolocalisation]
    Backend <--> |Notifications| NotifService[Service de Notifications]
    
    subgraph Système PIVOT
        Frontend
        Backend
        DB
    end
```

### Flux d'authentification

```mermaid
sequenceDiagram
    actor User as Utilisateur
    participant UI as Interface Utilisateur
    participant Auth as Service d'Authentification
    participant DB as Base de données
    
    User->>UI: Saisie email/mot de passe
    UI->>Auth: Demande d'authentification
    Auth->>DB: Vérification des identifiants
    DB-->>Auth: Résultat de la vérification
    
    alt Authentification réussie
        Auth-->>UI: Génération token + rôle utilisateur
        UI-->>User: Redirection vers dashboard
    else Échec d'authentification
        Auth-->>UI: Message d'erreur
        UI-->>User: Affichage erreur
    end
```

### Flux de recherche et navigation

```mermaid
sequenceDiagram
    actor Client
    participant UI as Interface Utilisateur
    participant API as API Backend
    participant DB as Base de données
    participant GeoAPI as Service de Géolocalisation
    
    Client->>UI: Recherche produits/filtres
    UI->>API: Requête de recherche
    
    alt Recherche par géolocalisation
        API->>GeoAPI: Demande coordonnées
        GeoAPI-->>API: Retourne coordonnées
    end
    
    API->>DB: Requête filtrée
    DB-->>API: Résultats de recherche
    API-->>UI: Données formatées
    UI-->>Client: Affichage résultats
    
    Client->>UI: Sélection produit
    UI->>API: Demande détails produit
    API->>DB: Requête détails
    DB-->>API: Données produit
    API-->>UI: Détails formatés
    UI-->>Client: Affichage détails produit
```

### Flux de commande click-and-collect

```mermaid
sequenceDiagram
    actor Client
    participant UI as Interface Utilisateur
    participant API as API Backend
    participant DB as Base de données
    participant Notif as Service Notifications
    participant Ressourcerie
    
    Client->>UI: Ajout produits au panier
    UI->>UI: Stockage local panier
    
    Client->>UI: Validation panier
    UI->>API: Soumission commande
    API->>DB: Vérification disponibilité
    
    alt Produits disponibles
        DB-->>API: Confirmation disponibilité
        API->>DB: Création commande
        API->>DB: Mise à jour stocks
        DB-->>API: Confirmation commande
        API-->>UI: Confirmation + référence
        UI-->>Client: Confirmation commande
        
        API->>Notif: Notification nouvelle commande
        Notif->>Ressourcerie: Email/SMS nouvelle commande
    else Produits indisponibles
        DB-->>API: Signalement indisponibilité
        API-->>UI: Message d'erreur
        UI-->>Client: Notification produits indisponibles
    end
```

### Flux de gestion des produits (Ressourcerie)

```mermaid
sequenceDiagram
    actor Ressourcerie
    participant UI as Interface Admin
    participant API as API Backend
    participant DB as Base de données
    participant Storage as Stockage Images
    
    Ressourcerie->>UI: Connexion espace admin
    Ressourcerie->>UI: Création/édition produit
    UI->>API: Soumission données produit
    
    alt Avec images
        UI->>API: Upload images
        API->>Storage: Stockage images
        Storage-->>API: URLs images
    end
    
    API->>DB: Enregistrement produit
    DB-->>API: Confirmation
    API-->>UI: Statut opération
    UI-->>Ressourcerie: Confirmation
    
    Ressourcerie->>UI: Consultation inventaire
    UI->>API: Demande liste produits
    API->>DB: Requête produits
    DB-->>API: Liste produits
    API-->>UI: Données formatées
    UI-->>Ressourcerie: Affichage inventaire
```

### Flux de gestion des commandes (Ressourcerie)

```mermaid
sequenceDiagram
    actor Ressourcerie
    participant UI as Interface Admin
    participant API as API Backend
    participant DB as Base de données
    participant Notif as Service Notifications
    participant Client
    
    Ressourcerie->>UI: Consultation commandes
    UI->>API: Demande liste commandes
    API->>DB: Requête commandes
    DB-->>API: Liste commandes
    API-->>UI: Données formatées
    UI-->>Ressourcerie: Affichage commandes
    
    Ressourcerie->>UI: Mise à jour statut commande
    UI->>API: Soumission nouveau statut
    API->>DB: Mise à jour statut
    DB-->>API: Confirmation
    
    alt Commande prête
        API->>Notif: Notification commande prête
        Notif->>Client: Email/SMS commande prête
    end
    
    API-->>UI: Confirmation mise à jour
    UI-->>Ressourcerie: Confirmation
```

### Flux de calcul d'impact environnemental

```mermaid
flowchart TD
    Ressourcerie[Ressourcerie] -->|Saisie caractéristiques produit| UI[Interface Admin]
    UI -->|Transmission données| API[API Backend]
    API -->|Requête calcul| ImpactCalc[Service Calcul Impact]
    
    ImpactCalc -->|Récupération facteurs| FactorsDB[(Base facteurs d'impact)]
    ImpactCalc -->|Algorithme de calcul| Calculation[Calcul d'impact]
    Calculation -->|Résultat| API
    
    API -->|Stockage résultat| DB[(Base de données)]
    API -->|Transmission résultat| UI
    UI -->|Affichage impact| Ressourcerie
    
    Client[Client] -->|Consultation produit| ClientUI[Interface Client]
    ClientUI -->|Requête produit| API
    API -->|Données produit avec impact| ClientUI
    ClientUI -->|Affichage impact environnemental| Client
```

## Flux de données secondaires

### Flux de notifications

```mermaid
flowchart LR
    Events[Événements Système] -->|Déclenchement| NotifService[Service de Notifications]
    
    NotifService -->|Email| EmailProvider[Fournisseur Email]
    NotifService -->|SMS| SMSProvider[Fournisseur SMS]
    NotifService -->|Push| PushService[Service Notifications Push]
    
    EmailProvider -->|Envoi| UserEmail[Email Utilisateur]
    SMSProvider -->|Envoi| UserPhone[Téléphone Utilisateur]
    PushService -->|Envoi| UserBrowser[Navigateur Utilisateur]
```

### Flux d'analyse et reporting

```mermaid
flowchart TD
    DB[(Base de données)] -->|Extraction données| ETL[Processus ETL]
    ETL -->|Données transformées| DW[(Entrepôt de données)]
    
    DW -->|Requêtes| Analytics[Service d'Analyse]
    
    Analytics -->|Rapports périodiques| Scheduler[Planificateur]
    Analytics -->|Tableaux de bord| Dashboard[Interface Tableaux de bord]
    
    Scheduler -->|Envoi rapports| AdminEmail[Email Administrateurs]
    Dashboard -->|Visualisation| Admin[Administrateurs]
```

## Intégration avec les systèmes externes

### API de géolocalisation

```mermaid
sequenceDiagram
    participant Client
    participant UI as Interface Utilisateur
    participant API as API Backend
    participant GeoAPI as API Géolocalisation
    participant DB as Base de données
    
    Client->>UI: Recherche par proximité
    UI->>API: Requête ressourceries proches
    
    alt Avec géolocalisation navigateur
        UI->>Client: Demande permission géolocalisation
        Client->>UI: Autorise et fournit coordonnées
    else Sans géolocalisation navigateur
        UI->>Client: Demande code postal
        Client->>UI: Saisit code postal
        UI->>GeoAPI: Convertit code postal en coordonnées
        GeoAPI-->>UI: Retourne coordonnées
    end
    
    UI->>API: Transmet coordonnées
    API->>DB: Requête ressourceries par distance
    DB-->>API: Liste ressourceries triées
    API-->>UI: Données formatées
    UI-->>Client: Affichage résultats
```

### Passerelles de paiement (préparation future)

```mermaid
sequenceDiagram
    actor Client
    participant UI as Interface Utilisateur
    participant API as API Backend
    participant PayGateway as Passerelle de Paiement
    participant DB as Base de données
    
    Client->>UI: Choix méthode paiement
    UI->>API: Initialisation transaction
    API->>PayGateway: Création transaction
    PayGateway-->>API: Token transaction
    API-->>UI: Redirection paiement
    
    UI->>PayGateway: Redirection client
    Client->>PayGateway: Saisie informations paiement
    PayGateway-->>UI: Callback résultat
    
    UI->>API: Vérification transaction
    API->>PayGateway: Vérification statut
    PayGateway-->>API: Confirmation paiement
    
    API->>DB: Enregistrement paiement
    API->>DB: Mise à jour commande
    API-->>UI: Confirmation finale
    UI-->>Client: Confirmation paiement
```

## Conclusion

Les diagrammes de flux de données présentés dans ce document illustrent les interactions complexes entre les différentes parties du système PIVOT. Ils servent de référence pour comprendre comment les données sont créées, transformées et utilisées tout au long des processus métier.

Ces flux ont été conçus pour optimiser l'expérience utilisateur tout en garantissant l'intégrité des données et la performance du système. Ils constituent également une base solide pour les évolutions futures du système.

---

*Dernière mise à jour : Février 2025*

[Retour à l'index](../README.md)
