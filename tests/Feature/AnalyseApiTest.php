<?php

namespace Tests\Feature;

use App\Models\Analyse;
use App\Models\Candidat;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyseApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_api(): void
    {
        $this->getJson('/api/analyses/1')->assertStatus(401);
    }

    public function test_show_analysis_loads_relationships(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->create();
        $candidat = Candidat::factory()->create();
        $analyse = Analyse::factory()->create([
            'job_offer_id' => $offer->id,
            'candidate_id' => $candidat->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson("/api/analyses/{$analyse->id}");

        $response->assertStatus(200);
        $this->assertArrayHasKey('candidat', $response->json());
        $this->assertArrayHasKey('offer', $response->json());
    }

    public function test_analysis_has_correct_score_range(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->create();
        $candidat = Candidat::factory()->create();
        $analyse = Analyse::factory()->create([
            'job_offer_id' => $offer->id,
            'candidate_id' => $candidat->id,
            'matching_score' => 85,
        ]);

        $response = $this->actingAs($user)
            ->getJson("/api/analyses/{$analyse->id}");

        $score = $response->json('matching_score');
        $this->assertGreaterThanOrEqual(0, $score);
        $this->assertLessThanOrEqual(100, $score);
    }
}
