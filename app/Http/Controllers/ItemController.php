<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemSkin;
use App\Models\MarketplacePrice;
use App\Models\ItemPrice;


use App\Models\Category;
use App\Models\Vote;
use App\Models\View; // Import the SkinView model

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
            // Initialize variables for prices
            $prices = [
                'normal' => ['Bitskins' => ['lowest' => null, 'highest' => null], 'Skinport' => ['lowest' => null, 'highest' => null], 'Steam' => ['lowest' => null, 'highest' => null]],
                'stattrak' => ['Bitskins' => ['lowest' => null, 'highest' => null], 'Skinport' => ['lowest' => null, 'highest' => null], 'Steam' => ['lowest' => null, 'highest' => null]],
                'souvenir' => ['Bitskins' => ['lowest' => null, 'highest' => null], 'Skinport' => ['lowest' => null, 'highest' => null], 'Steam' => ['lowest' => null, 'highest' => null]],
            ];
    
            // Fetch item prices
            $itemPrices = DB::table('item_prices')
                ->where('item_skin_id', $itemSkin->id)
                ->get(['id', 'type_id']); // Get item_price IDs and type_ids
    
            foreach ($itemPrices as $itemPrice) {
                // Fetch marketplace prices for each item_price_id
                $marketplacePrices = DB::table('marketplace_prices')
                    ->where('item_price_id', $itemPrice->id)
                    ->where('active', 1)
                    ->get(['marketplace_id', 'price']);
    
                foreach ($marketplacePrices as $marketplacePrice) {
                    // Determine the type name based on item price type_id
                    $typeName = $this->getTypeName($itemPrice->type_id);
    
                    if ($typeName) {
                        // Determine the marketplace name based on marketplace_id
                        $marketplaceName = $this->getMarketplaceName($marketplacePrice->marketplace_id);
    
                        // Update the lowest and highest prices for the marketplace
                        if ($prices[$typeName][$marketplaceName]['lowest'] === null || $marketplacePrice->price < $prices[$typeName][$marketplaceName]['lowest']) {
                            $prices[$typeName][$marketplaceName]['lowest'] = $marketplacePrice->price;
                        }
    
                        if ($prices[$typeName][$marketplaceName]['highest'] === null || $marketplacePrice->price > $prices[$typeName][$marketplaceName]['highest']) {
                            $prices[$typeName][$marketplaceName]['highest'] = $marketplacePrice->price;
                        }
                    }
                }
            }
    
            // Handle special case for knives (item category ID is 1 or 2)
            if ($item->category_id == 1 || $item->category_id == 2) {
                foreach ([4 => 'normal', 5 => 'stattrak'] as $typeId => $typeName) {
                    // Fetch knife prices from the database
                    $knifePrices = DB::table('item_prices')
                        ->where('item_skin_id', $itemSkin->id)
                        ->where('type_id', $typeId)
                        ->get(['id']); // Get item_price IDs
    
                    foreach ($knifePrices as $knifePrice) {
                        // Fetch marketplace prices for each item_price_id
                        $knifeMarketplacePrices = DB::table('marketplace_prices')
                            ->where('item_price_id', $knifePrice->id)
                            ->where('active', 1)
                            ->get(['marketplace_id', 'price']);
    
                        foreach ($knifeMarketplacePrices as $knifeMarketplacePrice) {
                            // Determine the marketplace name based on marketplace_id
                            $marketplaceName = $this->getMarketplaceName($knifeMarketplacePrice->marketplace_id);
    
                            // Update the lowest and highest prices for the marketplace
                            if ($prices[$typeName][$marketplaceName]['lowest'] === null || $knifeMarketplacePrice->price < $prices[$typeName][$marketplaceName]['lowest']) {
                                $prices[$typeName][$marketplaceName]['lowest'] = $knifeMarketplacePrice->price;
                            }
    
                            if ($prices[$typeName][$marketplaceName]['highest'] === null || $knifeMarketplacePrice->price > $prices[$typeName][$marketplaceName]['highest']) {
                                $prices[$typeName][$marketplaceName]['highest'] = $knifeMarketplacePrice->price;
                            }
                        }
                    }
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
                        'Bitskins' => $prices['normal']['Bitskins'],
                        'Skinport' => $prices['normal']['Skinport'],
                        'Steam' => $prices['normal']['Steam'],
                    ],
                    'stattrak' => $itemSkin->stattrak ? [
                        'Bitskins' => $prices['stattrak']['Bitskins'],
                        'Skinport' => $prices['stattrak']['Skinport'],
                        'Steam' => $prices['stattrak']['Steam'],
                    ] : null,
                    'souvenir' => $itemSkin->souvenir ? [
                        'Bitskins' => $prices['souvenir']['Bitskins'],
                        'Skinport' => $prices['souvenir']['Skinport'],
                        'Steam' => $prices['souvenir']['Steam'],
                    ] : null,
                ],
            ];
        });
    
        return response()->json($itemSkinsTransformed);
    }
    
    /**
     * Get the type name based on type_id.
     *
     * @param int $typeId
     * @return string|null
     */
    protected function getTypeName($typeId)
    {
        $types = [
            1 => 'normal',
            3 => 'stattrak',
            2 => 'souvenir',
            4 => 'normal', // Special case for knives
            5 => 'stattrak', // Special case for knives
        ];
    
        return $types[$typeId] ?? null;
    }
    
    /**
     * Get the marketplace name based on marketplace_id.
     *
     * @param int $marketplaceId
     * @return string|null
     */
    protected function getMarketplaceName($marketplaceId)
    {
        $marketplaces = [
            1 => 'Bitskins',
            2 => 'Steam',
            3 => 'Skinport',
        ];
    
        return $marketplaces[$marketplaceId] ?? null;
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
    
        // Retrieve the category name
        $category = Category::find($item->category_id);
        $categoryName = $category ? $category->name : null;
    
        $userVotes = [];
        if ($user) {
            // Fetch the votes for the user for the given item_skin_id
            $userVotes = Vote::where('user_id', $user->id)
                ->where('item_skin_id', $itemSkin->id)
                ->pluck('sticker_id') // Get only the sticker_ids
                ->toArray(); // Convert the collection to an array
        }
    
        // Log the view for this specific item skin
        View::create([
            'item_skin_id' => $itemSkin->id,
            'viewed_at' => now(),
        ]);
    
        // Fetch votes and stickers
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
                    'steam_price' => $sticker->steam_price, // Include Steam price
                    'vote_count' => $vote->vote_count, // Get the vote count for this sticker
                    'user_voted' => $userVoted, // Set user_voted property
                ];
            }
        }
    
        // Fetch prices for all variations of the item skin
        $itemPrices = ItemPrice::where('item_skin_id', $itemSkin->id)->get();
    
        // Initialize arrays to store prices
        $pricesBitskins = [
            'normal' => [],
            'stattrak' => [],
            'souvenir' => [],
        ];
        $pricesSkinport = [
            'normal' => [],
            'stattrak' => [],
            'souvenir' => [],
        ];
        $pricesSteam = [
            'normal' => [],
            'stattrak' => [],
            'souvenir' => [],
        ];
    
        // Fetch prices for each variation type
        foreach ($itemPrices as $itemPrice) {
            $exteriorName = Exterior::find($itemPrice->exterior_id)->name ?? 'No Exterior';
            $type = $itemPrice->type_id;
    
            $marketplacePrices = MarketplacePrice::where('item_price_id', $itemPrice->id)->where('active', 1)->get();
    
            foreach ($marketplacePrices as $marketplacePrice) {
                switch ($marketplacePrice->marketplace_id) {
                    case 1: // BitSkins
                        if ($type === 1 || $type === 4) { // Normal or other type
                            $pricesBitskins['normal'][$exteriorName] = $marketplacePrice->price;
                        } elseif ($type === 3 || $type === 5) { // StatTrak
                            $pricesBitskins['stattrak'][$exteriorName] = $marketplacePrice->price;
                        } elseif ($type === 2) { // Souvenir
                            $pricesBitskins['souvenir'][$exteriorName] = $marketplacePrice->price;
                        }
                        break;
                    case 2: // Steam
                        if ($type === 1 || $type === 4) { // Normal or other type
                            $pricesSteam['normal'][$exteriorName] = $marketplacePrice->price;
                        } elseif ($type === 3 || $type === 5) { // StatTrak
                            $pricesSteam['stattrak'][$exteriorName] = $marketplacePrice->price;
                        } elseif ($type === 2) { // Souvenir
                            $pricesSteam['souvenir'][$exteriorName] = $marketplacePrice->price;
                        }
                        break;
                    case 3: // Skinport
                        if ($type === 1 || $type === 4) { // Normal or other type
                            $pricesSkinport['normal'][$exteriorName] = $marketplacePrice->price;
                        } elseif ($type === 3 || $type === 5) { // StatTrak
                            $pricesSkinport['stattrak'][$exteriorName] = $marketplacePrice->price;
                        } elseif ($type === 2) { // Souvenir
                            $pricesSkinport['souvenir'][$exteriorName] = $marketplacePrice->price;
                        }
                        break;
                }
            }
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
            'normal_prices_bitskins' => $pricesBitskins['normal'],
            'normal_prices_skinport' => $pricesSkinport['normal'],
            'normal_prices_steam' => $pricesSteam['normal'],
            'stattrak_prices_bitskins' => $pricesBitskins['stattrak'],
            'stattrak_prices_skinport' => $pricesSkinport['stattrak'],
            'stattrak_prices_steam' => $pricesSteam['stattrak'],
            'souvenir_prices_bitskins' => $pricesBitskins['souvenir'],
            'souvenir_prices_skinport' => $pricesSkinport['souvenir'],
            'souvenir_prices_steam' => $pricesSteam['souvenir'],
            'user_votes' => $remainingUserVotes,
        ];
    
        return response()->json($itemSkinTransformed);
    }
    
    
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        if (!$query) {
            return response()->json([]);
        }
    
        // Normalize the query string for better matching
        $normalizedQuery = trim($query);
    
        // Split the query into individual words for more flexible matching
        $searchTerms = explode(' ', $normalizedQuery);
        $searchPattern = '%' . implode('%', $searchTerms) . '%';
        
        // Search for item skins by joining the weapon and skin tables
        $itemSkins = ItemSkin::select('item_skin.*', 'items.name as item_name', 'skins.name as skin_name')
            ->join('items', 'item_skin.item_id', '=', 'items.id')
            ->join('skins', 'item_skin.skin_id', '=', 'skins.id')
            ->where(function ($queryBuilder) use ($searchPattern) {
                $queryBuilder->where('items.name', 'LIKE', $searchPattern)
                    ->orWhere('skins.name', 'LIKE', $searchPattern)
                    ->orWhereRaw("CONCAT(items.name, ' ', skins.name) LIKE ?", [$searchPattern]);
            })
            ->limit(10) // Limit the number of results
            ->get()
            ->map(function ($itemSkin) use ($searchTerms) {
                // Combine the item name and skin name
                $itemSkin->combined_name = $itemSkin->item_name . ' ' . $itemSkin->skin_name;
    
                // Calculate a relevance score based on how many search terms match
                $itemSkin->relevance_score = $this->calculateRelevanceScore($itemSkin->combined_name, $searchTerms);
    
                return $itemSkin;
            })
            ->sortByDesc('relevance_score') // Sort results by relevance score
            ->values(); // Re-index the results to reset array keys
        
        return response()->json($itemSkins);
    }
    
    /**
     * Calculate a relevance score based on how many search terms match the item name and skin name.
     *
     * @param string $combinedName
     * @param array $searchTerms
     * @return int
     */
    private function calculateRelevanceScore($combinedName, $searchTerms)
    {
        $score = 0;
        
        foreach ($searchTerms as $term) {
            if (stripos($combinedName, $term) !== false) {
                $score += 1; // Increase score for each matching term
            }
        }
    
        return $score;
    }
    
    
    
    
    
}

