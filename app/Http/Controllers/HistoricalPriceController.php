<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoricalPriceRaw;
use App\Models\HistoricalPriceHourly;
use App\Models\HistoricalPriceDaily;
use App\Models\ItemPrice;
use App\Models\Item;
use App\Models\Exterior;



class HistoricalPriceController extends Controller
{
    /**
     * Retrieve raw historical prices for an item.
     */
    public function getAllHistoricalPrices(Request $request)
    {
        // Validate request input
        $request->validate([
            'item_name' => 'required|string',
            'skin_name' => 'required|string',
        ]);

        // Retrieve the item by name
        $item = Item::where('name', $request->input('item_name'))->firstOrFail();

        // Retrieve the specific item skin based on the skin name
        $itemSkin = $item->itemSkins()
            ->whereHas('skin', function ($query) use ($request) {
                $query->where('name', $request->input('skin_name'));
            })
            ->with(['skin', 'quality'])
            ->firstOrFail();

        // Fetch all ItemPrice IDs associated with this item skin
        $itemPrices = ItemPrice::where('item_skin_id', $itemSkin->id)->get();

        if ($itemPrices->isEmpty()) {
            return response()->json(['message' => 'No price data found for this item skin'], 404);
        }

        // Initialize an array to store historical data organized by type + exterior
        $historicalPrices = [];

        // Loop through each item price to fetch historical data
        foreach ($itemPrices as $itemPrice) {
            $exteriorName = Exterior::find($itemPrice->exterior_id)->name ?? 'No Exterior';
            $type = $itemPrice->type_id;

            // Define type category
            $typeCategory = match ($type) {
                1, 4 => 'normal',
                3, 5 => 'stattrak',
                2 => 'souvenir',
                default => 'unknown',
            };

            // Fetch historical price data for this item price ID
            $historicalData = [
                'raw' => HistoricalPriceRaw::where('item_price_id', $itemPrice->id)->orderBy('created_at', 'desc')->get(),
                'hourly' => HistoricalPriceHourly::where('item_price_id', $itemPrice->id)->orderBy('hour', 'desc')->get(),
                'daily' => HistoricalPriceDaily::where('item_price_id', $itemPrice->id)->orderBy('day', 'desc')->get(),
            ];

            // Organize data
            if (!isset($historicalPrices[$typeCategory])) {
                $historicalPrices[$typeCategory] = [];
            }

            $historicalPrices[$typeCategory][$exteriorName] = $historicalData;
        }

        return response()->json($historicalPrices);
    }


}
