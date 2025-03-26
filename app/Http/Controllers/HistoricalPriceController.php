<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoricalPriceRaw;
use App\Models\HistoricalPriceHourly;
use App\Models\HistoricalPriceDaily;

class HistoricalPriceController extends Controller
{
    /**
     * Retrieve raw historical prices for an item.
     */
    public function getAllHistoricalPrices($itemPriceId)
    {
        return response()->json([
            'raw' => HistoricalPriceRaw::where('item_price_id', $itemPriceId)
                ->orderBy('created_at', 'desc')
                ->get(),

            'hourly' => HistoricalPriceHourly::where('item_price_id', $itemPriceId)
                ->orderBy('hour', 'desc')
                ->get(),

            'daily' => HistoricalPriceDaily::where('item_price_id', $itemPriceId)
                ->orderBy('day', 'desc')
                ->get(),
        ]);
    }

}