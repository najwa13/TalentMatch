# Agent Conversationnel — Spécification fonctionnelle

## Objectif de l'agent conversationnel

Permettre aux recruteurs d'interagir en langage naturel avec les analyses de CV via un assistant IA contextuel. L'agent répond aux questions sur les candidats, les scores, les compétences et les comparaisons, en utilisant des tools d'accès aux données réelles plutôt que de générer des informations.

## Périmètre fonctionnel

- Répondre aux questions sur une analyse de candidat (score, compétences, recommandation)
- Expliquer la justification d'un score
- Lister les compétences requises d'une offre d'emploi
- Comparer deux candidats côte à côte
- Maintenir le contexte de la conversation entre les messages
- S'adapter au contexte d'une analyse spécifique (analysis_id)
- Interface API JSON + interface web Blade

## Hors périmètre

- Analyse de nouveaux CV (déléguée au job `AnalyzeCvJob`)
- Modification des données d'analyse (lecture seule)
- Chat entre utilisateurs RH
- Génération de rapports ou exports
- Support multi-langues
- Notifications ou alertes

## Entrées (message utilisateur + contexte analyse)

| Champ | Type | Requis | Description |
|---|---|---|---|
| `message` | string | Oui | Texte de la question de l'utilisateur |
| `conversation_id` | string (UUID) | Non | ID de conversation existante pour continuer |
| `analysis_id` | integer | Non | ID d'analyse pour centrer le contexte |

Contraintes :
- `message` : min 1 caractère, max 2000 caractères
- `conversation_id` : format UUID v4 si présent
- `analysis_id` : doit correspondre à une analyse existante si présent

## Sorties (réponse IA)

| Champ | Type | Toujours présent | Description |
|---|---|---|---|
| `response` | string | Oui | Texte de la réponse de l'assistant |
| `conversation_id` | string (UUID) | Oui | ID de la conversation (nouvelle ou existante) |

## Structure de conversation (Analyse → Conversation → Messages)

```
Analyse (model Analyse)
  ↑ référence optionnelle
Conversation (model AI SDK)
  ↓ contient
Messages (model AI SDK)
  ├── rôle: "user" (question)
  └── rôle: "assistant" (réponse)
  ↓ horodatés (created_at)
```

- Une `Analyse` représente l'évaluation d'un CV complet
- Une `Conversation` regroupe une série de messages entre un utilisateur et l'agent
- Les `Messages` sont les échanges individuels avec leur rôle et contenu
- La relation entre Analyse et Conversation est implicite via le contexte transmis par l'utilisateur

## Règles de mémoire (context retention)

1. **Persistance automatique** : via le trait `RemembersConversations` de l'AI SDK
2. **Historique complet** : les messages sont stockés en base (tables `agent_conversations` et `agent_conversation_messages`)
3. **Chargement au fil de l'eau** : les messages précédents sont automatiquement chargés lors de l'utilisation de `continue(conversationId)`
4. **Limite de contexte** : seuls les 50 derniers messages sont chargés pour respecter la fenêtre de tokens du LLM
5. **Propriété** : chaque conversation appartient à un utilisateur (relation `user`)

## Injection de contexte IA (analyse candidat)

Lorsqu'un `analysis_id` est fourni, l'agent charge automatiquement l'analyse complète via le tool `getCandidateAnalysis` avant de répondre. Le contexte injecté comprend :

- Compétences extraites du CV
- Années d'expérience estimées
- Niveau d'études
- Langues parlées
- Score de matching (0–100)
- Points forts et lacunes
- Compétences manquantes
- Recommandation RH
- Justification détaillée

## Format des messages (role user/assistant)

```json
{
  "role": "user" | "assistant",
  "content": "texte du message",
  "created_at": "2026-06-19T21:00:00Z"
}
```

- **user** : message envoyé par le recruteur via l'API
- **assistant** : réponse générée par l'agent IA
- Chaque message est horodaté automatiquement par la base

## Règles de sécurité IA (ne pas inventer de données)

1. **Tools obligatoires** : l'agent DOIT utiliser `getCandidateAnalysis()`, `getJobRequirements()`, `compareCandidates()` pour toute information factuelle
2. **Interdiction d'inventer** : le prompt système interdit explicitement de générer des données
3. **Reconnaissance des limites** : si un tool retourne une erreur ou des données vides, l'agent DOIT informer l'utilisateur
4. **Hors périmètre** : l'agent NE DOIT PAS répondre aux questions personnelles sur les candidats (âge, adresse, etc.)
5. **Prompt système stricte** : instructions claires dans la classe `AssistantAgent`

## Cas d'usage concrets

### "Pourquoi ce score ?"

L'utilisateur fournit un `analysis_id`. L'agent appelle `getCandidateAnalysis(id)` et répond :
> "Le score de 45/100 s'explique par les compétences manquantes suivantes : Docker et Kubernetes. Le candidat possède 3 des 6 compétences requises."

### "Quels sont les prérequis pour l'offre ?"

L'utilisateur mentionne une offre. L'agent appelle `getJobRequirements(id)` et répond :
> "L'offre Développeur Laravel requiert : PHP, Laravel, MySQL, Git, et 3 ans d'expérience minimum."

### "Compare les compétences du candidat A et B"

L'agent appelle `compareCandidates(id1, id2)` et répond :
> "Le candidat A (score 78) maîtrise PHP et Laravel mais pas Docker. Le candidat B (score 62) a plus d'expérience mais moins de compétences techniques. Recommandation : convoquer A, attente pour B."

### "Ce candidat correspond-il au profil ?"

L'agent appelle `getCandidateAnalysis(id)` et `getJobRequirements(id)` puis compare :
> "Le candidat correspond à 4 des 6 compétences requises. Il manque Docker et CI/CD. Son expérience de 5 ans dépasse le minimum requis de 3 ans."

## Gestion du contexte long (résumé / limitation tokens)

| Mécanisme | Description |
|---|---|
| Limite de messages | 50 derniers messages chargés maximum |
| Fenêtre de contexte | Gérée par le LLM (sliding window) |
| Fallback | Si le contexte est trop long, l'agent répond avec les messages les plus récents uniquement |
| Pas de résumé automatique | Le résumé n'est pas implémenté (hors périmètre v1) |

## Erreurs possibles

| Erreur | Cause | HTTP | Récupération |
|---|---|---|---|
| `validation_error` | Message manquant ou invalide | 422 | Correction de la requête |
| `unauthorized` | Utilisateur non connecté | 401 | Connexion requise |
| `conversation_not_found` | conversation_id invalide | 404 | Vérifier l'ID ou créer une nouvelle conversation |
| `analysis_not_found` | analysis_id ne correspond à aucune analyse | Erreur tool | Vérifier l'ID de l'analyse |
| `ai_timeout` | L'API IA ne répond pas dans le délai | 503 | Réessayer le message |
| `ai_error` | Erreur générale de l'API IA | 500 | Réessayer plus tard |

## Contraintes techniques Laravel

| Contrainte | Implémentation |
|---|---|
| Auth | Middleware `auth` sur toutes les routes API |
| Modèle | `Conversation` (existant AI SDK) avec relation `user` |
| AI SDK | `AssistantAgent` implémente `Agent`, `Conversational`, `HasTools` |
| Memory | `RemembersConversations` trait + `HasConversations` sur User |
| Tools | Classes implémentant `Tool` dans `app/Ai/Tools/` |
| Queue | Non requis pour le chat (synchrone, latence acceptable) |
| Validation | Form Request ou validation inline dans le contrôleur |
| Routes | `routes/api.php` avec `auth` middleware |
