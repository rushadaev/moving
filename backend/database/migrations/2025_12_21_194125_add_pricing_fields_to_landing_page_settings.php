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
        Schema::table('landing_page_settings', function (Blueprint $table) {
            // Additional fees
            $table->decimal('floor_fee', 8, 2)->default(0)->after('hourly_rate');
            $table->decimal('transportation_fee_per_mile', 8, 2)->default(2.00)->after('floor_fee');

            // Packing materials prices
            $table->decimal('small_box_price', 8, 2)->default(3.00)->after('transportation_fee_per_mile');
            $table->decimal('medium_box_price', 8, 2)->default(5.00)->after('small_box_price');
            $table->decimal('large_box_price', 8, 2)->default(7.00)->after('medium_box_price');
            $table->decimal('wardrobe_box_price', 8, 2)->default(12.00)->after('large_box_price');
            $table->decimal('paper_price', 8, 2)->default(6.00)->after('wardrobe_box_price');
            $table->decimal('plastic_tape_price', 8, 2)->default(4.00)->after('paper_price');
            $table->decimal('bubble_wrap_price', 8, 2)->default(10.00)->after('plastic_tape_price');

            // Special services
            $table->decimal('piano_fee', 8, 2)->default(0)->after('bubble_wrap_price');
            $table->decimal('gun_safe_fee', 8, 2)->default(0)->after('piano_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_page_settings', function (Blueprint $table) {
            $table->dropColumn([
                'floor_fee',
                'transportation_fee_per_mile',
                'small_box_price',
                'medium_box_price',
                'large_box_price',
                'wardrobe_box_price',
                'paper_price',
                'plastic_tape_price',
                'bubble_wrap_price',
                'piano_fee',
                'gun_safe_fee',
            ]);
        });
    }
};
