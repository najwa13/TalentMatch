<?php

namespace Tests\Feature;

use App\Ai\Agents\CvAnalysisAgent;
use App\Enums\AnalysisStatus;
use App\Jobs\AnalyzeCvJob;
use App\Models\Analyse;
use App\Models\Candidat;
use App\Models\Offer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AnalyseCvJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_updates_analysis_on_success(): void
    {
        $offer = Offer::factory()->create();
        $candidat = Candidat::factory()->create();
        $analyse = Analyse::factory()->create([
            'job_offer_id' => $offer->id,
            'candidate_id' => $candidat->id,
            'status' => AnalysisStatus::Pending,
        ]);

        CvAnalysisAgent::fake();

        (new AnalyzeCvJob($analyse))->handle();

        $analyse->refresh();

        $this->assertSame(AnalysisStatus::Completed, $analyse->status);
        $this->assertIsInt($analyse->matching_score);
        $this->assertNotNull($analyse->justification);
    }

    public function test_job_handles_failure_gracefully(): void
    {
        $offer = Offer::factory()->create();
        $candidat = Candidat::factory()->create();
        $analyse = Analyse::factory()->create([
            'job_offer_id' => $offer->id,
            'candidate_id' => $candidat->id,
            'status' => AnalysisStatus::Pending,
        ]);

        CvAnalysisAgent::fake(function () {
            throw new \Exception('AI API Error');
        });

        try {
            (new AnalyzeCvJob($analyse))->handle();
        } catch (\Exception) {
        }

        $analyse->refresh();

        $this->assertSame(AnalysisStatus::Failed, $analyse->status);
    }

    public function test_job_is_queued(): void
    {
        Queue::fake();

        $offer = Offer::factory()->create();
        $candidat = Candidat::factory()->create();
        $analyse = Analyse::factory()->create([
            'job_offer_id' => $offer->id,
            'candidate_id' => $candidat->id,
        ]);

        AnalyzeCvJob::dispatch($analyse);

        Queue::assertPushed(AnalyzeCvJob::class);
    }
}
