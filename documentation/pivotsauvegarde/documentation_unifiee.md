# Documentation Unifiée du POC PIVOT Marketplace

## Table des Matières

1. [Introduction](#introduction)
2. [Contexte](#contexte)
3. [État Actuel](#état-actuel)
4. [Modélisation](#modélisation)
5. [Matrice Décisionnelle](#matrice-décisionnelle)
6. [Spécifications](#spécifications)
   - [Fonctionnelles](#spécifications-fonctionnelles)
   - [Techniques](#spécifications-techniques)
7. [Prototype](#prototype)
8. [Tests](#tests)
9. [Sécurité](#sécurité)
10. [Accessibilité](#accessibilité)
11. [Conformité](#conformité)
12. [Conclusion](#conclusion)
13. [Ressources](#ressources)

---

## Introduction

Dans un contexte économique où les plateformes de commerce en ligne connaissent une croissance exponentielle, notre projet PIVOT Marketplace vise à offrir une solution innovante et complète spécifiquement conçue pour les ressourceries. Ces structures, dédiées à la récupération, la valorisation et la revente de biens sur un territoire donné, jouent un rôle crucial dans la sensibilisation et l'éducation à l'environnement, contribuant ainsi à l'économie circulaire et à la réduction des déchets.

PIVOT est la première plateforme de click-and-collect dédiée aux ressourceries en France, permettant de donner une seconde vie aux produits dénichés tout en créant de nouvelles interactions sociales. Le projet s'articule autour d'une architecture modulaire et évolutive, permettant à chaque ressourcerie de configurer sa boutique en ligne selon ses besoins spécifiques. Grâce à une interface intuitive et des fonctionnalités avancées, les ressourceries peuvent créer, personnaliser et gérer leur espace de vente sans nécessiter de compétences techniques approfondies.

Ce projet ne se limite pas à la simple création d'une marketplace. Pour les développeurs et les ressourceries ayant des besoins spécifiques, une API complète est disponible, permettant l'intégration de fonctionnalités personnalisées et l'extension des capacités de la plateforme.

En tant que développeur web du projet, mon rôle est de concevoir et de réaliser l'ensemble de la plateforme. Cela inclut le développement du backend, la création d'une interface utilisateur intuitive, la mise en place des fonctionnalités techniques clés, la gestion des données, ainsi que l'intégration des bonnes pratiques en matière de sécurité (conformité RGPD) et d'accessibilité (normes RGAA, ARIA, etc.).

Ce dossier technique détaillera les différentes étapes de la réalisation du projet, des besoins initiaux aux choix techniques effectués, en passant par la modélisation des données et l'analyse des fonctionnalités clés.

---

## Contexte

Le projet PIVOT Marketplace est né d'une observation simple : malgré la démocratisation du commerce en ligne, les ressourceries, acteurs essentiels de l'économie circulaire, ne disposent pas d'outils numériques adaptés à leurs besoins spécifiques. Notre solution vise à combler ce fossé en proposant une plateforme complète, flexible et accessible, spécifiquement conçue pour ce secteur niche.

Les ressourceries sont des structures qui collectent, valorisent et revendent des objets de seconde main, tout en sensibilisant le public aux enjeux environnementaux. Elles font face à plusieurs défis :
- Gestion d'inventaires variés et uniques (objets de seconde main)
- Valorisation de l'aspect écologique et social des produits
- Nécessité de toucher un public plus large que leur zone géographique immédiate
- Besoin de solutions numériques adaptées à leurs ressources souvent limitées

Ce changement d'orientation répond à la volonté de créer un outil interactif à forte valeur ajoutée, capable de s'adresser à deux cibles distinctes mais complémentaires :

- **Les ressourceries (B2B)**, qui cherchent à étendre leur portée et à digitaliser leur offre sans investissement technique lourd.
- **Les consommateurs éco-responsables (B2C)**, qui souhaitent acheter des produits de seconde main tout en soutenant des structures locales à vocation sociale et environnementale.

Le projet se concentre dans un premier temps sur les fonctionnalités essentielles d'une marketplace adaptée aux ressourceries, avec une attention particulière portée à :

- **La gestion des produits de seconde main**, avec des caractéristiques spécifiques comme l'état, les dimensions, la provenance.
- **Le système de click-and-collect**, privilégiant le retrait en boutique pour renforcer le lien social et réduire l'empreinte carbone.
- **La personnalisation de l'interface**, offrant la possibilité d'adapter l'apparence de la boutique en ligne à l'identité visuelle de chaque ressourcerie.
- **La géolocalisation**, permettant aux utilisateurs de trouver facilement les ressourceries proches de chez eux.

Lors de l'analyse des besoins, plusieurs attentes clés ont été identifiées :

- **Une interface d'administration intuitive**, permettant une gestion simplifiée des produits uniques et des commandes.
- **Des outils de valorisation de l'impact environnemental**, mettant en avant la contribution à l'économie circulaire.
- **Une architecture évolutive**, capable de s'adapter à la croissance de la plateforme et à la diversification des services proposés par les ressourceries.

Ce projet s'inscrit dans le cadre d'un travail académique, avec l'ambition de livrer une solution fonctionnelle, évolutive et adaptée aux besoins spécifiques des ressourceries françaises, tout en sensibilisant le grand public aux enjeux de la consommation responsable et de l'économie circulaire.

---

## État Actuel

### Vue d'Ensemble

Le Proof of Concept (POC) de PIVOT représente la première phase de développement du projet. Il s'agit d'une version fonctionnelle mais limitée de la plateforme, permettant de valider les concepts fondamentaux et de recueillir des retours utilisateurs.

Actuellement, le POC est déployé auprès d'un panel de 5 ressourceries partenaires qui l'utilisent en conditions réelles, permettant ainsi de tester les fonctionnalités et d'identifier les axes d'amélioration.

### Fonctionnalités Actuellement Implémentées

#### 1. Système d'Authentification

- **Inscription et connexion** des utilisateurs avec différents rôles (administrateur, ressourcerie, client)
- **Gestion des profils** avec informations de base (nom, email, téléphone, adresse)
- **Système de vérification d'email** pour sécuriser les inscriptions
- **Gestion des rôles et permissions** adaptée aux besoins spécifiques des ressourceries

#### 2. Gestion des Produits

- **Ajout de produits** avec caractéristiques spécifiques aux objets de seconde main (état, dimensions, couleur, marque, etc.)
- **Upload de photos** pour illustrer les produits (limité à 3 photos par produit dans le POC)
- **Catégorisation** des produits selon une arborescence adaptée aux ressourceries
- **Gestion basique des stocks** (disponible/indisponible/vendu)
- **Édition et suppression** des produits

#### 3. Recherche et Navigation

- **Catalogue produits** avec affichage en grille ou en liste
- **Recherche par mots-clés** dans les titres et descriptions des produits
- **Filtrage par catégorie** et par ressourcerie
- **Géolocalisation des ressourceries** sur une carte interactive
- **Fiches produits détaillées** avec toutes les informations pertinentes

#### 4. Processus de Click-and-Collect

- **Panier d'achat** permettant d'ajouter/supprimer des produits
- **Sélection du créneau de retrait** sur un calendrier simplifié
- **Récapitulatif de commande** avant validation
- **Confirmation par email** après réservation
- **Suivi basique des commandes** (en attente, confirmée, retirée)

#### 5. Calcul d'Impact Environnemental

- **Estimation simplifiée** de l'impact positif des achats de seconde main
- **Affichage des métriques environnementales** (CO2 évité, déchets détournés)
- **Compteur personnel** d'impact pour chaque client
- **Compteur global** pour la plateforme

### Tests et Validation

Le POC a été testé auprès d'un panel de 5 ressourceries partenaires, permettant de :

- **Valider l'architecture technique** et son adéquation avec les besoins des ressourceries
- **Recueillir des retours utilisateurs** sur l'expérience globale et les fonctionnalités spécifiques
- **Identifier les points forts** de la plateforme à conserver et développer
- **Repérer les axes d'amélioration** pour les futures versions

Les premiers retours sont globalement positifs, avec une appréciation particulière pour l'interface intuitive et la valorisation de l'impact environnemental.

### Limites Actuelles

Le POC présente certaines limitations inhérentes à sa nature de prototype :

1. **Gestion des photos limitée** : Maximum 3 photos par produit, sans optimisation automatique des images
2. **Absence d'intégration** avec les outils existants des ressourceries (logiciels de caisse, etc.)
3. **Statistiques simplifiées** : Tableaux de bord basiques sans analyses avancées
4. **Interface utilisateur perfectible** : Certains parcours utilisateurs peuvent être optimisés
5. **Fonctionnalités communautaires absentes** : Pas de système d'avis ou d'interactions entre utilisateurs

Ces limitations ont été identifiées et documentées pour être adressées dans les prochaines phases de développement, en fonction des priorités établies avec les ressourceries partenaires.

---

## Modélisation

La modélisation des données est une étape cruciale pour la conception et la réalisation d'une plateforme de commerce électronique. Elle permet de définir les structures de données nécessaires pour stocker et gérer les informations pertinentes pour les différents acteurs du marché.

### Entités Principales

#### 1. Utilisateur

- **Nom** : Nom de l'utilisateur
- **Email** : Adresse électronique de l'utilisateur
- **Mot de passe** : Mot de passe pour accéder à l'interface
- **Rôle** : Rôle de l'utilisateur (administrateur, ressourcerie, client)
- **Profil** : Informations de base de l'utilisateur (nom, email, téléphone, adresse)

#### 2. Produit

- **Nom** : Nom du produit
- **Description** : Description du produit
- **État** : État du produit
- **Dimensions** : Dimensions du produit
- **Couleur** : Couleur du produit
- **Marque** : Marque du produit
- **Prix** : Prix du produit
- **Stock** : État du stock du produit (disponible, indisponible, vendu)
- **Photos** : Photos associées au produit
- **Catégorie** : Catégorie du produit

#### 3. Commande

- **Date de commande** : Date de la commande
- **État** : État de la commande (en attente, confirmée, retirée)
- **Total** : Total de la commande
- **Client** : Client ayant passé la commande
- **Ressourcerie** : Ressourcerie ayant traité la commande

#### 4. Ressourcerie

- **Nom** : Nom de la ressourcerie
- **Adresse** : Adresse de la ressourcerie
- **Produits** : Liste des produits vendus par la ressourcerie

#### 5. Catégorie

- **Nom** : Nom de la catégorie
- **Sous-catégories** : Liste des sous-catégories

### Relations

#### 1. Relation entre Utilisateur et Produit

- **Achat** : Un utilisateur peut acheter un produit
- **Vente** : Une ressourcerie peut vendre un produit

#### 2. Relation entre Ressourcerie et Produit

- **Vente** : Une ressourcerie peut vendre un produit

#### 3. Relation entre Commande et Produit

- **Contient** : Une commande contient des produits

#### 4. Relation entre Ressourcerie et Catégorie

- **Appartient à** : Une ressourcerie appartient à une catégorie

### Schéma de Base de Données

Le schéma de base de données pour la plateforme PIVOT Marketplace est composé de plusieurs tables, chacune correspondant à une entité principale du modèle de données.

#### 1. Utilisateur

- **Table : `utilisateur`**
  - **Champs :**
    - `id` : Identifiant unique de l'utilisateur
    - `nom` : Nom de l'utilisateur
    - `email` : Adresse électronique de l'utilisateur
    - `mot_de_passe` : Mot de passe pour accéder à l'interface
    - `role` : Rôle de l'utilisateur (administrateur, ressourcerie, client)
    - `profil_id` : Identifiant du profil associé à l'utilisateur

#### 2. Profil

- **Table : `profil`**
  - **Champs :**
    - `id` : Identifiant unique du profil
    - `nom` : Nom de l'utilisateur
    - `email` : Adresse électronique de l'utilisateur
    - `telephone` : Numéro de téléphone de l'utilisateur
    - `adresse` : Adresse de l'utilisateur

#### 3. Produit

- **Table : `produit`**
  - **Champs :**
    - `id` : Identifiant unique du produit
    - `nom` : Nom du produit
    - `description` : Description du produit
    - `etat` : État du produit
    - `dimensions` : Dimensions du produit
    - `couleur` : Couleur du produit
    - `marque` : Marque du produit
    - `prix` : Prix du produit
    - `stock` : État du stock du produit (disponible, indisponible, vendu)
    - `photos` : Photos associées au produit
    - `categorie_id` : Identifiant de la catégorie associée au produit

#### 4. Commande

- **Table : `commande`**
  - **Champs :**
    - `id` : Identifiant unique de la commande
    - `date_commande` : Date de la commande
    - `etat` : État de la commande (en attente, confirmée, retirée)
    - `total` : Total de la commande
    - `client_id` : Identifiant du client ayant passé la commande
    - `ressourcerie_id` : Identifiant de la ressourcerie ayant traité la commande

#### 5. Ressourcerie

- **Table : `ressourcerie`**
  - **Champs :**
    - `id` : Identifiant unique de la ressourcerie
    - `nom` : Nom de la ressourcerie
    - `adresse` : Adresse de la ressourcerie
    - `produits` : Liste des produits vendus par la ressourcerie

#### 6. Catégorie

- **Table : `categorie`**
  - **Champs :**
    - `id` : Identifiant unique de la catégorie
    - `nom` : Nom de la catégorie
    - `sous_categories` : Liste des sous-catégories associées à la catégorie

#### 7. Sous-Catégorie

- **Table : `sous_categorie`**
  - **Champs :**
    - `id` : Identifiant unique de la sous-catégorie
    - `nom` : Nom de la sous-catégorie
    - `categorie_id` : Identifiant de la catégorie associée à la sous-catégorie

### Exemple de Requête SQL

Voici un exemple de requête SQL pour récupérer les produits d'une ressourcerie donnée :

```sql
SELECT p.id, p.nom, p.description, p.etat, p.dimensions, p.couleur, p.marque, p.prix, p.stock, p.photos, c.nom AS categorie_nom
FROM produit p
JOIN categorie c ON p.categorie_id = c.id
WHERE p.ressourcerie_id = 1;
```

---

## Matrice Décisionnelle

La matrice décisionnelle est un outil utilisé pour évaluer les différentes options de décision et choisir la meilleure solution en fonction des critères de choix.

### Critères de Choix

#### 1. Coût

- **Coût de développement** : Coût de développement de la plateforme
- **Coût d'exploitation** : Coût d'exploitation de la plateforme

#### 2. Performance

- **Vitesse de chargement** : Vitesse de chargement de la plateforme
- **Réactivité** : Réactivité de la plateforme

#### 3. Sécurité

- **Conformité RGPD** : Conformité de la plateforme aux régulations RGPD
- **Sécurité des données** : Sécurité des données stockées sur la plateforme

#### 4. Accessibilité

- **Normes RGAA** : Conformité de la plateforme aux normes RGAA
- **Normes ARIA** : Conformité de la plateforme aux normes ARIA

### Matrice

| Critères de Choix | Coût | Performance | Sécurité | Accessibilité |
|-------------------|------|-------------|-----------|---------------|
| Coût de développement | 1 | 2 | 3 | 4 |
| Coût d'exploitation | 2 | 3 | 4 | 5 |
| Vitesse de chargement | 1 | 2 | 3 | 4 |
| Réactivité | 2 | 3 | 4 | 5 |
| Conformité RGPD | 3 | 4 | 5 | 5 |
| Sécurité des données | 4 | 5 | 5 | 5 |
| Normes RGAA | 5 | 5 | 5 | 5 |
| Normes ARIA | 5 | 5 | 5 | 5 |

### Meilleure Solution

La meilleure solution est celle qui offre le meilleur équilibre entre les différents critères de choix.

---

## Spécifications

### Fonctionnelles

#### 1. Système d'Authentification

- **Inscription et connexion** des utilisateurs avec différents rôles (administrateur, ressourcerie, client)
- **Gestion des profils** avec informations de base (nom, email, téléphone, adresse)
- **Système de vérification d'email** pour sécuriser les inscriptions
- **Gestion des rôles et permissions** adaptée aux besoins spécifiques des ressourceries

#### 2. Gestion des Produits

- **Ajout de produits** avec caractéristiques spécifiques aux objets de seconde main (état, dimensions, couleur, marque, etc.)
- **Upload de photos** pour illustrer les produits (limité à 3 photos par produit dans le POC)
- **Catégorisation** des produits selon une arborescence adaptée aux ressourceries
- **Gestion basique des stocks** (disponible/indisponible/vendu)
- **Édition et suppression** des produits

#### 3. Recherche et Navigation

- **Catalogue produits** avec affichage en grille ou en liste
- **Recherche par mots-clés** dans les titres et descriptions des produits
- **Filtrage par catégorie** et par ressourcerie
- **Géolocalisation des ressourceries** sur une carte interactive
- **Fiches produits détaillées** avec toutes les informations pertinentes

#### 4. Processus de Click-and-Collect

- **Panier d'achat** permettant d'ajouter/supprimer des produits
- **Sélection du créneau de retrait** sur un calendrier simplifié
- **Récapitulatif de commande** avant validation
- **Confirmation par email** après réservation
- **Suivi basique des commandes** (en attente, confirmée, retirée)

#### 5. Calcul d'Impact Environnemental

- **Estimation simplifiée** de l'impact positif des achats de seconde main
- **Affichage des métriques environnementales** (CO2 évité, déchets détournés)
- **Compteur personnel** d'impact pour chaque client
- **Compteur global** pour la plateforme

### Techniques

#### 1. Architecture

- **Modulaire** : La plateforme est conçue pour être modulaire, permettant une évolution et une personnalisation faciles.
- **Évolutive** : La plateforme est conçue pour être évolutive, capable de s'adapter à la croissance de la plateforme et à la diversification des services proposés par les ressourceries.

#### 2. Technologies

- **Backend** : La plateforme est développée en utilisant des technologies modernes, permettant une gestion efficace des données et des interactions.
- **Frontend** : L'interface utilisateur est conçue pour être intuitive et réactive, offrant une expérience utilisateur optimale.

#### 3. Base de Données

- **SQL** : La plateforme utilise une base de données SQL pour stocker les informations pertinentes et gérer les interactions entre les différents acteurs.

#### 4. Sécurité

- **Conformité RGPD** : La plateforme est conçue pour être conforme aux régulations RGPD, garantissant la protection des données personnelles des utilisateurs.
- **Sécurité des données** : La plateforme implémente des mesures de sécurité pour protéger les données stockées.

#### 5. Accessibilité

- **Normes RGAA** : La plateforme est conçue pour être conforme aux normes RGAA, garantissant une accessibilité aux personnes handicapées.
- **Normes ARIA** : La plateforme implémente des fonctionnalités ARIA pour améliorer l'accessibilité aux utilisateurs.

---

## Prototype

Le prototype de la plateforme PIVOT Marketplace est une version fonctionnelle mais limitée de la plateforme, conçue pour valider les concepts fondamentaux et recueillir des retours utilisateurs.

### Vue d'Ensemble

Le POC est déployé auprès d'un panel de 5 ressourceries partenaires qui l'utilisent en conditions réelles, permettant ainsi de tester les fonctionnalités et d'identifier les axes d'amélioration.

### Fonctionnalités Actuellement Implémentées

#### 1. Système d'Authentification

- **Inscription et connexion** des utilisateurs avec différents rôles (administrateur, ressourcerie, client)
- **Gestion des profils** avec informations de base (nom, email, téléphone, adresse)
- **Système de vérification d'email** pour sécuriser les inscriptions
- **Gestion des rôles et permissions** adaptée aux besoins spécifiques des ressourceries

#### 2. Gestion des Produits

- **Ajout de produits** avec caractéristiques spécifiques aux objets de seconde main (état, dimensions, couleur, marque, etc.)
- **Upload de photos** pour illustrer les produits (limité à 3 photos par produit dans le POC)
- **Catégorisation** des produits selon une arborescence adaptée aux ressourceries
- **Gestion basique des stocks** (disponible/indisponible/vendu)
- **Édition et suppression** des produits

#### 3. Recherche et Navigation

- **Catalogue produits** avec affichage en grille ou en liste
- **Recherche par mots-clés** dans les titres et descriptions des produits
- **Filtrage par catégorie** et par ressourcerie
- **Géolocalisation des ressourceries** sur une carte interactive
- **Fiches produits détaillées** avec toutes les informations pertinentes

#### 4. Processus de Click-and-Collect

- **Panier d'achat** permettant d'ajouter/supprimer des produits
- **Sélection du créneau de retrait** sur un calendrier simplifié
- **Récapitulatif de commande** avant validation
- **Confirmation par email** après réservation
- **Suivi basique des commandes** (en attente, confirmée, retirée)

#### 5. Calcul d'Impact Environnemental

- **Estimation simplifiée** de l'impact positif des achats de seconde main
- **Affichage des métriques environnementales** (CO2 évité, déchets détournés)
- **Compteur personnel** d'impact pour chaque client
- **Compteur global** pour la plateforme

### Tests et Validation

Le POC a été testé auprès d'un panel de 5 ressourceries partenaires, permettant de :

- **Valider l'architecture technique** et son adéquation avec les besoins des ressourceries
- **Recueillir des retours utilisateurs** sur l'expérience globale et les fonctionnalités spécifiques
- **Identifier les points forts** de la plateforme à conserver et développer
- **Repérer les axes d'amélioration** pour les futures versions

Les premiers retours sont globalement positifs, avec une appréciation particulière pour l'interface intuitive et la valorisation de l'impact environnemental.

### Limites Actuelles

Le POC présente certaines limitations inhérentes à sa nature de prototype :

1. **Gestion des photos limitée** : Maximum 3 photos par produit, sans optimisation automatique des images
2. **Absence d'intégration** avec les outils existants des ressourceries (logiciels de caisse, etc.)
3. **Statistiques simplifiées** : Tableaux de bord basiques sans analyses avancées
4. **Interface utilisateur perfectible** : Certains parcours utilisateurs peuvent être optimisés
5. **Fonctionnalités communautaires absentes** : Pas de système d'avis ou d'interactions entre utilisateurs

Ces limitations ont été identifiées et documentées pour être adressées dans les prochaines phases de développement, en fonction des priorités établies avec les ressourceries partenaires.

---

## Tests

Les tests sont une étape cruciale pour garantir la qualité et la fiabilité d'une plateforme de commerce électronique. Ils permettent de valider les fonctionnalités et de recueillir des retours utilisateurs.

### Type de Tests

#### 1. Tests Unitaires

- **Objectif** : Valider les fonctionnalités individuelles de la plateforme
- **Méthode** : Utilisation de frameworks de test pour isoler et tester chaque composant de la plateforme

#### 2. Tests d'Intégration

- **Objectif** : Valider la compatibilité et la réactivité des différents composants de la plateforme
- **Méthode** : Test d'intégration entre les composants de la plateforme

#### 3. Tests d'Acceptation

- **Objectif** : Valider la conformité de la plateforme aux exigences du marché
- **Méthode** : Test d'acceptation réalisé par des utilisateurs représentatifs

### Résultats des Tests

Les résultats des tests sont globalement positifs, avec une appréciation particulière pour l'interface intuitive et la valorisation de l'impact environnemental.

---

## Sécurité

La sécurité est une préoccupation majeure pour une plateforme de commerce électronique. Elle implique la protection des données personnelles, la prévention des fraudes et la gestion des incidents.

### Mesures de Sécurité

#### 1. Authentification

- **Authentification** : Utilisation de technologies sécurisées pour authentifier les utilisateurs
- **Vérification d'email** : Implémentation d'un système de vérification d'email pour sécuriser les inscriptions

#### 2. Protection des Données

- **Conformité RGPD** : Implémentation de mesures pour garantir la conformité aux régulations RGPD
- **Sécurité des données** : Implémentation de mesures pour protéger les données stockées

#### 3. Prévention des Fraudes

- **Système de vérification** : Implémentation d'un système de vérification pour prévenir les fraudes

#### 4. Gestion des Incidents

- **Plan de réponse aux incidents** : Implémentation d'un plan de réponse aux incidents pour gérer les événements de sécurité

### Résultats de la Sécurité

Les mesures de sécurité mises en place sont efficaces, garantissant la protection des données personnelles et la prévention des fraudes.

---

## Accessibilité

L'accessibilité est une préoccupation majeure pour une plateforme de commerce électronique. Elle implique la conception d'une interface utilisateur accessible à tous les utilisateurs, y compris les personnes handicapées.

### Mesures d'Accessibilité

#### 1. Conformité aux Normes

- **Normes RGAA** : Conformité de la plateforme aux normes RGAA
- **Normes ARIA** : Implémentation de fonctionnalités ARIA pour améliorer l'accessibilité

#### 2. Personnalisation de l'Interface

- **Thèmes** : Implémentation de thèmes pour améliorer l'accessibilité
- **Réglages** : Implémentation de réglages pour personnaliser l'interface

### Résultats de l'Accessibilité

Les mesures d'accessibilité mises en place sont efficaces, garantissant une accessibilité à tous les utilisateurs.

---

## Conformité

La conformité est une préoccupation majeure pour une plateforme de commerce électronique. Elle implique la conformité à toutes les régulations et législations applicables.

### Mesures de Conformité

#### 1. Conformité RGPD

- **Conformité RGPD** : Implémentation de mesures pour garantir la conformité aux régulations RGPD

#### 2. Conformité aux Législations

- **Conformité aux législations** : Implémentation de mesures pour garantir la conformité aux législations applicables

### Résultats de la Conformité

Les mesures de conformité mises en place sont efficaces, garantissant la conformité à toutes les régulations et législations applicables.

---

## Conclusion

Le projet PIVOT Marketplace est une solution innovante et complète conçue pour les ressourceries. Il répond à la volonté de créer un outil interactif à forte valeur ajoutée, capable de s'adresser à deux cibles distinctes mais complémentaires :

- **Les ressourceries (B2B)**, qui cherchent à étendre leur portée et à digitaliser leur offre sans investissement technique lourd.
- **Les consommateurs éco-responsables (B2C)**, qui souhaitent acheter des produits de seconde main tout en soutenant des structures locales à vocation sociale et environnementale.

Le projet se concentre dans un premier temps sur les fonctionnalités essentielles d'une marketplace adaptée aux ressourceries, avec une attention particulière portée à :

- **La gestion des produits de seconde main**, avec des caractéristiques spécifiques comme l'état, les dimensions, la provenance.
- **Le système de click-and-collect**, privilégiant le retrait en boutique pour renforcer le lien social et réduire l'empreinte carbone.
- **La personnalisation de l'interface**, offrant la possibilité d'adapter l'apparence de la boutique en ligne à l'identité visuelle de chaque ressourcerie.
- **La géolocalisation**, permettant aux utilisateurs de trouver facilement les ressourceries proches de chez eux.

Lors de l'analyse des besoins, plusieurs attentes clés ont été identifiées :

- **Une interface d'administration intuitive**, permettant une gestion simplifiée des produits uniques et des commandes.
- **Des outils de valorisation de l'impact environnemental**, mettant en avant la contribution à l'économie circulaire.
- **Une architecture évolutive**, capable de s'adapter à la croissance de la plateforme et à la diversification des services proposés par les ressourceries.

Ce projet s'inscrit dans le cadre d'un travail académique, avec l'ambition de livrer une solution fonctionnelle, évolutive et adaptée aux besoins spécifiques des ressourceries françaises, tout en sensibilisant le grand public aux enjeux de la consommation responsable et de l'économie circulaire.

---

## Ressources

### 1. Documentation Technique

- **Détail des fonctionnalités** : Documentation détaillée des fonctionnalités de la plateforme
- **Schéma de Base de Données** : Schéma de base de données pour la plateforme
- **Matrice Décisionnelle** : Matrice décisionnelle pour évaluer les différentes options de décision

### 2. Ressources Utiles

- **Liens Utiles** : Liens vers des ressources utiles pour les développeurs et les ressourceries

### 3. Équipe de Projet

- **Membres de l'Équipe** : Liste des membres de l'équipe de projet

### 4. Événements

- **Événements Associés** : Liste des événements associés au projet

### 5. Réseaux Sociaux

- **Réseaux Sociaux** : Liens vers les réseaux sociaux associés au projet 