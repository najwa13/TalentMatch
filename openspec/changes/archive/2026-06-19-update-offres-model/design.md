## Context

Le projet TalentMatch a un CRUD Offres fonctionnel avec des noms français. Le cahier des charges et le modèle MCD/MLD imposent un modèle en anglais avec une table `analyses` séparée. Cette mise à jour aligne le code sur les spécifications officielles.

## Goals / Non-Goals

**Goals:**
- Renommer le modèle Offre → Offer avec colonnes anglaises
- Ajouter la table analyses avec toutes les colonnes du cahier des charges
- Mettre à jour tous les fichiers dépendants (controller, requests, policy, views, tests)
- Maintenir la fonctionnalité existante (CRUD complet)

**Non-Goals:**
- Implémenter la feature analyses (séparée)
- Implémenter la feature conversation (séparée)
- Changer les routes (URI reste `/offres`)

## Decisions

### Decision 1: Renommage complet du modèle
**Choix**: Renommer `Offre` → `Offer` et toutes les colonnes en anglais.
**Rationale**: Alignement avec le MCD/MLD officiel. Les noms anglais sont standards dans le domaine du développement.
**Alternatives**: Garder les noms français (rejeté - non conforme au cahier des charges).

### Decision 2: Migration de renommage
**Choix**: Créer une migration de renommage de la table `offres` → `offers` et des colonnes.
**Rationale**: Préserve les données existantes. Permet un rollback propre.
**Alternatives**: Supprimer et recréer la table (rejeté - perte de données).

### Decision 3: Table analyses séparée
**Choix**: Créer une nouvelle table `analyses` avec toutes les colonnes du cahier des charges.
**Rationale**: Séparation des responsabilités. Les données d'analyse IA sont séparées des données de l'offre.
**Alternatives**: Ajouter les colonnes d'analyse à la table offers (rejeté - violation de normalisation).

### Decision 4: Garder les routes en français
**Choix**: Garder l'URI `/offres` et les noms de routes `offres.*`.
**Rationale**: Cohérence avec l'interface utilisateur en français. Les routes sont déjà utilisées dans les vues.
**Alternatives**: Renommer en `/offers` (rejeté - nécessite de modifier toutes les vues).

## Risks / Trade-offs

| Risk | Mitigation |
|------|------------|
| Perte de données lors du renommage | Migration de renommage (pas de suppression) |
| Incompatibilité avec les tests existants | Mise à jour simultanée des tests |
| Routes incohérentes | Garder les routes en français, renommer seulement les modèles |
