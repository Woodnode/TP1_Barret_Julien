<?php

namespace Tests\Feature;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    // Route 4 – cas succès : créer un utilisateur 
    public function test_create_user_returns_201(): void
    {
        $payload = [
            'login'      => 'johndoe',
            'password'   => 'password123',
            'email'      => 'john@example.com',
            'last_name'  => 'Doe',
            'first_name' => 'John',
        ];

        $response = $this->postJson('/api/users', $payload);

        $response->assertStatus(Controller::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'login',
                    'email',
                    'last_name',
                    'first_name',
                ],
            ])
            ->assertJsonFragment([
                'login' => 'johndoe',
                'email' => 'john@example.com',
                'last_name' => 'Doe',
                'first_name' => 'John',
            ]);

        $this->assertDatabaseHas('users', [
            'login' => 'johndoe',
            'email' => 'john@example.com',
        ]);
    }

    // Route 4 – cas erreur : validation 422 (champ manquant) 
    public function test_create_user_returns_422_when_required_field_missing(): void
    {
        $payload = [
            'password'   => 'password123',
            'email'      => 'john@example.com',
            'last_name'  => 'Doe',
            'first_name' => 'John',
        ];

        $response = $this->postJson('/api/users', $payload);

        $response->assertStatus(Controller::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['login']);
    }

    // Route 4 – cas erreur : validation 422 (email invalide)
    public function test_create_user_returns_422_when_email_invalid(): void
    {
        $payload = [
            'login'      => 'johndoe',
            'password'   => 'password123',
            'email'      => 'not-an-email',
            'last_name'  => 'Doe',
            'first_name' => 'John',
        ];

        $response = $this->postJson('/api/users', $payload);

        $response->assertStatus(Controller::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email']);
    }

    // Route 4 – cas erreur : validation 422 (email déjà existant)
    public function test_create_user_returns_422_when_email_already_exists(): void
    {
        $this->seed();
        $existingUser = User::first();

        $payload = [
            'login'      => 'newuser',
            'password'   => 'password123',
            'email'      => $existingUser->email,
            'last_name'  => 'Doe',
            'first_name' => 'John',
        ];

        $response = $this->postJson('/api/users', $payload);

        $response->assertStatus(Controller::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email']);
    }

    // Route 4 – cas erreur : validation 422 (login déjà existant)
    public function test_create_user_returns_422_when_login_already_exists(): void
    {
        $this->seed();
        $existingUser = User::first();

        $payload = [
            'login'      => $existingUser->login,
            'password'   => 'password123',
            'email'      => 'newemail@example.com',
            'last_name'  => 'Doe',
            'first_name' => 'John',
        ];

        $response = $this->postJson('/api/users', $payload);

        $response->assertStatus(Controller::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['login']);
    }

    // Route 4 – cas erreur : validation 422 (password trop court)
    public function test_create_user_returns_422_when_password_too_short(): void
    {
        $payload = [
            'login'      => 'johndoe',
            'password'   => 'short',
            'email'      => 'john@example.com',
            'last_name'  => 'Doe',
            'first_name' => 'John',
        ];

        $response = $this->postJson('/api/users', $payload);

        $response->assertStatus(Controller::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['password']);
    }

    // Route 5 – cas succès : mise à jour complète 
    public function test_update_user_returns_200(): void
    {
        $this->seed();
        $user = User::first();

        $payload = [
            'login'      => 'updatedlogin',
            'password'   => 'newpassword123',
            'email'      => 'updated@example.com',
            'last_name'  => 'Updated',
            'first_name' => 'User',
        ];

        $response = $this->putJson("/api/users/{$user->id}", $payload);

        $response->assertStatus(Controller::HTTP_OK)
            ->assertJsonFragment([
                'login' => 'updatedlogin',
                'email' => 'updated@example.com',
            ]);

        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'login' => 'updatedlogin',
        ]);
    }

    // Route 5 – cas erreur : utilisateur inexistant 
    public function test_update_unknown_user_returns_404(): void
    {
        $payload = [
            'login'      => 'test',
            'password'   => 'password123',
            'email'      => 'test@example.com',
            'last_name'  => 'Test',
            'first_name' => 'User',
        ];

        $response = $this->putJson('/api/users/999999', $payload);

        $response->assertStatus(Controller::HTTP_NOT_FOUND);
    }

    // Route 5 – cas erreur : validation 422 
    public function test_update_user_returns_422_when_data_invalid(): void
    {
        $this->seed();
        $user = User::first();

        $payload = [
            'login'      => '',                
            'password'   => 'short',          
            'email'      => 'not-an-email',   
            'last_name'  => 'Test',
            'first_name' => 'User',
        ];

        $response = $this->putJson("/api/users/{$user->id}", $payload);

        $response->assertStatus(Controller::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['login', 'password', 'email']);
    }

    // Route 5 – cas erreur : validation 422 (email déjà existant pour un autre utilisateur)
    public function test_update_user_returns_422_when_email_already_exists(): void
    {
        $this->seed();
        $users = User::take(2)->get();
        $user1 = $users[0];
        $user2 = $users[1];

        $payload = [
            'login'      => 'updatedlogin',
            'password'   => 'newpassword123',
            'email'      => $user2->email,
            'last_name'  => 'Updated',
            'first_name' => 'User',
        ];

        $response = $this->putJson("/api/users/{$user1->id}", $payload);

        $response->assertStatus(Controller::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email']);
    }

    // Route 5 – cas erreur : validation 422 (login déjà existant pour un autre utilisateur)
    public function test_update_user_returns_422_when_login_already_exists(): void
    {
        $this->seed();
        $users = User::take(2)->get();
        $user1 = $users[0];
        $user2 = $users[1];

        $payload = [
            'login'      => $user2->login,
            'password'   => 'newpassword123',
            'email'      => 'updated@example.com',
            'last_name'  => 'Updated',
            'first_name' => 'User',
        ];

        $response = $this->putJson("/api/users/{$user1->id}", $payload);

        $response->assertStatus(Controller::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['login']);
    }

    // Route 5 – cas erreur : validation 422 (champ manquant)
    public function test_update_user_returns_422_when_required_field_missing(): void
    {
        $this->seed();
        $user = User::first();

        $payload = [
            'password'   => 'newpassword123',
            'email'      => 'updated@example.com',
            'last_name'  => 'Updated',
            'first_name' => 'User',
        ];

        $response = $this->putJson("/api/users/{$user->id}", $payload);

        $response->assertStatus(Controller::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['login']);
    }
}
