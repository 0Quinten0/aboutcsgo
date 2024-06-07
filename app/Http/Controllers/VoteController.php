<?php
// app/Http/Controllers/VoteController.php

namespace App\Http\Controllers;

use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'item_skin_id' => 'required|exists:item_skin,id',
            'sticker_id' => 'required|exists:stickers,id',
        ]);

        $user = Auth::user();

        // Check if the user has already voted for the same item_skin_id and sticker_id
        $existingVote = Vote::where('user_id', $user->id)
            ->where('item_skin_id', $request->item_skin_id)
            ->where('sticker_id', $request->sticker_id)
            ->first();

        if ($existingVote) {
            return response()->json(['message' => 'You have already voted for this sticker on this item skin.'], 400);
        }

        // Count the total number of votes the user has for the specified item_skin_id
        $votesCount = Vote::where('user_id', $user->id)
            ->where('item_skin_id', $request->item_skin_id)
            ->count();

        if ($votesCount >= 3) {
            return response()->json(['message' => 'You have reached the maximum of 5 votes for this item skin.'], 400);
        }

        // Create the new vote
        $vote = Vote::create([
            'user_id' => $user->id,
            'item_skin_id' => $request->item_skin_id,
            'sticker_id' => $request->sticker_id,
        ]);

        return response()->json($vote, 201);
    }

    public function index()
    {
        $votes = Vote::all();
        return response()->json($votes);
    }
    public function destroy(Request $request)
    {
        $request->validate([
            'sticker_id' => 'required|exists:votes,sticker_id',
            'item_skin_id' => 'required|exists:votes,item_skin_id',
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Fetch the vote to delete
        $vote = Vote::where('user_id', $user->id)
            ->where('sticker_id', $request->sticker_id)
            ->where('item_skin_id', $request->item_skin_id)
            ->first();

        if (!$vote) {
            return response()->json(['message' => 'Vote not found'], 404);
        }

        // Delete the vote
        $vote->delete();

        return response()->json(['message' => 'Vote deleted successfully'], 200);
    }
}
