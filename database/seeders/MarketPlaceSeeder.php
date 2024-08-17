<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketplaceSeeder extends Seeder
{
    /**
     * Seed the marketplaces table.
     *
     * @return void
     */
    public function run()
    {
        // Insert marketplace records with predefined IDs
        DB::table('marketplaces')->insert([
            [
                'id' => 1,
                'name' => 'bitskins',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'steam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'skinport',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
