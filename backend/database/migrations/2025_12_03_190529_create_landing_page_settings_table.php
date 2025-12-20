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
        Schema::create('landing_page_settings', function (Blueprint $table) {
            $table->id();

            // Header Section
            $table->string('logo')->nullable();
            $table->string('company_name')->default('MOOWEE');
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();

            // Photo Section
            $table->string('photo_title')->default('Unloaded photo');
            $table->string('photo_url')->nullable();

            // Video Section
            $table->string('video_title')->default('Unloaded video');
            $table->string('video_url')->nullable();

            // Contact Information
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('youtube_url')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_settings');
    }
};
