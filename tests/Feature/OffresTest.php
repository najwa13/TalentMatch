<?php

namespace Tests\Feature;

use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OffresTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $this->get('/offres')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_offres_index(): void
    {
        $user = User::factory()->create();
        $offre = Offer::factory()->for($user)->create();

        $this->actingAs($user)
            ->get('/offres')
            ->assertStatus(200)
            ->assertSee($offre->title);
    }

    public function test_user_only_sees_own_offres(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $offre = Offer::factory()->for($user)->create();
        $otherOffre = Offer::factory()->for($otherUser)->create();

        $this->actingAs($user)
            ->get('/offres')
            ->assertSee($offre->title)
            ->assertDontSee($otherOffre->title);
    }

    public function test_user_can_view_create_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/offres/create')
            ->assertStatus(200);
    }

    public function test_user_can_create_offre(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/offres', [
                'title' => 'Développeur Laravel',
                'description' => 'Une description',
                'required_skills' => ['PHP', 'Laravel'],
                'minimum_experience' => 3,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('offers', [
            'title' => 'Développeur Laravel',
            'user_id' => $user->id,
        ]);
    }

    public function test_create_offre_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/offres', [])
            ->assertSessionHasErrors(['title', 'description', 'required_skills', 'minimum_experience']);
    }

    public function test_user_can_view_offre(): void
    {
        $user = User::factory()->create();
        $offre = Offer::factory()->for($user)->create();

        $this->actingAs($user)
            ->get("/offres/{$offre->id}")
            ->assertStatus(200)
            ->assertSee($offre->title);
    }

    public function test_user_cannot_view_other_users_offre(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $offre = Offer::factory()->for($otherUser)->create();

        $this->actingAs($user)
            ->get("/offres/{$offre->id}")
            ->assertStatus(403);
    }

    public function test_view_nonexistent_offre_returns_404(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/offres/999')
            ->assertStatus(404);
    }

    public function test_user_can_view_edit_form(): void
    {
        $user = User::factory()->create();
        $offre = Offer::factory()->for($user)->create();

        $this->actingAs($user)
            ->get("/offres/{$offre->id}/edit")
            ->assertStatus(200)
            ->assertSee($offre->title);
    }

    public function test_user_can_update_offre(): void
    {
        $user = User::factory()->create();
        $offre = Offer::factory()->for($user)->create();

        $this->actingAs($user)
            ->put("/offres/{$offre->id}", [
                'title' => 'Titre modifié',
                'description' => 'Description modifiée',
                'required_skills' => ['PHP', 'Laravel', 'Vue.js'],
                'minimum_experience' => 5,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('offers', [
            'id' => $offre->id,
            'title' => 'Titre modifié',
        ]);
    }

    public function test_user_cannot_update_other_users_offre(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $offre = Offer::factory()->for($otherUser)->create();

        $this->actingAs($user)
            ->put("/offres/{$offre->id}", [
                'title' => 'Titre modifié',
                'description' => 'Description',
                'required_skills' => ['PHP'],
                'minimum_experience' => 3,
            ])
            ->assertStatus(403);
    }

    public function test_user_can_delete_offre(): void
    {
        $user = User::factory()->create();
        $offre = Offer::factory()->for($user)->create();

        $this->actingAs($user)
            ->delete("/offres/{$offre->id}")
            ->assertRedirect('/offres');

        $this->assertDatabaseMissing('offers', ['id' => $offre->id]);
    }

    public function test_user_cannot_delete_other_users_offre(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $offre = Offer::factory()->for($otherUser)->create();

        $this->actingAs($user)
            ->delete("/offres/{$offre->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('offers', ['id' => $offre->id]);
    }
}
