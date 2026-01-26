<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Catalog;
use App\Models\Category;
use App\Models\Product;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Wood Catalog
        $woodCatalog = Catalog::create([
            'name' => 'Wood',
            'slug' => 'wood',
            'image' => null,
            'description' => 'High-quality wood finish films for a natural look.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Create Premium Wood Category
        $premiumWoodCategory = Category::create([
            'catalog_id' => $woodCatalog->id,
            'name' => 'Premium Wood',
            'slug' => 'premium-wood',
            'image' => null,
            'description' => 'Experience the elegance of premium wood finishes with our top-tier vinyl wraps.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Create Simple Wood Floor Vinyl Wrap Product
        Product::create([
            'category_id' => $premiumWoodCategory->id,
            'name' => 'Simple Wood Floor Vinyl Wrap',
            'slug' => 'simple-wood-floor-vinyl-wrap',
            'images' => [],
            'price' => 25.99,
            'description' => 'A high-quality vinyl wrap that mimics the look and feel of real wood flooring.',
            'is_featured' => true,
            'is_popular' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Create additional sample data
        $stoneCategory = Category::create([
            'catalog_id' => $woodCatalog->id,
            'name' => 'Classic Wood',
            'slug' => 'classic-wood',
            'image' => null,
            'description' => 'Traditional wood finishes for timeless appeal.',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Product::create([
            'category_id' => $stoneCategory->id,
            'name' => 'Oak Finish Vinyl Wrap',
            'slug' => 'oak-finish-vinyl-wrap',
            'images' => [],
            'price' => 22.99,
            'description' => 'Classic oak wood finish that brings warmth to any space.',
            'is_featured' => false,
            'is_popular' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Create another catalog
        $stoneCatalog = Catalog::create([
            'name' => 'Stone',
            'slug' => 'stone',
            'image' => null,
            'description' => 'Elegant stone finish films for modern and classic designs.',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $marbleCategory = Category::create([
            'catalog_id' => $stoneCatalog->id,
            'name' => 'Marble',
            'slug' => 'marble',
            'image' => null,
            'description' => 'Luxurious marble finishes for premium applications.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Product::create([
            'category_id' => $marbleCategory->id,
            'name' => 'White Marble Vinyl Wrap',
            'slug' => 'white-marble-vinyl-wrap',
            'images' => [],
            'price' => 35.99,
            'description' => 'Stunning white marble finish for a luxurious appearance.',
            'is_featured' => true,
            'is_popular' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->command->info('Catalog sample data seeded successfully!');
    }
}
