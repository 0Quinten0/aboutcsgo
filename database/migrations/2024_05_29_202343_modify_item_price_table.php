<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyItemPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_price', function (Blueprint $table) {
            $table->decimal('Bitskins_Value', 8, 2)->nullable()->change();
            $table->decimal('Skinport_Value', 8, 2)->nullable()->change();
            $table->decimal('Steam_Value', 8, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_price', function (Blueprint $table) {
            $table->decimal('Bitskins_Value', 8, 2)->change();
            $table->decimal('Skinport_Value', 8, 2)->change();
            $table->decimal('Steam_Value', 8, 2)->change();
        });
    }
}
