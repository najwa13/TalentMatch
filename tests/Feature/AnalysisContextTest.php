<?php

namespace Tests\Feature;

use App\Ai\Agents\AssistantAgent;
use App\Models\Analyse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalysisContextTest extends TestCase
{
    use RefreshDatabase;

    public function test_ask_with_analysis_id_injects_context(): void
    {
        $user = User::factory()->create();
        $analyse = Analyse::factory()->create();

        AssistantAgent::fake(['Réponse avec contexte']);

        $response = $this->actingAs($user)
            ->postJson('/api/assistant/ask', [
                'message' => 'Explique ce score',
                'analysis_id' => $analyse->id,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['response', 'conversation_id']);
    }

    public function test_ask_with_invalid_analysis_id_returns_validation_error(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/assistant/ask', [
                'message' => 'Bonjour',
                'analysis_id' => 999,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['analysis_id']);
    }

    public function test_ask_without_analysis_id_works(): void
    {
        $user = User::factory()->create();

        AssistantAgent::fake(['Réponse sans contexte']);

        $response = $this->actingAs($user)
            ->postJson('/api/assistant/ask', [
                'message' => 'Bonjour',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['response', 'conversation_id']);
    }
}
