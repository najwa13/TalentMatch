## ADDED Requirements

### Requirement: User can create a job offer
The system SHALL allow authenticated and verified users to create a job offer with titre, description, competences requises, and experience minimale.

#### Scenario: Successful offer creation
- **WHEN** an authenticated and verified user submits a valid offer form with titre="Développeur Laravel Senior", description="...", competences=["PHP","Laravel"], experience_min=3
- **THEN** the offer is persisted in the database with user_id set to the authenticated user, and the user is redirected to the offer detail page with a success message

#### Scenario: Validation failure on create
- **WHEN** an authenticated user submits an offer form with missing titre (empty string)
- **THEN** the system returns to the create form with validation errors displayed and the previously entered data preserved

### Requirement: User can view list of their offers
The system SHALL display a list of all offers belonging to the authenticated user, with the count of analyzed candidates per offer.

#### Scenario: User with offers sees list
- **WHEN** an authenticated user navigates to /offres and has 3 offers in the database
- **THEN** the system displays exactly 3 offers with titre, created_at, and candidates_count for each

#### Scenario: User with no offers sees empty state
- **WHEN** an authenticated user navigates to /offres and has no offers
- **THEN** the system displays an empty state message "Vous n'avez pas encore créé d'offre" with a link to /offres/create

#### Scenario: User only sees own offers
- **WHEN** an authenticated user with id=1 navigates to /offres and another user (id=2) has offers
- **THEN** the system displays only offers where user_id=1

### Requirement: User can view offer detail
The system SHALL display the complete detail of an offer, including title, description, competences, experience minimale, and a list of associated candidates sorted by score descending.

#### Scenario: View offer detail
- **WHEN** an authenticated user navigates to /offres/{id} for an offer they own
- **THEN** the system displays the full offer details and a list of candidates with scores sorted by score descending

#### Scenario: View non-existent offer
- **WHEN** an authenticated user navigates to /offres/999 and offer 999 does not exist
- **THEN** the system returns a 404 error page

### Requirement: User can update their offer
The system SHALL allow the owner of an offer to update its titre, description, competences, and experience_min.

#### Scenario: Successful offer update
- **WHEN** the owner submits a valid update form for their offer
- **THEN** the offer is updated in the database and the user is redirected to the offer detail page with a success message

#### Scenario: Update offer with invalid data
- **WHEN** the owner submits an update form with empty titre
- **THEN** the system returns to the edit form with validation errors and previously entered data preserved

#### Scenario: Non-owner attempts update
- **WHEN** an authenticated user attempts to access the edit form for an offer owned by another user
- **THEN** the system returns a 403 forbidden response

### Requirement: User can delete their offer
The system SHALL allow the owner of an offer to delete it and all associated candidates and analyses.

#### Scenario: Successful offer deletion
- **WHEN** the owner confirms deletion of their offer
- **THEN** the offer and all associated data are removed from the database and the user is redirected to the offers list with a success message

#### Scenario: Non-owner attempts deletion
- **WHEN** an authenticated user attempts to delete an offer owned by another user
- **THEN** the system returns a 403 forbidden response

### Requirement: Authentication and authorization enforcement
The system SHALL require authentication and email verification for all CRUD operations on offers. The system SHALL enforce that only the owner of an offer can view, update, or delete it.

#### Scenario: Unauthenticated access
- **WHEN** a non-authenticated user attempts to access /offres
- **THEN** the system redirects to /login

#### Scenario: Unverified email access
- **WHEN** an authenticated user with unverified email attempts to access /offres
- **THEN** the system redirects to /verify-email

#### Scenario: Cross-user access attempt
- **WHEN** an authenticated user attempts to view/update/delete an offer belonging to another user
- **THEN** the system returns a 403 forbidden response

### Requirement: Input validation
The system SHALL validate all input data server-side using Form Requests. The system SHALL validate that competences is a non-empty JSON array of strings, experience_min is an integer between 0 and 50, titre is required with max 255 characters, and description is required.

#### Scenario: Invalid competences format
- **WHEN** a user submits an offer with competences="not an array"
- **THEN** the system returns a validation error "Les compétences doivent être un tableau JSON valide"

#### Scenario: experience_min out of range
- **WHEN** a user submits an offer with experience_min=60
- **THEN** the system returns a validation error about maximum value

#### Scenario: Empty competences array
- **WHEN** a user submits an offer with competences=[]
- **THEN** the system returns a validation error requiring at least one competence
