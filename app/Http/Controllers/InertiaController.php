<?php

// app/Http/Controllers/InertiaController.php
namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class InertiaController extends Controller
{
    public function index(Request $request)
    {
        // Simply render the Home page without passing any data
        return Inertia::render('Home');
    }
}
