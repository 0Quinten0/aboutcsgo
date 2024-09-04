<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemSkin;
use App\Models\MarketplacePrice;
use App\Models\Marketplace;
use App\Models\ItemPrice;


use App\Models\Category;
use App\Models\Vote;
use App\Models\View; // Import the SkinView model

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;



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
    
        // Prepare an array to hold the final results
        $itemSkinsTransformed = $itemSkins->map(function ($itemSkin) use ($item) {
            // Initialize arrays to store the lowest price for each type and exterior
            $cheapestPricesByTypeAndExterior = [];
    
            // Fetch item prices
            $itemPrices = DB::table('item_prices')
                ->where('item_skin_id', $itemSkin->id)
                ->get(['id', 'type_id', 'exterior_id']);  // Fetch 'exterior_id' from item_prices table
    
            foreach ($itemPrices as $itemPrice) {
                // Fetch marketplace prices for each item_price_id
                $marketplacePrices = DB::table('marketplace_prices')
                    ->where('item_price_id', $itemPrice->id)
                    ->where('active', 1)
                    ->pluck('price')
                    ->toArray(); // Convert to array to find the minimum price
    
                if (!empty($marketplacePrices)) {
                    // Determine the type name based on item price type_id
                    $typeName = $this->getTypeName($itemPrice->type_id);
    
                    // Fetch the exterior_id from the itemPrice record
                    $exterior = $itemPrice->exterior_id;
    
                    // Find the lowest price in the current marketplace prices
                    $lowestPrice = min($marketplacePrices);
    
                    // If there is no current entry for this type and exterior, or if the current price is lower, update it
                    if (!isset($cheapestPricesByTypeAndExterior[$typeName][$exterior]) || $lowestPrice < $cheapestPricesByTypeAndExterior[$typeName][$exterior]) {
                        $cheapestPricesByTypeAndExterior[$typeName][$exterior] = $lowestPrice;
                    }
                }
            }
    
            // Log the results for debugging
            Log::info('Cheapest Prices by Type and Exterior: ' . print_r($cheapestPricesByTypeAndExterior, true));
    
            // Initialize the result prices
            $finalPrices = [
                'normal' => ['lowest' => null, 'highest' => null],
                'stattrak' => ['lowest' => null, 'highest' => null],
                'souvenir' => ['lowest' => null, 'highest' => null],
            ];
    
            // Store minimum and maximum prices for each type based on the cheapest price per exterior
            $minMaxPricesByType = [
                'normal' => [],
                'stattrak' => [],
                'souvenir' => [],
            ];
    
            // Compute the minimum and maximum prices for each type and exterior combination
            foreach ($cheapestPricesByTypeAndExterior as $typeName => $exteriors) {
                foreach ($exteriors as $exterior => $price) {
                    // Store the minimum and maximum price for this type and exterior
                    $minMaxPricesByType[$typeName]['min'][] = $price;
                    $minMaxPricesByType[$typeName]['max'][] = $price;
                }
            }
    
            // Determine overall lowest and highest from minimum and maximum prices for each type
            foreach ($minMaxPricesByType as $typeName => $priceGroup) {
                if (!empty($priceGroup['min']) && !empty($priceGroup['max'])) {
                    $finalPrices[$typeName]['lowest'] = min($priceGroup['min']);
                    $finalPrices[$typeName]['highest'] = max($priceGroup['max']);
                }
            }
    
            // Build the transformed item data
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
                'prices' => [
                    'normal' => $finalPrices['normal'],
                    'stattrak' => $itemSkin->stattrak ? $finalPrices['stattrak'] : null,
                    'souvenir' => $itemSkin->souvenir ? $finalPrices['souvenir'] : null,
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
                ->pluck('sticker_id')
                ->toArray();
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
            ->with('sticker')
            ->get();
    
        // Transform the sticker vote data
        $stickerVotesTransformed = [];
        foreach ($votes as $vote) {
            $sticker = $vote->sticker;
            if ($sticker) {
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
                    'steam_price' => $sticker->steam_price,
                    'vote_count' => $vote->vote_count,
                    'user_voted' => $userVoted,
                ];
            }
        }
    
        // Fetch prices for all variations of the item skin
        $itemPrices = ItemPrice::where('item_skin_id', $itemSkin->id)->get();
    
        // Initialize an array to store prices for each marketplace with additional details
        $prices = [];
    
        // Fetch prices for each variation type and include marketplace details
        foreach ($itemPrices as $itemPrice) {
            $exteriorName = Exterior::find($itemPrice->exterior_id)->name ?? 'No Exterior';
            $type = $itemPrice->type_id;
    
            $marketplacePrices = MarketplacePrice::where('item_price_id', $itemPrice->id)
                ->where('active', 1)
                ->with('marketplace') // Load the related marketplace with pretty_name and image_url
                ->get();
    
            foreach ($marketplacePrices as $marketplacePrice) {
                $marketplace = $marketplacePrice->marketplace;
    
                // Initialize the arrays for this marketplace if not already done
                if (!isset($prices[$marketplace->name])) {
                    $prices[$marketplace->name] = [
                        'pretty_name' => $marketplace->pretty_name,
                        'image_url' => $marketplace->image_url,
                        'normal' => [],
                        'stattrak' => [],
                        'souvenir' => [],
                    ];
                }
    
                // Assign prices based on type
                if ($type === 1 || $type === 4) { // Normal or other type
                    $prices[$marketplace->name]['normal'][$exteriorName] = $marketplacePrice->price;
                } elseif ($type === 3 || $type === 5) { // StatTrak
                    $prices[$marketplace->name]['stattrak'][$exteriorName] = $marketplacePrice->price;
                } elseif ($type === 2) { // Souvenir
                    $prices[$marketplace->name]['souvenir'][$exteriorName] = $marketplacePrice->price;
                }
            }
        }
    
        if ($user) {
            $userVotesCount = Vote::where('user_id', $user->id)
                ->where('item_skin_id', $itemSkin->id)
                ->count();
    
            $remainingUserVotes = $userVotesCount;
        } else {
            $remainingUserVotes = null;
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
            'prices' => $prices, // Prices now include marketplace details
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

