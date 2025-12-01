<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FilmTest extends TestCase
{
    
    use RefreshDatabase;

    // Route 1 – cas succès : tous les films 
    public function test_get_all_films_returns_paginated_list(): void
    {
        $this->seed(); 

        $response = $this->getJson('/api/films');

        $films_array = $response->decodeResponseJson();

        $response->assertStatus(Controller::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'release_year',
                        'length',
                        'description',
                        'rating',
                        'language_id',
                        'special_features',
                        'image',
                        'created_at',
                    ],
                ],
                'links',
                'meta',
            ]);

        $this->assertEquals(20, count($films_array['data']));
    }

    // Route 1 – cas sans données : retourne une liste vide 
    public function test_get_all_films_returns_empty_when_no_films(): void
    {
        $response = $this->getJson('/api/films');

        $response->assertStatus(Controller::HTTP_OK)
            ->assertJsonCount(0, 'data');
    }


    // Route 2 – cas succès : acteurs d’un film 
    public function test_get_actors_of_film_returns_200(): void
    {
        $this->seed();
        $film = Film::first(); 

        $response = $this->getJson("/api/films/{$film->id}/actors");

        $response->assertStatus(Controller::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'last_name',
                        'first_name',
                        'birthdate',
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    // Route 2 – cas erreur : film inexistant 
    public function test_get_actors_of_unknown_film_returns_404(): void
    {
        $response = $this->getJson('/api/films/999999/actors');

        $response->assertStatus(Controller::HTTP_NOT_FOUND);
    }


    // Route 3 – cas succès : film avec critiques 
    public function test_get_film_with_critics_returns_200(): void
    {
        $this->seed();
        $film = Film::first();

        $response = $this->getJson("/api/films/{$film->id}");

        $response->assertStatus(Controller::HTTP_OK)
            ->assertJsonStructure([
                'film' => [
                        'id',
                        'title',
                        'release_year',
                        'length',
                        'description',
                        'rating',
                        'language_id',
                        'special_features',
                        'image',
                        'created_at',
                ],
                'critics' => [
                    'data' => [
                        '*' => [
                            'id',
                            'user_id',
                            'film_id',
                            'score',
                            'comment',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                    'links',
                    'meta',
                ],
            ]);
    }
    
    
    // Route 3 – cas erreur : film inexistant 
    public function test_get_unknown_film_returns_404(): void
    {
        $response = $this->getJson('/api/films/9999');

        $response->assertStatus(Controller::HTTP_NOT_FOUND);
    }

}
