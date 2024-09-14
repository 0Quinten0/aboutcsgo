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
        Schema::table('historical_prices_raw', function (Blueprint $table) {
            $table->foreignId('marketplace_id')->after('item_price_id')->constrained('marketplaces');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historical_prices_raw', function (Blueprint $table) {
            $table->dropForeign(['marketplace_id']);
            $table->dropColumn('marketplace_id');
        });
    }
};
