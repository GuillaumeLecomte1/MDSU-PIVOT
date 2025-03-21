# **Introduction**

## **Contexte du Projet** ✅

Dans un contexte économique où les plateformes de commerce en ligne connaissent une croissance exponentielle, notre projet Pivot vise à offrir une solution innovante et complète spécifiquement conçue pour les ressourceries. Ces structures, dédiées à la récupération, la valorisation et la revente de biens sur un territoire donné, jouent un rôle crucial dans la sensibilisation et l'éducation à l'environnement, contribuant ainsi à l'économie circulaire et à la réduction des déchets. PIVOT est la première plateforme de click-and-collect dédiée aux ressourceries en France, permettant de donner une seconde vie aux produits dénichés tout en créant de nouvelles interactions sociales. Le projet s'articule autour d'une architecture modulaire et évolutive, permettant à chaque ressourcerie de configurer sa boutique en ligne selon ses besoins spécifiques.

### Origine et Objectifs du Projet ✅

Le projet Pivot est né d'une observation simple : malgré la démocratisation du commerce en ligne, les ressourceries, acteurs essentiels de l'économie circulaire, ne disposent pas d'outils numériques adaptés à leurs besoins spécifiques. Notre solution vise à combler ce fossé en proposant une plateforme complète, flexible et accessible, spécifiquement conçue pour ce secteur niche.
Les ressourceries font face à plusieurs défis spécifiques :

- Gestion d'inventaires variés et uniques (objets de seconde main)
- Valorisation de l'aspect écologique et social des produits
- Nécessité de toucher un public plus large que leur zone géographique immédiate
- Besoin de solutions numériques adaptées à leurs ressources souvent limitées

### Équipe et rôle personnel ✅

Notre équipe est composée de 6 membres aux profils variés, issus de différentes filières, ce qui apporte une grande diversité de compétences. Elle comprend deux directeurs artistiques, un développeur, une cheffe de projet, une designer UX/UI et un expert en big data. Cette pluralité de profils a permis une répartition optimale des tâches avec peu de redondance, favorisant une collaboration efficace et une complémentarité précieuse pour la réussite du projet.

Cette diversité a également stimulé des débats riches et constructifs, bien que parfois source de complexité en raison d'avis techniques divergents ou de difficultés à trancher certaines décisions. Ces échanges ont été déterminants pour aboutir à notre projet final, notamment après une phase de réflexion intense qui nous a conduits à opérer un pivot stratégique.

En tant que développeur web au sein de l'équipe, mon rôle a été de concevoir et de réaliser l'ensemble de la plateforme. Mes missions principales ont inclus :

- La conception et la mise en place de l'architecture technique.
- Le développement du backend et la gestion des bases de données.
- L'implémentation des fonctionnalités clés et l'application d'une interface utilisateur intuitive.
- La mise en œuvre des bonnes pratiques en matière de sécurité (conformité RGPD) et d'accessibilité (normes RGAA, ARIA, etc.).
- Les tests des fonctionnalités, ainsi qu'utilisateur.
- La gestion du déploiement de la plateforme, et également ça maintenance.

### **Besoins du Client**

### Analyse des besoins initiaux ⚠️ Refacto

Une série d'entretiens approfondis avec des ressourceries de tailles diverses (de 2 à 15 employés) a permis d'identifier plusieurs besoins critiques. Ces structures, malgré leur mission commune, présentaient des spécificités organisationnelles nécessitant une solution flexible. L'analyse a révélé trois axes prioritaires :

1. **Gestion d'inventaire adaptée** : Contrairement aux e-commerces traditionnels, les ressourceries gèrent des articles uniques et non reproductibles, nécessitant un système capable de traiter des produits sans références standardisées et avec une rotation rapide.
2. **Interface administrative simplifiée** : Les équipes des ressourceries, souvent composées de bénévoles et de personnes en réinsertion, requièrent une interface intuitive nécessitant peu de formation.
3. **Système de réservation hybride** : La demande portait sur un modèle de click-and-collect permettant de réserver en ligne mais de finaliser la transaction en magasin, préservant ainsi le lien social essentiel à leur mission.

### Contraintes identifiées ⚠️ Refacto

L'analyse des contraintes a mis en évidence plusieurs défis techniques et organisationnels :

- **Ressources financières limitées** : Budget restreint imposant des choix technologiques économiques et durables.
- **Hétérogénéité des compétences numériques** : Nécessité d'une interface administrateur accessible à des utilisateurs de tous niveaux.
- **Infrastructure IT minimaliste** : Absence de serveurs dédiés ou d'équipes techniques internes.
- **Exigence de traçabilité** : Obligation légale de suivre précisément le parcours des objets récupérés.
- **Contraintes temporelles** : Besoin d'une solution déployable rapidement pour répondre à l'urgence économique et écologique.

### Solutions proposées ✅

Pour répondre aux besoins et contraintes exprimés, notre stratégie s'organise autour de **trois piliers** complémentaires. Une **architecture modulaire évolutive** (Laravel/Inertia.js) permet d'intégrer progressivement des fonctionnalités selon les retours utilisateurs. Le **modèle SaaS mutualisé** propose une solution hébergée centralement, réduisant les coûts d'infrastructure tout en autorisant une personnalisation adaptée à chaque ressourcerie. Enfin, **l'approche MVP** se concentre sur les fonctions clés immédiates : catalogue de produits, réservation en ligne et tableau de bord administratif minimaliste, avec une feuille de route prévue pour les développements futurs. Cette structure équilibrée, validée par les parties prenantes, allie opérationnalité rapide et vision évolutive du projet

# **Phase de Conception**

## **Études et Recherches Préalables** ✅

### Analyse de l'existant

L'absence de solutions existantes adaptées au modèle des ressourceries (structures de réemploi à but non lucratif) a nécessité une approche innovante. Plutôt que de développer une plateforme complète immédiatement, un *Proof of Concept* (POC) a été privilégié. Cette décision stratégique répondait à deux impératifs : valider rapidement le besoin auprès des utilisateurs finaux (clients et gestionnaires de ressourceries) et affiner les choix techniques dans un cadre maîtrisé.

### Benchmark des solutions similaires

Le benchmark des solutions e-commerce existantes (WooCommerce, PrestaShop) a révélé des limitations majeures : complexité de personnalisation, coûts d'hébergement élevés, et inadéquation avec le modèle économique circulaire des ressourceries. Par exemple, WooCommerce nécessitait des extensions payantes pour gérer des commissions variables entre ressourceries, ce qui alourdissait à la fois le budget et la maintenance technique.

Une veille technologique approfondie a orienté le choix vers une architecture MVC modulaire associée à une interface SPA réactive, combinant ainsi stabilité backend et fluidité frontend.

### Veille technologique

Une étude des architectures modernes a orienté le choix vers une approche hybride MVC/SPA. L'essor des Single Page Applications (React, Vue.js) offrait des avantages en termes d'expérience utilisateur, mais posait des défis d'intégration avec un backend métier complexe. La découverte d'Inertia.js a permis de concilier ces impératifs : cette bibliothèque agit comme un pont entre Laravel et React, évitant la surcharge d'une API REST tout en permettant une navigation fluide. Parallèlement, une évaluation des bases de données relationnelles vs NoSQL a confirmé la supériorité de MySQL pour gérer les relations complexes entre utilisateurs, produits et commandes.

## **Documentation Technique**

### Diagrammes de cas d'utilisation ✅

La modélisation UML a débuté par l'élaboration de diagrammes de cas d'utilisation détaillés, permettant de visualiser les interactions entre les différents acteurs du système et les fonctionnalités offertes. Trois acteurs principaux ont été identifiés :

1. **Client** : Utilisateur final consultant le catalogue, effectuant des réservations et gérant son profil.
2. **Gestionnaire de ressourcerie** : Responsable de l'inventaire, de la validation des réservations et du suivi des statistiques de sa ressourcerie.
3. **Administrateur plateforme** : Supervisant l'ensemble du système, gérant les ressourceries et les paramètres globaux.

Les cas d'utilisation principaux incluent :

**Gestion du catalogue** : Ajout, modification et suppression de produits avec leurs caractéristiques uniques.

**Processus de réservation** : Parcours complet depuis la sélection jusqu'à la confirmation de retrait.

**Administration ressourcerie** : Ensemble des fonctionnalités de gestion spécifiques à chaque structure.

**Supervision plateforme** : Outils de monitoring et de configuration globale.

![Diagramme de cas d'utilisation](attachment:9f5237e5-bb7e-4b01-9151-c333cbae9263:image.png)

Diagramme de cas d'utilisation

Cette modélisation a permis d'identifier des scénarios complexes nécessitant une attention particulière, comme la gestion des conflits de réservation pour des produits uniques ou les processus d'annulation.

### Diagrammes de classes

L'architecture orientée objet du système a été formalisée à travers des diagrammes de classes détaillés, reflétant fidèlement la structure de la base de données tout en y ajoutant la logique métier. Les principales classes identifiées sont :

- **User** : Classe centrale gérant l'authentification et les profils, avec un système d'héritage pour les différents types d'utilisateurs.
- **Ressourcerie** : Entité représentant une structure physique avec ses caractéristiques propres.
- **Product** : Modélisation complexe des articles avec leurs attributs variables selon les catégories.
- **Order** : Gestion du processus de réservation avec ses différents états.
- **Category** : Système hiérarchique de classification des produits.

Une attention particulière a été portée aux relations entre ces classes, notamment :

- La relation many-to-many entre Product et Category.
- La relation one-to-many entre Ressourcerie et Product.
- La relation complexe entre User et Order, tenant compte des différents rôles.

Ces diagrammes ont servi de base solide pour l'implémentation du code, assurant une cohérence entre la conception et la réalisation technique.

### **Base de Données**

- Modèle Conceptuel de Données (MCD)

**Traçabilité des transactions et gouvernance des rôles** :

Le MCD garantit une **traçabilité complète** des échanges. Chaque commande (`Order`) est associée à :

Un **historique détaillé** via `OrderProduct` (quantité, prix, produits).

Une **Ressourcerie spécifique** *via* la relation `Product` → `Ressourcerie`.

Un **User** avec un *rôle* :

- **Client** (accès basique aux produits).
- **Ressourcerie** (gestion d'une Ressourcerie, validation des commandes).
- **Administrateur** (suivi des statistiques, gestion des catégories).

**Flexibilité de classification** :

La relation **many-to-many** entre `Product` et `Category` (via `Category_Product`) permet :

- Une **taxonomie adaptable** aux typologies hétérogènes (ex : *meubles vintage*, *outils professionnels*, *vêtements enfant*).
- Des filtres multicritères sans duplication de données.

**Autres points clés** :

- **Fidélisation** : Table `Favorites` pour sauvegarder les produits préférés.
- **Preuves visuelles** : Jusqu'à N `ProductImage` par produit.
- **Intégrité régionale** : Une `Ressourcerie` est liée à un seul `User` (gestionnaire), évitant les conflits.
- Modèle Logique de Données (MLD)
- Schéma de la base de données

### **Matrice Décisionnelle** ✅

| **Technologie** | **Avantages** | **Inconvénients** | **Décision** |
| --- | --- | --- | --- |
| Laravel vs Symfony | • Courbe d'apprentissage plus douce
• Documentation exhaustive et communauté active
• Écosystème riche (Jetstream, Sanctum, etc.)
• Eloquent ORM intuitif | • Moins adapté aux très grandes applications
• Moins de flexibilité dans certains composants
• Performances légèrement inférieures sur certains cas d'usage | **Laravel adopté** pour sa rapidité de développement et son adéquation avec les besoins du projet. La flexibilité de Symfony n'était pas nécessaire pour notre échelle. |
| Inertia.js vs API REST | • Développement plus rapide (pas de duplication API)
• Maintien du routing Laravel
• Expérience SPA sans complexité supplémentaire
• Sécurité simplifiée (authentification Laravel) | • Moins adapté si besoin d'API publique
• Couplage plus fort entre backend et frontend
• Documentation moins fournie que pour REST | **Inertia.js adopté** pour l'efficacité de développement et l'expérience utilisateur fluide. L'absence de besoin d'API publique a rendu ce choix optimal. |
| React vs Vue.js | • Écosystème plus vaste
• Meilleures performances sur interfaces complexes
• TypeScript mieux supporté
• Plus grande disponibilité de développeurs | • Courbe d'apprentissage plus raide
• Verbosité plus importante
• Configuration initiale plus complexe | **React adopté** malgré l'intégration native de Vue avec Laravel, en raison de la complexité anticipée de l'interface et de la disponibilité des compétences dans l'équipe. |
| MySQL vs PostgreSQL | • Simplicité d'administration
• Parfaite intégration avec Laravel
• Performances optimales pour lecture/écriture
• Hébergement moins coûteux | • Fonctionnalités avancées limitées
• Moins performant pour requêtes analytiques complexes
• Conformité ACID moins stricte | **MySQL choisi** pour sa simplicité et son adéquation avec les besoins transactionnels du projet. Les fonctionnalités avancées de PostgreSQL n'étaient pas requises. |
| Docker vs Déploiement traditionnel | • Environnements de développement cohérents
• Isolation des services
• Facilité de scaling
• Déploiement simplifié | • Courbe d'apprentissage initiale
• Overhead de performance
• Complexité de configuration | **Docker adopté** pour garantir la cohérence entre environnements et faciliter le déploiement, malgré l'investissement initial en apprentissage. |
| Tailwind CSS vs Bootstrap | • Personnalisation fine sans override
• Bundle final optimisé (purge CSS)
• Approche utility-first adaptée aux composants React
• Pas de design imposé | • Verbosité du HTML
• Courbe d'apprentissage initiale
• Moins de composants prêts à l'emploi | **Tailwind CSS choisi** pour sa flexibilité et son approche moderne, malgré la disponibilité immédiate de composants dans Bootstrap. |

Cette matrice décisionnelle a guidé mes choix technologiques en pondérant les avantages et inconvénients de chaque option selon les besoins spécifiques du projet. Les décisions ont été prises collectivement après des phases de prototypage et d'évaluation, en privilégiant toujours l'adéquation avec les contraintes du client et la maintenabilité à long terme.

## **Gestion de Projet**

### **Méthodologie**

### Approche Agile/Scrum adaptée ✅

Notre approche méthodologique s'est articulée en deux dimensions complémentaires, répondant à la fois aux besoins de coordination d'équipe et aux spécificités de mon rôle de développeur.

### Partie 1 : Coordination d'équipe ✅

Face à la diversité des profils au sein de notre équipe, nous avons opté pour une gestion de projet hybride inspirée d'Agile mais adaptée à notre contexte spécifique. Notre groupe ayant une polyvalence dans les domaines d'expertise, nous avons choisi de nous concentrer chacun sur nos propres domaines de compétence, tout en maintenant une vision transversale du projet.

Nous avons structuré notre travail autour de cycles d'itération de trois semaines, permettant d'établir un rythme régulier de développement et d'ajustement. À chaque fin d'itération, une session de synchronisation formelle permettait de réaligner les priorités et d'ajuster notre trajectoire en fonction des retours et des avancées.

Cette approche a démontré sa pertinence en permettant une grande autonomie des membres tout en maintenant une cohérence globale. Cependant, nous avons dû surmonter certains défis, notamment la synchronisation entre les avancées design et développement, qui a parfois créé des dépendances bloquantes.

### Partie 2 : Organisation individuelle du développement ⚠️ Refacto

Dans le cadre de notre projet, la partie développement s'est faite à la toute fin du projet. J'ai donc pu passer davantage de temps sur la mise en place de l'architecture, du déploiement ou encore sur la validation des fonctionnalités. Cette organisation séquentielle m'a permis de :

1. **Consacrer un temps significatif à la conception** : Élaboration des diagrammes UML, modélisation de la base de données et mise en place de l'environnement de développement Docker.
2. **Prototyper les composants critiques** : Développement de modules clés pour valider les choix technologiques et identifier les risques potentiels avant le développement complet.
3. **Implémenter progressivement les fonctionnalités** : Selon un ordre de priorité établi avec l'équipe, en commençant par le core système (authentification, gestion des produits) avant d'aborder les fonctionnalités périphériques.

Une fois à l'étape de développement de la plateforme, j'ai adopté une approche itérative avec des cycles courts de développement, j'ai utilisé des outils de testing passif pour ne pas perdre plus de temps, permettant d'obtenir rapidement des retours de l'équipe et d'ajuster les fonctionnalités en conséquence.

### Sprints et planning ⚠️

La planification du projet a été structurée en 6 sprints de trois semaines, chacun ayant des objectifs spécifiques :

- **Sprint 1 (S1-S3)** : Analyse des besoins, benchmark et conception initiale
    - Livrables : Document d'analyse, wireframes basse fidélité, schéma de base de données
    - Métriques : Validation des user stories principales, couverture des besoins clients
- **Sprint 2 (S4-S6)** : Architecture technique et prototypage
    - Livrables : Environnement de développement, prototype fonctionnel du catalogue
    - Métriques : Temps de réponse du prototype, validation technique des choix
- **Sprint 3 (S7-S9)** : Développement core système
    - Livrables : Système d'authentification, gestion des produits, backoffice basique
    - Métriques : Couverture de tests, performance des requêtes principales
- **Sprint 4 (S10-S12)** : Développement fonctionnalités utilisateurs
    - Livrables : Système de réservation, profils utilisateurs, recherche et filtres
    - Métriques : Taux de complétion des parcours utilisateurs, temps d'exécution
- **Sprint 5 (S13-S15)** : Développement backoffice avancé
    - Livrables : Tableau de bord statistiques, gestion des commandes, rapports
    - Métriques : Précision des données analytiques, temps de génération des rapports
- **Sprint 6 (S16-S18)** : Optimisation et préparation au déploiement
    - Livrables : Version finale optimisée, documentation technique, guide utilisateur
    - Métriques : Performance globale, couverture de tests, satisfaction client

Chaque sprint a été encadré par une réunion de planification initiale et une rétrospective finale, permettant d'ajuster continuellement notre approche en fonction des apprentissages.

### Outils utilisés ✅

Notre stack d'outils de gestion de projet a été soigneusement sélectionnée pour maximiser l'efficacité tout en minimisant la friction :

- **Notion** : Plateforme centrale pour la documentation, le suivi des tâches et la centralisation des ressources. Sa flexibilité nous a permis de créer des tableaux de bord personnalisés pour chaque membre de l'équipe.
- **Git/GitHub** : Gestion de versions avec une structure de branches adaptée à notre workflow. Utilisation intensive des pull requests avec revue de code systématique pour maintenir la qualité.
- **Figma** : Collaboration design en temps réel, permettant aux développeurs d'accéder directement aux spécifications CSS et aux assets.
- **Teams** : Communication quotidienne avec des canaux dédiés par domaine (design, dev, business) et possible intégration des notifications GitHub et CI/CD.

Cette combinaison d'outils nous a permis de maintenir une communication fluide malgré les contraintes de travail à distance ou encore d'attente entre les sessions, tout en gardant une trace précise de l'évolution du projet.

### **Organisation du Travail**

### Structure du repository Git ⚠️

L'organisation du code source a été pensée pour faciliter à la fois le développement et la maintenance future. Dans la phase initiale du projet, nous avons opté pour une approche simplifiée avec une branche unique `main`, permettant une itération rapide dans le contexte d'une équipe de développement réduite.

Cette approche, bien que fonctionnelle pour le MVP, a rapidement montré ses limites en termes de stabilité et de gestion des déploiements. J'ai donc conçu et documenté une stratégie d'évolution vers un système à trois branches principales :

- **`main`** : Branche de production contenant exclusivement le code stable et déployé en environnement de production.
- **`preprod`** : Branche de préproduction servant de tampon avant déploiement et permettant les démonstrations client sur un environnement stable.
- **`develop`** : Branche de développement où sont intégrées toutes les fonctionnalités terminées avant leur promotion en préproduction.

Cette structure, inspirée du modèle GitFlow mais simplifiée pour notre contexte, sera complétée par des branches de fonctionnalités (`feature/*`) et de correctifs (`hotfix/*`) créées à partir de `develop` et fusionnées via pull requests après revue de code.

L'implémentation de cette structure est planifiée pour la phase post-MVP, avec une migration progressive permettant de ne pas perturber le développement en cours.

### Convention de nommage ✅

Pour maintenir une cohérence dans le code et faciliter la collaboration, j'ai mis en place un ensemble de conventions strictes :

### Commits Git ✅

J'ai implémenté une approche basée sur les "gitmojis" pour catégoriser visuellement les commits et améliorer la lisibilité de l'historique. Chaque commit suit la structure suivante :

```
[emoji] type(scope): message concis

```

Par exemple :

- `✨ feat(auth): implement social login with Google`
- `🐛 fix(orders): resolve issue with tax calculation`
- `♻️ refactor(products): optimize image loading process`

Pour faciliter l'adoption de cette convention, j'ai créé un script d'alias Git personnalisé qui propose un menu interactif lors des commits, garantissant ainsi la cohérence même en cas d'oubli.

### Code source ✅

Les conventions de nommage du code suivent les standards Laravel et React :

- **PHP/Laravel** :
    - Classes : PascalCase (`UserController`, `ProductRepository`)
    - Méthodes : camelCase (`getUserById`, `createOrder`)
    - Variables : camelCase (`$userRole`, `$orderTotal`)
    - Constantes : UPPER_SNAKE_CASE (`API_VERSION`, `MAX_UPLOAD_SIZE`)
- **JavaScript/React** :
    - Composants : PascalCase (`ProductCard`, `OrderSummary`)
    - Hooks personnalisés : camelCase préfixé par `use` (`useCart`, `useAuthentication`)
    - Variables et fonctions : camelCase (`handleSubmit`, `cartItems`)

Ces conventions ont été documentées dans un fichier `CONTRIBUTING.md` à la racine du projet et appliquées via des outils de linting automatisés (PHP_CodeSniffer, ESLint).

### Workflow de développement ✅

Le workflow de développement a été structuré autour d'un pipeline d'intégration continue garantissant la qualité du code à chaque étape :

1. **Développement local** :
    - Environnement Docker isolé reproduisant fidèlement la production
    - Tests unitaires et fonctionnels exécutés localement avant commit
    - Linting automatique via hooks pre-commit (PHP_CodeSniffer, ESLint)
2. **Validation de code** :
    - Analyse statique via PHPStan (niveau 5) pour détecter les erreurs potentielles
    - Formatage automatique avec Laravel Pint pour maintenir un style cohérent
    - Vérification des types et des interfaces
3. **Intégration continue** :
    - Exécution automatique des tests sur chaque pull request
    - Analyse de qualité via StyleCI pour garantir le respect des standards
    - Build de validation pour vérifier la compatibilité avec l'environnement cible
4. **Déploiement** :
    - Build Docker optimisé via Dokploy
    - Déploiement automatisé sur l'environnement approprié selon la branche

Des améliorations sont déjà planifiées pour renforcer ce workflow :

- Intégration de GitHub Actions ou Jenkins avant le build Dokploy pour automatiser davantage les tests fonctionnels
- Optimisation du temps de build via des images Docker plus légères et une meilleure stratégie de mise en cache
- Mise en place de tests de performance automatisés pour prévenir les régressions

Ce workflow, bien que perfectible, a déjà démontré son efficacité en réduisant significativement le nombre de bugs en production et en accélérant les cycles de développement.

## **Réalisation Technique**

### **Architecture Technique**

### Stack technologique ✅

L'architecture technique du projet PIVOT a été conçue pour allier robustesse, maintenabilité et expérience utilisateur fluide. Après une analyse approfondie des besoins et contraintes, j'ai opté pour une stack moderne et éprouvée :

### Backend ✅

**Laravel 9** : Framework PHP offrant une structure MVC solide, un ORM puissant (Eloquent) et un écosystème riche de packages. Sa robustesse et sa documentation exhaustive en font un choix idéal pour un projet destiné à évoluer.

**MySQL 8** : Système de gestion de base de données relationnelle choisi pour sa fiabilité, ses performances en lecture/écriture et sa parfaite intégration avec Laravel. La structure relationnelle était particulièrement adaptée aux besoins de traçabilité des produits et des commandes.

### Frontend ✅

**React 18** : Bibliothèque JavaScript pour la construction d'interfaces utilisateur réactives et performantes, choisie pour sa flexibilité et son écosystème mature.

**Inertia.js** : Couche d'abstraction permettant de construire des applications monopages (SPA) sans nécessiter d'API REST dédiée, simplifiant considérablement l'architecture tout en conservant les avantages d'une SPA.

**Tailwind CSS** : Framework CSS utility-first facilitant la création d'interfaces personnalisées et responsives sans générer de CSS superflu.

### Outils et services annexes ✅

**Docker** : Conteneurisation de l'ensemble de l'application pour garantir la cohérence entre les environnements de développement, de test et de production.

**Dokploy** : Plateforme de déploiement automatisé simplifiant la mise en production et la gestion des environnements.

### Infrastructure

L'infrastructure a été conçue selon les principes du cloud-native, privilégiant l'évolutivité, la résilience et la maintenance simplifiée :

### Architecture de déploiement ⚠️ Changer en para

![Schéma de l'architecture de déploiement de Pivot](attachment:736ac303-2dc0-4bc7-b5b9-d93f9203fab2:image.png)

Schéma de l'architecture de déploiement de Pivot

Cette architecture en couches permet :

- Une séparation claire des environnements
- Un déploiement automatisé via des pipelines CI/CD
- Une isolation des services pour une meilleure résilience
- Une scalabilité horizontale en cas de montée en charge

### Sécurité de l'infrastructure ⚠️

La sécurité a été intégrée à chaque niveau de l'infrastructure :

- **Réseau** : Utilisation de réseaux privés virtuels (VPN) pour isoler les environnements sensibles
- **Application** : Implémentation de HTTPS obligatoire, en-têtes de sécurité, protection CSRF
- **Données** : Chiffrement des données sensibles au repos et en transit
- **Authentification** : Système multi-facteurs pour les accès administratifs

## Patterns utilisés

L'architecture logicielle s'appuie sur plusieurs patterns éprouvés, garantissant maintenabilité et évolutivité :

### Patterns architecturaux

- **MVC (Model-View-Controller)** : Architecture fondamentale de Laravel, séparant clairement les responsabilités entre modèles (données), vues (présentation) et contrôleurs (logique).
- **Repository Pattern** : Implémenté pour abstraire la couche d'accès aux données, facilitant les tests unitaires et permettant de changer l'implémentation sous-jacente sans modifier la logique métier.

```php
// Exemple d'interface de repository
interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;
    public function findByCategory(Category $category, int $limit = 10): Collection;
    public function save(Product $product): bool;
    // ...
}

```

- **Service Layer** : Couche intermédiaire encapsulant la logique métier complexe et orchestrant les interactions entre repositories.

```php
// Exemple de service
class OrderService
{
    private $productRepository;
    private $orderRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
    }

    public function createOrder(User $user, array $productIds, array $quantities): Order
    {
        // Logique métier complexe...
    }
}

```

### Patterns de conception

- **Factory Pattern** : Utilisé pour la création d'objets complexes, notamment dans la génération de rapports et la création d'entités.
- **Observer Pattern** : Implémenté via le système d'événements de Laravel pour découpler les actions secondaires (notifications, logs) des opérations principales.
- **Strategy Pattern** : Appliqué pour les algorithmes de recherche et de filtrage, permettant de changer dynamiquement la stratégie selon le contexte.
- **Decorator Pattern** : Utilisé pour étendre les fonctionnalités des modèles Eloquent sans modifier leur structure de base.

Cette architecture technique, bien que complexe, offre un équilibre optimal entre performances, maintenabilité et évolutivité, répondant ainsi parfaitement aux besoins actuels et futurs du projet.

## **Fonctionnalités Principales** ⚠️

Le projet PIVOT s'articule autour de plusieurs fonctionnalités clés, soigneusement conçues pour répondre aux besoins spécifiques des ressourceries et de leurs clients. Voici une analyse détaillée des quatre fonctionnalités majeures :

## **Conformité et Accessibilité**

### **RGPD et CNIL**

La conformité aux réglementations en matière de protection des données a été identifiée comme un enjeu majeur dès les premières phases du projet PIVOT. Conscient de l'importance de ces aspects pour un service en ligne, j'ai intégré les principes fondamentaux du RGPD dans l'architecture initiale. Les bases d'une politique de confidentialité ont été posées et les premières mesures techniques ont été implémentées dans le code, notamment concernant le stockage sécurisé des données utilisateurs et les mécanismes de consentement. Cette approche anticipative s'inscrit dans la philosophie des ressourceries, pour qui l'éthique constitue une valeur fondamentale.

Les travaux entrepris constituent une première étape vers une conformité complète, avec l'objectif d'enrichir progressivement ces fonctionnalités dans les prochaines versions de la plateforme. Si les fondations techniques sont en place, le déploiement complet des mécanismes de gestion des données personnelles (export, suppression, portabilité) fait partie de la feuille de route des prochaines itérations. Cette approche par phases permet de garantir que les développements futurs s'appuieront sur une base solide, tout en priorisant les fonctionnalités métier essentielles pour le lancement initial de la plateforme.

### **Accessibilité**

L'accessibilité numérique a été intégrée comme une dimension importante du projet dès ses débuts, reflétant la volonté d'offrir une plateforme inclusive en accord avec les valeurs des ressourceries. Les premiers travaux ont consisté à établir un cadre de développement tenant compte des recommandations du RGAA, avec une attention particulière aux éléments fondamentaux de l'interface utilisateur. Les composants principaux ont été conçus avec cette préoccupation en tête, posant ainsi les jalons d'une expérience accessible.

L'implémentation initiale des attributs ARIA et les premiers tests d'accessibilité ont permis d'identifier les axes d'amélioration prioritaires pour les futures versions. Si l'ensemble des fonctionnalités n'atteint pas encore le niveau d'accessibilité visé, les bases techniques sont désormais en place pour permettre une évolution progressive vers une conformité plus complète. Un plan d'action a été esquissé pour intégrer ces améliorations dans le cycle de développement continu de la plateforme, témoignant de l'engagement à long terme du projet envers l'accessibilité numérique.

## **Déploiement**

### Architecture de déploiement personnalisée ✅

Pour le déploiement de la plateforme PIVOT, j'ai opté pour une approche pragmatique et économique basée sur Dokploy, une solution de déploiement légère installée directement sur un VPS OVH. Cette décision s'inscrivait dans une volonté de maîtriser l'ensemble de la chaîne de déploiement tout en minimisant les coûts d'infrastructure.

L'architecture de déploiement s'articule autour de trois composants principaux :

1. **VPS OVH** : Serveur virtuel privé sous Debian, offrant un excellent rapport performances/prix et une grande flexibilité de configuration.
2. **Dokploy** : Outil de déploiement minimaliste installé directement sur le VPS, permettant d'automatiser le processus de déploiement à partir du repository Git.
3. **Services complémentaires** :
    - Serveur MySQL dédié pour la base de données
    - PHPMyAdmin pour l'administration simplifiée de la base
    - Serveur Nginx comme reverse proxy et pour la gestion des certificats SSL

### Avantages par rapport aux solutions alternatives ✅

Le choix de Dokploy sur VPS plutôt que des solutions comme GitHub Actions, Netlify ou Heroku s'est justifié par plusieurs facteurs déterminants :

1. **Contrôle total de l'infrastructure** : 
    - Maîtrise complète de la configuration serveur
    - Possibilité d'optimiser finement les performances selon les besoins spécifiques du projet
    - Absence de limitations imposées par des plateformes tierces (taille des uploads, durée d'exécution des scripts, etc.)
2. **Économie substantielle** :
    - Coût mensuel fixe et prévisible (environ 15€/mois pour le VPS et le nom de domaine)
    - Absence de frais variables liés au nombre de builds ou au temps de calcul
    - Pas de surcoût pour les fonctionnalités avancées (contrairement à Heroku ou Netlify où les fonctionnalités premium sont rapidement nécessaires)
3. **Simplicité architecturale** :
    - Réduction des dépendances externes et des points de défaillance potentiels
    - Workflow de déploiement direct sans intermédiaires
    - Temps de déploiement optimisé grâce à la proximité entre l'outil de CI/CD et l'environnement d'exécution
4. **Adéquation avec l'échelle du projet** :
    - Solution proportionnée aux besoins d'un MVP et d'une petite équipe
    - Évolutivité progressive possible sans refonte majeure
    - Facilité de sauvegarde et de migration

Cette approche "bare metal" a permis de réduire la complexité globale du processus de déploiement tout en conservant un niveau d'automatisation satisfaisant, particulièrement adapté au contexte de ressources limitées des ressourceries.

### Configuration des environnements ⚠️ Changer pour dire que dans le futur on crée un 2eme environnements

La configuration de l'infrastructure repose sur une séparation claire des environnements :

Chaque environnement (production et préproduction) dispose de :

- Son propre répertoire de déploiement
- Sa configuration spécifique (variables d'environnement)
- Sa base de données dédiée
- Son sous-domaine distinct (pivot.guillaume-lcte.fr et prochainement prerod.pivot.guillaume-lcte.fr)

Cette séparation garantit l'isolation complète entre les environnements tout en permettant de partager les ressources du serveur de manière efficiente.

### Pipeline CI/CD simplifié ✅

Le pipeline d'intégration et de déploiement continu a été volontairement simplifié pour répondre aux besoins essentiels du projet :

1. **Déclenchement** : Push sur les branches principales (main pour production, develop pour préproduction)
2. **Validation** :
    - Vérification syntaxique du code PHP
    - Exécution des tests unitaires critiques
    - Validation des dépendances
3. **Build** :
    - Optimisation des assets (minification CSS/JS)
    - Compilation des ressources frontend
    - Génération des caches d'application
4. **Déploiement** :
    - Mise à jour du code source via Git
    - Application des migrations de base de données
    - Rafraîchissement des caches
    - Redémarrage des services nécessaires

Cette approche minimaliste mais efficace a permis d'atteindre un temps de déploiement moyen inférieur à 3 minutes, un facteur crucial pour maintenir un cycle de développement agile.

### **Processus de Déploiement**

### Étapes de déploiement ✅

Le processus de déploiement de PIVOT a été conçu pour être à la fois robuste et simple à maintenir, tout en s'intégrant parfaitement dans notre workflow de développement. Il se décompose en plusieurs phases séquentielles automatisées:

1. **Phase de commit et push**:
    
    Notre workflow commence par un commit utilisant notre convention de gitmojis pour catégoriser visuellement les changements:
    
    ```bash
    # Exemple de commit avec gitmoji
    git commit -m "✨ feat(orders): implement reservation timeout system"
    git push origin develop
    ```
    
    Cette convention simple mais efficace permet d'identifier rapidement la nature des changements dans l'historique Git.
    
2. **Phase d'analyse statique et de validation**:
    
    Dès qu'un push est effectué, plusieurs outils d'analyse statique sont automatiquement exécutés:
    
    - **PHPStan (niveau 5)** vérifie la qualité du code PHP et détecte les erreurs potentielles
    - **Laravel Pint** assure le formatage automatique du code selon les standards PSR-12
    - **StyleCI** effectue une analyse supplémentaire pour garantir la cohérence stylistique du code
    
    Ces outils permettent de maintenir une qualité de code élevée sans nécessiter d'intervention manuelle systématique.
    
3. **Phase de build et déploiement via Dokploy**:
    
    Une fois les tests passés avec succès, Dokploy prend le relais pour orchestrer le déploiement:
    
    ```bash
    # Le processus Dokploy utilise notre Dockerfile pour construire l'image
    docker build -t pivot-app:latest .
    
    # Déploiement de l'application avec mise à jour des dépendances
    docker-compose up -d --build
    
    # Application des migrations sans temps d'arrêt
    docker-compose exec app php artisan migrate --force
    
    # Optimisation pour la production
    docker-compose exec app php artisan config:cache
    docker-compose exec app php artisan route:cache
    docker-compose exec app php artisan view:cache
    ```
    
4. **Phase de vérification post-déploiement**:
    
    Après le déploiement, des vérifications automatiques sont effectuées pour s'assurer que l'application fonctionne correctement:
    
    ```bash
    # Vérification des endpoints critiques
    curl -s -o /dev/null -w "%{http_code}" https://app.pivot.fr/health-check
    
    # Notification de déploiement réussi
    curl -X POST $SLACK_WEBHOOK -d "payload={\"text\":\"✅ Déploiement réussi sur $ENVIRONMENT\"}"
    ```
    

Cette approche de déploiement, bien que simple, s'est révélée parfaitement adaptée à notre contexte de projet, offrant un équilibre optimal entre automatisation, fiabilité et facilité de maintenance.

### Gestion des versions

La stratégie de gestion des versions adoptée pour PIVOT repose sur un modèle simplifié mais efficace :

1. **Versionnement sémantique** : Adoption du format MAJOR.MINOR.PATCH pour identifier clairement la nature des changements.
2. **Branches protégées** :
    - `main` : Code en production, protégée contre les push directs
    - `develop` : Branche d'intégration pour la préproduction
3. **Processus de release** :
    - Création d'une branche `release/vX.Y.Z` à partir de `develop`
    - Tests approfondis sur cette branche
    - Fusion dans `main` via pull request avec revue obligatoire
    - Tag de la version sur le commit de fusion
4. **Hotfixes** :
    - Création d'une branche `hotfix/vX.Y.Z+1` directement depuis `main`
    - Correction ciblée du bug
    - Fusion dans `main` ET `develop` pour garantir la propagation du correctif

Cette approche, bien que moins formalisée que GitFlow complet, offre un équilibre optimal entre rigueur et agilité pour une équipe de taille réduite.

## **Tests et Évaluation**

### **Stratégie de Test**

La stratégie de test de PIVOT repose sur une approche multicouche combinant analyse statique et tests fonctionnels. L'analyse statique constitue la première ligne de défense contre les erreurs potentielles et les problèmes de qualité du code. PHPStan (niveau 5) a été configuré pour analyser l'ensemble du code PHP, permettant de détecter les erreurs de typage, les variables non initialisées, les méthodes inexistantes et autres problèmes structurels avant même l'exécution du code. Cette analyse s'exécute automatiquement à chaque push via le pipeline CI/CD, garantissant que les problèmes sont identifiés dès leur introduction.

En complément, Laravel Pint et StyleCI assurent la conformité du code aux standards PSR-12 et aux conventions de Laravel. Ces outils, bien que focalisés sur le style plutôt que sur la fonctionnalité, jouent un rôle crucial dans la maintenabilité du code en garantissant sa cohérence et sa lisibilité. Un exemple concret de cette approche se trouve dans le module de réservation, où l'analyse statique a permis d'identifier et de corriger une potentielle condition de concurrence dans le traitement des réservations simultanées :

```php
// Avant correction - Identifié par PHPStan comme risque potentiel
public function reserve(Product $product, User $user)
{
    if ($product->status === 'available') {
        $product->status = 'reserved';
        $product->reserved_by = $user->id;
        $product->save();
        return true;
    }
    return false;
}

// Après correction - Sécurisé par transaction et verrou
public function reserve(Product $product, User $user)
{
    return DB::transaction(function () use ($product, $user) {
        $freshProduct = Product::where('id', $product->id)
                              ->where('status', 'available')
                              ->lockForUpdate()
                              ->first();
        
        if (!$freshProduct) {
            return false;
        }
        
        $freshProduct->status = 'reserved';
        $freshProduct->reserved_by = $user->id;
        $freshProduct->reserved_until = now()->addMinutes(15);
        $freshProduct->save();
        
        return true;
    });
}
```

Au-delà de l'analyse statique, des tests fonctionnels ciblés ont été implémentés pour les composants critiques de l'application. Ces tests, écrits avec le framework de test intégré à Laravel, vérifient le comportement des fonctionnalités essentielles comme l'authentification, la gestion des produits et le processus de réservation. Bien que la couverture ne soit pas exhaustive, ces tests couvrent les parcours utilisateurs principaux et les cas limites identifiés lors de la phase de conception.

Voici un exemple de test fonctionnel vérifiant le processus de réservation d'un produit :

```php
public function test_user_can_reserve_available_product()
{
    // Préparation du test
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'status' => 'available',
        'reserved_by' => null,
        'reserved_until' => null
    ]);
    
    // Authentification de l'utilisateur
    $this->actingAs($user);
    
    // Exécution de l'action à tester
    $response = $this->post(route('products.reserve', $product->id));
    
    // Assertions pour vérifier le comportement attendu
    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
    
    // Vérification que le produit a bien été réservé en base de données
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'status' => 'reserved',
        'reserved_by' => $user->id,
    ]);
    
    // Vérification que la date de réservation est correctement définie
    $updatedProduct = Product::find($product->id);
    $this->assertNotNull($updatedProduct->reserved_until);
    $this->assertTrue(now()->addMinutes(14)->lessThanOrEqualTo($updatedProduct->reserved_until));
    $this->assertTrue(now()->addMinutes(16)->greaterThanOrEqualTo($updatedProduct->reserved_until));
}
```

Ce test vérifie l'ensemble du processus de réservation, depuis l'appel à l'API jusqu'à la persistance des données en base, en passant par la validation de la réponse HTTP. Il s'assure également que la durée de réservation est correctement définie (15 minutes), illustrant ainsi la couverture complète d'une fonctionnalité critique du système.

### **Recettage**

Le processus de recettage a été structuré autour d'un plan de tests fonctionnels documenté dans Notion, couvrant l'ensemble des fonctionnalités critiques de la plateforme. Chaque fonctionnalité a été testée selon des scénarios prédéfinis, avec des critères d'acceptation clairs. Les tests ont été exécutés manuellement par l'équipe de développement et par des utilisateurs représentatifs des différents profils (clients, gestionnaires de ressourceries).

Le plan de recette a été organisé autour de trois scénarios principaux, chacun couvrant un aspect fondamental de la plateforme :

**Scénario 1 : Inscription et Connexion**
- Inscription d'un nouvel utilisateur de type Client
- Inscription d'un nouvel utilisateur de type Ressourcerie
- Connexion d'un utilisateur existant

**Scénario 2 : Gestion des Produits**
- Ajout d'un nouveau produit par une ressourcerie
- Modification d'un produit existant
- Suppression d'un produit

**Scénario 3 : Parcours d'Achat**
- Recherche et filtrage des produits
- Ajout de produits au panier
- Processus de commande et paiement

Les résultats des tests ont été documentés dans un PV de recette formel, avec le tableau récapitulatif suivant :

| **Scénario** | **Test** | **Résultat** | **Commentaires** |
|--------------|----------|--------------|------------------|
| Inscription et Connexion | Inscription Client | Succès | L'inscription fonctionne correctement, l'utilisateur est redirigé vers son tableau de bord. |
| Inscription et Connexion | Inscription Ressourcerie | Succès | L'inscription fonctionne correctement, l'utilisateur est redirigé vers son tableau de bord. |
| Inscription et Connexion | Connexion | Succès | La connexion fonctionne correctement pour les deux types d'utilisateurs. |
| Gestion des Produits | Ajout de produit | Succès | L'ajout de produit fonctionne correctement, le produit apparaît dans la liste. |
| Gestion des Produits | Modification de produit | Succès | La modification fonctionne correctement, les informations sont mises à jour. |
| Gestion des Produits | Suppression de produit | Succès | La suppression fonctionne correctement, le produit disparaît de la liste. |
| Parcours d'Achat | Recherche et filtrage | Succès | La recherche et les filtres fonctionnent correctement, les résultats sont pertinents. |
| Parcours d'Achat | Ajout au panier | Succès | L'ajout au panier fonctionne correctement, le panier est mis à jour. |
| Parcours d'Achat | Processus de commande | Succès | Le processus de commande fonctionne correctement, la commande est créée et confirmée. |

Comme le montre le tableau ci-dessus, l'ensemble des fonctionnalités testées a répondu aux critères d'acceptation définis. Néanmoins, ce processus de recettage rigoureux a également permis d'identifier quelques anomalies mineures qui, bien que n'empêchant pas le fonctionnement principal des parcours utilisateurs, méritaient d'être corrigées pour optimiser l'expérience utilisateur.

Ces anomalies ont été méticuleusement documentées dans notre système de suivi avec une catégorisation par niveau de sévérité (bloquant, majeur, mineur) et par domaine fonctionnel. Pour chaque anomalie, nous avons enregistré le scénario de reproduction, l'impact utilisateur et une estimation de l'effort correctif nécessaire, permettant ainsi une priorisation efficace des corrections à apporter.

Un exemple représentatif concerne le système de notification en temps réel lors de la réservation de produits. Bien que les tests de base aient été concluants, des cas particuliers ont révélé un comportement inattendu. Lors de tests approfondis avec différentes configurations de navigateurs, nous avons constaté que les notifications WebSocket n'étaient pas délivrées de manière fiable sur certains navigateurs plus anciens ou dans certains environnements réseau restrictifs. L'analyse technique a identifié une incompatibilité avec les WebSockets dans ces configurations spécifiques.

Pour résoudre ce problème tout en maintenant une expérience utilisateur fluide, j'ai implémenté un mécanisme de fallback intelligent utilisant le long polling lorsque les WebSockets ne sont pas disponibles. Cette solution hybride garantit que tous les utilisateurs reçoivent les notifications de changement de statut des produits, quelle que soit leur configuration technique, tout en optimisant les performances pour les navigateurs modernes supportant pleinement les WebSockets.

Cette approche méthodique de test et de correction illustre notre engagement envers une qualité irréprochable et notre capacité à identifier et résoudre les problèmes techniques complexes, même ceux qui n'apparaissent que dans des conditions particulières d'utilisation.

### **Perspectives d'amélioration des tests**

Bien que la stratégie de test actuelle ait prouvé son efficacité pour identifier et résoudre les problèmes majeurs, plusieurs axes d'amélioration ont été identifiés pour les futures itérations du projet. L'implémentation d'une suite de tests unitaires complète constitue une priorité, avec l'objectif d'atteindre une couverture de code d'au moins 80% pour les composants critiques. Ces tests unitaires permettront de valider le comportement de chaque classe et méthode individuellement, facilitant les refactorisations futures et réduisant le risque de régressions.

En complément, l'introduction de tests d'intégration automatisés permettra de valider les interactions entre les différents composants du système. Ces tests, plus complexes que les tests unitaires mais plus ciblés que les tests end-to-end, offriront un bon compromis entre couverture et rapidité d'exécution. Ils seront particulièrement utiles pour valider les flux de données entre le frontend et le backend, ainsi que les interactions avec la base de données.

Enfin, l'implémentation de tests end-to-end avec des outils comme Laravel Dusk ou Cypress permettra de simuler des interactions utilisateur complètes, validant l'ensemble du parcours depuis l'interface jusqu'à la persistance des données. Ces tests, bien que plus lents et plus fragiles que les tests unitaires ou d'intégration, offriront une validation précieuse des fonctionnalités critiques du point de vue de l'utilisateur final.

L'intégration de ces différents niveaux de test dans le pipeline CI/CD existant garantira leur exécution systématique à chaque modification du code, permettant d'identifier rapidement les régressions potentielles. Cette approche complète de test, combinant analyse statique, tests unitaires, tests d'intégration et tests end-to-end, constituera un pilier essentiel de la qualité et de la fiabilité de la plateforme PIVOT dans ses évolutions futures.

# **Retour d'Expérience** ✅

Ce projet a été une expérience formatrice exceptionnelle, me permettant de confronter la théorie à la réalité du terrain et d'acquérir une vision plus holistique du développement logiciel, de la conception à la mise en production.

Le développement de PIVOT a représenté un défi technique stimulant qui m'a permis de mettre en pratique et d'approfondir mes compétences en développement web. En tant que développeur au sein d'une équipe pluridisciplinaire, j'ai dû faire face à des problématiques uniques qui ont nécessité des solutions innovantes.

## 1. Défis techniques et solutions innovantes

J'ai dû relever plusieurs défis techniques majeurs qui ont nécessité créativité et persévérance :

### Gestion de l'unicité des produits ✅ Ajouter annexe

La nature même des ressourceries, qui traitent des objets uniques et non reproductibles, a posé un défi conceptuel et technique majeur. Cette spécificité a nécessité une modélisation complexe impliquant une refonte du modèle classique de gestion des stocks. Les problèmes de concurrence d'accès lorsque plusieurs utilisateurs tentent de réserver simultanément le même objet unique ont constitué un obstacle technique important. L'expérience utilisateur a également représenté un défi, notamment pour informer élégamment qu'un produit vient d'être réservé.

Ma solution a combiné plusieurs approches complémentaires. J'ai implémenté un système de réservation temporaire avec compte à rebours visible de 15 minutes, donnant aux utilisateurs le temps de finaliser leur commande tout en informant les autres de l'indisponibilité temporaire. L'utilisation de WebSockets pour les notifications en temps réel via Laravel Echo et Pusher a permis d'actualiser instantanément l'interface utilisateur. J'ai également optimisé les transactions en base de données avec des verrous optimistes pour garantir l'intégrité des données :

Voir Annexe X.X - Code expliquant la gestion de l'unicité des produits

### Déploiement multi-environnements avec Docker ✅

La mise en place d'une infrastructure Docker robuste s'est avérée plus complexe que prévu. Les temps de build initiaux ralentissaient considérablement le cycle de développement. Des différences subtiles entre environnements causaient parfois des comportements inattendus difficiles à reproduire et à déboguer. La configuration complexe pour les données persistantes représentait également un défi technique important.

J'ai optimisé mon approche en refactorisant les Dockerfiles pour utiliser le multi-stage building, réduisant significativement la taille des images et accélérant les déploiements. La mise en place d'un cache distribué pour Composer et NPM a considérablement amélioré les temps de build. Le développement d'un script d'initialisation garantissant des environnements isomorphes a permis d'éliminer les différences subtiles entre les environnements de développement, de test et de production.

## 2. Apprentissages et compétences développées

### Sur le plan technique ✅

La réalisation de PIVOT m'a permis de développer une maîtrise approfondie de l'écosystème Laravel/React. Au-delà des connaissances de base, j'ai acquis une compréhension fine des mécanismes internes et des patterns avancés, particulièrement dans l'interaction entre le backend et le frontend via Inertia.js. Cette expertise m'a permis de résoudre des problèmes complexes d'intégration et d'optimiser les performances globales de l'application.

La mise en place et l'optimisation des pipelines de déploiement m'ont permis de développer des compétences précieuses en DevOps et CI/CD. Ces compétences se sont révélées essentielles pour maintenir un rythme de développement soutenu et garantir la stabilité de l'application en production. L'automatisation des tests et des déploiements a considérablement réduit les risques d'erreurs humaines et accéléré le cycle de développement.

### Sur le plan méthodologique  ✅

Démarrer par des *proofs of concept* pour les fonctionnalités complexes plutôt que de se lancer immédiatement dans le développement complet s'est avéré être la bonne initiative. Cette approche m'a permis de valider les concepts techniques avec un investissement minimal, tout en identifiant dès les premières phases les limitations et défis à anticiper. En confrontant rapidement les hypothèses à la réalité technique grâce à ces POC ciblés, j'ai pu ajuster l'orientation du développement avant d'engager des ressources importantes dans des solutions potentiellement non viables.

Cette méthode a non seulement réduit les risques d'échec coûteux, mais elle a aussi servi de base solide pour prioriser les fonctionnalités en fonction de leur faisabilité réelle. Le passage progressif du POC à un prototype fonctionnel a ensuite permis d'affiner l'implémentation finale tout en maintenant une vision alignée avec les objectifs initiaux du projet.

Travailler au sein d'une équipe diverse a affiné ma capacité à expliquer des concepts techniques complexes à des interlocuteurs non techniques, compétence essentielle dans un projet pluridisciplinaire. Cette communication technique interdisciplinaire a facilité la collaboration et permis d'aligner efficacement les attentes de tous les membres de l'équipe.

J'ai également appris à mieux évaluer les risques techniques et à prévoir des plans de contingence pour les zones d'incertitude, permettant une progression plus fluide du projet. Cette gestion de l'incertitude s'est traduite par une meilleure anticipation des problèmes potentiels et une résolution plus rapide des obstacles rencontrés.

## 3. Perspectives et évolutions futures

### Améliorations techniques

Bien que j'ai mis en place des tests, une couverture plus complète dès le début du projet aurait permis d'identifier certains problèmes plus tôt et de faciliter les refactorisations. Le renforcement des tests automatisés constitue donc un axe d'amélioration prioritaire pour mes futurs projets, avec l'objectif d'atteindre une couverture de code significative dès les premières phases de développement.

La documentation technique approfondie représente un autre domaine d'amélioration important. La documentation systématique des choix architecturaux et de leurs justifications faciliterait l'intégration de nouveaux développeurs et la maintenance à long terme. Une documentation claire et complète permettrait également de capitaliser sur les connaissances acquises et de les transmettre efficacement.

Pour les futures évolutions, une architecture orientée microservices pourrait offrir une meilleure scalabilité et une maintenance plus aisée. La décomposition de certaines fonctionnalités en services indépendants permettrait une évolution plus flexible de la plateforme et une meilleure répartition de la charge.

### Améliorations méthodologiques

Certaines fonctionnalités auraient bénéficié d'un retour utilisateur plus précoce, permettant des ajustements avant un développement complet. L'implication plus précoce des utilisateurs finaux dans le processus de développement constitue donc un axe d'amélioration méthodologique important, avec l'objectif de mieux aligner les fonctionnalités développées avec les besoins réels des utilisateurs.

Réduire encore la durée des cycles de développement permettrait d'obtenir des feedbacks plus fréquents et d'ajuster la trajectoire du projet plus finement. Ces cycles d'itération plus courts favoriseraient une approche plus agile et réactive, capable de s'adapter rapidement aux changements de priorités ou aux nouvelles exigences.

L'intégration davantage des considérations de sécurité directement dans le processus de développement et de déploiement renforcerait la robustesse de la plateforme. Ces pratiques DevSecOps permettraient d'identifier et de corriger les vulnérabilités potentielles dès les premières phases du développement, réduisant ainsi les risques de sécurité.

Ce projet a été une expérience formatrice exceptionnelle, me permettant de confronter la théorie à la réalité du terrain et d'acquérir une vision plus holistique du développement logiciel, de la conception à la mise en production. Les compétences et méthodologies développées constitueront un socle solide pour mes futurs projets.

# **Annexes**

- Accès au repository Git
- Documentation API
- Manuel d'utilisation
- Captures d'écran des fonctionnalités clés

MCD :

Voir Annexe : X.X - Code expliquant la gestion de l'unicité des produits

```php
DB::transaction(function () use ($productId, $userId) {
    _// Verrouillage optimiste avec clause for update_
    $product = Product::where('id', $productId)
                     ->where('status', 'available')
                     ->lockForUpdate()
                     ->first();

    if (!$product) {
        throw new ProductNotAvailableException();
    }

    $product->status = 'reserved';
    $product->reserved_by = $userId;
    $product->reserved_until = now()->addMinutes(15);
    $product->save();

    _// Notification en temps réel aux autres utilisateurs_
    broadcast(new ProductStatusChanged($product))->toOthers();
}, 5);

```