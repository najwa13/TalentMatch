# Analyse IA — Spécification fonctionnelle

## Objectif de l'analyse IA

Générer automatiquement une évaluation structurée d'un candidat à partir de son CV soumis, en le comparant à une offre d'emploi existante. L'analyse produit un score de matching objectif (0–100) et une recommandation RH standardisée.

## Entrées (CV + offre)

| Entrée | Source | Format |
|---|---|---|
| Texte du CV | Soumission utilisateur (formulaire) | Texte libre |
| Nom du candidat | Soumission utilisateur | Chaîne |
| Offre d'emploi | Base de données (Offer model) | Titre, description, compétences requises, expérience minimale |

L'analyse est effectuée dans le contexte d'une offre précise : un CV est toujours analysé par rapport à une offre.

## Sorties (JSON structuré)

```json
{
  "competences_extraites": ["PHP", "Laravel", "MySQL"],
  "annees_experience": 5,
  "niveau_etudes": "Master en informatique",
  "langues": ["Français (natif)", "Anglais (courant)"],
  "matching_score": 78,
  "points_forts": ["Maîtrise du framework Laravel", "Expérience en équipe agile"],
  "lacunes": ["Pas d'expérience en DevOps"],
  "competences_manquantes": ["Docker", "CI/CD"],
  "recommandation": "convoquer",
  "justification": "Le candidat possède les compétences techniques clés (PHP, Laravel) et dépasse l'expérience minimale requise."
}
```

## Pipeline de traitement

1. **Soumission** — L'utilisateur soumet un CV (texte + nom) via un formulaire lié à une offre
2. **Validation** — `FormRequest` Laravel valide les champs requis
3. **Dispatch** — Un job `AnalyzeCvJob` est dispatché dans la queue (driver database)
4. **Analyse IA** — Le job appelle Laravel AI SDK avec structured output, en transmettant le CV et l'offre
5. **Parsing** — Le JSON structuré est validé et parsé en PHP
6. **Persistance** — Le résultat complet est stocké en base (model `Analysis` avec cast JSON)
7. **Notification** — L'utilisateur peut consulter l'analyse via l'interface

```
[Soumission] → [Validation] → [Queue Job] → [AI SDK Structured Output] → [Parsing] → [Persistance]
```

## Règles métier IA

1. **Extraction** : l'IA extrait les compétences, l'expérience, les études et les langues depuis le texte du CV
2. **Matching** : le score est calculé en comparant chaque compétence requise de l'offre avec les compétences extraites du CV
3. **Pondération implicite** : les compétences techniques comptent plus que les études dans le score
4. **Recommandation** :
   - Score >= 70 → `convoquer`
   - Score 40–69 → `attente`
   - Score < 40 → `rejeter`
5. **Justification** : l'IA doit expliquer pourquoi le candidat est recommandé ou non

## Contraintes (score, recommandation)

- Score : entier 0–100, calculé par l'IA selon les critères de l'offre
- Recommandation : enum PHP `Recommendation` avec cast Eloquent
- L'analyse est immutable une fois générée (pas de modification, seulement ré-analyse)
- Chaque analyse est liée à un couple (candidat, offre) unique

## Hors périmètre

- Analyse de CV au format PDF ou DOCX (texte uniquement)
- OCR ou extraction depuis images
- Matching multi-offres (un CV analysé pour une seule offre à la fois)
- Analyse comparative automatique entre candidats (hors agent conversationnel)
- Dashboard de statistiques globales
- API publique

## Cas limites

| Situation | Comportement attendu |
|---|---|
| CV vide | Erreur de validation `AnalyzeCvRequest` (422) |
| Offre sans compétences requises | Analyse partielle : score basé sur expérience et études uniquement |
| Expérience non mentionnée | Valeur par défaut 0 an |
| Langue non spécifiée | Tableau vide |
| Recommandation IA invalide | Fallback vers `attente`, erreur loggée |
| Timeout API IA | Nouvelle tentative (max 3), puis statut `failed` |
| JSON mal formaté | Nouvelle tentative d'analyse, puis statut `failed` |
| Plusieurs analyses pour même couple candidat-offre | Nouvelle analyse écrase la précédente |

## Format du prompt IA

Le prompt système combine les règles métier et le schéma structured output :

```
Tu es un assistant RH spécialisé dans l'analyse de CV. Ton rôle est d'analyser un CV
par rapport à une offre d'emploi et de produire un résultat JSON structuré.

Offre d'emploi :
- Titre : {titre}
- Compétences requises : {competences}
- Expérience minimale requise : {annees} ans

CV du candidat ({nom_candidat}) :
{cv_texte}

Règles :
1. Extrais les compétences techniques et non-techniques du CV
2. Estime les années d'expérience totales du candidat
3. Identifie le niveau d'études le plus élevé
4. Liste les langues parlées avec leur niveau
5. Calcule un score de matching (0-100) entre le CV et l'offre
6. Identifie les points forts et les lacunes
7. Détermine les compétences manquantes par rapport à l'offre
8. Produis une recommandation : convoquer (score >= 70), attente (40-69), rejeter (< 40)
9. Justifie la recommandation en 2-3 phrases

Réponds UNIQUEMENT avec le JSON structuré suivant, sans texte additionnel :
{schéma_structured_output_json}
```

## Erreurs possibles

| Erreur | Cause | Effet | Récupération |
|---|---|---|---|
| `validation_error` | Données entrée invalides | HTTP 422, analyse non créée | Correction formulaire |
| `ai_timeout` | API IA ne répond pas dans le délai | Nouvelle tentative job | Max 3 tentatives |
| `ai_malformed_response` | JSON non conforme au schéma | Nouvelle tentative analyse | Max 2 tentatives |
| `ai_empty_response` | Réponse vide de l'API | Statut `failed` | Réanalyse manuelle |
| `analysis_failed` | Erreur non rattrapable | Statut `failed` + message d'erreur | Log journalisé |
| `offer_not_found` | Offre supprimée pendant l'analyse | Job échoue avec message clair | Vérifier existence offre avant analyse |
