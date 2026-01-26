<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixProductThumbnails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:fix-thumbnails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix product thumbnail and image paths to use absolute URLs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $products = \App\Models\Product::all();
        $updated = 0;

        foreach ($products as $product) {
            $changed = false;

            // Fix thumbnail
            if ($product->getRawOriginal('thumbnail')) {
                $thumb = $product->getRawOriginal('thumbnail');
                if (!str_starts_with($thumb, '/storage/') && !str_starts_with($thumb, 'http')) {
                    $thumb = '/storage/' . ltrim($thumb, '/');
                    $product->setRawAttributes(array_merge($product->getAttributes(), ['thumbnail' => $thumb]));
                    $changed = true;
                }
            }

            // Fix images array
            $images = $product->getRawOriginal('images');
            if ($images) {
                $decoded = json_decode($images, true);
                if (is_array($decoded)) {
                    $fixed = array_map(function ($img) {
                        if (!str_starts_with($img, '/storage/') && !str_starts_with($img, 'http')) {
                            return '/storage/' . ltrim($img, '/');
                        }
                        return $img;
                    }, $decoded);

                    if ($fixed !== $decoded) {
                        $product->setRawAttributes(array_merge($product->getAttributes(), ['images' => json_encode($fixed)]));
                        $changed = true;
                    }
                }
            }

            if ($changed) {
                $product->saveQuietly();
                $updated++;
                $this->info("Updated: {$product->name}");
            }
        }

        $this->info("Done! Updated {$updated} products out of {$products->count()}.");
        return 0;
    }
}
