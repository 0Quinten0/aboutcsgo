<?php

// database/migrations/xxxx_xx_xx_create_stickers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStickersTable extends Migration
{
    public function up()
    {
        Schema::create('stickers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('rarity_id');
            $table->string('rarity_name');
            $table->string('rarity_color');
            $table->string('tournament_event')->nullable();
            $table->string('tournament_team')->nullable();
            $table->string('market_hash_name')->nullable();
            $table->string('type')->nullable();
            $table->string('effect')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stickers');
    }
}
