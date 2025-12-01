<?php

namespace Tests\Feature;

use App\Models\Critic;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CriticTest extends TestCase
{
    use RefreshDatabase;

    // Route 6 – cas succès : supprimer une critique 
    public function test_delete_critic_returns_204(): void
    {
        $this->seed();
        $critic = Critic::first();

        $response = $this->deleteJson("/api/critics/{$critic->id}");

        $response->assertStatus(Controller::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('critics', [
            'id' => $critic->id,
        ]);
    }

    // Route 6 – cas erreur : critique inexistante 
    public function test_delete_unknown_critic_returns_404(): void
    {
        $response = $this->deleteJson('/api/critics/9999');

        $response->assertStatus(Controller::HTTP_NOT_FOUND);
    }
}
