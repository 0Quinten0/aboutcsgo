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
        $marketplaces = [
            [
                'id' => 1,
                'name' => 'bitskins',
            ],
            [
                'id' => 2,
                'name' => 'steam',
            ],
            [
                'id' => 3,
                'name' => 'skinport',
            ],
            [
                'id' => 4,
                'name' => 'market_csgo',
            ],
            [
                'id' => 5,
                'name' => 'waxpeer',
            ],
            [
                'id' => 6,
                'name' => 'skinwallet',
            ],
            [
                'id' => 7,
                'name' => 'shadowpay',
            ],
            [
                'id' => 8,
                'name' => 'skinbaron',
            ],
            [
                'id' => 9,
                'name' => 'csfloat',
            ],
            [
                'id' => 10,
                'name' => 'gamerpay',
            ],
            [
                'id' => 11,
                'name' => 'dmarket',
            ],
        ];
    
        foreach ($marketplaces as $marketplace) {
            DB::table('marketplaces')->updateOrInsert(
                ['id' => $marketplace['id']], // The attributes to check for
                [
                    'name' => $marketplace['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
    
}
