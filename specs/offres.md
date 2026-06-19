# Gestion des Offres

## Objectif

TalentMatch est un outil interne destiné au département RH d'une startup marocaine. Le département reçoit entre 50 et 200 CVs par offre, rendant le traitement manuel répétitif, subjectif et chronophage.

La fonctionnalité **Gestion des Offres** permet aux responsables RH de **créer, consulter, modifier et supprimer des offres d'emploi**. Chaque offre constitue l'unité de base autour de laquelle s'articulent les analyses IA des candidats (feature séparée).

Sans offres, aucune analyse de candidat n'est possible : l'offre est le **prerequis fonctionnel** de tout le workflow TalentMatch.

## Périmètre

Cette fonctionnalité couvre :

- La création d'une offre d'emploi avec title, description, required_skills et minimum_experience
- La consultation de la liste des offres appartenant à l'utilisateur connecté, avec le nombre de candidats analysés par offre
- La consultation du détail d'une offre et de ses analyses associées avec les scores
- La modification des informations d'une offre existante
- La suppression d'une offre existante
- L'isolation complète des données : chaque utilisateur ne voit et ne gère que ses propres offres
- La validation serveur complète des données entrantes via Form Requests
- Le contrôle d'accès basé sur une Policy (OfferPolicy)

## Hors périmètre

Cette fonctionnalité ne couvre **pas** :

- **L'analyse IA des CV** : la soumission de CV, le scoring automatique et la génération de recommandations relèvent d'une feature séparée (cf. spec `analyses.md`)
- **La gestion des candidats** : l'ajout et la consultation des candidats ne font pas partie de cette spec
- **La table Analyse** : les résultats d'analyse IA (extracted_skills, matching_score, recommendation, etc.) sont gérés par la feature analyses
- **L'assistant conversationnel** : les fonctionnalités de chat et de questioning sur les candidats sont exclues
- **L'authentification** : la création de compte, connexion et déconnexion sont déjà implémentées via Laravel Breeze (US1)
- **Le téléchargement de fichiers** : la gestion de CV au format fichier (PDF, DOCX) n'est pas couverte ici
- **Les notifications** : aucun email ni notification push n'est envoyé lors de la création/modification d'une offre
- **Le dashboard RH** : l'interface de visualisation globale est exclue de cette spec
- **L'API REST** : cette spec couvre uniquement les routes web (Blade), pas d'API REST

## Modèle de données

```
User
│
│ 1,N
│
Offer
│
│ 1,N
│
Analyse
│
│ N,1
│
Candidat
```

### Table offer

| Colonne | Type | Description |
|---------|------|-------------|
| `id` | bigint (PK) | Identifiant unique |
| `user_id` | bigint (FK) | Propriétaire de l'offre |
| `title` | string | Titre de l'offre |
| `description` | text | Description complète |
| `required_skills` | json | Compétences requises (tableau de strings) |
| `minimum_experience` | integer | Expérience minimale en années |
| `created_at` | timestamp | Date de création |
| `updated_at` | timestamp | Date de mise à jour |

### Relations

- `Offer belongsTo User` (via `user_id`)
- `User hasMany Offer`
- `Offer hasMany Analyse` (via `job_offer_id`)
- `Analyse belongsTo Offer` (via `job_offer_id`)
- `Analyse belongsTo Candidat` (via `candidate_id`)
- `Candidat hasMany Analyse`

## Fonctionnalités

### Création d'une offre

**Description** : L'utilisateur RH crée une nouvelle offre d'emploi en renseignant les informations essentielles.

**Préconditions** :
- L'utilisateur est authentifié
- L'utilisateur a vérifié son email

**Déclencheur** : Clic sur le bouton "Nouvelle offre" depuis la page de liste des offres, ou navigation directe vers `/offres/create`

**Résultat attendu** :
- Un formulaire de création est affiché avec les champs : title, description, required_skills, minimum_experience
- Après soumission valide, l'offre est persistée en base de données
- L'utilisateur est redirigé vers la page de détail de l'offre créée avec un message de succès
- En cas d'erreur de validation, les erreurs sont affichées à côté des champs concernés

---

### Consultation de la liste des offres

**Description** : L'utilisateur RH consulte la liste de toutes les offres qu'il a créées.

**Préconditions** :
- L'utilisateur est authentifié
- L'utilisateur a vérifié son email

**Déclencheur** : Navigation vers `/offres` ou clic sur "Mes offres" dans le menu

**Résultat attendu** :
- La page affiche la liste des offres appartenant à l'utilisateur connecté uniquement
- Chaque offre affiche : title, date de création, nombre de candidats analysés
- Aucune offre d'un autre utilisateur n'est visible
- Si l'utilisateur n'a aucune offre, un message informatif "Aucune offre créée" est affiché

---

### Consultation du détail d'une offre

**Description** : L'utilisateur RH consulte les informations détaillées d'une offre spécifique, incluant la liste des analyses associées et leurs scores.

**Préconditions** :
- L'utilisateur est authentifié
- L'utilisateur a vérifié son email
- L'offre demandée appartient à l'utilisateur connecté

**Déclencheur** : Clic sur une offre dans la liste, ou navigation vers `/offres/{id}`

**Résultat attendu** :
- La page affiche les informations complètes de l'offre : title, description, required_skills, minimum_experience
- La page affiche la liste des analyses ayant été réalisées pour cette offre, avec leur matching_score (0-100)
- Les analyses sont affichées par ordre décroissant de score
- Des liens permettent de modifier ou supprimer l'offre

---

### Modification d'une offre

**Description** : L'utilisateur RH modifie les informations d'une offre existante.

**Préconditions** :
- L'utilisateur est authentifié
- L'utilisateur a vérifié son email
- L'offre à modifier appartient à l'utilisateur connecté

**Déclencheur** : Clic sur "Modifier" depuis la page de détail de l'offre

**Résultat attendu** :
- Un formulaire pré-rempli avec les données actuelles de l'offre est affiché
- Après soumission valide, l'offre est mise à jour en base de données
- L'utilisateur est redirigé vers la page de détail avec un message de succès
- En cas d'erreur de validation, les erreurs sont affichées avec les données saisies

---

### Suppression d'une offre

**Description** : L'utilisateur RH supprime une offre existante et toutes ses associations (analyses).

**Préconditions** :
- L'utilisateur est authentifié
- L'utilisateur a vérifié son email
- L'offre à supprimer appartient à l'utilisateur connecté

**Déclencheur** : Clic sur "Supprimer" depuis la page de détail ou la page d'édition, suivi d'une confirmation

**Résultat attendu** :
- Une boîte de dialogue de confirmation est affichée ("Êtes-vous sûr de vouloir supprimer cette offre ?")
- Après confirmation, l'offre et toutes ses associations sont supprimées de la base de données
- L'utilisateur est redirigé vers la liste des offres avec un message de succès

## Contrat d'entrée

### Créer une offre

| Champ | Type | Obligatoire | Règles de validation |
|-------|------|-------------|---------------------|
| `title` | string | Oui | `required\|string\|max:255` |
| `description` | text | Oui | `required\|string` |
| `required_skills` | json | Oui | `required\|array\|min:1` |
| `minimum_experience` | integer | Oui | `required\|integer\|min:0\|max:50` |

**Exemple de payload valide** :
```json
{
  "title": "Développeur Laravel Senior",
  "description": "Nous recherchons un développeur Laravel expérimenté pour rejoindre notre équipe technique.",
  "required_skills": ["PHP", "Laravel", "MySQL", "Git", "API REST"],
  "minimum_experience": 3
}
```

### Modifier une offre

| Champ | Type | Obligatoire | Règles de validation |
|-------|------|-------------|---------------------|
| `title` | string | Oui | `required\|string\|max:255` |
| `description` | text | Oui | `required\|string` |
| `required_skills` | json | Oui | `required\|array\|min:1` |
| `minimum_experience` | integer | Oui | `required\|integer\|min:0\|max:50` |

Les mêmes règles de validation s'appliquent lors de la modification.

## Contrat de sortie

### Création

- **HTTP Status** : 302 (redirect)
- **Redirect** : `/offres/{id}` (page de détail)
- **Session flash** : `success` = "Offre créée avec succès"
- **Données persistées** : id, title, description, required_skills (JSON), minimum_experience, user_id, created_at, updated_at

### Consultation (liste)

- **HTTP Status** : 200
- **Vue** : `offres.index`
- **Données** : Collection d'offres appartenant à l'utilisateur, avec eager loading du compteur d'analyses
- **Structure** : `[
  {
    "id": 1,
    "title": "Développeur Laravel Senior",
    "created_at": "2026-06-19",
    "analyses_count": 12
  }
]`

### Consultation (détail)

- **HTTP Status** : 200
- **Vue** : `offres.show`
- **Données** : Offre complète avec relations (owner, analyses avec scores)
- **Structure** : `{
  "id": 1,
  "title": "Développeur Laravel Senior",
  "description": "...",
  "required_skills": ["PHP", "Laravel"],
  "minimum_experience": 3,
  "owner": { "id": 1, "name": "John" },
  "analyses": [
    {
      "id": 1,
      "candidate": { "id": 1, "name": "Ahmed Benali" },
      "matching_score": 85,
      "recommendation": "convoquer"
    }
  ],
  "created_at": "2026-06-19"
}`

### Modification

- **HTTP Status** : 302 (redirect)
- **Redirect** : `/offres/{id}` (page de détail)
- **Session flash** : `success` = "Offre mise à jour avec succès"
- **Données mises à jour** : champs modifiés, updated_at mis à jour

### Suppression

- **HTTP Status** : 302 (redirect)
- **Redirect** : `/offres` (liste)
- **Session flash** : `success` = "Offre supprimée avec succès"
- **Données** : offre et associations supprimées de la base

## Règles métier

### Propriété des offres

- Chaque offre appartient **exclusivement** à l'utilisateur qui l'a créée
- Un utilisateur ne peut voir, modifier ou supprimer que **ses propres offres**
- Aucun partage d'offres entre utilisateurs n'est autorisé

### Validation des compétences

- Les compétences (required_skills) sont stockées en JSON (tableau de strings)
- Chaque compétence est une chaîne de caractères non vide
- Le tableau ne peut pas être vide (au moins une compétence requise)
- Maximum raisonnable : 20 compétences par offre

### Expérience minimale

- Entier positif (0 inclus)
- Maximum plafonné à 50 ans (cohérence métier)
- Représente le nombre minimum d'années d'expérience requises

### Isolation des données utilisateur

- Toutes les requêtes de lecture sont filtrées par `user_id` de l'utilisateur connecté
- Aucune fuite de données entre utilisateurs n'est tolérée
- Les tentatives d'accès à des offres d'autres utilisateurs sont bloquées au niveau Policy

### Gestion des erreurs

- Les erreurs de validation affichent des messages explicites en français
- Les erreurs 404 (offre inexistante) affichent une page dédiée
- Les erreurs 403 (accès interdit) affichent une page dédiée

## Règles de sécurité

### Authentification requise

- Toutes les routes CRUD sont protégées par le middleware `auth` de Laravel Breeze
- Un utilisateur non authentifié est redirigé vers `/login`

### Vérification d'email

- L'utilisateur doit avoir vérifié son email avant d'accéder aux fonctionnalités
- Non vérification → redirection vers `/verify-email`

### Autorisation via OfferPolicy

- Chaque action CRUD est contrôlée par une `OfferPolicy`
- La policy vérifie que `offer.user_id === auth()->id()`
- Les méthodes policy : `viewAny`, `view`, `create`, `update`, `delete`

### Accès limité au propriétaire

- Seul le propriétaire de l'offre peut la consulter, modifier ou supprimer
- Toute tentative d'accès à une offre d'un autre utilisateur retourne une réponse 403

### Comportement en cas d'accès interdit ou ressource inexistante

- **Ressource inexistante** : réponse HTTP 404, vue `errors.404`
- **Accès interdit** : réponse HTTP 403, vue `errors.403`
- **Non authentifié** : redirection vers `/login` avec message flash

## Routes

| Méthode | URI | Nom | Description |
|---------|-----|-----|-------------|
| GET | `/offres` | `offres.index` | Liste des offres de l'utilisateur |
| GET | `/offres/create` | `offres.create` | Formulaire de création |
| POST | `/offres` | `offres.store` | Enregistrer une nouvelle offre |
| GET | `/offres/{offer}` | `offres.show` | Détail d'une offre |
| GET | `/offres/{offer}/edit` | `offres.edit` | Formulaire de modification |
| PUT/PATCH | `/offres/{offer}` | `offres.update` | Mettre à jour une offre |
| DELETE | `/offres/{offer}` | `offres.destroy` | Supprimer une offre |

**Middleware** : `auth`, `verified` (pour toutes les routes)

**Modèle Route** : `Route::resource('offres', OffreController::class)->middleware(['auth', 'verified']);`

## Cas limites

### Offre inexistante

- **Scénario** : Navigation vers `/offres/999` alors que l'offre n'existe pas
- **Comportement attendu** : Réponse HTTP 404 avec page d'erreur dédiée
- **Message** : "L'offre demandée n'existe pas."

### Utilisateur non authentifié

- **Scénario** : Accès à `/offres` sans être connecté
- **Comportement attendu** : Redirection vers `/login`
- **Message flash** : "Veuillez vous connecter pour accéder à cette page."

### Utilisateur non autorisé

- **Scénario** : Tentative d'accès à `/offres/1` alors que l'offre appartient à un autre utilisateur
- **Comportement attendu** : Réponse HTTP 403 avec page d'erreur dédiée
- **Message** : "Vous n'avez pas les droits pour accéder à cette offre."

### Formulaire invalide

- **Scénario** : Soumission du formulaire avec des données manquantes ou invalides
- **Comportement attendu** : Retour au formulaire avec erreurs affichées, données saisies préservées
- **Exemples d'erreurs** :
  - "Le titre est requis."
  - "L'expérience minimale doit être un nombre positif."
  - "Les compétences doivent être un tableau."

### Liste vide

- **Scénario** : Utilisateur connecté sans offre créée
- **Comportement attendu** : Affichage d'un message informatif
- **Message** : "Vous n'avez pas encore créé d'offre. Commencez par en créer une !"
- **Action** : Lien vers `/offres/create`

## Critères d'acceptation

### US2 - Création d'une offre

- [ ] L'utilisateur peut créer une offre avec title, description, required_skills et minimum_experience
- [ ] Les données sont validées côté serveur avant persistence
- [ ] Les required_skills sont stockées en JSON (tableau de strings)
- [ ] L'offre est associée à l'utilisateur connecté (user_id)
- [ ] Après création, l'utilisateur est redirigé vers la page de détail

### US3 - Liste des offres

- [ ] L'utilisateur voit uniquement ses propres offres
- [ ] Chaque offre affiche le nombre d'analyses réalisées
- [ ] La liste est vide si aucune offre n'a été créée
- [ ] Un message approprié est affiché en cas de liste vide

### US4 - Détail d'une offre

- [ ] L'utilisateur peut voir le détail complet d'une offre
- [ ] Les analyses associées sont affichées avec leur matching_score
- [ ] Les analyses sont triées par score décroissant
- [ ] Les liens "Modifier" et "Supprimer" sont accessibles

### Non-fonctionnel

- [ ] Aucune offre d'un autre utilisateur n'est accessible
- [ ] Les routes sont protégées par authentification et vérification d'email
- [ ] La validation serveur est complète et explicite
- [ ] Le code suit les conventions Laravel (Form Requests, Policies, Eloquent)

## Contraintes techniques Laravel

| Composant | Version / Détail |
|-----------|------------------|
| PHP | 8.3 |
| Laravel Framework | 13.x |
| Laravel Breeze | 2.4 (authentification) |
| Base de données | MySQL |
| Frontend | Blade + Tailwind CSS 3 + Alpine.js 3 |
| Form Requests | `App\Http\Requests\StoreOffreRequest`, `App\Http\Requests\UpdateOffreRequest` |
| Policy | `App\Policies\OffrePolicy` |
| Model | `App\Models\Offer` |
| Migration | `create_offers_table` |
| Factory | `OfferFactory` |
| Relationships | `Offer belongsTo User`, `User hasMany Offer`, `Offer hasMany Analyse` |
| Eloquent Casts | `required_skills` → `array` (JSON) |
| Validation | Côté serveur via Form Requests, validation côté client optionnelle |
