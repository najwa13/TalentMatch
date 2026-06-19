<?php

namespace App\Ai\Tools;

use App\Models\Analyse;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class CompareCandidates implements Tool
{
    public function description(): Stringable|string
    {
        return 'Comparer deux candidats par leurs IDs d\'analyse. Retourne les scores, forces et faiblesses de chacun pour une comparaison côte à côte.';
    }

    public function handle(Request $request): Stringable|string
    {
        $analysisId1 = $request->input('analysis_id_1');
        $analysisId2 = $request->input('analysis_id_2');

        $analyse1 = Analyse::with('candidat')->find($analysisId1);
        $analyse2 = Analyse::with('candidat')->find($analysisId2);

        if (! $analyse1 || ! $analyse2) {
            return json_encode(['error' => 'Une ou les deux analyses sont introuvables.']);
        }

        return json_encode([
            'candidate_1' => [
                'name' => $analyse1->candidat?->name,
                'matching_score' => $analyse1->matching_score,
                'years_experience' => $analyse1->years_experience,
                'strengths' => $analyse1->strengths,
                'weaknesses' => $analyse1->weaknesses,
                'recommendation' => $analyse1->recommendation?->value,
            ],
            'candidate_2' => [
                'name' => $analyse2->candidat?->name,
                'matching_score' => $analyse2->matching_score,
                'years_experience' => $analyse2->years_experience,
                'strengths' => $analyse2->strengths,
                'weaknesses' => $analyse2->weaknesses,
                'recommendation' => $analyse2->recommendation?->value,
            ],
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'analysis_id_1' => $schema->integer()->description('L\'ID de la première analyse')->required(),
            'analysis_id_2' => $schema->integer()->description('L\'ID de la deuxième analyse')->required(),
        ];
    }
}
