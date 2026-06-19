## Context

TalentMatch est une application Laravel qui doit analyser des CVs par IA et les comparer à des offres d'emploi. Actuellement, le projet dispose d'une structure Laravel de base avec authentification Breeze. L'analyse IA est le cœur du projet : elle doit être asynchrone, structurée (JSON typé), et reposer sur Laravel AI SDK avec structured output.

Contraintes techniques issues du cahier des charges :
- PHP 8.3, Laravel 13, Laravel AI SDK
- Jobs & Queues pour le traitement asynchrone
- Eloquent Casts (JSON + Enum)
- Tools / Function Calling pour l'agent conversationnel
- Form Requests Laravel pour la validation

## Goals / Non-Goals

**Goals:**
- Pipeline d'analyse IA asynchrone : soumission → file d'attente → analyse → résultat
- Structured output JSON validé côté IA et côté application
- Score de matching 0-100 avec critères pondérés
- Recommandation automatique (convoquer / attente / rejeter)
- Stockage persistant des analyses
- Agent IA conversationnel avec tools pour questions contextuelles

**Non-Goals:**
- Pas d'analyse de CV PDF (texte uniquement)
- Pas d'OCR ou extraction depuis images
- Pas de matching multi-offres simultané pour un CV
- Pas de dashboard analytics ou statistiques avancées
- Pas d'API publique exposee

## Decisions

1. **Laravel AI SDK avec structured output** — Utilisation de `Ai\AiManager` avec un provider configurable (OpenAI par défaut) pour garantir un JSON validé côté LLM. Alternative rejetée : parsing manuel du texte (fragile, non fiable).

2. **Queue asynchrone via `database` driver** — L'analyse peut prendre 10-30s. Un job `AnalyzeCvJob` est dispatché après soumission. Alternative rejetée : synchrone (bloque l'utilisateur).

3. **Enum `Recommendation` avec cast Eloquent** — `convoquer`, `attente`, `rejeter` typés via un backed enum PHP. Alternative rejetée : string simple (pas de typage).

4. **Agent IA avec tools Laravel** — L'agent conversationnel utilise `getCandidateAnalysis(id)` et `getJobRequirements(id)` comme tools réels. Alternative rejetée : agent sans tools = hallucinations.

5. **Form Request dédié `AnalyzeCvRequest`** — Validation du CV soumis avant dispatch du job. Alternative rejetée : validation inline dans le controller (non réutilisable).

## Risks / Trade-offs

- [Coût API] analyse IA systématique = coût par appel → Mitigation : mettre en cache les analyses et proposer une réanalyse explicite
- [Temps d'attente] job asynchrone = délai avant résultat → Mitigation : notification par événement ou polling coté frontend
- [Qualité IA] le structured output peut dévier → Mitigation : validation secondaire en PHP après réception du JSON
- [Provider IA] dépendance à un service externe → Mitigation : abstraction via Laravel AI SDK, fallback configurable
