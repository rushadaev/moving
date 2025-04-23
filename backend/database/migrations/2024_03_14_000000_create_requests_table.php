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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->enum('property_type', ['commercial', 'residential']);
            $table->decimal('square_feet', 10, 2)->nullable();
            $table->json('additional_objects')->nullable(); // ['garage', 'storage', 'backyard', 'guest_house', 'other']
            $table->integer('movers_count');
            $table->decimal('hourly_rate', 8, 2);
            $table->dateTime('departure_time');
            $table->boolean('labor_included')->default(false);
            $table->string('package_type')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('status')->default('pending'); // pending, confirmed, active, break, completed, cancelled
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('operator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
