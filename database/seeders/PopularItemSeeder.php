<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\ItemSkin;
use App\Models\View;
use Faker\Factory as Faker;
use Carbon\Carbon;

class PopularItemSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Get all item skins from the database
        $itemSkins = ItemSkin::all();

        // Add random views for the last 24 hours
        foreach ($itemSkins as $itemSkin) {
            // Generate a random number of views between 1 and 1000
            $viewCount = rand(1, 1000);

            // Generate random timestamps within the last 24 hours for each view
            for ($i = 0; $i < $viewCount; $i++) {
                $viewTimestamp = Carbon::now()->subMinutes(rand(1, 1440)); // Random timestamp within the last 24 hours

                // Insert a view record into the `views` table for each view
                View::create([
                    'item_skin_id' => $itemSkin->id,
                    'viewed_at' => $viewTimestamp,
                ]);
            }
        }
    }
}
