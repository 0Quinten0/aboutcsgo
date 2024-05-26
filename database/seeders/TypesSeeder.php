<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Normal', 'Souvenir', 'StatTrak™', '★', '★ StatTrak™'];
        foreach ($categories as $category) {
            DB::table('types')->updateOrInsert(
                ['name' => $category], // Condition to check
                ['created_at' => now(), 'updated_at' => now()] // Values to update or insert
            );
        }
    }
}
