<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    public function definition(): array
    {

        return [

            'user_id' => User::inRandomOrder()
                ->first()?->id,

            'type' => fake()->randomElement([
                'reservation',
                'message',
                'avis'
            ]),

            'contenu' => fake()->sentence(),

            'lu' => false,
        ];
    }
}