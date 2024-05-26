<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\NavigationController;
use App\Http\Controllers\ItemController;




Route::prefix('api')->group(function () {
    Route::get('/websites', [WebsiteController::class, 'index']);
    Route::get('/websites/filter', [WebsiteController::class, 'filter']);
    Route::get('/payment-methods', [WebsiteController::class, 'getPaymentMethods']);
    Route::get('/games', [WebsiteController::class, 'getGames']);
    Route::get('/reviews/{website_id}', [ReviewController::class, 'show']);
    Route::get('/websites/{id}', [WebsiteController::class, 'show']);
    Route::get('/categories-with-items', [NavigationController::class, 'getCategoriesWithItems']);
    Route::get('/item-skins/{item_name}', [ItemController::class, 'getItemSkins']);


});


