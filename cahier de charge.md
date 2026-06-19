# TalentMatch — Assistant IA de Présélection RH

---

## 📌 Contexte du projet

Tu travailles comme développeur backend Laravel dans une startup marocaine en pleine croissance.

Le département RH reçoit entre **50 et 200 CVs par offre**.
Le traitement manuel est :

* répétitif
* subjectif
* chronophage

Le directeur technique souhaite un outil interne permettant :

* d’analyser un CV automatiquement
* de comparer un CV à une offre d’emploi
* de générer un score de matching (0-100)
* de produire une recommandation RH

---

## 🎯 Objectif du projet

Développer une application Laravel appelée **TalentMatch** qui permet :

* de créer des offres d’emploi
* de soumettre des CV
* d’analyser automatiquement les CV avec IA
* de générer un score et une recommandation
* d’interagir avec un assistant conversationnel contextuel

---

## 👤 User Stories

### 🔐 Authentification

**US1**

* En tant qu’utilisateur (RH), je veux créer un compte, me connecter et me déconnecter.

---

### 📋 Gestion des offres

**US2**

* Créer une offre d’emploi avec :

  * titre
  * description
  * compétences requises
  * expérience minimale

**US3**

* Voir la liste de mes offres avec nombre de candidats analysés

**US4**

* Voir le détail d’une offre et ses candidats avec leurs scores

---

### 🤖 Analyse des candidats

**US5**

* Soumettre un CV (texte + nom candidat) pour analyse

**US6**

* L’IA doit générer une analyse structurée :

  * compétences extraites
  * années d’expérience
  * niveau d’études
  * langues
  * score (0–100)
  * points forts
  * lacunes
  * compétences manquantes
  * recommandation
  * justification

**US7**

* Voir une analyse détaillée lisible

**US8**

* Afficher une recommandation :

  * convoquer
  * attente
  * rejeter

---

### 💬 Assistant conversationnel

**US9**

* Poser des questions sur un candidat (ex : pourquoi ce score ?)

**US10**

* L’assistant doit se souvenir du contexte de conversation

**US11**

* L’assistant utilise des tools Laravel pour répondre avec des données réelles

---

## ⚙️ Contraintes techniques

### 🧠 IA (2 couches obligatoires)

#### Couche 1 — Structured Output

Le résultat IA doit respecter ce JSON :

```json
{
  "competences_extraites": ["string"],
  "annees_experience": "integer",
  "niveau_etudes": "string",
  "langues": ["string"],
  "matching_score": "integer (0-100)",
  "points_forts": ["string"],
  "lacunes": ["string"],
  "competences_manquantes": ["string"],
  "recommandation": "convoquer | attente | rejeter",
  "justification": "string"
}
```

---

#### Couche 2 — Agent IA avec Tools

L’agent doit utiliser :

* `getCandidateAnalysis(id)`
* `getJobRequirements(id)`
* `compareCandidates(id1, id2)`

et ne jamais inventer de données.

---

## ⚙️ Concepts obligatoires

* Jobs & Queues (analyse IA asynchrone)
* Eloquent Casts (JSON + Enum)
* Tools / Function Calling
* Conversation Memory persistée
* Form Requests Laravel

---

## 🤖 Workflow AI obligatoire

* Laravel Boost (MCP server)
* OpenSpec (spec-driven development)
* AGENTS.md obligatoire
* commits avec mention AI

---

## 📦 Livrables

* Repository GitHub
* Minimum 20 commits
* Jira board
* MCD + MLD
* Dossier `specs/`
* README.md

---

## 🧪 Critères d’évaluation

### Architecture (35%)

* structured output respecté
* tools utilisés correctement
* memory conversationnelle
* jobs + queues

### Fonctionnalités (25%)

* CRUD offres
* analyse IA fonctionnelle
* assistant conversationnel

### Workflow AI (25%)

* OpenSpec utilisé
* Boost MCP utilisé
* AGENTS.md présent

### Qualité (15%)

* code propre
* pas de N+1
* cas limites gérés
