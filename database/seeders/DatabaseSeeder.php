<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // Register the CategoriesSeeder
        $this->call(CategoriesSeeder::class);
        $this->call(ExteriorsSeeder::class);
        $this->call(ItemsSeeder::class);
        $this->call(QualitiesSeeder::class);
        $this->call(TypesSeeder::class);

    }
}
