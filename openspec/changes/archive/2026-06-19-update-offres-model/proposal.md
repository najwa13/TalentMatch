## Why

Le modèle de données actuel de TalentMatch utilise des noms français et manque de séparation entre les offres et les analyses IA. Le cahier des charges original et le modèle MCD/MLD imposent un modèle en anglais avec une table `analyse` séparée liant les candidats aux offres. Cette mise à jour aligne le code sur le modèle de données officiel.

## What Changes

- Renommer le modèle `Offre` → `Offer` avec colonnes anglaises (`title`, `description`, `required_skills`, `minimum_experience`)
- Renommer le modèle `Candidat` → `Candidat` avec colonnes anglaises (`name`, `cv_text`)
- Créer la table `analyses` avec les colonnes du cahier des charges (extracted_skills, matching_score, recommendation, etc.)
- Renommer les migrations existantes
- Mettre à jour tous les controllers, requests, policies, vues et tests
- Ajouter les relations manquantes (Offer hasMany Analyse, Analyse belongsTo Candidat)

## Capabilities

### New Capabilities

Aucune nouvelle capability. Mise à jour de la capability existante `gestion-offres`.

### Modified Capabilities

- `gestion-offres`: Changement de modèle de données (noms de colonnes en anglais, ajout de la table analyses)

## Impact

- **Modèles**: `Offre` → `Offer`, `Candidat` reste, ajout `Analyse`
- **Migrations**: Renommage des colonnes, ajout table `analyses`
- **Controller**: `OffreController` → `OfferController`
- **Form Requests`: `StoreOffreRequest` → `StoreOfferRequest`, `UpdateOffreRequest` → `UpdateOfferRequest`
- **Policy**: `OffrePolicy` → `OfferPolicy`
- **Routes`: `/offres` reste (nom de route en français OK)
- **Vues**: Mise à jour des templates Blade
- **Tests`: Mise à jour de `OffresTest`
