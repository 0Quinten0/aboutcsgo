<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historical_prices_hourly', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_price_id')->constrained('item_prices'); // Reference to the item
            $table->decimal('avg_price', 10, 2); // Average price for the hour
            $table->decimal('lowest_price', 10, 2); // Lowest price for the hour
            $table->timestamp('hour'); // The hour this data represents
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historical_prices_hourly');
    }
};
