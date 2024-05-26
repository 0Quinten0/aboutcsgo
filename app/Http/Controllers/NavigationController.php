<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class NavigationController extends Controller
{
    public function getCategoriesWithItems()
    {
        // Retrieve categories with their items
        $categories = Category::with('items')->get();

        return response()->json($categories);
    }
}
