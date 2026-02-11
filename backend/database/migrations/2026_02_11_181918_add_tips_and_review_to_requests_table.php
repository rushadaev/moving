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
        Schema::table('requests', function (Blueprint $table) {
            $table->decimal('tips_amount', 10, 2)->nullable()->after('price');
            $table->decimal('tips_percentage', 5, 2)->nullable()->after('tips_amount');
            $table->json('tips_distribution')->nullable()->after('tips_percentage')->comment('Tips distribution per mover');
            $table->string('tips_payment_status')->default('pending')->after('tips_distribution');
            $table->string('tips_stripe_session_id')->nullable()->after('tips_payment_status');
            $table->timestamp('completed_at')->nullable()->after('tips_stripe_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn([
                'tips_amount',
                'tips_percentage',
                'tips_distribution',
                'tips_payment_status',
                'tips_stripe_session_id',
                'completed_at'
            ]);
        });
    }
};
