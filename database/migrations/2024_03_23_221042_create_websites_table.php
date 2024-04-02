<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebsitesTable extends Migration
{
    public function up()
    {
        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->string('image_url');
            $table->string('name');
            $table->decimal('rating', 5, 2);            
            $table->string('refferal_link');
            $table->string('bonus_code')->nullable();
            $table->integer('bonus_percentage')->nullable();
            $table->integer('bonus_max')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('websites');
    }
}
