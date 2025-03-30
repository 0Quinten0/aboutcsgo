<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\NavigationController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\StickerController;
use App\Http\Controllers\PopularItemController;
use App\Http\Controllers\HistoricalPriceController;


    // Versioning (optional, but recommended)
    Route::prefix('v1')->group(function () {

        // General website-related endpoints
        Route::get('/websites', [WebsiteController::class, 'index']);
        Route::get('/websites/filter', [WebsiteController::class, 'filter']);
        Route::get('/payment-methods', [WebsiteController::class, 'getPaymentMethods']);
        Route::get('/games', [WebsiteController::class, 'getGames']);
        Route::get('/websites/{id}', [WebsiteController::class, 'show']);

        // Reviews
        Route::get('/reviews/{website_id}', [ReviewController::class, 'show']);

        // Navigation
        Route::get('/categories-with-items', [NavigationController::class, 'getCategoriesWithItems']);

        // Item Skins
        Route::get('/item-skins/{item_name}', [ItemController::class, 'getItemSkins']);
        Route::get('/item-skin/search', [ItemController::class, 'search']);
        Route::get('/historical', [HistoricalPriceController::class, 'getAllHistoricalPrices']);

        // Authentication (Steam)
        Route::post('/auth/steam/callback', [AuthController::class, 'handleSteamCallback']);

        // Popular Items
        Route::get('/popular-items', [PopularItemController::class, 'getMostViewedItems']);

        // Unauthenticated Item Skin (if no auth is required)
        Route::get('/item-skin', [ItemController::class, 'getItemSkin'])->name('item-skin.unauthenticated');
    });
