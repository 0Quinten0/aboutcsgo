<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebsiteGamesTable extends Migration
{
    public function up()
    {
        Schema::create('website_games', function (Blueprint $table) {
            $table->foreignId('website_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->primary(['website_id', 'game_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('website_games');
    }
}

