<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $column) {
            // Menambahkan kolom price dan stock setelah kolom outlet_id
            $column->decimal('price', 15, 2)->default(0)->after('outlet_id');
            $column->integer('stock')->default(0)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $column) {
            $column->dropColumn(['price', 'stock']);
        });
    }
};
