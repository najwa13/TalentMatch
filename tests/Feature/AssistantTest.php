<?php

namespace Tests\Feature;

use App\Ai\Agents\AssistantAgent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssistantTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_ask(): void
    {
        $this->postJson('/api/assistant/ask', [
            'message' => 'Bonjour',
        ])->assertStatus(401);
    }

    public function test_ask_requires_message(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/assistant/ask', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }

    public function test_ask_returns_response(): void
    {
        $user = User::factory()->create();

        AssistantAgent::fake([
            'Je suis un assistant RH. Je peux vous aider avec les analyses de CV.',
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/assistant/ask', [
                'message' => 'Bonjour, que peux-tu faire ?',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['response', 'conversation_id']);
    }

    public function test_ask_continues_conversation(): void
    {
        $user = User::factory()->create();

        AssistantAgent::fake([
            'Première réponse',
            'Deuxième réponse',
        ]);

        $first = $this->actingAs($user)
            ->postJson('/api/assistant/ask', ['message' => 'Salut']);

        $conversationId = $first->json('conversation_id');

        $second = $this->actingAs($user)
            ->postJson('/api/assistant/ask', [
                'message' => 'Parle-moi des candidats',
                'conversation_id' => $conversationId,
            ]);

        $second->assertStatus(200)
            ->assertJsonStructure(['response', 'conversation_id']);
    }
}
