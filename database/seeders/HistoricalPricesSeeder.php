<?php

namespace Database\Seeders;

use App\Models\Exterior;
use App\Models\HistoricalPriceDaily;
use App\Models\HistoricalPriceHourly;
use App\Models\HistoricalPriceRaw;
use App\Models\ItemPrice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class HistoricalPricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $itemPriceId = 73; // Replace with your ItemPrice ID
        $exteriors = Exterior::all(); // Get all exteriors

        $types = [1, 2]; // 1 = normal, 2 = souvenir

        foreach ($types as $type) {
            foreach ($exteriors as $exterior) {
                // Find or create the ItemPrice for the specific type and exterior.
                $itemPrice = ItemPrice::firstOrCreate([
                    'item_skin_id' => $itemPriceId,
                    'exterior_id' => $exterior->id,
                    'type_id' => $type,
                ]);

                // Seed HistoricalPriceRaw data
                for ($i = 0; $i < 50; $i++) {
                    HistoricalPriceRaw::create([
                        'item_price_id' => $itemPrice->id,
                        'price' => rand(100, 1000) / 10.0,
                        'created_at' => Carbon::now()->subDays($i),
                        'updated_at' => Carbon::now()->subDays($i),
                        'marketplace_id' => 1, // Add marketplace_id here.
                    ]);
                }

                // Seed HistoricalPriceHourly data
                for ($i = 0; $i < 100; $i++) {
                    HistoricalPriceHourly::create([
                        'item_price_id' => $itemPrice->id,
                        'lowest_price' => rand(90, 950) / 10.0, // changed to lowest_price
                        'avg_price' => rand(100, 1000) / 10.0, // changed to avg_price
                        'hour' => Carbon::now()->subHours($i),
                    ]);
                }

                // Seed HistoricalPriceDaily data
                for ($i = 0; $i < 30; $i++) {
                    HistoricalPriceDaily::create([
                        'item_price_id' => $itemPrice->id,
                        'lowest_price' => rand(90, 950) / 10.0, // changed to lowest_price
                        'avg_price' => rand(100, 1000) / 10.0, // changed to avg_price
                        'day' => Carbon::now()->subDays($i),
                    ]);
                }
            }
        }
    }
}
