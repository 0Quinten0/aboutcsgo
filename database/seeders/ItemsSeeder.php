<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItemsSeeder extends Seeder
{
    public function run()
    {
        $weapons = [
            'Knifes' => ['Shadow Daggers', 'Huntsman Knife', 'Skeleton Knife', 'Talon Knife', 'M9 Bayonet', 'Ursus Knife', 'Nomad Knife', 'Stiletto Knife', 'Flip Knife', 'Butterfly Knife', 'Paracord Knife', 'Gut Knife', 'Survival Knife', 'Classic Knife', 'Bowie Knife', 'Bayonet', 'Karambit', 'Navaja Knife', 'Falchion Knife'],
            'Gloves' => ['Driver Gloves', 'Hand Wraps', 'Hydra Gloves', 'Moto Gloves', 'Specialist Gloves', 'Sport Gloves', 'Broken Fang Gloves', 'Bloodhound Gloves'],
            'Rifle' => ['SG 553', 'AUG', 'FAMAS', 'AK-47', 'Galil AR', 'M4A1-S', 'M4A4'],
            'Sniper Rifle' => ['G3SG1', 'SCAR-20', 'SSG 08', 'AWP'],
            'Pistol' => ['Glock-18', 'P250', 'Five-SeveN', 'Tec-9', 'CZ75-Auto', 'Desert Eagle', 'USP-S', 'R8 Revolver', 'P2000', 'Dual Berettas'],
            'Machinegun' => ['M249', 'Negev'],
            'Shotgun' => ['Nova', 'XM1014', 'MAG-7', 'Sawed-Off'],
            'SMG' => ['MAC-10', 'MP9', 'MP7', 'MP5-SD', 'UMP-45', 'P90', 'PP-Bizon'],
            'Sticker' => ['Team Logo', 'Tournament', 'Player Autograph', 'Regular'],
            'Graffiti' => ['Graffiti'],
            'Container' => ['Container'],
            'Key' => ['Key'],
            'Agent' => ['Agent'],
            'Zeus x27' => ['Zeus x27']
        ];

        foreach ($weapons as $categoryName => $items) {
            $category = DB::table('categories')->where('name', $categoryName)->first();
            $categoryId = $category->id ?? null;

            if ($categoryId) {
                foreach ($items as $itemName) {
                    DB::table('items')->updateOrInsert(
                        ['name' => $itemName, 'category_id' => $categoryId], // Condition to check
                        ['created_at' => now(), 'updated_at' => now()] // Values to update or insert
                    );
                }
            } else {
                Log::warning("Category '{$categoryName}' not found, items not seeded.");
            }
        }
    }
}
