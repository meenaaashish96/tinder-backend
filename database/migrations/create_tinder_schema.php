<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. The 'Opponents' or Profiles to swipe on
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('age');
            $table->string('location');
            $table->text('bio')->nullable();
            $table->timestamps();
        });

        // 2. Images for the profiles (One Profile -> Many Images)
        Schema::create('profile_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('profiles')->onDelete('cascade');
            $table->string('image_url');
            $table->timestamps();
        });

        // 3. Swipes (Tracks if User A liked Profile B)
        Schema::create('swipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('profile_id')->constrained('profiles')->onDelete('cascade');
            $table->boolean('is_like'); // true = like, false = nope
            $table->timestamps();

            // Prevent duplicate swipes on same person
            $table->unique(['user_id', 'profile_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('swipes');
        Schema::dropIfExists('profile_images');
        Schema::dropIfExists('profiles');
    }
};