<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class AvisFactory extends Factory
{
    public function definition(): array
    {

        return [

            'reservation_id' => Reservation::inRandomOrder()
                ->first()?->id,

            'auteur_id' => User::inRandomOrder()
                ->first()?->id,

            'cible_id' => User::inRandomOrder()
                ->first()?->id,

            'note' => fake()->numberBetween(1, 5),

            'commentaire' => fake()->paragraph(),

            'signale' => false,
        ];
    }
}