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

        $this->info('Item and sticker prices have been updated.');

        $logMessage = 'UpdatePrices command finished at ' . now();
        Log::channel('daily')->info($logMessage); // Log to the daily log file
    }

    protected function fetchAllPrices()
    {
        $this->fetchItemPricesFromBitSkins();
        $this->fetchPricesFromSkinPort();
    }

    protected function fetchItemPricesFromBitSkins()
    {
        $query = '
        SELECT 
            item_price.*, 
            items.name AS item_name, 
            skins.name AS skin_name, 
            exteriors.name AS exterior_name, 
            types.name AS type_name,
            categories.name AS category_name  -- Add the category name
        FROM 
            item_price
        JOIN 
            item_skin ON item_price.item_skin_id = item_skin.id
        JOIN 
            skins ON item_skin.skin_id = skins.id
        JOIN 
            items ON item_skin.item_id = items.id
        JOIN 
            exteriors ON item_price.exterior_id = exteriors.id
        JOIN 
            types ON item_price.type_id = types.id
        JOIN 
            categories ON items.category_id = categories.id  -- Join the categories table
        WHERE 
            categories.name = "Knifes"  -- Filter for Knives or Gloves
            AND skins.name = "Vanilla"  -- Filter for Vanilla skins
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
        // $this->info('Sending request to BitSkins API...');
        // Send the API request
        $response = Http::withHeaders([
            'content-type' => 'application/json',
        ])->timeout(500)->post('https://api.bitskins.com/market/search/730', $params);
    
        // Decode the API response
        $data = $response->json();
        $apiTime = microtime(true) - $startTime;
        // $this->info("Received response from BitSkins API in {$apiTime} seconds.");
    
        if (is_array($data) && isset($data['list'])) {
            // $this->info('Processing ' . count($data['list']) . ' items from response...');
    
            // Initialize variables to find the lowest price and suggested price
            $lowestPrice = null;
            $suggestedPrice = null;
    
            foreach ($data['list'] as $item) {
                $price = $item['price'] ?? 0;
                $currentSuggestedPrice = $item['suggested_price'] ?? 0;
    
                // Update lowest price if current item price is lower
                if ($lowestPrice === null || $price < $lowestPrice) {
                    $lowestPrice = $price;
                    $suggestedPrice = $currentSuggestedPrice;
                }
            }
    
            // Update the item price in the database with the lowest price
            if ($lowestPrice !== null) {
                $affectedRows = DB::table('item_price')
                    ->where('item_skin_id', $item_skin_id)
                    ->where('exterior_id', $exterior_id)
                    ->where('type_id', $type_id)
                    ->update([
                        'Bitskins_Value' => $lowestPrice / 1000,
                        'Steam_Value' => $suggestedPrice / 1000,
                        'updated_at' => now(),
                    ]);
    
                if ($affectedRows > 0) {
                    // $this->info("Updated {$affectedRows} rows for Item Skin ID: {$item_skin_id}, Lowest Price: {$lowestPrice}.");
                } else {
                    // $this->info("No rows updated for Item Skin ID: {$item_skin_id}.");
                }
            } else {
                // $this->info("No valid prices found for Item Skin ID: {$item_skin_id}.");
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
    
    
    
    protected function updateItemPrice($itemName, $source, $price)
    {
        if (!isset($this->skinPrices[$itemName])) {
            $this->skinPrices[$itemName] = [
                'name' => $itemName,
            ];
        }
    
        $this->skinPrices[$itemName][$source] = $price;
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
    
            // Concatenate the names of item and skin to form the fullName
            $fullName = $item->name . ' | ' . $skin->name;
    
            foreach ($this->skinPrices as $name => $priceData) {
                if (strpos($name, $fullName) !== false) {
                    $skinportPrice = $priceData['skinport_price'] ?? null;
    
    
                    // Extract exterior and type from name
                    $exteriorName = $this->extractExteriorFromName($name);
                    $typeName = $this->extractTypeFromName($name);
    
                    $type = Type::where('name', $typeName)->first();
    
                    if (empty($exteriorName)) {
                        $exteriorName = 'No Exterior';
                    }
    
                    $exterior = Exterior::where('name', $exteriorName)->first();
    
                    // Create or update item price record with all available prices
                    $itemPrice = ItemPrice::updateOrCreate(
                        [
                            'item_skin_id' => $itemSkin->id,
                            'exterior_id' => $exterior ? $exterior->id : null,
                            'type_id' => $type ? $type->id : null,
                        ],
                        [
                            'Skinport_Value' => $skinportPrice,
                        ]
                    );
    
                    // Log success message to live console (optional)
                    $this->info('Item price updated successfully.');
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
