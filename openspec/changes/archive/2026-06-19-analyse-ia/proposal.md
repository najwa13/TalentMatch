## Why

Le département RH reçoit 50 à 200 CVs par offre d'emploi. Le tri manuel est chronophage, subjectif et répétitif. L'analyse IA permet d'automatiser l'évaluation des CVs, de générer un score de matching objectif (0-100) et une recommandation RH, réduisant le temps de présélection de plusieurs heures à quelques minutes.

## What Changes

- Nouveau système d'analyse IA de CV avec structured output (JSON typé)
- Pipeline asynchrone : soumission CV → analyse IA → notification résultat
- Score de matching CV/offre avec critères pondérés (compétences, expérience, études, langues)
- Recommandation automatique : convoquer / attente / rejeter
- Stockage persistant de l'analyse complète en base de données
- Agent IA conversationnel avec tools Laravel pour répondre aux questions sur les candidats

## Capabilities

### New Capabilities
- `analyse-ia`: Analyse automatique de CV par IA : extraction de compétences, expérience, études, langues ; calcul de score de matching ; génération de recommandation RH avec justification

### Modified Capabilities

<!-- Aucune capability existante modifiée -->

## Impact

- Models : `Candidate`, `Analysis` (nouveau), `Offer` (existants)
- Controllers : `CandidateAnalysisController` (nouveau), `OfferController` (modifié)
- Jobs : `AnalyzeCvJob` (nouveau, asynchrone)
- IA : intégration Laravel AI SDK avec structured output + tools/function calling
- Queue : driver database pour traitement asynchrone
- Routes : nouvelles routes API pour soumission, consultation analyse, assistant
- Tests : nouveaux tests d'analyse, de matching et de l'agent IA
