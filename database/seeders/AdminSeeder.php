<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {

        User::create([

            'nom' => 'Admin',

            'prenom' => 'KayJob',

            'email' => 'admin@kayjob.com',

            'password' => Hash::make('admin123'),

            'role' => 'admin',

            'telephone' => '770000000',

            'localisation' => 'Dakar',

            'email_verified_at' => now(),
        ]);
    }
}