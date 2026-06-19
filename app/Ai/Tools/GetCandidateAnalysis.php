<?php

namespace App\Ai\Tools;

use App\Models\Analyse;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetCandidateAnalysis implements Tool
{
    public function description(): Stringable|string
    {
        return 'Récupérer l\'analyse complète d\'un candidat par son ID d\'analyse. Retourne les compétences, le score, la recommandation et la justification.';
    }

    public function handle(Request $request): Stringable|string
    {
        $analysisId = $request->integer('analysis_id');

        $analyse = Analyse::with('candidat', 'offer')->find($analysisId);

        if (! $analyse) {
            return json_encode(['error' => 'Analyse introuvable.']);
        }

        return json_encode([
            'candidate_name' => $analyse->candidat?->name,
            'job_title' => $analyse->offer?->title,
            'extracted_skills' => $analyse->extracted_skills,
            'years_experience' => $analyse->years_experience,
            'education_level' => $analyse->education_level,
            'languages' => $analyse->languages,
            'matching_score' => $analyse->matching_score,
            'strengths' => $analyse->strengths,
            'weaknesses' => $analyse->weaknesses,
            'missing_skills' => $analyse->missing_skills,
            'recommendation' => $analyse->recommendation?->value,
            'justification' => $analyse->justification,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'analysis_id' => $schema->integer()->description('L\'ID de l\'analyse du candidat')->required(),
        ];
    }
}
