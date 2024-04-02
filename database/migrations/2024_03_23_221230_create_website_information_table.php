<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebsiteInformationTable extends Migration
{
    public function up()
    {
        Schema::create('website_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->cascadeOnDelete();
            $table->string('information');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('website_information');
    }
}

