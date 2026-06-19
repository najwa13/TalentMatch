## Why

Le département RH d'une startup marocaine reçoit entre 50 et 200 CVs par offre d'emploi. Le traitement manuel est répétitif, subjectif et chronophage. TalentMatch a besoin d'une base structurée d'offres d'emploi pour permettre ensuite l'analyse IA des candidats. Sans offres, aucun workflow de screening automatisé n'est possible.

## What Changes

- Ajout du modèle `Offre` avec migration, factory et seeders
- Création du controller `OffreController` avec les 5 actions CRUD
- Implémentation de `OfferPolicy` pour le contrôle d'accès propriétaire
- Création des Form Requests `StoreOffreRequest` et `UpdateOffreRequest`
- Définition des routes resource protégées par auth et verified
- Création des vues Blade pour le CRUD (index, show, create, edit)
- Intégration du compteur de candidats par offre (relation `candidates_count`)

## Capabilities

### New Capabilities

- `gestion-offres`: CRUD complet des offres d'emploi (création, consultation, modification, suppression) avec validation serveur, isolation des données et contrôle d'accès par Policy

### Modified Capabilities

Aucune. Il n'existe pas de capabilities existantes dans `openspec/specs/`.

## Impact

- **Modèles**: Nouveau modèle `App\Models\Offre` avec relation `belongsTo(User)`
- **Migration**: Nouvelle table `offres` (id, titre, description, competences JSON, experience_min, user_id, timestamps)
- **Controller**: Nouveau `App\Http\Controllers\OffreController`
- **Policy**: Nouvelle `App\Policies\OfferPolicy`
- **Form Requests**: `StoreOffreRequest`, `UpdateOffreRequest`
- **Routes**: Ajout de `Route::resource('offres', OffreController::class)` dans `routes/web.php`
- **Vues**: Nouveau dossier `resources/views/offres/` avec 5 templates Blade
- **User Model**: Ajout de la relation `hasMany(Offre::class)`
