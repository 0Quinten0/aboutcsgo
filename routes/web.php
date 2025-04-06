<?php

// routes/web.php
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InertiaController;

Route::get('/', [InertiaController::class, 'home']);
Route::get('/weapon/{weaponName}', [InertiaController::class, 'weapon'])->name('weapon.show');
Route::get('/skin/{weaponName}/{skinName}', [InertiaController::class, 'skin'])->name('skin.show');

Route::fallback(function () {
    return Inertia::render('NotFoundPage');
});

Route::get('/privacy-policy', function () {
    return Inertia::render('PrivacyPolicyPage');
})->name('privacy-policy');


Route::get('/terms-of-service', function () {
    return Inertia::render('TermsOfServicePage');
})->name('terms-of-service');
