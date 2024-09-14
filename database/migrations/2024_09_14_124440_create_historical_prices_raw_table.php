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
        Schema::create('historical_prices_raw', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_price_id')->constrained('item_prices'); // Reference to the item
            $table->decimal('price', 10, 2); // Retrieved price
            $table->timestamp('retrieved_at'); // Time when the data was retrieved
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historical_prices_raw');
    }
};
