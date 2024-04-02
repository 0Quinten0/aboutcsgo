<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebsitePaymentMethodsTable extends Migration
{
    public function up()
    {
        Schema::create('website_payment_methods', function (Blueprint $table) {
            $table->foreignId('website_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_method_id')->constrained()->cascadeOnDelete();
            $table->primary(['website_id', 'payment_method_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('website_payment_methods');
    }
}

