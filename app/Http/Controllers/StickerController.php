<?php

// app/Http/Controllers/StickerController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sticker;

use App\Models\Vote;
use Illuminate\Support\Facades\Auth;

class StickerController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        $itemSkin_id = $request->input('itemSkin_id');

    
        if(!$query | !$itemSkin_id) {
            return response()->json([]);
        }
    
        // Fetch the authenticated user (if available)
        $user = Auth::user();
    
        // Search for stickers by name, description, or other relevant fields
        $stickers = Sticker::where('name', 'LIKE', "%{$query}%")
            ->orWhere('market_hash_name', 'LIKE', "%{$query}%")
            ->limit(10) // limit the number of results
            ->get();
    
        // Determine if the user has voted for each sticker in the search results
        foreach ($stickers as $sticker) {
            if ($user) {
                // Fetch the votes for the user for the given item_skin_id and sticker_id
                $userVoted = Vote::where('user_id', $user->id)
                    ->where('item_skin_id', $itemSkin_id)
                    ->where('sticker_id', $sticker->id)
                    ->exists();
            } else {
                // If the user is not authenticated, set userVoted to false
                $userVoted = false;
            }
    
            // Append the user_voted property to each sticker
            $sticker->user_voted = $userVoted;
        }
    
        return response()->json($stickers);
    }
    
}
