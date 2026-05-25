<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call custom seeders to ensure test users and categories exist
        $this->call([
            \Database\Seeders\UsersTableSeeder::class,
            \Database\Seeders\CategoriesTableSeeder::class,
        ]);
    }
}
