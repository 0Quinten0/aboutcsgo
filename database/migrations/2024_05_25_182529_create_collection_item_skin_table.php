<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectionItemSkinTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('collection_item_skin', function (Blueprint $table) {
            $table->unsignedBigInteger('collection_id');
            $table->unsignedBigInteger('item_skin_id');
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('collection_id')->references('id')->on('collections')->onDelete('cascade');
            $table->foreign('item_skin_id')->references('id')->on('item_skin')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_item_skin');
    }
}
