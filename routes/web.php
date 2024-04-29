<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\ReviewController;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/websites', [WebsiteController::class, 'index']);
Route::get('/websites/filter', [WebsiteController::class, 'filter']);
Route::get('/payment-methods', [WebsiteController::class, 'getPaymentMethods']);
Route::get('/games', [WebsiteController::class, 'getGames']);
Route::get('/reviews/{website_id}', [ReviewController::class, 'show']);
Route::get('/websites/{id}', [WebsiteController::class, 'show']);

