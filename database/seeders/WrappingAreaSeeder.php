<?php

namespace Database\Seeders;

use App\Models\WrappingArea;
use Illuminate\Database\Seeder;

class WrappingAreaSeeder extends Seeder 
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wrappingAreas = [
            [
                'slug' => 'kitchen-wrapping',
                'title' => 'Kitchen Wrapping',
                'meta_title' => 'Kitchen Cabinet Wrapping | KOINTEC Interior Films UAE',
                'meta_description' => 'Transform your kitchen with premium KOINTEC kitchen cabinet wrapping films. Professional-grade vinyl wraps for kitchen cabinets, countertops, and more in the UAE.',
                'keywords' => [
                    'kitchen cabinet wrapping UAE',
                    'kitchen wrapping films',
                    'cabinet vinyl wrap Dubai',
                    'KOINTEC kitchen films',
                    'kitchen renovation UAE',
                    'cabinet refacing Dubai',
                    'kitchen wrap installer UAE',
                ],
                'main_heading' => 'Premium Korean Interior Films for Kitchen Wrapping & Renovation',
                'main_description' => 'Are you a kitchen wrapping or interior renovation company looking for premium interior films imported from Korea? Our high-performance vinyl films give your clients stunning results, with finishes and durability that make every project stand out—without the cost and hassle of traditional materials.',
                'main_image' => '/assets/images/kitchen01.png',
                'why_partner_heading' => 'Why Partner With Us for Kitchen Wrapping',
                'why_partner_description' => 'Our Korean-made interior films are engineered for professional kitchen wrap installers, offering:',
                'why_partner_image' => '/assets/images/kitchen02.jpg',
                'features' => [
                    [
                        'title' => 'Easy to Install',
                        'description' => 'Self-adhesive backing allows for quick, bubble-free application on cabinet surfaces.',
                    ],
                    [
                        'title' => 'Durable & Waterproof',
                        'description' => 'Engineered to withstand daily wear, moisture, and heat—ideal for kitchens.',
                    ],
                    [
                        'title' => 'Premium Finishes',
                        'description' => 'Choose from a wide range of textures and colors to match any kitchen design vision.',
                    ],
                    [
                        'title' => 'Heat Resistant',
                        'description' => 'Specially formulated to resist heat near cooking areas and appliances.',
                    ],
                    [
                        'title' => 'Stain Resistant',
                        'description' => 'Protects against kitchen stains, grease, and everyday spills.',
                    ],
                    [
                        'title' => 'Cost-Effective',
                        'description' => 'The kitchen wrap cost is significantly lower while delivering high-end visual results.',
                    ],
                ],
                'guide_heading' => 'Kitchen Wrapping Application Guide',
                'guide_description' => 'See how our premium architectural film can be utilized to achieve professional-grade kitchen refurbishments in every corner.',
                'guide' => [
                    [
                        'image' => '/assets/images/kitchen03.jpg',
                        'heading' => 'Seamless Kitchen Cabinet Wrapping',
                        'subheading' => 'Transform Old Cabinets Instantly',
                        'description' => 'Give kitchens a fresh, modern look with our films—no need for costly replacements. Perfect for cabinet doors, drawers, and panels.',
                        'features' => [
                            ['title' => 'Quick Installation'],
                            ['title' => 'Stain & Water Resistant'],
                            ['title' => 'Easy Maintenance'],
                        ],
                    ],
                    [
                        'image' => '/assets/images/kitchen04.jpg',
                        'heading' => 'Kitchen Island & Countertop Wrapping',
                        'subheading' => 'Revitalize Surfaces Effortlessly',
                        'description' => 'Wrap kitchen islands, countertops, and breakfast bars for a cohesive new look. Our films adhere to wood, MDF, and laminate surfaces.',
                        'features' => [
                            ['title' => 'Scratch Resistant'],
                            ['title' => 'Customizable Styles'],
                            ['title' => 'Food-Safe Coating'],
                        ],
                    ],
                    [
                        'image' => '/assets/images/kitchen05.jpg',
                        'heading' => 'Kitchen Walls & Backsplash',
                        'subheading' => 'Complete Kitchen Transformation',
                        'description' => 'Apply to kitchen walls and backsplash areas for a premium finish throughout the entire space.',
                        'features' => [
                            ['title' => 'Large Surface Coverage'],
                            ['title' => 'Fade Resistant'],
                            ['title' => 'Easy to Clean'],
                        ],
                    ],
                ],
                'why_use_heading' => 'Why Use Kointec Kitchen Wrapping?',
                'why_use_description' => 'All our films are imported directly from Korea—renowned globally for architectural and interior vinyl quality. These materials give your kitchen installation business the edge: better performance, richer finishes, and happier customers.',
                'hero_text' => 'Premium Korean Materials You Can Trust',
                'hero_subtext' => 'Recommended Wrappings for Kitchen',
                'hero_image' => '/assets/images/kitchen06.webp',
                'gallery_heading' => 'Kitchen Wrapping Styles & Visual Concepts',
                'gallery_description' => 'Discover a variety of kitchen styles where our premium wrapping solutions can transform interiors. From modern to classic kitchens, our gallery showcases the versatility and beauty of our work.',
                'photos' => [
                    ['src' => '/assets/images/01.jpg', 'alt' => 'Kitchen Wrapping Style 1'],
                    ['src' => '/assets/images/01.jpg', 'alt' => 'Kitchen Wrapping Style 2'],
                    ['src' => '/assets/images/01.jpg', 'alt' => 'Kitchen Wrapping Style 3'],
                    ['src' => '/assets/images/01.jpg', 'alt' => 'Kitchen Wrapping Style 4'],
                    ['src' => '/assets/images/01.jpg', 'alt' => 'Kitchen Wrapping Style 5'],
                    ['src' => '/assets/images/01.jpg', 'alt' => 'Kitchen Wrapping Style 6'],
                    ['src' => '/assets/images/01.jpg', 'alt' => 'Kitchen Wrapping Style 7'],
                    ['src' => '/assets/images/01.jpg', 'alt' => 'Kitchen Wrapping Style 8'],
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'bathroom-wrapping',
                'title' => 'Bathroom Wrapping',
                'meta_title' => 'Bathroom Cabinet Wrapping | KOINTEC Interior Films UAE',
                'meta_description' => 'Transform your Bathroom with premium KOINTEC Bathroom cabinet wrapping films. Professional-grade vinyl wraps for Bathroom cabinets, countertops, and more in the UAE.',
                'keywords' => [
                    'bathroom cabinet wrapping UAE',
                    'bathroom wrapping films',
                    'cabinet vinyl wrap Dubai',
                    'KOINTEC bathroom films',
                    'bathroom renovation UAE',
                    'cabinet refacing Dubai',
                    'bathroom wrap installer UAE',
                ],
                'main_heading' => 'Premium Korean Interior Films for Bathroom Wrapping & Renovation',
                'main_description' => 'Are you a Bathroom wrapping or interior renovation company looking for premium interior films imported from Korea? Our high-performance vinyl films give your clients stunning results, with finishes and durability that make every project stand out—without the cost and hassle of traditional materials.',
                'main_image' => '/assets/images/kitchen01.png',
                'why_partner_heading' => 'Why Partner With Us for Bathroom Wrapping',
                'why_partner_description' => 'Our Korean-made interior films are engineered for professional Bathroom wrap installers, offering:',
                'why_partner_image' => '/assets/images/kitchen02.jpg',
                'features' => [
                    [
                        'title' => 'Easy Bathroom Wrap Installation',
                        'description' => 'Our self-adhesive vinyl wrap films allow quick, smooth, and bubble-free installation.',
                    ],
                    [
                        'title' => 'Waterproof & Moisture Resistant',
                        'description' => 'Our interior films are 100% water-resistant, protecting surfaces from humidity.',
                    ],
                    [
                        'title' => 'Premium Vinyl Wrap Finishes',
                        'description' => 'Choose from a wide range of vinyl wrap designs, including marble, wood and matte.',
                    ],
                    [
                        'title' => 'Heat & Steam Resistant',
                        'description' => 'Our vinyl wrap films are formulated to withstand heat and steam from showers.',
                    ],
                    [
                        'title' => 'Stain & Chemical Resistant',
                        'description' => 'Resists soap marks, water stains, cleaning chemicals, and everyday spills.',
                    ],
                    [
                        'title' => 'Cost-Effective Bathroom Renovation',
                        'description' => 'Upgrade at a fraction of the cost of tiles replacement or traditional renovation.',
                    ],
                ],
                'guide_heading' => 'Bathroom Wrapping Application Guide',
                'guide_description' => 'See how our premium architectural film can be utilized to achieve professional-grade bathroom refurbishments in every corner.',
                'guide' => [
                    [
                        'image' => '/assets/images/kitchen03.jpg',
                        'heading' => 'Bathroom Countertop Wrapping',
                        'subheading' => 'Upgrade Your Bathroom Without Renovation',
                        'description' => 'Transform outdated bathroom countertops with our bathroom countertop wrapping service, designed to deliver a fresh, modern finish without the cost or disruption of replacement. Using high-quality waterproof vinyl interior films, we wrap existing countertops to achieve a seamless, stone-like or solid finish in just a short time.',
                        'features' => [
                            ['title' => 'Waterproof'],
                            ['title' => 'Scratch & stain resistant'],
                            ['title' => 'Cost-effective renovation'],
                        ],
                    ],
                    [
                        'image' => '/assets/images/kitchen04.jpg',
                        'heading' => 'Bathroom Sink Wrapping',
                        'subheading' => 'Modern Sink Finishes with Waterproof Vinyl Wraps',
                        'description' => 'Our bathroom sink wrapping service allows you to refresh sinks, vanity units, and surrounding surfaces using waterproof vinyl wraps specifically designed for wet areas. This solution is perfect for upgrading bathroom sinks without removing existing fixtures.',
                        'features' => [
                            ['title' => 'Humidity-resistant materials'],
                            ['title' => 'Easy to clean'],
                            ['title' => 'Ideal for residential'],
                        ],
                    ],
                    [
                        'image' => '/assets/images/kitchen05.jpg',
                        'heading' => 'Bathroom Cabinet Wrapping',
                        'subheading' => 'Revitalize Bathroom Cabinets Instantly',
                        'description' => 'Give your bathroom cabinets a brand-new look with our bathroom cabinet wrapping solutions. Designed for high-use and moisture-prone environments, our premium vinyl interior films restore tired cabinets with durable, modern finishes.',
                        'features' => [
                            ['title' => 'Durable & moisture-resistant'],
                            ['title' => 'Perfect for daily use'],
                            ['title' => 'Wide range of styles'],
                        ],
                    ],
                ],
                'why_use_heading' => 'Why Use Kointec Bathroom Wrapping?',
                'why_use_description' => 'All our bathroom wrapping vinyl films are imported directly from Korea, globally recognized for premium architectural interior film quality. Designed for professional bathroom vinyl wrap installations in Dubai, our materials deliver superior durability, refined finishes, and long-term performance in high-moisture environments.',
                'hero_text' => 'Premium Korean Materials You Can Trust',
                'hero_subtext' => 'Recommended Wrappings for Bathroom',
                'hero_image' => '/assets/images/kitchen06.webp',
                'gallery_heading' => 'Bathroom Wrapping Styles & Visual Concepts',
                'gallery_description' => 'Explore a wide range of bathroom wrapping styles using our premium vinyl wrap and interior film solutions. From sleek modern bathrooms to timeless classic designs.',
                'photos' => [
                    ['src' => '/assets/images/01.jpg', 'alt' => 'Bathroom Wrapping Style 1'],
                    ['src' => '/assets/images/01.jpg', 'alt' => 'Bathroom Wrapping Style 2'],
                    ['src' => '/assets/images/01.jpg', 'alt' => 'Bathroom Wrapping Style 3'],
                    ['src' => '/assets/images/01.jpg', 'alt' => 'Bathroom Wrapping Style 4'],
                    ['src' => '/assets/images/01.jpg', 'alt' => 'Bathroom Wrapping Style 5'],
                    ['src' => '/assets/images/01.jpg', 'alt' => 'Bathroom Wrapping Style 6'],
                    ['src' => '/assets/images/01.jpg', 'alt' => 'Bathroom Wrapping Style 7'],
                    ['src' => '/assets/images/01.jpg', 'alt' => 'Bathroom Wrapping Style 8'],
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
        ];

        foreach ($wrappingAreas as $area) {
            WrappingArea::create($area);
        }
    }
}
