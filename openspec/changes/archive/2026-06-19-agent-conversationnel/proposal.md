## Why

Les recruteurs ont besoin de comprendre le détail des analyses de CV : pourquoi un score est bas, quelles compétences manquent, comment deux candidats se comparent. Un assistant conversationnel contextuel leur permet d'interagir en langage naturel avec l'analyse, sans avoir à naviguer dans des interfaces complexes.

## What Changes

- Nouvel agent IA conversationnel avec mémoire persistée
- Tools Laravel pour récupérer les données réelles (analyse candidat, requis offre, comparaison)
- Endpoint API POST /api/assistant/ask avec support de conversation_id
- Interface web de chat dans le tableau de bord

## Capabilities

### New Capabilities
- `agent-conversationnel`: Assistant IA conversationnel permettant aux recruteurs de poser des questions en langage naturel sur les analyses de CV, avec mémoire de conversation, outils d'accès aux données réelles, et injection de contexte d'analyse.

### Modified Capabilities
<!-- Aucune capability existante modifiée -->

## Impact

- Models : `Conversation`, `AgentConversationMessage` (existants AI SDK)
- Controllers : `AssistantController` (nouveau), `ChatController` (modifié)
- AI Agent : `AssistantAgent` avec `RemembersConversations` trait
- Tools : `GetCandidateAnalysis`, `GetJobRequirements`, `CompareCandidates`
- Routes : nouvelle route API POST /api/assistant/ask
- Views : nouvelle interface chat dans le dashboard
- Tests : nouveaux tests de l'agent conversationnel et des tools
