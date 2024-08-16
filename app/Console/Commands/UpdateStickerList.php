<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sticker;
use App\Models\StickerCapsule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateStickerList extends Command
{
    protected $signature = 'update:sticker-list';

    protected $description = 'Update sticker list from the API';

    public function handle()
    {
        $response = Http::timeout(500)->get('https://bymykel.github.io/CSGO-API/api/en/stickers.json');
        $stickers = $response->json();

        foreach ($stickers as $stickerData) {
            // Handle sticker
            $sticker = Sticker::updateOrCreate(
                ['name' => $stickerData['name']],
                [
                    'description' => $stickerData['description'] ?? 'No description available',
                    'rarity_id' => $stickerData['rarity']['id'],
                    'rarity_name' => $stickerData['rarity']['name'],
                    'rarity_color' => $stickerData['rarity']['color'],
                    'tournament_event' => $stickerData['tournament_event'] ?? null,
                    'tournament_team' => $stickerData['tournament_team'] ?? null,
                    'type' => $stickerData['type'] ?? 'Unknown',
                    'market_hash_name' => $stickerData['market_hash_name'] ?? '',
                    'effect' => $stickerData['effect'] ?? 'None',
                    'image' => $stickerData['image'] ?? ''
                ]
            );

            // Handle sticker capsules
            if (isset($stickerData['crates'])) {
                foreach ($stickerData['crates'] as $crateData) {
                    $stickerCapsule = StickerCapsule::updateOrCreate(
                        ['name' => $crateData['name']],
                        ['image' => $crateData['image'] ?? '']
                    );
                    $sticker->stickerCapsules()->syncWithoutDetaching([$stickerCapsule->id]);
                }
            }
        }

        $this->info('Sticker list has been updated.');
    }
}
