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
        // Drop the existing table if it exists
        Schema::dropIfExists('item_price');

        // Create the new table with the updated structure
        Schema::create('item_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_skin_id');
            $table->unsignedBigInteger('exterior_id')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            

            
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('item_skin_id')->references('id')->on('item_skin')->onDelete('cascade');
            $table->foreign('exterior_id')->references('id')->on('exteriors')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');

            // These should reference marketplace_prices


            // $table->unsignedBigInteger('bitskins_active_price_id')->nullable();
            // $table->unsignedBigInteger('skinport_active_price_id')->nullable();
            // $table->unsignedBigInteger('steam_active_price_id')->nullable();
            // $table->foreign('bitskins_active_price_id')->references('id')->on('marketplace_prices')->onDelete('cascade');
            // $table->foreign('skinport_active_price_id')->references('id')->on('marketplace_prices')->onDelete('cascade');
            // $table->foreign('steam_active_price_id')->references('id')->on('marketplace_prices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the table in case of rollback
        Schema::dropIfExists('item_prices');
    }
};
