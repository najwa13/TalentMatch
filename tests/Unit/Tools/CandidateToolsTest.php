<?php

namespace Tests\Unit\Tools;

use App\Ai\Tools\CompareCandidates;
use App\Ai\Tools\GetCandidateAnalysis;
use App\Ai\Tools\GetJobRequirements;
use App\Models\Analyse;
use App\Models\Offer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Tools\Request;
use Tests\TestCase;

class CandidateToolsTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_candidate_analysis_returns_analysis_data(): void
    {
        $analyse = Analyse::factory()->create();

        $tool = new GetCandidateAnalysis;
        $result = $tool->handle(new Request(['analysis_id' => $analyse->id]));

        $data = json_decode($result, true);

        $this->assertSame($analyse->candidat->name, $data['candidate_name']);
        $this->assertSame($analyse->matching_score, $data['matching_score']);
        $this->assertSame($analyse->recommendation?->value, $data['recommendation']);
    }

    public function test_get_candidate_analysis_returns_error_for_missing_analysis(): void
    {
        $tool = new GetCandidateAnalysis;
        $result = $tool->handle(new Request(['analysis_id' => 999]));

        $data = json_decode($result, true);

        $this->assertArrayHasKey('error', $data);
    }

    public function test_get_job_requirements_returns_offer_data(): void
    {
        $offer = Offer::factory()->create();

        $tool = new GetJobRequirements;
        $result = $tool->handle(new Request(['offer_id' => $offer->id]));

        $data = json_decode($result, true);

        $this->assertSame($offer->title, $data['title']);
        $this->assertSame($offer->required_skills, $data['required_skills']);
        $this->assertSame($offer->minimum_experience, $data['minimum_experience']);
    }

    public function test_get_job_requirements_returns_error_for_missing_offer(): void
    {
        $tool = new GetJobRequirements;
        $result = $tool->handle(new Request(['offer_id' => 999]));

        $data = json_decode($result, true);

        $this->assertArrayHasKey('error', $data);
    }

    public function test_compare_candidates_returns_both_analyses(): void
    {
        $analyse1 = Analyse::factory()->create();
        $analyse2 = Analyse::factory()->create();

        $tool = new CompareCandidates;
        $result = $tool->handle(new Request([
            'analysis_id_1' => $analyse1->id,
            'analysis_id_2' => $analyse2->id,
        ]));

        $data = json_decode($result, true);

        $this->assertSame($analyse1->candidat->name, $data['candidate_1']['name']);
        $this->assertSame($analyse1->matching_score, $data['candidate_1']['matching_score']);
        $this->assertSame($analyse2->candidat->name, $data['candidate_2']['name']);
        $this->assertSame($analyse2->matching_score, $data['candidate_2']['matching_score']);
    }

    public function test_compare_candidates_returns_error_when_one_analysis_missing(): void
    {
        $analyse = Analyse::factory()->create();

        $tool = new CompareCandidates;
        $result = $tool->handle(new Request([
            'analysis_id_1' => $analyse->id,
            'analysis_id_2' => 999,
        ]));

        $data = json_decode($result, true);

        $this->assertArrayHasKey('error', $data);
    }
}
