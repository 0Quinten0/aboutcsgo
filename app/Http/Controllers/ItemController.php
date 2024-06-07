<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Vote;
use Illuminate\Support\Facades\Auth;



use App\Models\Exterior;

use Illuminate\Support\Facades\DB;


class ItemController extends Controller
{
    public function getItemSkins($item_name)
    {
        // Find the item by its name
        $item = Item::where('name', $item_name)->firstOrFail();

        // Retrieve the item skins for the found item, with the related skin and quality names
        $itemSkins = $item->itemSkins()->with(['skin', 'quality'])->get();

        // Transform the data to include the name values instead of IDs
        $itemSkinsTransformed = $itemSkins->map(function ($itemSkin) use ($item) {
            // Fetch the prices for the normal type (type_id 1)
            $normalPrices = DB::table('item_price')
                ->where('item_skin_id', $itemSkin->id)
                ->where('type_id', 1)
                ->orderBy('Bitskins_Value')
                ->get(['Bitskins_Value', 'Skinport_Value', 'Steam_Value']);

            // Initialize variables for lowest and highest prices
            $lowestNormalPrice = $normalPrices->min('Bitskins_Value');
            $highestNormalPrice = $normalPrices->max('Bitskins_Value');

            // Handle StatTrak™ and Souvenir prices if applicable
            $lowestStatTrakPrice = null;
            $highestStatTrakPrice = null;
            $lowestSouvenirPrice = null;
            $highestSouvenirPrice = null;

            if ($itemSkin->stattrak) {
                $statTrakPrices = DB::table('item_price')
                    ->where('item_skin_id', $itemSkin->id)
                    ->where('type_id', 3) // StatTrak™ type
                    ->orderBy('Bitskins_Value')
                    ->get(['Bitskins_Value', 'Skinport_Value', 'Steam_Value']);

                $lowestStatTrakPrice = $statTrakPrices->min('Bitskins_Value');
                $highestStatTrakPrice = $statTrakPrices->max('Bitskins_Value');
            }

            if ($itemSkin->souvenir) {
                $souvenirPrices = DB::table('item_price')
                    ->where('item_skin_id', $itemSkin->id)
                    ->where('type_id', 2) // Souvenir type
                    ->orderBy('Bitskins_Value')
                    ->get(['Bitskins_Value', 'Skinport_Value', 'Steam_Value']);

                $lowestSouvenirPrice = $souvenirPrices->min('Bitskins_Value');
                $highestSouvenirPrice = $souvenirPrices->max('Bitskins_Value');
            }

            // Handle special case for knives (item category ID is 1)
            if ($item->category_id == 1 | $item->category_id == 2 ) {
                $knifeNormalPrices = DB::table('item_price')
                    ->where('item_skin_id', $itemSkin->id)
                    ->where('type_id', 4) // Knife normal type
                    ->orderBy('Bitskins_Value')
                    ->get(['Bitskins_Value', 'Skinport_Value', 'Steam_Value']);

                $lowestNormalPrice = $knifeNormalPrices->min('Bitskins_Value');
                $highestNormalPrice = $knifeNormalPrices->max('Bitskins_Value');

                if ($itemSkin->stattrak) {
                    $knifeStatTrakPrices = DB::table('item_price')
                        ->where('item_skin_id', $itemSkin->id)
                        ->where('type_id', 5) // Knife StatTrak™ type
                        ->orderBy('Bitskins_Value')
                        ->get(['Bitskins_Value', 'Skinport_Value', 'Steam_Value']);

                    $lowestStatTrakPrice = $knifeStatTrakPrices->min('Bitskins_Value');
                    $highestStatTrakPrice = $knifeStatTrakPrices->max('Bitskins_Value');
                }
            }

            return [
                'id' => $itemSkin->id,
                'item_id' => $itemSkin->item->name,
                'skin' => $itemSkin->skin->name,
                'quality' => $itemSkin->quality->name,
                'quality_color' => $itemSkin->quality->color,
                'stattrak' => $itemSkin->stattrak,
                'souvenir' => $itemSkin->souvenir,
                'description' => $itemSkin->description,
                'image_url' => $itemSkin->image_url,
                'created_at' => $itemSkin->created_at,
                'updated_at' => $itemSkin->updated_at,
                'prices' => [
                    'normal' => [
                        'lowest' => $lowestNormalPrice,
                        'highest' => $highestNormalPrice,
                    ],
                    'stattrak' => $itemSkin->stattrak ? [
                        'lowest' => $lowestStatTrakPrice,
                        'highest' => $highestStatTrakPrice,
                    ] : null,
                    'souvenir' => $itemSkin->souvenir ? [
                        'lowest' => $lowestSouvenirPrice,
                        'highest' => $highestSouvenirPrice,
                    ] : null,
                ],
            ];
        });

        return response()->json($itemSkinsTransformed);
    }

    
public function getItemSkin(Request $request)
{
    // Validate request parameters
    $request->validate([
        'item_name' => 'required|string',
        'skin_name' => 'required|string',
    ]);

    $user = Auth::user();



        // Retrieve item and skin based on validated input
        $item_name = $request->input('item_name');
        $skin_name = $request->input('skin_name');
        $item = Item::where('name', $item_name)->firstOrFail();


        
    
        // Retrieve the specific item skin for the found item, with the related skin and quality names
        $itemSkin = $item->itemSkins()
            ->whereHas('skin', function ($query) use ($skin_name) {
                $query->where('name', $skin_name);
            })
            ->with(['skin', 'quality'])
            ->firstOrFail();

            if ($item) {
                $category = Category::find($item->category_id);
                $categoryName = $category ? $category->name : null;
            } else {
                $categoryName = null;
            }


            $userVotes = [];

            if ($user) {
                // Fetch the votes for the user for the given item_skin_id
                $userVotes = Vote::where('user_id', $user->id)
                    ->where('item_skin_id', $itemSkin->id)
                    ->pluck('sticker_id') // Get only the sticker_ids
                    ->toArray(); // Convert the collection to an array
            }
            

            $votes = Vote::where('item_skin_id', $itemSkin->id)
            ->select('sticker_id', DB::raw('count(*) as vote_count'))
            ->groupBy('sticker_id')
            ->with('sticker') // Ensure the related sticker is loaded
            ->get();
        
        // Transform the sticker vote data
        $stickerVotesTransformed = [];
        foreach ($votes as $vote) {
            $sticker = $vote->sticker; // Access the related sticker
            if ($sticker) {
                // Check if the user has voted for this sticker
                $userVoted = in_array($sticker->id, $userVotes);
                $stickerVotesTransformed[] = [
                    'id' => $sticker->id,
                    'name' => $sticker->name,
                    'description' => $sticker->description,
                    'rarity_id' => $sticker->rarity_id,
                    'rarity_name' => $sticker->rarity_name,
                    'rarity_color' => $sticker->rarity_color,
                    'tournament_event' => $sticker->tournament_event,
                    'tournament_team' => $sticker->tournament_team,
                    'type' => $sticker->type,
                    'market_hash_name' => $sticker->market_hash_name,
                    'effect' => $sticker->effect,
                    'image' => $sticker->image,
                    'created_at' => $sticker->created_at,
                    'updated_at' => $sticker->updated_at,
                    'bitskin_price' => $sticker->bitskin_price,
                    'skinport_price' => $sticker->skinport_price,
                    'vote_count' => $vote->vote_count, // Get the vote count for this sticker
                    'user_voted' => $userVoted, // Set user_voted property
                ];
            }
        }
        // Fetch the exterior type names from the exteriors table
        $exteriors = Exterior::pluck('name', 'id');
    
        // Fetch the prices for the normal type (type_id 1)
        $normalPricesBitskins = [];
        $normalPricesSkinport = [];
        $normalPrices = DB::table('item_price')
        ->where('item_skin_id', $itemSkin->id)
        ->whereIn('type_id', [1, 4]) // Check for type_id 1 or 4
        ->orderBy('Bitskins_Value')
        ->get(['Bitskins_Value', 'Skinport_Value', 'exterior_id']);
    
    
            foreach ($normalPrices as $price) {
                $normalPricesBitskins[$exteriors[$price->exterior_id]] = $price->Bitskins_Value;
                $normalPricesSkinport[$exteriors[$price->exterior_id]] = $price->Skinport_Value;
            }
            
            // Fetch the prices for the stattrak type (type_id 3)
            $stattrakPricesBitskins = [];
            $stattrakPricesSkinport = [];
            $stattrakPrices = DB::table('item_price')
                ->where('item_skin_id', $itemSkin->id)
                ->whereIn('type_id', [3, 5])
                ->orderBy('Bitskins_Value')
                ->get(['Bitskins_Value', 'Skinport_Value', 'exterior_id']);
            
            foreach ($stattrakPrices as $price) {
                $stattrakPricesBitskins[$exteriors[$price->exterior_id]] = $price->Bitskins_Value;
                $stattrakPricesSkinport[$exteriors[$price->exterior_id]] = $price->Skinport_Value;
            }
            
            // Fetch the prices for the souvenir type (type_id 2)
            $souvenirPricesBitskins = [];
            $souvenirPricesSkinport = [];
            $souvenirPrices = DB::table('item_price')
                ->where('item_skin_id', $itemSkin->id)
                ->where('type_id', 2)
                ->orderBy('Bitskins_Value')
                ->get(['Bitskins_Value', 'Skinport_Value', 'exterior_id']);
            
            foreach ($souvenirPrices as $price) {
                $souvenirPricesBitskins[$exteriors[$price->exterior_id]] = $price->Bitskins_Value;
                $souvenirPricesSkinport[$exteriors[$price->exterior_id]] = $price->Skinport_Value;
            }


            if ($user) {
                $userVotesCount = Vote::where('user_id', $user->id)
                    ->where('item_skin_id', $itemSkin->id)
                    ->count();
            
                $remainingUserVotes = $userVotesCount;
            } else {
                $remainingUserVotes = null; // or 0, depending on how you want to handle unauthenticated users
            }
            
    
        // Transform the data to include the name values instead of IDs
        $itemSkinTransformed = [
            'id' => $itemSkin->id,
            'item_id' => $itemSkin->item->name,
            'category' => $categoryName,
            'skin' => $itemSkin->skin->name,
            'quality' => $itemSkin->quality->name,
            'quality_color' => $itemSkin->quality->color,
            'stattrak' => $itemSkin->stattrak,
            'souvenir' => $itemSkin->souvenir,
            'description' => $itemSkin->description,
            'image_url' => $itemSkin->image_url,
            'created_at' => $itemSkin->created_at,
            'updated_at' => $itemSkin->updated_at,
            'sticker_votes' => $stickerVotesTransformed,
            'normal_prices_bitskins' => $normalPricesBitskins,
            'normal_prices_skinport' => $normalPricesSkinport,
            'stattrak_prices_bitskins' => $stattrakPricesBitskins,
            'stattrak_prices_skinport' => $stattrakPricesSkinport,
            'souvenir_prices_bitskins' => $souvenirPricesBitskins,
            'souvenir_prices_skinport' => $souvenirPricesSkinport,
            'user_votes' => $remainingUserVotes,
        ];
    
        return response()->json($itemSkinTransformed);
    }
    
    
}

