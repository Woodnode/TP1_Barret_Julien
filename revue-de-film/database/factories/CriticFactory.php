<?php

namespace Database\Factories;

use App\Models\Critic;
use App\Models\Film;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Critic>
 */
class CriticFactory extends Factory
{
    protected $model = Critic::class;

    public function definition(): array
    {
        return [
            //'user_id' => User::factory(),
            
            // https://laravel.com/docs/master/queries (Ordering, Grouping, Limiting, and Offset)
            'film_id' => Film::inRandomOrder()->first()->id ?? Film::factory(),
            'score' => $this->faker->randomFloat(1, 0, 10),
            'comment' => $this->faker->paragraph(),
        ];
    }
}
