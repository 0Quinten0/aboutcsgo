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
        Schema::table('users', function (Blueprint $table) {
            // Remove unnecessary columns
            $table->dropColumn(['email', 'email_verified_at', 'password', 'remember_token']);

            // Add Steam-specific columns
            $table->string('steam_id')->unique();
            $table->string('nickname');
            $table->string('profile_url')->nullable();
            $table->string('avatar')->nullable();

            // Add any other necessary columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Restore previous columns
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            // Remove Steam-specific columns
            $table->dropColumn(['steam_id', 'nickname', 'profile_url', 'avatar']);

            // Add any other necessary columns
            $table->dropTimestamps();
        });
    }
};
