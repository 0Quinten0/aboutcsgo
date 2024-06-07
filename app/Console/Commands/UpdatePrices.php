<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Type;
use App\Models\ItemPrice;
use App\Models\ItemSkin;
use App\Models\Item;
use App\Models\Skin;

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
        // Call the other commands to ensure items and stickers are up-to-date
        // Artisan::call('update:sticker-list');
        // Artisan::call('update:skinweapon-list');

        // Fetch prices from BitSkins and SkinPort
        $this->fetchAllPrices();

        // Update prices for item skins
        $this->updateItemSkinPrices();

        // Update prices for stickers
        $this->updateStickerPrices();

        $this->info('Item and sticker prices have been updated.');
    }

    protected function fetchAllPrices()
    {
        $this->fetchPricesFromBitSkins();
        $this->fetchPricesFromSkinPort();
    }

    protected function fetchPricesFromBitSkins()
    {
        $response = Http::withHeaders([
            'content-type' => 'application/json',
        ])->get('https://api.bitskins.com/market/skin/730');
    
        $data = $response->json();
    
        if (!is_array($data)) {
            Log::error('Failed to fetch prices from BitSkins.', ['response' => $response->body()]);
            return;
        }
    
        foreach ($data as $item) {
            if (isset($item['name']) && $this->shouldIncludeItem($item['name'])) {
                $itemName = $item['name'];
    
                if (substr($itemName, -5) === 'Knife') {
                    $itemName .= ' | Vanilla';
                }
    
                if (strpos($itemName, 'Sticker |') === 0) {
                    $this->updateItemPrice($itemName, 'bitskin_price', $item['suggested_price']);
                } else {
                    $type = $this->extractTypeFromName($itemName);
                    $exterior = $this->extractExteriorFromName($itemName);
    
                    if ($type !== null || $exterior !== 'No Exterior') {
                        $this->updateItemPrice($itemName, 'bitskin_price', $item['suggested_price']);
                    }
                }
            }
        }
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
    
                if (substr($itemName, -5) === 'Knife') {
                    $itemName .= ' | Vanilla';
                }
    
                if (strpos($itemName, 'Sticker |') === 0) {
                    $this->updateItemPrice($itemName, 'skinport_price', $item['suggested_price']);
                } else {
                    $type = $this->extractTypeFromName($itemName);
                    $exterior = $this->extractExteriorFromName($itemName);
    
                    if ($type !== null || $exterior !== 'No Exterior') {
                        $this->updateItemPrice($itemName, 'skinport_price', $item['suggested_price']);
                    }
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
    
            // Log the fullName to ensure it's correctly formed
            // $this->info("Full name: " . $fullName);
    
            // Check if the fullName contains 'Vanilla'
// Check if prices exist for the item
// Iterate over skinPrices and find matching items by name
foreach ($this->skinPrices as $name => $priceData) {


        if (strpos($name, $fullName) !== false) {
            // Found a match based on partial name for non-vanilla items
            // Proceed with existing logic for non-vanilla items
            $bitSkinsPrice = isset($priceData['bitskin_price']) ? $priceData['bitskin_price'] : null;
            $skinportPrice = isset($priceData['skinport_price']) ? $priceData['skinport_price'] : null;

            if ($bitSkinsPrice !== null) {
                $bitSkinsPrice /= 1000; // Adjust conversion if necessary
            }

            // Use the returned names to extract exterior and type
            $exteriorName = $this->extractExteriorFromName($name);
            $typeName = $this->extractTypeFromName($name);

            $type = Type::where('name', $typeName)->first();

            // Now you have the price, exterior, and type for the item
            if (empty($exteriorName)) {
                $exteriorName = 'No Exterior';
            }

            // Find the exterior record by name or create it if it doesn't exist
            $exterior = Exterior::where('name', $exteriorName)->first();

            // Create or update item price record
            $itemPrice = ItemPrice::updateOrCreate(
                [
                    'item_skin_id' => $itemSkin->id,
                    'exterior_id' => $exterior ? $exterior->id : null,
                    'type_id' => $type ? $type->id : null,
                ],
                [
                    'Bitskins_Value' => $bitSkinsPrice,
                    'Skinport_Value' => $skinportPrice,
                ]
            );

            // Log success message to live console
            // $this->info('Item price updated successfully.');
        }
        // Your further actions for non-vanilla items can be added here
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
                Log::info('Found known exterior: ' . $exteriorName);
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
