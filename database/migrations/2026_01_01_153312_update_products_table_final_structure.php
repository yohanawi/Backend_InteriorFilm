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
        Schema::table('products', function (Blueprint $table) {
            // Remove stock_shelf column if it exists
            if (Schema::hasColumn('products', 'stock_shelf')) {
                $table->dropColumn('stock_shelf');
            }

            // Ensure tax_class_id is integer (not bigint) if it exists
            if (Schema::hasColumn('products', 'tax_class_id')) {
                $table->integer('tax_class_id')->nullable()->change();
            }

            // Ensure all columns are in correct order and type
            // Note: weight is decimal(10,3), others are decimal(10,2)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add back stock_shelf if needed
            if (!Schema::hasColumn('products', 'stock_shelf')) {
                $table->unsignedInteger('stock_shelf')->default(0)->after('sku');
            }
        });
    }
};
