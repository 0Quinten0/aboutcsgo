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
        Schema::table('marketplace_prices', function (Blueprint $table) {
            // Drop the 'active' column from marketplace_prices
            $table->dropColumn('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketplace_prices', function (Blueprint $table) {
            // Add back the 'active' column in case we want to revert
            $table->boolean('active')->default(true);
        });
    }
};
