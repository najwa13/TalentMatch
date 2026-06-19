<?php

namespace Tests\Feature;

use App\Ai\Agents\AssistantAgent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationMemoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_conversation_creates_and_returns_conversation_id(): void
    {
        $user = User::factory()->create();

        AssistantAgent::fake(['Premier message']);

        $response = $this->actingAs($user)
            ->postJson('/api/assistant/ask', [
                'message' => 'Bonjour',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['response', 'conversation_id']);

        $this->assertNotNull($response->json('conversation_id'));
    }

    public function test_continuing_conversation_loads_context(): void
    {
        $user = User::factory()->create();

        AssistantAgent::fake(['Première réponse', 'Deuxième réponse']);

        $first = $this->actingAs($user)
            ->postJson('/api/assistant/ask', ['message' => 'Salut']);

        $conversationId = $first->json('conversation_id');

        $second = $this->actingAs($user)
            ->postJson('/api/assistant/ask', [
                'message' => 'Parle-moi des candidats',
                'conversation_id' => $conversationId,
            ]);

        $second->assertStatus(200)
            ->assertJsonStructure(['response', 'conversation_id'])
            ->assertJson(['conversation_id' => $conversationId]);
    }

    public function test_continuing_non_existent_conversation_returns_404(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/assistant/ask', [
                'message' => 'Bonjour',
                'conversation_id' => '00000000-0000-0000-0000-000000000000',
            ]);

        $response->assertStatus(404);
    }
}
