<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActivePriceToItemPricesTable extends Migration
{
    public function up()
    {
        Schema::table('item_price', function (Blueprint $table) {
            $table->boolean('active_price')->default(true); // Add column with default value true
        });
    }

    public function down()
    {
        Schema::table('item_price', function (Blueprint $table) {
            $table->dropColumn('active_price'); // Remove column if rollback is needed
        });
    }
}

