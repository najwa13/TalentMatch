<?php

namespace App\Jobs;

use App\Ai\Agents\CvAnalysisAgent;
use App\Enums\AnalysisStatus;
use App\Enums\Recommendation;
use App\Models\Analyse;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class AnalyzeCvJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public Analyse $analyse,
    ) {}

    public function handle(): void
    {
        $this->analyse->update(['status' => AnalysisStatus::Processing]);

        $analyse = $this->analyse->load('offer', 'candidat');

        try {
            $agent = new CvAnalysisAgent(
                offer: $analyse->offer,
                candidateName: $analyse->candidat->name,
                cvText: $analyse->candidat->cv_text,
            );

            $response = $agent->prompt(
                'Analyse ce CV par rapport à l\'offre d\'emploi.'
            );

            $result = $response->toArray();

            $recommendation = match ($result['recommandation']) {
                'convoquer' => Recommendation::Convocation,
                'attente' => Recommendation::Attente,
                'rejeter' => Recommendation::Rejet,
                default => Recommendation::Attente,
            };

            $this->analyse->update([
                'extracted_skills' => $result['competences_extraites'],
                'years_experience' => $result['annees_experience'],
                'education_level' => $result['niveau_etudes'],
                'languages' => $result['langues'],
                'matching_score' => $result['matching_score'],
                'strengths' => $result['points_forts'],
                'weaknesses' => $result['lacunes'],
                'missing_skills' => $result['competences_manquantes'],
                'recommendation' => $recommendation,
                'justification' => $result['justification'],
                'status' => AnalysisStatus::Completed,
            ]);
        } catch (\Throwable $e) {
            Log::error('Analyse CV échouée', [
                'analysis_id' => $this->analyse->id,
                'error' => $e->getMessage(),
            ]);

            $this->analyse->update(['status' => AnalysisStatus::Failed]);

            throw $e;
        }
    }
}
