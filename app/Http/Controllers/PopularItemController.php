<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\View;
use App\Models\ItemSkin;

class PopularItemController extends Controller
{
    /**
     * Get the 20 most viewed item skins in the last 24 hours.
     */
    public function getMostViewedItems()
    {
        // Calculate the timestamp for 24 hours ago
        $twentyFourHoursAgo = Carbon::now()->subHours(24);

        // Query the views table for the most viewed item skins in the last 24 hours
        $mostViewedSkins = View::select('item_skin_id', DB::raw('COUNT(*) as view_count'))
            ->where('viewed_at', '>=', $twentyFourHoursAgo)
            ->groupBy('item_skin_id')
            ->orderBy('view_count', 'desc')
            ->limit(20)
            ->get();

        // Retrieve detailed information for each of the top item skins
        $itemSkinDetails = ItemSkin::with(['item', 'skin', 'quality'])
            ->whereIn('id', $mostViewedSkins->pluck('item_skin_id'))
            ->get();

        // Merge view counts with item skin details
        $result = $itemSkinDetails->map(function ($itemSkin) use ($mostViewedSkins) {
            $viewCount = $mostViewedSkins->firstWhere('item_skin_id', $itemSkin->id)->view_count;

            return [
                'id' => $itemSkin->id,
                'item_name' => $itemSkin->item->name,
                'skin_name' => $itemSkin->skin->name,
                'quality' => $itemSkin->quality->name,
                'quality_color' => $itemSkin->quality->color,
                'image_url' => $itemSkin->image_url,
                'view_count' => $viewCount,
            ];
        });

        return response()->json($result);
    }
}
