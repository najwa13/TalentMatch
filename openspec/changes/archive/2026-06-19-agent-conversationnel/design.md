## Context

TalentMatch dispose déjà d'une analyse IA structurée des CV avec stockage en base. Les recruteurs peuvent consulter les analyses individuellement mais n'ont pas de moyen interactif d'explorer les résultats. Le cahier des charges impose un assistant conversationnel contextuel (US9-US11) avec mémoire persistée, tools Laravel, et interdiction d'inventer des données.

L'infrastructure existante inclut :
- Tables `agent_conversations` et `agent_conversation_messages` de l'AI SDK
- Modèle `Conversation` existant
- Trait `RemembersConversations` et `HasConversations` disponibles dans l'AI SDK
- Tools déjà implémentés : `GetCandidateAnalysis`, `GetJobRequirements`, `CompareCandidates`

## Goals / Non-Goals

**Goals:**
- Assistant IA répondant en langage naturel sur les analyses de candidats
- Mémoire de conversation persistée automatiquement via AI SDK
- Tools obligatoires : getCandidateAnalysis, getJobRequirements, compareCandidates
- Endpoint API POST /api/assistant/ask avec conversation_id optionnel
- Interface web de chat intégrée au dashboard

**Non-Goals:**
- Pas de chat entre utilisateurs (seulement IA → RH)
- Pas d'analyse de CV dans le chat (délégué à l'analyse IA existante)
- Pas de modification d'analyse via le chat
- Pas de support multi-langues pour l'interface

## Decisions

1. **RemembersConversations trait** — Utilisation du trait AI SDK pour la persistance automatique des messages. Alternative rejetée : stockage manuel (réinvente la roue).

2. **Tools plutôt que sub-agents** — Les trois fonctions (getCandidateAnalysis, getJobRequirements, compareCandidates) sont implémentées comme des Tools Laravel AI SDK. Alternative rejetée : sub-agents (surcharge inutile pour des requêtes simples).

3. **API + Web** — Double interface : API JSON pour des intégrations futures et interface Blade pour l'utilisation quotidienne. L'API est le contrat principal, le web est une surcouche.

4. **Conversation memory via AI SDK** — Utilisation de `continue(conversationId)` pour les conversations existantes et `forUser(user)` pour les nouvelles. La persistence est gérée par le trait.

## Risks / Trade-offs

- [Coût API] chaque message = un appel IA → Mitigation : pas de limite stricte, mais informer l'utilisateur
- [Hallucination] l'agent pourrait inventer des données sans les tools → Mitigation : tools obligatoires, instructions strictes dans le prompt système
- [Contexte long] les conversations longues dépassent le contexte token → Mitigation : le modèle gère le sliding window, mais prévoir une limite de 50 messages
- [Dépendance AI SDK] version en évolution rapide → Mitigation : interface contractuelle, mise à jour suivie
