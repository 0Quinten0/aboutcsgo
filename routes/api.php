<?php

use Illuminate\Http\Request;
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






Route::prefix('api')->group(function () {
    Route::get('/websites', [WebsiteController::class, 'index']);
    Route::get('/websites/filter', [WebsiteController::class, 'filter']);
    Route::get('/payment-methods', [WebsiteController::class, 'getPaymentMethods']);
    Route::get('/games', [WebsiteController::class, 'getGames']);
    Route::get('/reviews/{website_id}', [ReviewController::class, 'show']);
    Route::get('/websites/{id}', [WebsiteController::class, 'show']);
    Route::get('/categories-with-items', [NavigationController::class, 'getCategoriesWithItems']);
    Route::get('/item-skins/{item_name}', [ItemController::class, 'getItemSkins']);
    Route::post('/auth/steam/callback', [AuthController::class, 'handleSteamCallback']);
    Route::get('/popular-items', [PopularItemController::class, 'getMostViewedItems']);
    Route::get('/item-skin/search', [ItemController::class, 'search']);
    Route::get('/historical/{item_price_id}', [HistoricalPriceController::class, 'getAllHistoricalPrigit ces']);






});

Route::get('/item-skin', [ItemController::class, 'getItemSkin'])
    ->middleware('auth:sanctum')
    ->name('item-skin.authenticated');

Route::get('/item-skin', [ItemController::class, 'getItemSkin'])
    ->name('item-skin.unauthenticated');

    Route::get('/stickers/search', [StickerController::class, 'search'])
    ->middleware('auth:sanctum')
    ->name('stickers.search.authenticated');

// Route accessible without authentication
Route::get('/stickers/search', [StickerController::class, 'search'])
->name('stickers.search.unauthenticated');



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/votes', [VoteController::class, 'store']);
    Route::get('/votes', [VoteController::class, 'index']);
    Route::delete('/votes', [VoteController::class, 'destroy']);
    Route::get('/stickers/search', [StickerController::class, 'search']);


});


