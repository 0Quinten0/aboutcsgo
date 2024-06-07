<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExteriorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exteriors = ['Factory New', 'Minimal Wear', 'Field-Tested', 'Well-Worn', 'Battle-Scarred', 'No Exterior'];
        foreach ($exteriors as $exterior) {
            DB::table('exteriors')->updateOrInsert(
                ['name' => $exterior], // Condition to check
                ['created_at' => now(), 'updated_at' => now()] // Values to update or insert
            );
        }
    }
}
