# Guide d'Intégration des Diagrammes et Schémas

Ce document sert de guide pour l'intégration des diagrammes et schémas dans la documentation du projet PIVOT. Il définit les standards, les outils recommandés, et les procédures à suivre pour créer et intégrer des éléments visuels cohérents et informatifs.

## Standards et Conventions

### Format et Résolution

- **Formats acceptés** : PNG, SVG (préféré pour les diagrammes), JPEG (uniquement pour les photos)
- **Résolution minimale** : 1200 x 800 pixels pour les captures d'écran
- **DPI** : 72 minimum, 144 recommandé pour une meilleure qualité
- **Taille maximale** : 500 Ko par image (optimiser si nécessaire)

### Nommage des Fichiers

Format de nommage : `section_type-de-diagramme_description-courte.extension`

Exemples :
- `modelisation_mcd_entites-principales.svg`
- `securite_architecture_couches-protection.png`
- `accessibilite_parcours_lecteur-ecran.svg`

### Style et Apparence

- **Palette de couleurs** : Utiliser la palette officielle du projet PIVOT
  - Primaire : #3B82F6 (bleu)
  - Secondaire : #10B981 (vert)
  - Accent : #F59E0B (orange)
  - Texte : #1F2937 (gris foncé)
  - Fond : #F9FAFB (gris clair)

- **Typographie** :
  - Titres : Inter, 16-24px
  - Légendes : Inter, 12-14px
  - Étiquettes : Inter, 10-12px

- **Éléments graphiques** :
  - Bordures arrondies (4px)
  - Ombres légères pour la profondeur
  - Icônes cohérentes (style outline)

## Outils Recommandés

### Création de Diagrammes

1. **[Draw.io](https://app.diagrams.net/)** (gratuit)
   - Recommandé pour les diagrammes d'architecture, flux de données, et MCD
   - Exporter en SVG pour une meilleure qualité

2. **[Figma](https://www.figma.com/)** (freemium)
   - Recommandé pour les maquettes d'interface et parcours utilisateur
   - Utiliser les composants réutilisables pour maintenir la cohérence

3. **[Mermaid](https://mermaid.js.org/)** (gratuit)
   - Pour les diagrammes simples intégrés directement dans Markdown
   - Idéal pour les diagrammes de séquence et les organigrammes

4. **[dbdiagram.io](https://dbdiagram.io/)** (gratuit)
   - Spécifique pour les diagrammes de base de données
   - Export en PNG ou PDF

### Capture et Édition d'Écran

1. **[Snagit](https://www.techsmith.com/screen-capture.html)** (payant)
   - Pour les captures d'écran annotées
   - Possibilité de créer des GIF pour les démonstrations

2. **[ShareX](https://getsharex.com/)** (gratuit, Windows)
   - Alternative gratuite pour les captures d'écran
   - Nombreuses options de personnalisation

3. **[GIMP](https://www.gimp.org/)** (gratuit)
   - Édition avancée d'images
   - Optimisation et redimensionnement

## Types de Diagrammes par Section

### Modélisation

1. **Modèle Conceptuel de Données (MCD)**
   - Entités, attributs et relations
   - Cardinalités clairement indiquées
   - Groupement logique des entités

2. **Diagramme de Classes**
   - Structure des modèles Laravel
   - Relations Eloquent
   - Méthodes principales

3. **Schéma de Base de Données**
   - Tables, colonnes et types
   - Clés primaires et étrangères
   - Indexes

### Spécifications Techniques

1. **Architecture Globale**
   - Couches applicatives
   - Composants principaux
   - Interactions entre composants

2. **Flux de Données**
   - Parcours des requêtes
   - Traitement des données
   - Points d'intégration

3. **Diagramme de Déploiement**
   - Environnements (dev, test, prod)
   - Services et serveurs
   - Configuration réseau

### Spécifications Fonctionnelles

1. **Parcours Utilisateur**
   - Étapes principales
   - Points de décision
   - États et transitions

2. **Wireframes**
   - Maquettes basse fidélité
   - Annotations fonctionnelles
   - Variantes selon les rôles

3. **Diagrammes de Cas d'Utilisation**
   - Acteurs
   - Actions principales
   - Relations entre cas d'utilisation

### Sécurité

1. **Architecture de Sécurité**
   - Couches de protection
   - Points de contrôle
   - Mécanismes de défense

2. **Flux d'Authentification**
   - Processus de login/logout
   - Vérification des permissions
   - Gestion des sessions

3. **Matrice des Menaces**
   - Types de menaces
   - Probabilité et impact
   - Contre-mesures

### Accessibilité

1. **Checklist d'Accessibilité**
   - Critères WCAG 2.1
   - Statut d'implémentation
   - Priorités

2. **Parcours avec Lecteur d'Écran**
   - Séquence de navigation
   - Points d'attention
   - Alternatives textuelles

3. **Composants Accessibles**
   - Exemples d'implémentation
   - Attributs ARIA
   - États et feedback

### Conformité

1. **Flux de Données Personnelles**
   - Collecte
   - Traitement
   - Stockage
   - Suppression

2. **Processus d'Exercice des Droits**
   - Demande d'accès
   - Rectification
   - Suppression
   - Portabilité

3. **Matrice de Conformité RGPD**
   - Exigences
   - Statut
   - Actions requises

## Procédure d'Intégration

### Étapes à Suivre

1. **Création du diagramme**
   - Utiliser l'outil recommandé
   - Suivre les standards de style
   - Valider avec l'équipe

2. **Export et optimisation**
   - Exporter dans le format approprié
   - Optimiser la taille si nécessaire
   - Vérifier la lisibilité

3. **Placement dans le répertoire**
   - Créer un sous-dossier `images` dans la section concernée
   - Utiliser la convention de nommage
   - Mettre à jour le registre des diagrammes

4. **Intégration dans la documentation**
   - Ajouter la référence Markdown
   - Inclure une légende descriptive
   - Lier aux sections pertinentes

### Exemple d'Intégration Markdown

```markdown
## Architecture Technique

L'architecture du POC PIVOT est organisée en plusieurs couches, comme illustré ci-dessous :

![Architecture technique du POC PIVOT](./images/specifications_architecture_couches.svg)
*Figure 1 : Architecture en couches du POC PIVOT Marketplace*

Cette architecture permet de séparer clairement les responsabilités et facilite la maintenance.
```

## Registre des Diagrammes

| ID | Section | Type | Nom de fichier | Description | Statut |
|----|---------|------|---------------|-------------|--------|
| 01 | Modélisation | MCD | modelisation_mcd_entites-principales.svg | Modèle conceptuel de données principal | À créer |
| 02 | Spécifications | Architecture | specifications_architecture_globale.svg | Architecture technique globale | À créer |
| 03 | Sécurité | Flux | securite_flux_authentification.svg | Processus d'authentification | À créer |
| ... | ... | ... | ... | ... | ... |

## Conclusion

L'intégration cohérente de diagrammes et schémas dans la documentation du projet PIVOT est essentielle pour améliorer la compréhension et la clarté. En suivant ce guide, nous assurons une présentation visuelle professionnelle et informative qui complète efficacement le contenu textuel.

---

*Dernière mise à jour : Février 2025*

[Retour à l'index](../README.md) 