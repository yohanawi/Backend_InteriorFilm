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
            $table->foreignId('catalog_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('thumbnail')->nullable()->after('slug');

            $table->string('sku')->nullable()->after('description');
            $table->unsignedInteger('stock_shelf')->default(0)->after('sku');
            $table->unsignedInteger('stock_warehouse')->default(0)->after('stock_shelf');
            $table->boolean('allow_backorders')->default(false)->after('stock_warehouse');

            $table->string('status')->default('published')->after('allow_backorders');
            $table->timestamp('published_at')->nullable()->after('status');

            $table->string('discount_type')->default('none')->after('price');
            $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type'); 
            $table->decimal('vat', 10, 2)->nullable()->after('discount_value');
 
            $table->boolean('is_physical')->default(false)->after('vat');
            $table->decimal('weight', 10, 3)->nullable()->after('is_physical');
            $table->decimal('width', 10, 2)->nullable()->after('weight');
            $table->decimal('height', 10, 2)->nullable()->after('width');
            $table->decimal('length', 10, 2)->nullable()->after('height');

            $table->json('variations')->nullable()->after('length'); 
            $table->json('tags')->nullable()->after('variations');

            $table->string('meta_title')->nullable()->after('tags');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['catalog_id', 'category_id']);
            $table->index(['status', 'is_active']);
            $table->index(['is_featured', 'is_popular']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['catalog_id', 'category_id']);
            $table->dropIndex(['status', 'is_active']);
            $table->dropIndex(['is_featured', 'is_popular']);

            $table->dropConstrainedForeignId('catalog_id');

            $table->dropColumn([
                'thumbnail',
                'sku',
                'stock_shelf',
                'stock_warehouse',
                'allow_backorders',
                'status',
                'published_at',
                'discount_type',
                'discount_value',
                'vat',
                'is_physical',
                'weight',
                'width',
                'height',
                'length',
                'variations',
                'tags',
                'meta_title',
                'meta_description',
                'meta_keywords',
            ]);
        });
    }
};
