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
        Schema::create('product_specifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            // Certifications & Features
            $table->json('certifications')->nullable();
            $table->json('features')->nullable();

            // Dimensions & Physical Properties
            $table->string('spec_dimensions')->nullable();
            $table->string('surface_finish')->nullable();
            $table->string('tensile_strength')->nullable();
            $table->string('application_temperature')->nullable();
            $table->string('elongation')->nullable();
            $table->string('service_temperature')->nullable();

            // Storage & Material Properties
            $table->string('storage')->nullable();
            $table->string('dimensional_stability')->nullable();
            $table->string('release_paper')->nullable();
            $table->string('adhesive')->nullable();
            $table->string('adhesive_strength')->nullable();

            // Warranty & Lifecycle
            $table->string('shelf_life')->nullable();
            $table->string('warranty')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_specifications');
    }
};
