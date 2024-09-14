<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['Knives', 'Gloves', 'Rifle', 'Sniper Rifle', 'Pistol', 'Machinegun', 'Shotgun', 'SMG', 'Sticker', 'Graffiti', 'Container', 'Key', 'Agent', 'Zeus x27'];
        foreach ($types as $type) {
            DB::table('categories')->updateOrInsert(
                ['name' => $type], // Condition to check
                ['created_at' => now(), 'updated_at' => now()] // Values to update or insert
            );
        }
    }
}
