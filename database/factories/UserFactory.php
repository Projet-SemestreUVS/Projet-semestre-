<?php

namespace Database\Factories;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {

        return [

            'nom' => fake()->lastName(),

            'prenom' => fake()->firstName(),

            'email' => fake()->unique()->safeEmail(),

            'email_verified_at' => now(),

            'password' => Hash::make('password'),

            'role' => fake()->randomElement([
                'prestataire',
                'demandeur'
            ]),

            'telephone' => fake()->phoneNumber(),

            'photo' => null,

            'localisation' => fake()->city(),
        ];
    }
}