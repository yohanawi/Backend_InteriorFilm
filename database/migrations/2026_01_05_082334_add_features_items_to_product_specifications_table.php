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
        Schema::table('product_specifications', function (Blueprint $table) {
            // Add features_items JSON column for product feature icons (name and image)
            $table->json('features_items')->nullable()->after('features');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_specifications', function (Blueprint $table) {
            $table->dropColumn('features_items');
        });
    }
};
