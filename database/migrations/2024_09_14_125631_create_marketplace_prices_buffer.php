<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_prices_buffer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_price_id')->constrained('item_prices');
            $table->foreignId('marketplace_id')->constrained('marketplaces'); // Optional: If you’re storing marketplace-specific prices.
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_prices_buffer');
    }
};
