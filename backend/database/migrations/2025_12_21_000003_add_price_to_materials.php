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
        Schema::table('materials', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->default(0)->after('quantity');
            $table->foreignId('packing_material_id')->nullable()->after('request_id')
                ->constrained('packing_materials')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropForeign(['packing_material_id']);
            $table->dropColumn(['price', 'packing_material_id']);
        });
    }
};
