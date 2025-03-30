<?php

// routes/web.php
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InertiaController;

Route::get('/', [InertiaController::class, 'home']);
Route::get('/weapon/{weaponName}', [InertiaController::class, 'weapon'])->name('weapon.show');
Route::get('/skin/{weaponName}/{skinName}', [InertiaController::class, 'skin'])->name('skin.show');

