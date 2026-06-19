## 1. Database Migrations

- [x] 1.1 Créer une migration de renommage: `offres` → `offers` avec colonnes anglaises (title, description, required_skills, minimum_experience, user_id)
- [x] 1.2 Créer la migration `create_analyses_table` avec les colonnes: id, job_offer_id (FK), candidate_id (FK), extracted_skills, years_experience, education_level, languages, matching_score, strengths, weaknesses, missing_skills, recommendation, justification, status, timestamps
- [x] 1.3 Renommer la migration `create_candidats_table` pour utiliser `name` et `cv_text` au lieu de `nom` et `cv_texte`
- [x] 1.4 Exécuter `php artisan migrate:fresh --seed`

## 2. Models

- [x] 2.1 Renommer `App\Models\Offre` → `App\Models\Offer` avec colonnes anglaises et casts
- [x] 2.2 Créer `App\Models\Analyse` avec fillable, casts et relationships (belongsTo Offer, belongsTo Candidat)
- [x] 2.3 Mettre à jour `App\Models\Candidat` avec colonnes anglaises (name, cv_text)
- [x] 2.4 Mettre à jour `App\Models\User` avec relation `offers()` au lieu de `offres()`
- [x] 2.5 Mettre à jour `App\Models\Offer` avec relation `analyses()` (hasMany)
- [x] 2.6 Créer `AnalyseFactory` pour les tests
- [x] 2.7 Mettre à jour `OfferFactory` avec les nouvelles colonnes

## 3. Authorization

- [x] 3.1 Renommer `App\Policies\OffrePolicy` → `App\Policies\OfferPolicy`
- [x] 3.2 Mettre à jour la policy pour utiliser le modèle `Offer`

## 4. Form Requests

- [x] 4.1 Renommer `StoreOffreRequest` → `StoreOfferRequest` avec colonnes anglaises
- [x] 4.2 Renommer `UpdateOffreRequest` → `UpdateOfferRequest` avec colonnes anglaises

## 5. Controller

- [x] 5.1 Renommer `OffreController` → `OfferController` avec modèle `Offer`
- [x] 5.2 Mettre à jour toutes les méthodes pour utiliser le modèle `Offer` et les colonnes anglaises
- [x] 5.3 Mettre à jour les relations eager loading pour utiliser `analyses` au lieu de `candidats`

## 6. Routes

- [x] 6.1 Mettre à jour la route resource pour utiliser `OfferController`

## 7. Views Blade

- [x] 7.1 Mettre à jour `offres/index.blade.php` pour utiliser les colonnes anglaises (title, analyses_count)
- [x] 7.2 Mettre à jour `offres/show.blade.php` pour afficher les analyses avec matching_score
- [x] 7.3 Mettre à jour `offres/create.blade.php` avec champs anglais (title, required_skills, minimum_experience)
- [x] 7.4 Mettre à jour `offres/edit.blade.php` avec champs anglais

## 8. Tests

- [x] 8.1 Mettre à jour `OffresTest` pour utiliser le modèle `Offer` et les colonnes anglaises
- [x] 8.2 Exécuter `php artisan test --filter=OffresTest` pour valider
- [x] 8.3 Exécuter `php artisan test --compact` pour valider l'ensemble

## 9. Finalisation

- [x] 9.1 Exécuter `vendor/bin/pint --dirty --format agent` pour formater le code
- [x] 9.2 Vérifier que tous les tests passent
