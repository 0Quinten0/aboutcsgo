<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Check if the table exists
        Schema::dropIfExists('crates');

        // Recreate the table with the new structure
        Schema::create('crates', function (Blueprint $table) {
            $table->id(); 
            $table->string('name', 255);
            $table->string('image_url', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Drop the table
        Schema::dropIfExists('crates');
    }
};
