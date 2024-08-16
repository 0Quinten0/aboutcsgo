<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Skin;
use App\Models\Item;
use App\Models\Quality;
use App\Models\ItemSkin;
use App\Models\Collection;
use App\Models\Crate;
use App\Models\Package;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateItemList extends Command
{
    protected $signature = 'update:skinweapon-list';

    protected $description = 'Update item list from the API';

    public function handle()
    {
        $response = Http::timeout(500)->get('https://bymykel.github.io/CSGO-API/api/en/skins.json');
        $items = $response->json();

        foreach ($items as $item) {
            // Check if 'pattern' and 'pattern']['name' exist, handle vanilla knives
            if (isset($item['pattern']) && isset($item['pattern']['name'])) {
                $skinName = $item['pattern']['name'];
            } else {
                if ($item['category']['name'] === 'Knives') {
                    $skinName = 'Vanilla'; // Use the default Vanilla skin
                    // Log::info('Vanilla knife detected, using default Vanilla skin.', [
                    //     'item' => $item['name'],
                    // ]);
                } else {
                    // Log::warning('Item skipped due to missing pattern name.', [
                    //     'item' => $item,
                    // ]);
                    continue;
                }
            }

            // Handle skin
            $skin = Skin::firstOrCreate(['name' => $skinName]);

            // Handle weapon
            $weapon = Item::where('name', $item['weapon']['name'])->first();
            if (!$weapon) {
                // Log missing weapon
                // Log::error('Weapon not found.', ['weapon_name' => $item['weapon']['name']]);
                continue;
            }

            // Handle quality
            $quality = Quality::where('name', $item['rarity']['name'])->first();

            // Handle image
            $imageUrl = $item['image'];
            $description = $item['description'] ?? 'Default description';

            // Handle stattrak and souvenir
            $stattrak = $item['stattrak'] ?? false;
            $souvenir = $item['souvenir'] ?? false;

            if (!$skin || !$quality) {
                // Log::error('One of the entities is null.', [
                //     'weapon' => $weapon,
                //     'skin' => $skin,
                //     'quality' => $quality,
                // ]);
                continue;
            }


            // Create or update ItemSkin
            $itemSkin = ItemSkin::updateOrCreate(
                [
                    'item_id' => $weapon->id,
                    'skin_id' => $skin->id,
                    'quality_id' => $quality->id,
                ],
                [
                    'description' => $description,
                    'stattrak' => $stattrak,
                    'souvenir' => $souvenir,
                    'image_url' => $imageUrl,
                ]
            );

            // Handle collections
            if (isset($item['collections'])) {
                foreach ($item['collections'] as $collectionData) {
                    $collection = Collection::firstOrCreate([
                        'name' => $collectionData['name'],
                    ], [
                        'image_url' => $collectionData['image'],
                    ]);
                    $itemSkin->collections()->syncWithoutDetaching([$collection->id]);
                }
            }

            // Handle crates and packages
            if (isset($item['crates'])) {
                foreach ($item['crates'] as $crateData) {
                    // Check if the crate is a package
                    if (strpos($crateData['name'], 'Package') !== false) {
                        $package = Package::firstOrCreate([
                            'name' => $crateData['name'],
                        ], [
                            'image_url' => $crateData['image'],
                        ]);
                        $itemSkin->packages()->syncWithoutDetaching([$package->id]);
                    } else {
                        $crate = Crate::firstOrCreate([
                            'name' => $crateData['name'],
                        ], [
                            'image_url' => $crateData['image'],
                        ]);
                        $itemSkin->crates()->syncWithoutDetaching([$crate->id]);
                    }
                }
            }
        }

        $this->info('Item list has been updated.');
    }
}
