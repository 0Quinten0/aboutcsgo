<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('website_id');
            $table->foreign('website_id')->references('id')->on('websites')->onDelete('cascade');
            $table->text('introduction')->nullable();
            $table->text('what_offers_content')->nullable();
            $table->string('what_offers_image')->nullable();
            $table->text('faq_questions_and_answers')->nullable();
            $table->text('review_pros')->nullable();
            $table->text('review_cons')->nullable();
            $table->text('safety_and_transparency_content')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
