<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {

        /*
        |--------------------------------------------------------------------------
        | PRESTATAIRES
        |--------------------------------------------------------------------------
        */

        User::create([

            'nom' => 'Diop',

            'prenom' => 'Moussa',

            'email' => 'prestataire1@gmail.com',

            'password' => Hash::make('password'),

            'role' => 'prestataire',

            'telephone' => '771111111',

            'localisation' => 'Dakar',

            'email_verified_at' => now(),
        ]);

        User::create([

            'nom' => 'Fall',

            'prenom' => 'Awa',

            'email' => 'prestataire2@gmail.com',

            'password' => Hash::make('password'),

            'role' => 'prestataire',

            'telephone' => '772222222',

            'localisation' => 'Thiès',

            'email_verified_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | DEMANDEURS
        |--------------------------------------------------------------------------
        */

        User::create([

            'nom' => 'Ndiaye',

            'prenom' => 'Fatou',

            'email' => 'demandeur1@gmail.com',

            'password' => Hash::make('password'),

            'role' => 'demandeur',

            'telephone' => '773333333',

            'localisation' => 'Saint-Louis',

            'email_verified_at' => now(),
        ]);
    }
}