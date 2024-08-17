<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Type;
use App\Models\ItemPrice;
use App\Models\ItemSkin;
use App\Models\Item;
use App\Models\Skin;
use Illuminate\Support\Facades\DB;

use App\Models\Sticker;
use App\Models\Exterior;
use App\Models\MarketplacePrice;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;


class UpdatePrices extends Command
{
    protected $signature = 'update:prices';
    protected $description = 'Update item and sticker prices from the API';

    protected $skinPrices = [];
    protected $stickerPrices = [];

    public function handle()
    {
        $logFile = storage_path('logs/scheduler.log');
        $logMessage = 'UpdatePrices command started at ' . now();
        Log::channel('daily')->info($logMessage); // Log to the daily log file        // Call the other commands to ensure items and stickers are up-to-date
        Artisan::call('update:sticker-list');
        Artisan::call('update:skinweapon-list');

        // Fetch prices from BitSkins and SkinPort
        $this->fetchAllPrices();

        // Update prices for item skins
        $this->updateItemSkinPrices();

        // Update prices for stickers
        $this->updateStickerPrices();

        $this->fetchItemPricesFromBitSkins();

        $this->info('Item and sticker prices have been updated.');

        $logMessage = 'UpdatePrices command finished at ' . now();
        Log::channel('daily')->info($logMessage); // Log to the daily log file
    }

    protected function fetchAllPrices()
    {
        $this->fetchPricesFromSkinPort();
    }

    protected function fetchItemPricesFromBitSkins()
    {
        $query = '
        SELECT 
            item_prices.*, 
            items.name AS item_name, 
            skins.name AS skin_name, 
            exteriors.name AS exterior_name, 
            types.name AS type_name,
            categories.name AS category_name  -- Add the category name
        FROM 
            item_prices
        JOIN 
            item_skin ON item_prices.item_skin_id = item_skin.id
        JOIN 
            skins ON item_skin.skin_id = skins.id
        JOIN 
            items ON item_skin.item_id = items.id
        JOIN 
            exteriors ON item_prices.exterior_id = exteriors.id
        JOIN 
            types ON item_prices.type_id = types.id
        JOIN 
            categories ON items.category_id = categories.id  -- Join the categories table
    ';
    
        $items = DB::select($query);
    
        foreach ($items as $item) {
            $skinName = $item->skin_name;
            $itemName = $item->item_name;
            $exteriorName = $item->exterior_name;
            $typeName = $item->type_name;
            $itemCategory = $item->category_name;  // Add item category



                    // Initialize $queryStr to avoid undefined variable error
    
// Build the query string for BitSkins API
if ($itemCategory === 'Knifes'  && $skinName === 'Vanilla') {
    // Special case for vanilla skins on knives and gloves
    if ($typeName === '★ StatTrak™') {
        $queryStr = '★ StatTrak™ ' . $itemName;
    } else {
        $queryStr = '★ ' . $itemName;
    }
} else {
    // Handle all other cases
    $queryStr = $this->buildSkinNameFilter($itemName, $skinName, $exteriorName, $typeName);
}

    
            // Prepare query parameters
            $queryParams = [
                'where' => [
                    'skin_name' => $queryStr,
                ],
            ];
    
            // Send request for the current item
            $this->sendBitSkinsRequest($queryParams, $item->item_skin_id, $item->exterior_id, $item->type_id);
            sleep(1); // Delay to comply with the rate limit
        }
    }
    

    protected function buildSkinNameFilter($itemName, $skinName, $exteriorName, $typeName)
    {
        $filterParts = [];
    
        if ($typeName && $typeName !== 'Normal') {
            $filterParts[] = '%' . $typeName . '%';
        }
    
        if ($itemName) {
            $filterParts[] = '%' . $itemName . '%';
        }
    
        if ($skinName) {
            $filterParts[] = '%' . $skinName . '%';
        }
    
        if ($exteriorName) {
            $filterParts[] = '%' . $exteriorName . '%';
        }
    
        return implode(' ', $filterParts);
    }
    
    

    protected function sendBitSkinsRequest($params, $item_skin_id, $exterior_id, $type_id)
    {
        $startTime = microtime(true);
        $response = Http::withHeaders([
            'content-type' => 'application/json',
        ])->timeout(500)->post('https://api.bitskins.com/market/search/730', $params);
    
        $data = $response->json();
        $apiTime = microtime(true) - $startTime;
    
        if (is_array($data) && isset($data['list'])) {
            $lowestPrice = null;
            $suggestedPrice = null;
    
            foreach ($data['list'] as $item) {
                $price = $item['price'] ?? null;
                $currentSuggestedPrice = $item['suggested_price'] ?? null;
    
                if ($price !== null && ($lowestPrice === null || $price < $lowestPrice)) {
                    $lowestPrice = $price;
                    $suggestedPrice = $currentSuggestedPrice;
                }
            }
    
            if ($lowestPrice !== null || $suggestedPrice !== null) {
                // Create or update the ItemPrice record
                $itemPrice = ItemPrice::updateOrCreate(
                    [
                        'item_skin_id' => $item_skin_id,
                        'exterior_id' => $exterior_id,
                        'type_id' => $type_id,
                    ],
                    [
                        'updated_at' => now(),
                    ]
                );
            
                // Define marketplace IDs
                $bitSkinsMarketplaceId = 1;
                $steamMarketplaceId = 2;
            
                // Handle BitSkins price
                if ($lowestPrice !== null) {
                    // Deactivate old BitSkins prices
                    MarketplacePrice::where('item_price_id', $itemPrice->id)
                        ->where('marketplace_id', $bitSkinsMarketplaceId)
                        ->update(['active' => 0]);
            
                    // Create a new BitSkins price record
                    MarketplacePrice::create([
                        'item_price_id' => $itemPrice->id,
                        'marketplace_id' => $bitSkinsMarketplaceId,
                        'price' => $lowestPrice / 1000,
                        'active' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            
                    $this->info("BitSkins price updated successfully for Item Skin ID: {$item_skin_id}, Lowest Price: {$lowestPrice}.");
                }
            
                // Handle Steam price
                if ($suggestedPrice !== null) {
                    // Deactivate old Steam prices
                    MarketplacePrice::where('item_price_id', $itemPrice->id)
                        ->where('marketplace_id', $steamMarketplaceId)
                        ->update(['active' => 0]);
            
                    // Create a new Steam price record
                    MarketplacePrice::create([
                        'item_price_id' => $itemPrice->id,
                        'marketplace_id' => $steamMarketplaceId,
                        'price' => $suggestedPrice / 1000,
                        'active' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            
                    $this->info("Steam price updated successfully for Item Skin ID: {$item_skin_id}, Suggested Price: {$suggestedPrice}.");
                }
            } else {
                Log::info('No valid price found for Item Skin ID: ' . $item_skin_id);
            }
            
        } else {
            Log::error('Failed to fetch prices from BitSkins.', ['response' => $response->body()]);
            $this->error('Failed to fetch prices from BitSkins.');
        }
    }
    
    
    
    
    
    
    protected function generateUniqueKeyFromItem($item)
    {
        // Construct a unique key from the item data (implement this based on how you map the items)
        return "{$item['skin_id']}_{$item['exterior_id']}_{$item['type_id']}";
    }
    
    

    
    
    protected function fetchPricesFromSkinPort()
    {
        $response = Http::get('https://api.skinport.com/v1/items?app_id=730');
    
        $data = $response->json();
    
        if (!is_array($data)) {
            Log::error('Failed to fetch prices from SkinPort.', ['response' => $response->body()]);
            return;
        }
    
        foreach ($data as $item) {
            if (isset($item['market_hash_name']) && $this->shouldIncludeItem($item['market_hash_name'])) {
                $itemName = $item['market_hash_name'];
    
                // Extract exterior and type information
                $type = $this->extractTypeFromName($itemName);
                $exterior = $this->extractExteriorFromName($itemName);
    
                // Store the prices in the skinPrices array
                if (!isset($this->skinPrices[$itemName])) {
                    $this->skinPrices[$itemName] = [
                        'name' => $itemName,
                        'skinport_price' => $item['min_price']
                    ];
                } else {
                    $this->skinPrices[$itemName]['skinport_price'] = $item['min_price'];
                }
            }
        }
    }
    
    

    
    protected function shouldIncludeItem($itemName)
    {
        $type = $this->extractTypeFromName($itemName);
        $exterior = $this->extractExteriorFromName($itemName);
    
        return $type !== 'Normal' || $exterior !== 'No Exterior';
    }
    
    
    
    protected function updateItemSkinPrices()
    {
        $itemSkins = ItemSkin::all();
    
        foreach ($itemSkins as $itemSkin) {
            $item = Item::findOrFail($itemSkin->item_id);
            $skin = Skin::findOrFail($itemSkin->skin_id);
    
            if ($skin->name === 'Vanilla') {
                $fullName = $item->name;
                $this->info('Vanilla Skin Detected: ' . $fullName);
            } else {
                $fullName = $item->name . ' | ' . $skin->name;
            }
    
            foreach ($this->skinPrices as $name => $priceData) {
                if (strpos($name, $fullName) !== false && $skin->name !== 'Vanilla') {
                    $skinportPrice = $priceData['skinport_price'] ?? null;
    
                    $exteriorName = $this->extractExteriorFromName($name);
                    $typeName = $this->extractTypeFromName($name);
    
                    $type = Type::where('name', $typeName)->first();
                    $exterior = Exterior::where('name', $exteriorName)->first();
    
                    $itemPrice = ItemPrice::updateOrCreate(
                        [
                            'item_skin_id' => $itemSkin->id,
                            'exterior_id' => $exterior ? $exterior->id : null,
                            'type_id' => $type ? $type->id : null,
                        ]
                    );
    
                    $skinportMarketplaceId = 3;
    
                    if ($skinportPrice !== null) {
                        MarketplacePrice::where('item_price_id', $itemPrice->id)
                        ->where('marketplace_id', $skinportMarketplaceId)
                        ->update(['active' => 0]);
        
                    // Create a new MarketplacePrice record with the new price
                    MarketplacePrice::create([
                        'item_price_id' => $itemPrice->id,
                        'marketplace_id' => $skinportMarketplaceId,
                        'price' => $skinportPrice,
                        'active' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
    
    
                        $this->info('Item price updated successfully.');
                    } else {
                        // If the price is null, do not create or update the marketplace price entry
                        $this->info('Skipped updating marketplace price for ' . $fullName . ' as the price is null.');
                    }
                }
    
                if ($skin->name === 'Vanilla') {
                    $fullNameLength = strlen($fullName);
                    $nameEnd = substr($name, -$fullNameLength);
    
                    if ($nameEnd === $fullName) {
                        $skinportPrice = $priceData['skinport_price'] ?? null;
    
                        $exteriorName = 'No Exterior';
                        $exterior = Exterior::where('name', $exteriorName)->first();
                        $typeName = $this->extractTypeFromName($name);
                        $type = Type::where('name', $typeName)->first();
    
                        $itemPrice = ItemPrice::updateOrCreate(
                            [
                                'item_skin_id' => $itemSkin->id,
                                'exterior_id' => $exterior ? $exterior->id : null,
                                'type_id' => $type ? $type->id : null,
                            ]
                        );
    
                        if ($skinportPrice !== null) {
                            // Deactivate old prices for the same item_price_id and marketplace_id
                            MarketplacePrice::where('item_price_id', $itemPrice->id)
                                ->where('marketplace_id', $skinportMarketplaceId)
                                ->update(['active' => 0]);
                
                            // Create a new MarketplacePrice record with the new price
                            MarketplacePrice::create([
                                'item_price_id' => $itemPrice->id,
                                'marketplace_id' => $skinportMarketplaceId,
                                'price' => $skinportPrice,
                                'active' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                
    
                            $this->info('Item price updated successfully for ' . $fullName);
                        } else {
                            $this->info('Skipped updating marketplace price for ' . $fullName . ' as the price is null.');
                        }
                        break;
                    }
                }
            }
        }
    }
    
    
    
    
    
    

    
    
    protected function extractTypeFromName($itemName)
    {
        // Known types ordered by length to match the longest possible type first
        $knownTypes = ['★ StatTrak™', 'StatTrak™', '★', 'Souvenir', 'Normal'];
    
        foreach ($knownTypes as $type) {
            if (strpos($itemName, $type) === 0) {
                return $type;
            }
        }
    
        // Default to 'Normal' if no known type is found
        return 'Normal';
    }
    
    
    protected function extractExteriorFromName($itemName)
    {
        // Regular expression pattern to match the exterior name enclosed in parentheses
        // Matches any characters within parentheses that are not opening or closing parentheses
        $pattern = '/\(([^()]+)\)/';
    
        // Perform the regular expression match
        preg_match($pattern, $itemName, $matches);
    
        // Check if a match was found
        if (isset($matches[1])) {
            $exteriorName = trim($matches[1]);
    
            // Check if the extracted exterior matches any of the known exteriors
            $knownExteriors = ['Factory New', 'Minimal Wear', 'Field-Tested', 'Well-Worn', 'Battle-Scarred'];
            if (in_array($exteriorName, $knownExteriors)) {
                // Log a message indicating that a known exterior was found
                // Log::info('Found known exterior: ' . $exteriorName);
                return $exteriorName; // Return the exterior name if it's a known exterior
            } else {
                // If the extracted exterior is not a known exterior, return a default value
                return 'No Exterior'; // Or any other default value you prefer
            }
        } else {
            // If no match was found, return a default value
            return 'No Exterior'; // Or any other default value you prefer
        }
    }
    
    
    
    
    
    protected function updateStickerPrices()
    {
        foreach ($this->stickerPrices as $stickerName => $prices) {
            $bitskinPrice = $prices['bitskin_price'] ?? null;
            $skinportPrice = $prices['skinport_price'] ?? null;
    
            // Convert BitSkins price to cents if needed
            // Assuming the BitSkins price is in a different currency unit, adjust the conversion accordingly
            // For example, if it's in euros, multiply by 100 to convert to cents
            if ($bitskinPrice !== null) {
                $bitskinPrice /= 1000; // Adjust conversion if necessary
            }
    
            Sticker::where('name', $stickerName)->update([
                'bitskin_price' => $bitskinPrice,
                'skinport_price' => $skinportPrice,
            ]);
        }
    }
    

    protected function fetchPrice($itemName)
    {
        return $this->skinPrices[$itemName] ?? 0;
    }
}
