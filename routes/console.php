<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// Schedule prices update every 10 minutes
Artisan::command('update:prices-scheduler', function () {
    Artisan::call('update:prices');
    $this->info('Prices updated via scheduled command.');
})->everyFifteenMinutes();

// Schedule sticker-list and skinweapon-list update every 24 hours
Artisan::command('update:daily-lists', function () {
    Artisan::call('update:sticker-list');
    Artisan::call('update:skinweapon-list');
    $this->info('Sticker list and skinweapon list updated via scheduled command.');
})->daily();

