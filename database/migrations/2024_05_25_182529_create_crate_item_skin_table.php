<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crate_item_skin', function (Blueprint $table) {
            $table->unsignedBigInteger('crate_id');
            $table->unsignedBigInteger('item_skin_id');
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('crate_id')->references('id')->on('crates')->onDelete('cascade');
            $table->foreign('item_skin_id')->references('id')->on('item_skin')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crate_item_skin');
    }
};
