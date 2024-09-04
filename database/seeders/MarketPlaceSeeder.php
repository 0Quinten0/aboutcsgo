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
                'pretty_name' => 'BitSkins',
                'image_url' => 'https://iili.io/dklggef.png',
            ],
            [
                'id' => 2,
                'name' => 'steam',
                'pretty_name' => 'Steam Market',
                'image_url' => 'https://iili.io/dklgU5G.png',
            ],
            [
                'id' => 3,
                'name' => 'skinport',
                'pretty_name' => 'Skinport',
                'image_url' => 'https://iili.io/dklgSJs.png',
            ],
            [
                'id' => 4,
                'name' => 'market_csgo',
                'pretty_name' => 'Market CSGO',
                'image_url' => 'https://iili.io/dklgvgn.png',
            ],
            [
                'id' => 5,
                'name' => 'waxpeer',
                'pretty_name' => 'WAXPEER',
                'image_url' => 'https://iili.io/dklgk0X.png',
            ],
            [
                'id' => 6,
                'name' => 'skinwallet',
                'pretty_name' => 'SkinWallet',
                'image_url' => 'https://iili.io/dklgeft.png',
            ],
            [
                'id' => 7,
                'name' => 'shadowpay',
                'pretty_name' => 'Shadowpay',
                'image_url' => 'https://iili.io/dklgNsI.png',
            ],
            [
                'id' => 8,
                'name' => 'skinbaron',
                'pretty_name' => 'SkinBaron',
                'image_url' => 'https://iili.io/dklgXbR.png',
            ],
            [
                'id' => 9,
                'name' => 'csfloat',
                'pretty_name' => 'CSFloat',
                'image_url' => 'https://iili.io/dklgWOv.png',
            ],
            [
                'id' => 10,
                'name' => 'gamerpay',
                'pretty_name' => 'Gamerpay',
                'image_url' => 'https://iili.io/dklgjxp.png',
            ],
            [
                'id' => 11,
                'name' => 'dmarket',
                'pretty_name' => 'DMarket',
                'image_url' => 'https://iili.io/dmarket.dklgwWN.png',
            ],
        ];



    
        foreach ($marketplaces as $marketplace) {
            DB::table('marketplaces')->updateOrInsert(
                ['id' => $marketplace['id']], // The attributes to check for
                [
                    'name' => $marketplace['name'],
                    'pretty_name' => $marketplace['pretty_name'],
                    'image_url' => $marketplace['image_url'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
