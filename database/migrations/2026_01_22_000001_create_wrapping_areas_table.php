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
        Schema::create('wrapping_areas', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('meta_title');
            $table->text('meta_description');
            $table->json('keywords')->nullable();
            $table->string('main_heading');
            $table->text('main_description');
            $table->string('main_image')->nullable();
            $table->string('why_partner_heading');
            $table->text('why_partner_description');
            $table->string('why_partner_image')->nullable();
            $table->json('features')->nullable(); // Array of {title, description}
            $table->string('guide_heading');
            $table->text('guide_description');
            $table->json('guide')->nullable(); // Array of guide items
            $table->string('why_use_heading');
            $table->text('why_use_description');
            $table->string('hero_text');
            $table->text('hero_subtext')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('gallery_heading');
            $table->text('gallery_description');
            $table->json('photos')->nullable(); // Array of {src, alt}
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps(); 
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wrapping_areas');
    }
};
