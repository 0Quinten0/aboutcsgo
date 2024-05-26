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
        Schema::create('item_skin', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('skin_id');
            $table->unsignedBigInteger('quality_id');
            $table->boolean('stattrak')->default(false);
            $table->boolean('souvenir')->default(false);
            $table->string('description', 500);
            $table->string('image_url', 255);

            $table->timestamps();
    
            // Foreign keys
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('skin_id')->references('id')->on('skins')->onDelete('cascade');
            $table->foreign('quality_id')->references('id')->on('qualities')->onDelete('cascade');
            
            // Unique constraint to avoid duplicate combinations
            $table->unique(['item_id', 'skin_id', 'quality_id'], 'weapon_skin_quality_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_skin');
    }
};
