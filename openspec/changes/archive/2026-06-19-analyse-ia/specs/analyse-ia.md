## ADDED Requirements

### Requirement: Analyse IA de CV avec Structured Output
Le système SHALL analyser un CV soumis et produire un résultat JSON structuré via Laravel AI SDK avec structured output. L'analyse MUST être asynchrone (job + queue). Le résultat DOIT contenir les champs suivants : compétences extraites, années d'expérience, niveau d'études, langues, score de matching (0-100), points forts, lacunes, compétences manquantes, recommandation, justification.

#### Scenario: Analyse complète réussie
- **WHEN** un RH soumet un CV valide avec un texte de CV et un nom de candidat
- **THEN** le job `AnalyzeCvJob` est dispatché dans la queue
- **THEN** l'IA produit un structured output JSON avec tous les champs requis
- **THEN** le résultat est persisté en base de données
- **THEN** l'utilisateur peut consulter l'analyse

#### Scenario: CV soumis sans texte
- **WHEN** un RH soumet un CV avec un champ texte vide
- **THEN** le Form Request `AnalyzeCvRequest` retourne une erreur de validation 422
- **THEN** aucun job n'est dispatché

### Requirement: Calcul du score de matching
Le système SHALL calculer un score de 0 à 100 entre un CV et une offre d'emploi. Le score DOIT prendre en compte : les compétences requises vs. possédées, l'expérience minimale requise, le niveau d'études, les langues. La pondération des critères est définie dans la logique métier.

#### Scenario: CV parfaitement adapté
- **WHEN** le CV contient toutes les compétences requises, l'expérience minimale, le bon niveau d'études et les langues demandées
- **THEN** le score de matching est >= 80
- **THEN** la recommandation est "convoquer"

#### Scenario: CV partiellement adapté
- **WHEN** le CV contient environ la moitié des compétences requises mais pas l'expérience minimale
- **THEN** le score de matching est entre 40 et 60
- **THEN** la recommandation est "attente"

#### Scenario: CV non adapté
- **WHEN** le CV ne contient presque aucune compétence requise
- **THEN** le score de matching est < 30
- **THEN** la recommandation est "rejeter"

### Requirement: Recommandation RH avec énumération
Le système SHALL générer une recommandation parmi trois valeurs : `convoquer`, `attente`, `rejeter`. La recommandation DOIT être typée via un Enum PHP `Recommendation` avec Eloquent cast.

#### Scenario: Recommandation convoquer
- **WHEN** le score de matching est >= 70
- **THEN** la recommandation est "convoquer"
- **THEN** la justification explique pourquoi le candidat est retenu

#### Scenario: Recommandation attente
- **WHEN** le score de matching est entre 40 et 69
- **THEN** la recommandation est "attente"
- **THEN** la justification liste les compétences manquantes principales

#### Scenario: Recommandation rejeter
- **WHEN** le score de matching est < 40
- **THEN** la recommandation est "rejeter"
- **THEN** la justification explique le décalage avec les requis

### Requirement: Agent conversationnel avec Tools
Le système SHALL fournir un assistant IA conversationnel capable de répondre à des questions sur les candidats et leurs analyses. L'agent DOIT utiliser les tools Laravel : `getCandidateAnalysis(id)` pour récupérer une analyse, `getJobRequirements(id)` pour les requis d'une offre, `compareCandidates(id1, id2)` pour comparer deux candidats. L'agent NE DOIT PAS inventer de données.

#### Scenario: Question sur le score d'un candidat
- **WHEN** un utilisateur demande "Pourquoi ce candidat a-t-il ce score ?"
- **THEN** l'agent appelle `getCandidateAnalysis(id)` avec l'ID du candidat
- **THEN** l'agent répond avec la justification et les critères détaillés du score

#### Scenario: Question sur les requis d'une offre
- **WHEN** un utilisateur demande "Quels sont les prérequis pour cette offre ?"
- **THEN** l'agent appelle `getJobRequirements(id)` avec l'ID de l'offre
- **THEN** l'agent liste les compétences et l'expérience requises

#### Scenario: Comparaison de deux candidats
- **WHEN** un utilisateur demande "Comparez le candidat A et le candidat B"
- **THEN** l'agent appelle `compareCandidates(id1, id2)` avec les deux IDs
- **THEN** l'agent présente les différences de compétences, expérience et score

### Requirement: Format du prompt IA et gestion d'erreurs
Le système SHALL utiliser un prompt structuré pour l'analyse IA, définissant clairement le rôle, les entrées, le format de sortie attendu et les règles métier. Le système SHALL gérer les erreurs suivantes : réponse IA mal formatée, timeout API, analyse échouée. En cas d'erreur, le statut de l'analyse DOIT passer à "failed" avec un message d'erreur stocké.

#### Scenario: Réponse IA mal formatée
- **WHEN** l'IA retourne un JSON qui ne correspond pas au schéma attendu
- **THEN** le système tente une nouvelle fois l'analyse
- **THEN** si la seconde tentative échoue, le statut passe à "failed"
- **THEN** un message d'erreur est enregistré dans l'analyse

#### Scenario: Timeout API IA
- **WHEN** l'appel API IA dépasse le timeout configuré
- **THEN** le job est relancé avec un nouveau délai
- **THEN** après 3 échecs consécutifs, le statut passe à "failed"

#### Scenario: Données manquantes dans l'offre
- **WHEN** une offre n'a pas de compétences requises définies
- **THEN** l'analyse compare uniquement sur les années d'expérience et le niveau d'études
- **THEN** le score est calculé avec les critères disponibles uniquement
