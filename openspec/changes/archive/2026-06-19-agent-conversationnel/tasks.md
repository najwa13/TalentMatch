## 1. Assistant Agent et Tools

- [x] 1.1 Créer l'agent `AssistantAgent` avec instructions système, `RemembersConversations` trait, et les trois tools
- [x] 1.2 Créer le tool `GetCandidateAnalysis($id)` retournant l'analyse complète
- [x] 1.3 Créer le tool `GetJobRequirements($id)` retournant les requis de l'offre
- [x] 1.4 Créer le tool `CompareCandidates($id1, $id2)` comparant deux analyses

## 2. Contrôleur et Routes API

- [x] 2.1 Créer le contrôleur `AssistantController` avec méthode `ask()`
- [x] 2.2 Valider l'entrée : message requis, conversation_id optionnel, analysis_id optionnel
- [x] 2.3 Gérer nouvelle conversation (`forUser`) et suite de conversation (`continue`)
- [x] 2.4 Ajouter la route POST `/api/assistant/ask` avec middleware `auth`

## 3. Interface Web de Chat

- [x] 3.1 Créer la vue Blade `chat.show` avec historique des messages
- [x] 3.2 Créer le formulaire d'envoi de message avec AJAX
- [x] 3.3 Afficher le contexte d'analyse (analysis_id) dans l'interface
- [x] 3.4 Ajouter la navigation vers le chat depuis le dashboard

## 4. Persistance de la Mémoire

- [x] 4.1 Ajouter le trait `HasConversations` au modèle User
- [x] 4.2 Configurer la migration AI SDK pour `agent_conversations` et `agent_conversation_messages`
- [x] 4.3 Vérifier le chargement des 50 derniers messages maximum

## 5. Tests

- [x] 5.1 Écrire les tests unitaires pour les tools (GetCandidateAnalysis, GetJobRequirements, CompareCandidates)

- [x] 5.2 Écrire les tests de feature pour l'endpoint API (authentification, validation, réponse)

- [x] 5.3 Écrire les tests de feature pour la mémoire de conversation (nouvelle, suite, inexistante)

- [x] 5.4 Écrire les tests de feature pour l'injection de contexte (analysis_id)
