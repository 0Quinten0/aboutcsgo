<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function show($website_id)
    {
        $review = Review::where('website_id', $website_id)->first();
        if (!$review) {
            return response()->json(['error' => 'Review not found'], 404);
        }

        return response()->json($review);
    }
}
