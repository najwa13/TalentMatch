## Context

TalentMatch est une application Laravel 13 utilisant Breeze pour l'authentification. Le projet est en phase de développement initial avec une architecture MVC classique. La base de données contient uniquement les tables users, cache, jobs, et agent_conversations.

L'objectif est d'implémenter le CRUD des offres d'emploi comme prerequis fonctionnel pour l'analyse IA des candidats (feature ultérieure).

## Goals / Non-Goals

**Goals:**
- Implémenter un CRUD complet et sécurisé des offres d'emploi
- Assurer l'isolation totale des données entre utilisateurs
- Suivre les conventions Laravel (Form Requests, Policies, Eloquent)
- Créer des tests fonctionnels couvrant tous les cas limites

**Non-Goals:**
- Gestion des candidats (feature séparée)
- Analyse IA (feature séparée)
- API REST (uniquement routes web Blade)
- Notifications email
- Upload de fichiers

## Decisions

### Decision 1: Architecture MVC avec resource controller
**Choix**: Utiliser un `OffreController` avec les 7 méthodes du resource controller Laravel.
**Rationale**: Conforme aux conventions Laravel, structure standardisée, facile à maintenir.
**Alternatives considérées**: Controller unique avec méthodes personnalisées (rejeté - moins conventionnel).

### Decision 2: Validation via Form Requests séparés
**Choix**: Créer `StoreOffreRequest` et `UpdateOffreRequest` pour valider les données.
**Rationale**: Séparation des responsabilités, réutilisabilité, testabilité. Les deux ont des règles quasi identiques mais permettent des messages d'erreur spécifiques.
**Alternatives considérées**: Validation inline dans le controller (rejeté - moins propre).

### Decision 3: OfferPolicy pour le contrôle d'accès
**Choix**: Implémenter une `OfferPolicy` avec les méthodes `viewAny`, `view`, `create`, `update`, `delete`.
**Rationale**: Sécurité par conception, chaque action est vérifiée. La policy vérifie `offre.user_id === auth()->id()`.
**Alternatives considérées**: Vérification manuelle dans chaque méthode du controller (rejeté - répétitif, source d'erreurs).

### Decision 4: Table competences en JSON
**Choix**: Stocker les compétences en JSON (tableau de strings) avec Eloquent cast `array`.
**Rationale**: Simplicité pour une liste de strings, pas besoin de relation many-to-many pour l'instant. Si besoin de recherche par compétence, pourra être migré vers une table séparée.
**Alternatives considérées**: Table pivot competences/offres (rejeté - trop complexe pour le besoin actuel).

### Decision 5: Factory et seeders pour les tests
**Choix**: Créer `OffreFactory` avec des états prédéfinis (avec candidats, sans candidats).
**Rationale**: Permet de créer des données de test réalistes. Le factory sera utilisé dans les tests.feature.
**Alternatives considérations**: Création manuelle dans chaque test (rejeté - répétitif).

## Risks / Trade-offs

| Risk | Mitigation |
|------|------------|
| Fuite de données entre utilisateurs | OfferPolicy vérifie systématiquement user_id. Tests d'intégration avec multi-users. |
| Performance avec beaucoup d'offres | Pagination des listes. Eager loading des relations candidates. |
| Migration future si recherche par compétence requise | Le cast JSON permet la lecture. Si besoin de recherche, créer une migration vers table pivot. |
| Validation côté client vs serveur | Validation serveur obligatoire. Côté client optionnelle pour l'UX. |

## Migration Plan

1. Créer la migration `create_offres_table`
2. Exécuter `php artisan migrate`
3. Créer le modèle, factory, seeders
4. Créer les Form Requests
5. Créer la Policy
6. Créer le Controller
7. Définir les routes
8. Créer les vues Blade
9. Écrire les tests
10. Exécuter `php artisan test` pour valider

**Rollback**: Supprimer la migration, le controller, les vues, les routes. Les données seront perdues ( acceptable en phase de dev).

## Open Questions

- Faut-il ajouter un champ `statut` (active/inactive) aux offres ? → Non pour l'instant, à ajouter si besoin ultérieur.
- Faut-il un champ `date_limite` pour les offres ? → Non mentionné dans le cahier des charges.
- Les candidats seront-ils liés à une offre ou à un utilisateur ? → Liés à une offre (cf. feature séparée).
