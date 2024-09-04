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
        Schema::table('marketplaces', function (Blueprint $table) {
            $table->string('pretty_name')->nullable()->after('name');
            $table->string('image_url')->nullable()->after('pretty_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketplaces', function (Blueprint $table) {
            $table->dropColumn('pretty_name');
            $table->dropColumn('image_url');
        });
    }
};
