<?php
// database/migrations/xxxx_xx_xx_create_sticker_sticker_capsule_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStickerStickerCapsuleTable extends Migration
{
    public function up()
    {
        Schema::create('sticker_sticker_capsule', function (Blueprint $table) {
            $table->unsignedBigInteger('sticker_id');
            $table->unsignedBigInteger('sticker_capsule_id');
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('sticker_id')->references('id')->on('stickers')->onDelete('cascade');
            $table->foreign('sticker_capsule_id')->references('id')->on('sticker_capsules')->onDelete('cascade');

       });
    }

    public function down()
    {
        Schema::dropIfExists('sticker_sticker_capsule');
    }
}
