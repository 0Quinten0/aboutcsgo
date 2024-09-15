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
use App\Models\Marketplace;



use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;





class UpdatePrices extends Command
{


    protected $usdToEurRate = null;



    protected $signature = 'update:prices';
    protected $description = 'Update item and sticker prices from the API';

    protected $skinPrices = [];

    protected $marketplaces;


    public function __construct()
    {

        parent::__construct();  // Call the parent constructor to properly initialize the command


        $this->marketplaces = [
            'bitskins' => [
                'url' => 'https://api.bitskins.com/market/insell/730',
                'api_key' => null,
                'price_field' => 'price_min',
                'item_name_field' => 'name',
                'price_multiplier' => 0.001,
                'response_structure' => 'object',
                'items_key' => 'list',
                'price_array_name' => null,
                'currency' => 'eur',
            ],
            'steam' => [
                'url' => 'https://api.bitskins.com/market/skin/730',
                'api_key' => null,
                'price_field' => 'suggested_price',
                'item_name_field' => 'name',
                'price_multiplier' => 0.001,
                'response_structure' => 'array',
                'items_key' => null,
                'price_array_name' => null,
                'currency' => 'eur',
            ],
            'skinport' => [
                'url' => 'https://api.skinport.com/v1/items?app_id=730',
                'api_key' => null,
                'price_field' => 'min_price',
                'item_name_field' => 'market_hash_name',
                'price_multiplier' => 1,
                'response_structure' => 'array',
                'items_key' => null,
                'price_array_name' => null,
                'currency' => 'eur',
            ],
            'market_csgo' => [
                'url' => 'https://market.csgo.com/api/v2/prices/EUR.json',
                'api_key' => null,
                'price_field' => 'price',
                'item_name_field' => 'market_hash_name',
                'price_multiplier' => 1,
                'response_structure' => 'object',
                'items_key' => 'items',
                'price_array_name' => null,
                'currency' => 'eur',
            ],
            'waxpeer' => [
                'url' => 'https://api.waxpeer.com/v1/prices',
                'api_key' => null,
                'price_field' => 'min',
                'item_name_field' => 'name',
                'price_multiplier' => 0.001,
                'response_structure' => 'object',
                'items_key' => 'items',
                'price_array_name' => null,
                'currency' => 'eur',
            ],
            'skinwallet' => [
                'url' => 'https://www.skinwallet.com/market/api/offers/overview?appId=730&onlyTradable=false',
                'api_key' => env('SKINWALLET_API_KEY'),
                'price_field' => 'amount',
                'item_name_field' => 'marketHashName',
                'price_multiplier' => 1,
                'response_structure' => 'object',
                'items_key' => 'result',
                'offer_array_name' => 'cheapestOffer',
                'price_array_name' => 'price',
                'auth_scheme' => 'x-auth-token',
                'currency' => 'usd',
            ],
            'shadowpay' => [
                'url' => 'https://api.shadowpay.com/api/v2/user/items/prices',
                'api_key' => env('SHADOWPAY_API_KEY'),
                'price_field' => 'price',
                'item_name_field' => 'steam_market_hash_name',
                'price_multiplier' => 1,
                'response_structure' => 'object',
                'items_key' => 'data',
                'offer_array_name' => null,
                'price_array_name' => null,
                'auth_scheme' => 'bearer',
                'currency' => 'eur',
            ],
            'skinbaron' => [
                'url' => 'https://api.skinbaron.de/GetPriceList',
                'api_key' => env('SKINBARON_API_KEY'),
                'price_field' => 'lowestPrice',
                'item_name_field' => 'marketHashName',
                'price_multiplier' => 1,
                'response_structure' => 'object',
                'items_key' => 'map',
                'offer_array_name' => null,
                'price_array_name' => null,
                'auth_scheme' => 'XMLHttpRequest',
                'currency' => 'eur',
            ],
            'csfloat' => [
                'url' => 'https://csfloat.com/api/v1/listings/price-list',
                'price_field' => 'min_price',
                'item_name_field' => 'market_hash_name',
                'price_multiplier' => 0.01,
                'response_structure' => 'array',
                'offer_array_name' => null,
                'price_array_name' => null,
                'currency' => 'usd',
            ],
            'gamerpay' => [
                'url' => 'https://api.gamerpay.gg/prices',
                'api_key' => null,
                'price_field' => 'price',
                'item_name_field' => 'item',
                'price_multiplier' => 1,
                'response_structure' => 'xml',
                'items_key' => 'item',
                'currency' => 'eur',
            ],
        // 'dmarket' => [
        //     'url' => 'https://api.dmarket.com/marketplace-api/v1/user-offers?GameID=730&Status=OfferStatusDefault&SortType=UserOffersSortTypeDefault',
        //     'api_key' => env('DMARKET_API_KEY'),  // Retrieve from .env
        //     'private_key' => 'cd593caaf6c10f65e5e0d4e82e694e2507d557598c66e80a3c1d7db144a4d8f86b42f4a9f1fd93dc5728dbfd50bfaf00cb21129030231c45fbe05e6c18bca302', // Add your private key here
        //     'price_field' => 'price',
        //     'item_name_field' => 'item',
        //     'price_multiplier' => 1,
        //     'auth_scheme' => 'dmarket', // Specify the auth scheme here
        //     'response_structure' => 'object', // DMarket uses JSON response structure
        //     'items_key' => 'offers', // Adjust this to match the actual structure of DMarket's response
        //     'currency' => 'usd', // The currency of the response
        // ],
        ];


    }
    
    


    protected $stickerPrices = [];

    public function handle()
    {
        $this->info('UpdatePrices command started.');
    
        // Record the start time
        $startTime = microtime(true);
    
        // Call the other commands to ensure items and stickers are up-to-date
        // Artisan::call('update:sticker-list');
        // Artisan::call('update:skinweapon-list');
    
        // Fetch prices from all marketplaces
        $this->fetchAllPrices();
    
        // Update prices for item skins and stickers
        $this->updateItemSkinPrices();
    
        // Record the end time
        $endTime = microtime(true);
    
        // Calculate the duration
        $duration = $endTime - $startTime;
    
        // Log or output the duration
        $this->info('Item and sticker prices have been updated.');
        $this->info("Total execution time: " . number_format($duration, 2) . " seconds");
    }


    protected function fetchUsdToEurRateFromApi()
    {
        // Example code to fetch the rate from an external service.
        // You would replace this with the actual API call logic.
        $apiUrl = 'https://api.exchangerate-api.com/v4/latest/USD';
        
        $response = file_get_contents($apiUrl);
        $data = json_decode($response, true);
        
        return $data['rates']['EUR'] ?? null; // Assuming the API returns rates like this
    }
    
    

    protected function fetchAllPrices()
    {
        foreach ($this->marketplaces as $marketplace => $config) {
            $this->fetchPricesFromMarketplace($marketplace, $config);
        }
    }


    protected function fetchPricesFromMarketplace($marketplace, $config)
    {
        try {

            // $this->info("Fetching prices from {$marketplace}...");

            // Make the HTTP request with or without API key
            $response = $this->makeRequest($config);

                   // Log full response for debugging
        // $this->info("Response status from {$marketplace}: " . $response->status());
        // $this->info("Response body from {$marketplace}: " . $response->body());
    
            // Check if the response was successful
            if ($response->failed()) {
                Log::error("Request to {$marketplace} failed", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return;
            }
    
            // Ensure the response body size matches the Content-Length header (if provided)
            $expectedSize = $response->header('Content-Length');
            if ($expectedSize && strlen($response->body()) != $expectedSize) {
                throw new \Exception("Received data size does not match the expected size.");
            }
    
            // Decode JSON response
            $data = $response->json();
    
            // Check if data is empty
            if (empty($data) && $config['response_structure'] !== 'xml') {
                // $this->warn("No data received from {$marketplace}.");
                return;
            }
    

                    // Handle XML responses
                           // Handle XML responses
                           if ($config['response_structure'] === 'xml') {
                            // $this->info('XML response starting');
                
                            // Parse the XML response
                            $xmlObject = simplexml_load_string($response->body());
                
                            if ($xmlObject === false) {
                                $this->warn("Failed to parse XML response from {$marketplace}.");
                                Log::warning("Invalid XML response from {$marketplace}: " . $response->body());
                                return;
                            }
                
                            // Convert the SimpleXMLElement object to a PHP array
                            $items = json_decode(json_encode($xmlObject), true);
                
                            // Log the structure of the parsed XML data
                            // Log::info("Parsed XML data from {$marketplace}:", $items);
                
                            if (empty($items)) {
                                $this->warn("No data received from {$marketplace}.");
                                return;
                            }
                
                            // Iterate over the items and process each one
                            $itemKey = $config['items_key'];
                            $itemsArray = $this->getArrayFromPath($items, $itemKey);
                
                            // Log the extracted items array
                            // Log::info("Extracted items array from path {$itemKey}:", $itemsArray);
                
                            if (is_array($itemsArray)) {
                                foreach ($itemsArray as $item) {
                                    $this->processItem($marketplace, $item, $config);
                                }
                            } else {
                                $this->warn("Unexpected XML structure from {$marketplace}. Expected 'item' key.");
                                Log::warning('Unexpected XML structure from ' . $marketplace . ': ' . $response->body());
                            }
                        }
                    

        else{

        
            // Process the data based on its structure
            $responseStructure = $config['response_structure'];
    
            if ($responseStructure === 'object' && isset($data[$config['items_key']]) && is_array($data[$config['items_key']])) {
                foreach ($data[$config['items_key']] as $item) {
                    $this->processItem($marketplace, $item, $config);
                }
            } elseif ($responseStructure === 'array' && is_array($data)) {
                foreach ($data as $item) {
                    $this->processItem($marketplace, $item, $config);
                }
            } else {
                $this->warn("Unexpected data structure from {$marketplace}.");
                Log::warning('Unexpected data structure from ' . $marketplace . ': ' . $response->body());
            }

        }
        } catch (\Exception $e) {
            Log::error("Error fetching prices from {$marketplace}", [
                'error' => $e->getMessage(),
            ]);
        }
    }


    protected function getArrayFromPath($array, $path)
{
    $keys = explode('/', $path);
    foreach ($keys as $key) {
        if (isset($array[$key])) {
            $array = $array[$key];
        } else {
            return null;
        }
    }
    return $array;
}


    protected function makeRequest($config)
    {
        // Set default headers
        $headers = [];
    
        // Determine content type based on response structure
        if ($config['response_structure'] === 'xml') {
            $headers['Accept'] = 'application/xml';
        } else {
            $headers['Accept'] = 'application/json';
        }
    
        // Determine request method and body
        $method = 'GET'; // Default method is GET
        $body = null; // Default body is null
    
        // Handle authentication schemes
        if (!empty($config['api_key'])) {
            switch ($config['auth_scheme']) {
                case 'bearer':
                    $headers['Authorization'] = 'Bearer ' . $config['api_key'];
                    break;
    
                case 'x-auth-token':
                    $headers['x-auth-token'] = $config['api_key'];
                    break;
    
                case 'XMLHttpRequest':
                    // For XMLHttpRequest scheme, the API key is in the body
                    $method = 'POST';
                    $body = [
                        'apikey' => $config['api_key'],
                        'appId' => 730 // Hardcoded value for appId
                    ];
                    // Add the x-requested-with header
                    $headers['x-requested-with'] = 'XMLHttpRequest';
                    break;
                    case 'dmarket':
                        // Handle DMarket signature-based authentication
                        $timestamp = time(); // Current timestamp
                        $route = parse_url($config['url'], PHP_URL_PATH);

                        $signature = $this->generateDMarketSignature(
                            $config['private_key'], 
                            $method, 
                            $route, 
                            $timestamp, 
                            $body
                        );
                        
                        $headers['X-Api-Key'] = $config['api_key'];
                        $headers['X-Sign-Date'] = $timestamp;
                        $headers['X-Request-Sign'] = $signature;
                        $headers['Content-Type'] = 'application/json';

                                        // Log the DMarket request details
                // $this->info("DMarket request headers: " . json_encode($headers));
                // $this->info("DMarket request body: " . json_encode($body));

                        break;
                default:
                    throw new \Exception("Unsupported authentication scheme: " . $config['auth_scheme']);
            }
        }
    
        $request = Http::timeout(60) // Set timeout
        ->withHeaders($headers);

        $this->info("Sending {$method} request to {$config['url']}");

    
        if ($method === 'POST' && $body !== null) {
            // Send POST request with body
            $response = $request->post($config['url'], $body);
        } else {
            // Send GET request
            $response = $request->get($config['url']);
        }
    
        return $response;
    }
    
    

    
    protected function generateDMarketSignature($privateKey, $method, $route, $timestamp, $postParams = [])
{
    $text = $method . $route . ($postParams ? json_encode($postParams) : '') . $timestamp;

    // Create a detached signature using sodium_crypto_sign_detached
    $signature = sodium_crypto_sign_detached($text, sodium_hex2bin($privateKey));

    // Encode the signature as a hexadecimal string
    return 'dmar ed25519 ' . sodium_bin2hex($signature);
}

    
    
    
    
    protected $processedItemIds = [];

    protected function processItem($marketplace, $item, $config)
    {
        // Ensure item is an array
        if (is_array($item)) {
            // Use a unique identifier for deduplication
            $itemId = $item['id'] ?? null; // Replace 'id' with the actual unique identifier field from the API
    
            if ($itemId && in_array($itemId, $this->processedItemIds)) {
                // Skip already processed items
                return;
            }
    
            // Mark item as processed
            if ($itemId) {
                $this->processedItemIds[] = $itemId;
            }
    
            // Check if the item contains the necessary fields before processing
            if (isset($item[$config['item_name_field']])) {
                $itemName = $item[$config['item_name_field']];
                $price = $this->extractPrice($marketplace, $item, $config);
    
                $priceMultiplier = $config['price_multiplier'] ?? 1;
                $price = is_numeric($price) ? $price * $priceMultiplier : null;

    
                // Ensure the price is a number, if needed
                if (is_numeric($price)) {
    
                    // Store the price in the skinPrices array
                    $this->skinPrices[$itemName][$marketplace . '_price'] = $price;
                } else {

                }
            } 
        } else {
            $this->warn("Unexpected item format in {$marketplace} response: " . json_encode($item));
            Log::warning("Unexpected item format: ", $item);
        }
    }
    
    
    
    protected function extractPrice($marketplace, $item, $config)
    {
        $price = null;
    
        // Handle nested structures with offer_array_name and price_array_name
        if (!empty($config['offer_array_name']) && !empty($config['price_array_name'])) {
            $offerArray = $item[$config['offer_array_name']] ?? null;
    
            if ($offerArray) {
                $priceObject = $offerArray[$config['price_array_name']] ?? null;
                if ($priceObject) {
                    $price = $priceObject[$config['price_field']] ?? null;
                }
            }
        } elseif (!empty($config['price_array_name'])) {
            // Handle other cases (default or direct field access)
            $nestedFields = explode('.', $config['price_array_name']);
            $currentValue = $item;
    
            foreach ($nestedFields as $field) {
                if (isset($currentValue[$field])) {
                    $currentValue = $currentValue[$field];
                } else {
                    return null; // If any level of the nesting is missing, return null
                }
            }
    
            $price = $currentValue[$config['price_field']] ?? null;
        } else {
            // If no price_array_name, default to using the top-level price_field
            $price = $item[$config['price_field']] ?? null;
        }
    
        // Convert to EUR if currency is USD and price is not null
        if ($price !== null && $config['currency'] === 'usd') {
            $rate = $this->getUsdToEurRate(); // Fetch the rate only if it's not already stored
            if ($rate) {
                $price *= $rate;
            }
        }
    
        return $price;
    }
    

    protected function getUsdToEurRate()
{
    if ($this->usdToEurRate === null) {
        // Assume this is where you fetch the USD to EUR conversion rate from an API
        $this->usdToEurRate = $this->fetchUsdToEurRateFromApi();
    }
    
    return $this->usdToEurRate;
}

    

 

    
    
    
    
    

protected function updateItemSkinPrices()
{
    $itemSkins = ItemSkin::all();

    foreach ($itemSkins as $itemSkin) {
        $item = Item::findOrFail($itemSkin->item_id);
        $skin = Skin::findOrFail($itemSkin->skin_id);

        $fullName = $skin->name === 'Vanilla' ? $item->name : $item->name . ' | ' . $skin->name;

        foreach ($this->skinPrices as $name => $priceData) {
            $typeName = $this->extractTypeFromName($name);

            if ($skin->name === 'Vanilla') {
                $fullNameLength = strlen($fullName);
                $nameEnd = substr($name, -$fullNameLength);
                if ($nameEnd === $fullName) {
                    $allowedPrefixes = ['★ StatTrak™', '★'];
                    foreach ($allowedPrefixes as $prefix) {
                        $prefixLength = strlen($prefix);
                        $namePrefix = substr($name, 0, -$fullNameLength);
                        if (trim($namePrefix) === $prefix) {
                            $isStatTrak = ($prefix === '★ StatTrak™');
                            $expectedType = $isStatTrak ? '★ StatTrak™' : '★';
                            $type = Type::where('name', $expectedType)->first();
                            $this->processMarketplacePrices($itemSkin, $name, $priceData);
                            break;
                        }
                    }
                }
            } else {
                $exteriorName = $this->extractExteriorFromName($name);
                if ($this->matchesFullItemName($name, $fullName, $typeName)) {
                    $this->processMarketplacePrices($itemSkin, $name, $priceData);
                }
            }
        }
    }

    // Process all buffered prices after the loop
    $this->processBufferedPrices();
}


    
    protected function matchesFullItemName($marketplaceName, $fullName, $type)
    {
        // Define the known prefixes
        $knownTypes = ['★ StatTrak™', 'StatTrak™', '★', 'Souvenir'];
        
        // Escape the fullName to ensure it's safely included in the regex
        $escapedFullName = preg_quote($fullName, '/');
        
        // Determine if the type is Normal
        if ($type === 'Normal') {
            // Construct the regex pattern without type prefixes for Normal
            $pattern = '/^\s*' . $escapedFullName . '\s*\(.*/';
        } else {
            // Create a regex pattern for known prefixes
            $prefixPattern = implode('|', array_map('preg_quote', $knownTypes));
            
            // Construct the full regex pattern to match the prefix, followed by the fullName and then a parenthesis
            $pattern = '/(?:' . $prefixPattern . ')\s*' . $escapedFullName . '\s*\(.*/';
        }
        
        // Return the result of preg_match
        return preg_match($pattern, $marketplaceName);
    }
    

    
    
    

    protected function processMarketplacePrices($itemSkin, $name, $priceData)
    {
        // Extract the type from the name (like StatTrak™ or ★)
        $typeName = $this->extractTypeFromName($name);
    
        if ($itemSkin->skin->name === 'Vanilla') {
            $exteriorName = 'No Exterior';
            $exterior = Exterior::where('name', $exteriorName)->first();
            $isStatTrak = strpos($name, '★ StatTrak™') !== false;
            $expectedType = $isStatTrak ? '★ StatTrak™' : '★';
            $type = Type::where('name', $expectedType)->first();
        } else {
            $exteriorName = $this->extractExteriorFromName($name);
            $exterior = Exterior::where('name', $exteriorName)->first();
            $type = Type::where('name', $typeName)->first();
        }
    
        // Create or update the item price record
        $itemPrice = ItemPrice::updateOrCreate(
            [
                'item_skin_id' => $itemSkin->id,
                'exterior_id' => $exterior ? $exterior->id : null,
                'type_id' => $type ? $type->id : null,
            ]
        );
    
        // Update the marketplace prices
        foreach ($this->marketplaces as $marketplace => $config) {
            $marketplaceId = Marketplace::where('name', $marketplace)->pluck('id')->first();
            $price = $priceData[$marketplace . '_price'] ?? null;

            
    
            if ($price !== null) {
                DB::table('marketplace_prices_buffer')->insert([
                    'item_price_id' => $itemPrice->id,
                    'marketplace_id' => $marketplaceId,
                    'price' => $price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
    
    protected function processBufferedPrices()
    {
        // Start a transaction
        DB::beginTransaction();
    
        try {
            // Move data from buffer to historical_prices_raw
            $bufferData = DB::table('marketplace_prices_buffer')->orderBy('created_at')->get();
    
            if ($bufferData->isNotEmpty()) {
                // Insert old prices into historical_prices_raw
                $existingPrices = DB::table('marketplace_prices')->orderBy('created_at')->get();
    
                foreach ($existingPrices as $price) {
                    DB::table('historical_prices_raw')->insert([
                        'item_price_id' => $price->item_price_id,
                        'marketplace_id' => $price->marketplace_id,
                        'price' => $price->price,
                        'created_at' => $price->created_at,
                        'updated_at' => now(),
                        'retrieved_at' => now(), // Add this line with the appropriate value
                    ]);
                }
    
                // Clear old prices
                DB::table('marketplace_prices')->truncate();
    
                // Insert new prices from buffer into marketplace_prices
                foreach ($bufferData as $data) {
                    DB::table('marketplace_prices')->insert([
                        'item_price_id' => $data->item_price_id,
                        'marketplace_id' => $data->marketplace_id,
                        'price' => $data->price,
                        'created_at' => $data->created_at,
                        'updated_at' => now(),
                    ]);
                }
    
                // Clean up the buffer table
                DB::table('marketplace_prices_buffer')->truncate();
            }
    
// Get all distinct item_price_ids from both historical_prices_raw and marketplace_prices tables
$allItemPriceIds = DB::table('item_prices')
    ->pluck('id'); // Assuming 'id' is the primary key in your 'item_prices' table


// Perform aggregation for each unique item_price_id present in item_prices
foreach ($allItemPriceIds as $itemPriceId) {
    $this->aggregatePrices($itemPriceId);
}

    
            // Commit the transaction
            DB::commit();
        } catch (\Exception $e) {
            // Rollback the transaction if there was an error
            DB::rollBack();
    
            // Log or handle the exception as needed
            Log::error('Failed to process buffered prices: ' . $e->getMessage());
        }
    }
    
    
    
    
    

    
    


    protected function aggregatePrices($itemPriceId)
    {
        // Get the current time in the appropriate time zone
        $now = now();
        
        // Calculate the current and previous hour
        $currentHour = $now->copy()->startOfHour(); // Start of the current hour
        $previousHour = $currentHour->copy()->subHour(); // Previous completed hour
        
        // Calculate the current and previous day
        $currentDay = $now->copy()->startOfDay(); // Start of the current day
        $previousDay = $currentDay->copy()->subDay();   // Previous completed day
    
    
        /**
         * Aggregation for Hourly Prices
         */
        $rawPrices = DB::table('historical_prices_raw')
            ->where('item_price_id', $itemPriceId)
            ->where('created_at', '<', $currentHour) // Aggregate all data before the current hour
            ->get();
    
        if ($rawPrices->isNotEmpty()) {
            // Calculate lowest price and average price
            $lowestPrice = $rawPrices->min('price');
            $avgPrice = $rawPrices->avg('price');
    
            // Insert the aggregated hourly prices for the previous hour
            DB::table('historical_prices_hourly')->insert([
                'item_price_id' => $itemPriceId,
                'hour' => $previousHour->format('Y-m-d H:00:00'), // Insert for the previous hour
                'lowest_price' => $lowestPrice,
                'avg_price' => $avgPrice,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
    
            // Clear the raw prices that have been aggregated
            DB::table('historical_prices_raw')
                ->where('item_price_id', $itemPriceId)
                ->where('created_at', '<', $currentHour)
                ->delete();
        }
    
        /**
         * Aggregation for Daily Prices
         */
        $hourlyPrices = DB::table('historical_prices_hourly')
            ->where('item_price_id', $itemPriceId)
            ->where('hour', '<', $currentDay->format('Y-m-d H:00:00')) // Aggregate all hourly data before the current day
            ->get();
    
        if ($hourlyPrices->isNotEmpty()) {
            // Calculate lowest price and average price
            $lowestPrice = $hourlyPrices->min('lowest_price');
            $avgPrice = $hourlyPrices->avg('avg_price');
    
            // Insert the aggregated daily prices for the previous day
            DB::table('historical_prices_daily')->insert([
                'item_price_id' => $itemPriceId,
                'day' => $previousDay->format('Y-m-d'), // Insert for the previous day
                'lowest_price' => $lowestPrice,
                'avg_price' => $avgPrice,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
    
            // Clear the hourly prices that have been aggregated
            DB::table('historical_prices_hourly')
                ->where('item_price_id', $itemPriceId)
                ->where('hour', '<', $currentDay->format('Y-m-d H:00:00'))
                ->delete();
        }
    }
    

    
    
    
    
    

    


    protected function getFullName($item, $skin)
    {
        return $skin->name === 'Vanilla' ? $item->name : $item->name . ' | ' . $skin->name;
    }

    
    
    protected function shouldIncludeItem($itemName)
    {
        $type = $this->extractTypeFromName($itemName);
        $exterior = $this->extractExteriorFromName($itemName);
    
        return $type !== 'Normal' || $exterior !== 'No Exterior';
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
        // Special case for '龍王 (Dragon King)' where exterior extraction needs to be adjusted
        if (strpos($itemName, '龍王 (Dragon King)') !== false) {
            // Extract the last part in parentheses as the exterior
            $pattern = '/\(([^()]+)\)$/'; // Adjusted to match only the last set of parentheses
        } else {
            // General case for other skins
            $pattern = '/\(([^()]+)\)/';
        }
        
        // Perform the regular expression match
        preg_match($pattern, $itemName, $matches);
    
        // Check if a match was found
        if (isset($matches[1])) {
            $exteriorName = trim($matches[1]);
    

    
            // Check if the extracted exterior matches any of the known exteriors
            $knownExteriors = ['Factory New', 'Minimal Wear', 'Field-Tested', 'Well-Worn', 'Battle-Scarred'];
            if (in_array($exteriorName, $knownExteriors)) {
                return $exteriorName; // Return the exterior name if it's a known exterior
            } else {
                // Log unexpected exteriors
                if (strpos($itemName, '龍王 (Dragon King)') !== false) {
                    // Log::warning("Unexpected exterior name for '龍王 (Dragon King)': " . $exteriorName);
                }
                return 'No Exterior'; // Or any other default value you prefer
            }
        } else {
            // Log missing exterior information
            if (strpos($itemName, '龍王 (Dragon King)') !== false) {
                $this->warn("No exterior match found for '龍王 (Dragon King)': " . $itemName);
            }
            return 'No Exterior'; // Or any other default value you prefer
        }
    }
    
    
    
    
    
    
    
    // protected function updateStickerPrices()
    // {
    //     foreach ($this->stickerPrices as $stickerName => $prices) {
    //         $bitskinPrice = $prices['bitskin_price'] ?? null;
    //         $skinportPrice = $prices['skinport_price'] ?? null;
    
    //         // Convert BitSkins price to cents if needed
    //         // Assuming the BitSkins price is in a different currency unit, adjust the conversion accordingly
    //         // For example, if it's in euros, multiply by 100 to convert to cents
    //         if ($bitskinPrice !== null) {
    //             $bitskinPrice /= 1000; // Adjust conversion if necessary
    //         }
    
    //         Sticker::where('name', $stickerName)->update([
    //             'bitskin_price' => $bitskinPrice,
    //             'skinport_price' => $skinportPrice,
    //         ]);
    //     }
    // }
    

}
