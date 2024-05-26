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
        // Create the 'items' table
        Schema::create('item_price', function (Blueprint $table) {
            $table->id(); // Primary key

            // Assuming you have already defined these tables and their primary keys correctly
            $table->unsignedBigInteger('item_skin_id');
            $table->unsignedBigInteger('exterior_id');
            $table->unsignedBigInteger('type_id');
            $table->decimal('Bitskins_Value', 8, 2); // Adding the Value column
            $table->decimal('Skinport_Value', 8, 2); // Adding the Value column
            $table->decimal('Steam_Value', 8, 2); // Adding the Value column

            $table->timestamps(); // Laravel's default timestamp fields (created_at and updated_at)

            // Setting up foreign keys
            $table->foreign('item_skin_id')->references('id')->on('item_skin')->onDelete('cascade');
            $table->foreign('exterior_id')->references('id')->on('exteriors')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');

            // Optional: If you need to ensure combinations of these new fields are unique, you might consider adding a unique constraint.
            // $table->unique(['weapon_skin_quality_id', 'exterior_id', 'category_id'], 'unique_item_combinations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the 'items' table
        Schema::dropIfExists('item_price');
    }
};
