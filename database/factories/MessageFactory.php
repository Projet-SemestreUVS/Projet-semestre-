<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    public function definition(): array
    {

        return [

            'expediteur_id' => User::inRandomOrder()
                ->first()?->id,

            'destinataire_id' => User::inRandomOrder()
                ->first()?->id,

            'contenu' => fake()->sentence(),

            'lu' => fake()->boolean(),
        ];
    }
}