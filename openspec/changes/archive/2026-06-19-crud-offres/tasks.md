## 1. Database

- [x] 1.1 Créer la migration `create_offres_table` avec les colonnes: id, titre, description, competences (json), experience_min (integer), user_id (foreign), timestamps
- [x] 1.2 Exécuter `php artisan migrate` pour créer la table

## 2. Model & Relationships

- [x] 2.1 Créer le modèle `App\Models\Offre` avec casts competences→array et fillable
- [x] 2.2 Ajouter la relation `hasMany(Offre::class)` au modèle User
- [x] 2.3 Créer le `OffreFactory` avec états par défaut
- [x] 2.4 Créer le seeder `OffreSeeder` pour les données de démo

## 3. Authorization

- [x] 3.1 Créer `App\Policies\OfferPolicy` avec les méthodes viewAny, view, create, update, delete
- [x] 3.2 Enregistrer la policy dans `AuthServiceProvider` (ou auto-discovery)
- [ ] 3.3 Appliquer `authorizeResource` dans le controller

## 4. Form Requests

- [x] 4.1 Créer `App\Http\Requests\StoreOffreRequest` avec rules: titre required|max:255, description required, competences required|json, experience_min required|integer|min:0|max:50
- [x] 4.2 Créer `App\Http\Requests\UpdateOffreRequest` avec les mêmes règles

## 5. Controller

- [x] 5.1 Créer `App\Http\Controllers\OffreController` avec les 7 méthodes resource
- [x] 5.2 Implémenter `index()` avec eager loading candidates_count et filtre user_id
- [x] 5.3 Implémenter `store()` avec validation StoreOffreRequest et redirection
- [x] 5.4 Implémenter `show()` avec vérification policy view
- [x] 5.5 Implémenter `update()` avec validation UpdateOffreRequest
- [x] 5.6 Implémenter `destroy()` avec confirmation et suppression cascade

## 6. Routes

- [x] 6.1 Ajouter `Route::resource('offres', OffreController::class)->middleware(['auth', 'verified'])` dans web.php

## 7. Views Blade

- [x] 7.1 Créer le layout de base `resources/views/offres/layout.blade.php`
- [x] 7.2 Créer `offres/index.blade.php` avec liste des offres et empty state
- [x] 7.3 Créer `offres/show.blade.php` avec détail offre et liste candidats
- [x] 7.4 Créer `offres/create.blade.php` avec formulaire de création
- [x] 7.5 Créer `offres/edit.blade.php` avec formulaire de modification pré-rempli

## 8. Tests

- [x] 8.1 Créer le test `OffresTest` avec cas: création, liste, détail, modification, suppression
- [x] 8.2 Ajouter les cas limites: offre inexistante (404), non autorisé (403), non authentifié (redirect)
- [x] 8.3 Ajouter les tests de validation: données manquantes, format invalide
- [x] 8.4 Exécuter `php artisan test --filter=OffresTest` pour valider

## 9. Finalisation

- [x] 9.1 Exécuter `vendor/bin/pint --dirty --format agent` pour formater le code
- [x] 9.2 Exécuter `php artisan test --compact` pour valider l'ensemble des tests
