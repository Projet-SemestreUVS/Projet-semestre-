<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    public function definition(): array
    {

        return [

            'user_id' => User::inRandomOrder()->first()?->id,

            'category_id' => Category::inRandomOrder()->first()?->id,

            'titre' => fake()->sentence(3),

            'description' => fake()->paragraph(),

            'tarif' => fake()->numberBetween(5000, 150000),

            'photos' => json_encode([]),

            'disponibilite' => true,

            'statut' => 'actif',
        ];
    }
}