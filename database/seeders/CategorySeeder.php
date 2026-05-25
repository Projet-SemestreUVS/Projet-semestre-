<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {

        $categories = [

            [
                'nom' => 'Plomberie',
                'description' => 'Services de plomberie',
            ],

            [
                'nom' => 'Électricité',
                'description' => 'Services électriques',
            ],

            [
                'nom' => 'Développement Web',
                'description' => 'Création de sites web',
            ],

            [
                'nom' => 'Coiffure',
                'description' => 'Services de beauté',
            ],

            [
                'nom' => 'Transport',
                'description' => 'Livraison et déménagement',
            ],

        ];

        foreach ($categories as $category) {

            Category::create($category);
        }
    }
}