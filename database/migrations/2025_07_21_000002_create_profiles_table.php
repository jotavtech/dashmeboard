<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('username')->unique();
            $table->string('mood')->nullable();
            $table->text('public_agenda')->nullable();
            $table->text('private_agenda')->nullable();
            $table->string('daily_music')->nullable();
            $table->string('profession')->nullable();
            $table->text('bio')->nullable();
            $table->text('fortune_cookie_message')->nullable();
            $table->boolean('is_public')->default(true);
            $table->string('profile_image')->nullable();
            $table->string('background_image')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
}; 