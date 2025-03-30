<?php

// routes/web.php
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InertiaController;

Route::get('/', [InertiaController::class, 'home']);


