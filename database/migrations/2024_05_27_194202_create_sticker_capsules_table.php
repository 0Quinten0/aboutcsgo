<?php

// database/migrations/xxxx_xx_xx_create_sticker_capsules_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStickerCapsulesTable extends Migration
{
    public function up()
    {
        Schema::create('sticker_capsules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sticker_capsules');
    }
}
