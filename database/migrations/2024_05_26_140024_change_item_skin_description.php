<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('item_skin', function (Blueprint $table) {
            $table->string('description', 600)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('item_skin', function (Blueprint $table) {
            // Assuming the original length was 500, we revert it back to 500
            $table->string('description', 500)->change();
        });
    }
};
