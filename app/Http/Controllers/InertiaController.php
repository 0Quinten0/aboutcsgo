<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Models\View;
use App\Models\ItemSkin;
use App\Models\Item;
use Inertia\Response;
use App\Models\Category;
use App\Models\ItemPrice;
use App\Models\MarketplacePrice;
use Illuminate\Support\Facades\Log;


use Illuminate\Support\Facades\DB;

class InertiaController extends Controller
{
    /**
     * Render the home page with the 20 most viewed item skins in the last 24 hours.
     */
    public function home()
    {
        $twentyFourHoursAgo = Carbon::now()->subHours(24);

        // Get the most viewed skins in the last 24 hours
        $mostViewedSkins = View::select('item_skin_id', DB::raw('COUNT(*) as view_count'))
            ->where('viewed_at', '>=', $twentyFourHoursAgo)
            ->groupBy('item_skin_id')
            ->orderByDesc('view_count')
            ->limit(20)
            ->get();

        // Fetch detailed information for each skin
        $itemSkinDetails = ItemSkin::with(['item', 'skin', 'quality'])
            ->whereIn('id', $mostViewedSkins->pluck('item_skin_id'))
            ->get();

        // Merge view counts with item skin details
        $popularItems = $itemSkinDetails->map(function ($itemSkin) use ($mostViewedSkins) {
            $viewCount = $mostViewedSkins->firstWhere('item_skin_id', $itemSkin->id)->view_count ?? 0;

            return [
                'id' => $itemSkin->id,
                'item_name' => $itemSkin->item->name ?? 'Unknown',
                'skin_name' => $itemSkin->skin->name ?? 'Unknown',
                'quality' => $itemSkin->quality->name ?? 'Unknown',
                'quality_color' => $itemSkin->quality->color ?? '#ffffff',
                'image_url' => $itemSkin->image_url ?? '',
                'view_count' => $viewCount,
            ];
        })->toArray();

        // Debugging - check output in Laravel logs
        // dd($popularItems); // Uncomment to check in browser

        return Inertia::render('HomePage', [
            'popularItems' => $popularItems,
        ]);
    }


    private function getTypeName($typeId)
{
    $types = [
        1 => 'normal',
        2 => 'souvenir',
        3 => 'stattrak',
        5 => 'stattrak',
    ];

    return $types[$typeId] ?? 'normal'; // Default to 'normal' if not found
}


    public function weapon($weaponName): Response
    {
        // Find the item by its name
        $item = Item::where('name', $weaponName)->firstOrFail();

        // Retrieve item skins with related data
        $itemSkins = $item->itemSkins()->with(['skin', 'quality'])->get();

        // Transform the data
        $itemSkinsTransformed = $itemSkins->map(function ($itemSkin) use ($item) {
            $cheapestPricesByTypeAndExterior = [];

            // Fetch item prices
            $itemPrices = DB::table('item_prices')
                ->where('item_skin_id', $itemSkin->id)
                ->get(['id', 'type_id', 'exterior_id']);

            foreach ($itemPrices as $itemPrice) {
                $marketplacePrices = DB::table('marketplace_prices')
                    ->where('item_price_id', $itemPrice->id)
                    ->pluck('price')
                    ->toArray();

                $typeName = $this->getTypeName($itemPrice->type_id);
                $exterior = $itemPrice->exterior_id;


                if (!empty($marketplacePrices)) {
                    $lowestPrice = min($marketplacePrices);

                    if (!isset($cheapestPricesByTypeAndExterior[$typeName][$exterior]) ||
                        $lowestPrice < $cheapestPricesByTypeAndExterior[$typeName][$exterior]) {
                        $cheapestPricesByTypeAndExterior[$typeName][$exterior] = $lowestPrice;
                    }
                }
            }


            $finalPrices = [
                'normal' => ['lowest' => null, 'highest' => null],
                'stattrak' => ['lowest' => null, 'highest' => null],
                'souvenir' => ['lowest' => null, 'highest' => null],
            ];

            $minMaxPricesByType = [
                'normal' => [],
                'stattrak' => [],
                'souvenir' => [],
            ];

            foreach ($cheapestPricesByTypeAndExterior as $typeName => $exteriors) {
                foreach ($exteriors as $exterior => $price) {
                    $minMaxPricesByType[$typeName]['min'][] = $price;
                    $minMaxPricesByType[$typeName]['max'][] = $price;
                }
            }

            foreach ($minMaxPricesByType as $typeName => $priceGroup) {
                if (!empty($priceGroup['min']) && !empty($priceGroup['max'])) {
                    $finalPrices[$typeName]['lowest'] = min($priceGroup['min']);
                    $finalPrices[$typeName]['highest'] = max($priceGroup['max']);
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
                'prices' => [
                    'normal' => $finalPrices['normal'],
                    'stattrak' => $itemSkin->stattrak ? $finalPrices['stattrak'] : null,
                    'souvenir' => $itemSkin->souvenir ? $finalPrices['souvenir'] : null,
                ],
            ];
        });


        return Inertia::render('WeaponPage', [
            'weaponName' => $weaponName,
            'skins' => $itemSkinsTransformed,
        ]);
    }

    public function skin($weaponName, $skinName)
    {
        // Find the item by its name
        $item = Item::where('name', $weaponName)->firstOrFail();

        // Find the specific skin for the item
        $itemSkin = $item->itemSkins()
            ->whereHas('skin', function ($query) use ($skinName) {
                $query->where('name', $skinName);
            })
            ->with(['skin', 'quality'])
            ->firstOrFail();

        // Retrieve the category name
        $category = Category::find($item->category_id);
        $categoryName = $category ? $category->name : null;

        // Fetch prices for all variations of the item skin
        $itemPrices = ItemPrice::where('item_skin_id', $itemSkin->id)->get();

        $prices = [];
        foreach ($itemPrices as $itemPrice) {
            $exteriorName = $itemPrice->exterior_id ? $itemPrice->exterior->name : 'No Exterior';
            $marketplacePrices = MarketplacePrice::where('item_price_id', $itemPrice->id)
                ->with('marketplace') // Load the related marketplace
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

                // Assign prices based on type (normal, stattrak, souvenir)
                if ($itemPrice->type_id === 1 || $itemPrice->type_id === 4) {
                    $prices[$marketplace->name]['normal'][$exteriorName] = $marketplacePrice->price;
                } elseif ($itemPrice->type_id === 3 || $itemPrice->type_id === 5) {
                    $prices[$marketplace->name]['stattrak'][$exteriorName] = $marketplacePrice->price;
                } elseif ($itemPrice->type_id === 2) {
                    $prices[$marketplace->name]['souvenir'][$exteriorName] = $marketplacePrice->price;
                }
            }
        }

        View::create([
            'item_skin_id' => $itemSkin->id,
            'viewed_at' => now(),
        ]);

        // Transform the data to include the name values instead of IDs
        $itemSkinTransformed = [
            'id' => $itemSkin->id,
            'item_id' => $item->name,
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
            'prices' => $prices,
        ];

        // Return the data to the Inertia view
        return Inertia::render('SkinPage', [
            'weaponName' => $weaponName,
            'skinName' => $skinName,
            'skinData' => $itemSkinTransformed,
        ]);
    }
}
