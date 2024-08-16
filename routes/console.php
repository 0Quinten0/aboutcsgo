<?php

use Illuminate\Foundation\Inspiring;

use Illuminate\Support\Facades\Artisan;

// Schedule the existing command
Artisan::command('update:scheduler-item-updater', function () {
    // Call the existing command
    Artisan::call('update:prices');

    // Optionally, log or output something
    $this->info('Prices updated via scheduled command.');
})->everySixHours(); // Adjust frequency as needed


// Example for another command
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
