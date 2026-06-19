<?php

namespace Tests\Feature;

use App\Jobs\AnalyzeCvJob;
use App\Models\Analyse;
use App\Models\Candidat;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AnalyseCvTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_submit_analysis(): void
    {
        $offer = Offer::factory()->create();

        $this->postJson("/api/offers/{$offer->id}/analyses", [
            'candidate_name' => 'Jean Dupont',
            'cv_text' => 'Experienced PHP developer with 5 years in Laravel...',
        ])->assertStatus(401);
    }

    public function test_submit_analysis_requires_valid_data(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->create();

        $this->actingAs($user)
            ->postJson("/api/offers/{$offer->id}/analyses", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['candidate_name', 'cv_text']);
    }

    public function test_submit_analysis_requires_minimum_cv_length(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->create();

        $this->actingAs($user)
            ->postJson("/api/offers/{$offer->id}/analyses", [
                'candidate_name' => 'Jean Dupont',
                'cv_text' => 'Too short',
            ])->assertStatus(422)
            ->assertJsonValidationErrors(['cv_text']);
    }

    public function test_submit_analysis_dispatches_job(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->create();

        $response = $this->actingAs($user)
            ->postJson("/api/offers/{$offer->id}/analyses", [
                'candidate_name' => 'Jean Dupont',
                'cv_text' => fake()->paragraphs(5, true),
            ]);

        $response->assertStatus(202)
            ->assertJsonStructure(['message', 'analysis_id']);

        Queue::assertPushed(AnalyzeCvJob::class);
    }

    public function test_show_analysis_returns_data(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->create();
        $candidat = Candidat::factory()->create();
        $analyse = Analyse::factory()->create([
            'job_offer_id' => $offer->id,
            'candidate_id' => $candidat->id,
        ]);

        $this->actingAs($user)
            ->getJson("/api/analyses/{$analyse->id}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'id', 'job_offer_id', 'candidate_id', 'matching_score', 'recommendation',
            ]);
    }

    public function test_show_nonexistent_analysis_returns_404(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/api/analyses/999')
            ->assertStatus(404);
    }
}
