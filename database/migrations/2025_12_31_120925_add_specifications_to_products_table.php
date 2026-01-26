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
            // Certifications (stored as JSON array)
            $table->json('certifications')->nullable()->after('meta_keywords');

            // Product Features (stored as JSON array with name and image)
            $table->json('features')->nullable()->after('certifications');

            // Specifications
            $table->string('spec_dimensions')->nullable()->after('features');
            $table->string('surface_finish')->nullable()->after('spec_dimensions');
            $table->string('tensile_strength')->nullable()->after('surface_finish');
            $table->string('application_temperature')->nullable()->after('tensile_strength');
            $table->string('elongation')->nullable()->after('application_temperature');
            $table->string('service_temperature')->nullable()->after('elongation');
            $table->string('storage')->nullable()->after('service_temperature');
            $table->string('dimensional_stability')->nullable()->after('storage');
            $table->string('release_paper')->nullable()->after('dimensional_stability');
            $table->string('adhesive')->nullable()->after('release_paper');
            $table->string('adhesive_strength')->nullable()->after('adhesive');
            $table->string('shelf_life')->nullable()->after('adhesive_strength');
            $table->string('warranty')->nullable()->after('shelf_life');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'certifications',
                'features',
                'spec_dimensions',
                'surface_finish',
                'tensile_strength',
                'application_temperature',
                'elongation',
                'service_temperature',
                'storage',
                'dimensional_stability',
                'release_paper',
                'adhesive',
                'adhesive_strength',
                'shelf_life',
                'warranty',
            ]);
        });
    }
};
