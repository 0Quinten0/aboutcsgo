<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Models\View;
use App\Models\ItemSkin;

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




}
