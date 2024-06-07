<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPricesToStickersTable extends Migration
{
    public function up()
    {
        Schema::table('stickers', function (Blueprint $table) {
            $table->decimal('bitskin_price', 8, 2)->nullable();
            $table->decimal('skinport_price', 8, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::table('stickers', function (Blueprint $table) {
            $table->dropColumn('bitskin_price');
            $table->dropColumn('skinport_price');
        });
    }
}
