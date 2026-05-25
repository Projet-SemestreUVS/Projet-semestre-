<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    public function definition(): array
    {

        return [

            'service_id' => Service::inRandomOrder()->first()?->id,

            'demandeur_id' => User::where(
                'role',
                'demandeur'
            )->inRandomOrder()->first()?->id,

            'prestataire_id' => User::where(
                'role',
                'prestataire'
            )->inRandomOrder()->first()?->id,

            'date_debut' => fake()->dateTimeBetween(
                'now',
                '+1 month'
            ),

            'statut' => fake()->randomElement([
                'en_attente',
                'accepte',
                'refuse',
                'termine'
            ]),

            'commentaire' => fake()->sentence(),
        ];
    }
}