# Agent Conversationnel

## Purpose

Assistant IA conversationnel permettant aux recruteurs de poser des questions en langage naturel sur les analyses de CV, avec mémoire de session, outils d'accès aux données réelles, et injection de contexte d'analyse.

## Requirements

### Requirement: Agent conversationnel avec mémoire persistée
Le système SHALL fournir un assistant IA conversationnel permettant aux recruteurs de poser des questions en langage naturel sur les analyses de CV. L'assistant SHALL utiliser le trait `RemembersConversations` de l'AI SDK pour persister automatiquement l'historique des messages en base de données. La mémoire SHALL être conservée entre les sessions utilisateur.

#### Scenario: Nouvelle conversation
- **WHEN** un utilisateur envoie un premier message sans `conversation_id`
- **THEN** l'agent crée une nouvelle conversation
- **THEN** l'agent répond et retourne un `conversation_id`

#### Scenario: Suite de conversation
- **WHEN** un utilisateur envoie un message avec un `conversation_id` valide
- **THEN** l'agent charge l'historique de la conversation
- **THEN** l'agent répond en tenant compte du contexte des échanges précédents

#### Scenario: Conversation inexistante
- **WHEN** un utilisateur envoie un message avec un `conversation_id` invalide
- **THEN** l'API retourne une erreur 404

### Requirement: Tools d'accès aux données réelles
L'agent SHALL utiliser exclusivement les tools Laravel AI SDK pour accéder aux données. L'agent NE DOIT PAS générer de données qui ne proviennent pas des tools. Les tools obligatoires sont : `getCandidateAnalysis(id)`, `getJobRequirements(id)`, `compareCandidates(id1, id2)`.

#### Scenario: Question sur le score d'un candidat
- **WHEN** un utilisateur demande "Pourquoi ce candidat a-t-il un score de 45 ?"
- **THEN** l'agent appelle `getCandidateAnalysis(id)` avec l'ID de l'analyse
- **THEN** l'agent répond en citant les critères : compétences manquantes, expérience, etc.

#### Scenario: Question sur les requis d'une offre
- **WHEN** un utilisateur demande "Quels sont les prérequis pour l'offre Développeur ?"
- **THEN** l'agent appelle `getJobRequirements(id)` avec l'ID de l'offre
- **THEN** l'agent liste les compétences requises et l'expérience minimale

#### Scenario: Comparaison de deux candidats
- **WHEN** un utilisateur demande "Compare le candidat A et le candidat B"
- **THEN** l'agent appelle `compareCandidates(id1, id2)` avec les deux IDs
- **THEN** l'agent présente les différences de score, forces et faiblesses

#### Scenario: L'agent n'invente pas de données
- **WHEN** un utilisateur demande une information non couverte par les tools
- **THEN** l'agent indique qu'il ne peut pas répondre avec les données disponibles
- **THEN** l'agent suggère d'utiliser les outils à sa disposition

### Requirement: Format des messages et rôles
Le système SHALL gérer les messages avec les rôles `user` (messages de l'utilisateur) et `assistant` (réponses de l'IA). Chaque message SHALL être horodaté. L'historique SHALL être accessible via la relation Eloquent du modèle de conversation.

#### Scenario: Envoi d'un message utilisateur
- **WHEN** un utilisateur envoie un message via POST /api/assistant/ask
- **THEN** le message est enregistré avec le rôle "user"
- **THEN** l'agent traite la requête

#### Scenario: Réponse de l'assistant
- **WHEN** l'agent a terminé de répondre
- **THEN** le message est enregistré avec le rôle "assistant"
- **THEN** la réponse est retournée à l'utilisateur

### Requirement: Injection de contexte d'analyse
L'agent SHALL pouvoir recevoir un contexte d'analyse spécifique (analysis_id) pour centrer la conversation sur un candidat particulier. Le contexte SHALL être transmis en paramètre optionnel de la requête.

#### Scenario: Question avec contexte d'analyse
- **WHEN** un utilisateur envoie un message avec un `analysis_id`
- **THEN** l'agent charge automatiquement l'analyse via `getCandidateAnalysis(analysis_id)`
- **THEN** l'agent répond dans le contexte de cette analyse

#### Scenario: Question sans contexte
- **WHEN** un utilisateur demande une information sans analysis_id
- **THEN** l'agent demande les IDs nécessaires avant d'utiliser les tools

### Requirement: Gestion du contexte long
Le système SHALL limiter l'historique chargé aux 50 derniers messages pour éviter les dépassements de contexte token. Les conversations les plus anciennes SHALL être tronquées automatiquement.

#### Scenario: Conversation courte
- **WHEN** une conversation a moins de 50 messages
- **THEN** l'agent charge l'intégralité de l'historique

#### Scenario: Conversation longue
- **WHEN** une conversation dépasse 50 messages
- **THEN** l'agent charge uniquement les 50 derniers messages
- **THEN** la réponse de l'agent peut être moins précise sur les échanges anciens

### Requirement: Sécurité et règles de non-invention
L'agent SHALL suivre des instructions strictes dans son prompt système : ne jamais inventer de données, toujours utiliser les tools pour les informations factuelles, reconnaître les limites de ses connaissances. Le prompt système SHALL être défini dans la classe de l'agent.

#### Scenario: Question sur un candidat inexistant
- **WHEN** un utilisateur demande une analyse pour un ID qui n'existe pas
- **THEN** le tool retourne une erreur
- **THEN** l'agent informe que l'analyse est introuvable

#### Scenario: Question hors périmètre
- **WHEN** un utilisateur demande une information personnelle sur un candidat (âge, adresse)
- **THEN** l'agent indique que cette information n'est pas disponible dans les analyses

### Requirement: Endpoint API sécurisé
Le système SHALL exposer l'endpoint POST /api/assistant/ask protégé par le middleware `auth`. La requête SHALL contenir un champ `message` obligatoire et les champs optionnels `conversation_id` et `analysis_id`.

#### Scenario: Requête non authentifiée
- **WHEN** un utilisateur non connecté appelle POST /api/assistant/ask
- **THEN** l'API retourne une erreur 401

#### Scenario: Requête sans message
- **WHEN** un utilisateur envoie une requête sans champ `message`
- **THEN** l'API retourne une erreur 422 avec validation du champ requis

#### Scenario: Requête valide
- **WHEN** un utilisateur authentifié envoie un message valide
- **THEN** l'API retourne 200 avec la réponse et le `conversation_id`
