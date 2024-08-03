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
        Schema::create('views', function (Blueprint $table) {
            $table->id();
            $table->timestamp('viewed_at')->useCurrent();
            $table->unsignedBigInteger('item_skin_id');

            $table->foreign('item_skin_id')->references('id')->on('item_skin')->onDelete('cascade');


        });
    }

    public function down()
    {
        Schema::dropIfExists('views');
    }
};
