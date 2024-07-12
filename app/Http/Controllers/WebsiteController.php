<?php

namespace App\Http\Controllers;

use App\Models\Website;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\PaymentMethod;

class WebsiteController extends Controller
{
    public function index()
    {
        // Eager load websiteInformation and games
        $websites = Website::with(['websiteInformation', 'games'])->get();
        return response()->json($websites);
    }

    public function getPaymentMethods()
{
    $paymentMethods = PaymentMethod::all();
    return response()->json($paymentMethods);
}

public function show($id)
{
    $website = Website::with('websiteInformation')->find($id);
    
    if (!$website) {
        return response()->json(['error' => 'Website not found'], 404);
    }
    
    return response()->json($website);
}

public function getGames()
{
    $games = Game::all();
    return response()->json($games);
}
    

    public function filter(Request $request)
    {
        $query = Website::query();
    
        if ($request->has('games')) {
            $games = $request->input('games');
            $query->whereHas('games', function ($query) use ($games) {
                $query->whereIn('game_id', $games); // Ensure 'game_id' is the correct column name in 'website_games'
            });
        }
        
        
    
        if ($request->has('payment_methods')) {
            $paymentMethods = $request->input('payment_methods');
            $query->whereHas('paymentMethods', function ($query) use ($paymentMethods) {
                $query->whereIn('payment_method_id', $paymentMethods); // Ensure this is the correct column name
            });
        }
        
    
        if ($request->has('bonus_percentage')) {
            $bonusPercentage = $request->input('bonus_percentage');
            $query->where('bonus_percentage', '>=', $bonusPercentage);
        }
    
        if ($request->has('bonus_max')) {
            $bonusMax = $request->input('bonus_max');
            $query->where('bonus_max', '>=', $bonusMax);
        }
    
        $filteredWebsites = $query->with('websiteInformation')->get();
        return response()->json($filteredWebsites);
    }
    
}
