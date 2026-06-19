## 1. Modèles et migrations

- [ ] 1.1 Créer la migration `create_analyses_table` avec les colonnes : offer_id, candidate_name, cv_text, raw_response (JSON), result (JSON), status (enum: pending/processing/completed/failed), error_message, analysed_at
- [x] 1.2 Créer l'Enum `AnalysisStatus` avec les cas : Pending, Processing, Completed, Failed
- [x] 1.3 Créer l'Enum `Recommendation` avec les cas : Convoquer, Attente, Rejeter
- [x] 1.4 Créer le modèle `Analysis` avec les casts Eloquent (JSON pour result/raw_response, Enum pour status et recommendation)
- [ ] 1.5 Définir les relations Eloquent (Analysis belongsTo Offer, Offer hasMany Analysis)

## 2. Validation et soumission

- [x] 2.1 Créer le Form Request `AnalyzeCvRequest` avec règles : cv_text requis|string|min:50, candidate_name requis|string|max:255, offer_id requis|exists:offers,id
- [x] 2.2 Créer le contrôleur `CandidateAnalysisController` avec méthode `store()` et `show()`
- [x] 2.3 Créer les routes API : POST `/api/offers/{offer}/analyses` (store), GET `/api/analyses/{analysis}` (show)

## 3. Analyse IA asynchrone

- [x] 3.1 Configurer le driver queue `database` et créer la table jobs
- [x] 3.2 Créer le job `AnalyzeCvJob` avec gestion de l'analyse IA
- [x] 3.3 Implémenter l'appel Laravel AI SDK avec structured output pour l'analyse CV
- [x] 3.4 Construire le prompt système combinant CV + offre d'emploi
- [x] 3.5 Parsing et validation du JSON retourné par l'IA
- [x] 3.6 Gestion des erreurs : timeout, JSON malformé, réponse vide (max 3 tentatives)

## 4. Agent conversationnel IA

- [x] 4.1 Implémenter le tool `getCandidateAnalysis($id)` retournant l'analyse complète
- [x] 4.2 Implémenter le tool `getJobRequirements($id)` retournant les requis de l'offre
- [x] 4.3 Implémenter le tool `compareCandidates($id1, $id2)` comparant deux analyses
- [x] 4.4 Créer le contrôleur `AssistantController` avec endpoint POST `/api/assistant/ask`
- [x] 4.5 Configurer la mémoire conversationnelle persistée

## 5. Gestion des offres (mise à jour)

- [x] 5.1 Modifier l'offre existante si nécessaire (offers table existante)
- [x] 5.2 Afficher le nombre de candidats analysés dans la liste des offres
- [x] 5.3 Afficher le détail d'une offre avec ses candidats et scores

## 6. Tests

- [x] 6.1 Écrire les tests unitaires pour l'Enum `Recommendation` et `AnalysisStatus`
- [x] 6.2 Écrire les tests de feature pour la soumission et validation CV (AnalyzeCvRequest)
- [x] 6.3 Écrire les tests de feature pour le job `AnalyzeCvJob` (succès, timeout, malformed)
- [x] 6.4 Écrire les tests de feature pour l'agent conversationnel avec tools
- [x] 6.5 Écrire les tests de feature pour les endpoints API (store, show, assistant)
