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
Schema::create('marketplace_prices', function (Blueprint $table) {
    $table->id();
    $table->foreignId('item_price_id')->constrained('item_prices');
    $table->foreignId('marketplace_id')->constrained('marketplaces'); // Table for marketplaces (Bitskins, Skinport, Steam)
    $table->decimal('price', 10, 2);
    $table->boolean('active')->default(true); // Indicates if the price is active or historical
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_prices');
    }
};
