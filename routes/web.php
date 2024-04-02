<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebsiteController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/websites', [WebsiteController::class, 'index']);
Route::get('/websites/filter', [WebsiteController::class, 'filter']);
Route::get('/payment-methods', [WebsiteController::class, 'getPaymentMethods']);
Route::get('/games', [WebsiteController::class, 'getGames']);