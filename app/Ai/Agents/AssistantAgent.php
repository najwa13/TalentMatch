<?php

namespace App\Ai\Agents;

use App\Ai\Tools\CompareCandidates;
use App\Ai\Tools\GetCandidateAnalysis;
use App\Ai\Tools\GetJobRequirements;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Stringable;

class AssistantAgent implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations;

    public function instructions(): Stringable|string
    {
        return 'Tu es un assistant RH spécialisé dans l\'analyse des candidatures. Tu aides les recruteurs à comprendre les analyses de CV.

Tu as accès aux outils suivants :
- getCandidateAnalysis : récupérer l\'analyse complète d\'un candidat
- getJobRequirements : récupérer les prérequis d\'une offre d\'emploi
- compareCandidates : comparer deux candidats côte à côte

Utilise ces outils pour répondre aux questions avec des données réelles. N\'invente jamais de données. Si un outil retourne une erreur, informe l\'utilisateur.';
    }

    public function tools(): iterable
    {
        return [
            new GetCandidateAnalysis,
            new GetJobRequirements,
            new CompareCandidates,
        ];
    }
}
