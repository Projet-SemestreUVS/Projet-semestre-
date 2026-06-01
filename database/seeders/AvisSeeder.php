<?php

namespace Database\Seeders;

use App\Models\Avis;
use Illuminate\Database\Seeder;

class AvisSeeder extends Seeder
{
    public function run(): void
    {

        Avis::create([

            'reservation_id' => 1,

            'auteur_id' => 4,

            'cible_id' => 2,

            'note' => 5,

            'commentaire' => 'Excellent service',

            'signale' => false
        ]);
    }
}