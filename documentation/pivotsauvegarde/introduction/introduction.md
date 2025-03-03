# Dossier Technique - Projet PIVOT Marketplace

## Table des matières

- [Introduction](#introduction)
- [Contexte](#contexte)
- [Modélisation](#modélisation)
  - [Modèle Conceptuel de Données (MCD)](#modèle-conceptuel-de-données-mcd)
  - [UML de cas d'utilisation](#uml-de-cas-dutilisation)
- [Matrice Décisionnelle](#matrice-décisionnelle)
  - [Choix Technologiques](#choix-technologiques)
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
- [Étude Technique](#étude-technique)
  - [Architecture Technique](#architecture-technique)
  - [Choix Technologiques](#choix-technologiques)
  - [Contraintes Techniques](#contraintes-techniques)
  - [Environnement de Développement](#environnement-de-développement)
  - [Déploiement et Maintenance](#déploiement-et-maintenance)
  - [Conclusion des Spécifications Techniques](#conclusion-des-spécifications-techniques)
- [Spécifications Fonctionnelles](#spécifications-fonctionnelles)
- [Spécifications Techniques](#spécifications-techniques)
- [Prototype](#prototype)

## Introduction

Dans un contexte économique où les plateformes de commerce en ligne connaissent une croissance exponentielle, notre projet PIVOT Marketplace vise à offrir une solution innovante et complète spécifiquement conçue pour les ressourceries. Ces structures, dédiées à la récupération, la valorisation et la revente de biens sur un territoire donné, jouent un rôle crucial dans la sensibilisation et l'éducation à l'environnement, contribuant ainsi à l'économie circulaire et à la réduction des déchets.

PIVOT est la première plateforme de click-and-collect dédiée aux ressourceries en France, permettant de donner une seconde vie aux produits dénichés tout en créant de nouvelles interactions sociales. Le projet s'articule autour d'une architecture modulaire et évolutive, permettant à chaque ressourcerie de configurer sa boutique en ligne selon ses besoins spécifiques. Grâce à une interface intuitive et des fonctionnalités avancées, les ressourceries peuvent créer, personnaliser et gérer leur espace de vente sans nécessiter de compétences techniques approfondies.

En tant que développeur web du projet, mon rôle est de concevoir et de réaliser l'ensemble de la plateforme. Cela inclut le développement du backend, la création d'une interface utilisateur intuitive, la mise en place des fonctionnalités techniques clés, la gestion des données, ainsi que l'intégration des bonnes pratiques en matière de sécurité (conformité RGPD) et d'accessibilité (normes RGAA, ARIA, etc.).

Ce dossier technique détaillera les différentes étapes de la réalisation du projet, des besoins initiaux aux choix techniques effectués, en passant par la modélisation des données et l'analyse des fonctionnalités clés.

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