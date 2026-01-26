<?php

namespace App\Console\Commands;

use App\Models\WrappingArea;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class FixWrappingImagePaths extends Command
{
    protected $signature = 'wrapping:fix-image-paths';
    protected $description = 'Fix wrapping area image paths to remove /storage/ prefix';

    public function handle()
    {
        $this->info('Fixing wrapping area image paths...');

        $wrappingAreas = WrappingArea::all();
        $fixed = 0;

        foreach ($wrappingAreas as $area) {
            $changes = false;

            // Fix main_image
            if ($area->getRawOriginal('main_image') && Str::startsWith($area->getRawOriginal('main_image'), '/storage/')) {
                $newPath = Str::after($area->getRawOriginal('main_image'), '/storage/');
                $area->updateQuietly(['main_image' => $newPath]);
                $this->line("Fixed main_image for ID {$area->id}: {$newPath}");
                $changes = true;
            }

            // Fix why_partner_image
            if ($area->getRawOriginal('why_partner_image') && Str::startsWith($area->getRawOriginal('why_partner_image'), '/storage/')) {
                $newPath = Str::after($area->getRawOriginal('why_partner_image'), '/storage/');
                $area->updateQuietly(['why_partner_image' => $newPath]);
                $this->line("Fixed why_partner_image for ID {$area->id}: {$newPath}");
                $changes = true;
            }

            // Fix hero_image
            if ($area->getRawOriginal('hero_image') && Str::startsWith($area->getRawOriginal('hero_image'), '/storage/')) {
                $newPath = Str::after($area->getRawOriginal('hero_image'), '/storage/');
                $area->updateQuietly(['hero_image' => $newPath]);
                $this->line("Fixed hero_image for ID {$area->id}: {$newPath}");
                $changes = true;
            }

            // Fix guide images
            $guide = $area->getRawOriginal('guide');
            if ($guide) {
                $guideArray = is_string($guide) ? json_decode($guide, true) : $guide;
                if (is_array($guideArray)) {
                    $guideChanged = false;
                    foreach ($guideArray as $index => $item) {
                        if (isset($item['image']) && Str::startsWith($item['image'], '/storage/')) {
                            $guideArray[$index]['image'] = Str::after($item['image'], '/storage/');
                            $guideChanged = true;
                        }
                    }
                    if ($guideChanged) {
                        $area->updateQuietly(['guide' => $guideArray]);
                        $this->line("Fixed guide images for ID {$area->id}");
                        $changes = true;
                    }
                }
            }

            // Fix photos
            $photos = $area->getRawOriginal('photos');
            if ($photos) {
                $photosArray = is_string($photos) ? json_decode($photos, true) : $photos;
                if (is_array($photosArray)) {
                    $photosChanged = false;
                    foreach ($photosArray as $index => $photo) {
                        if (isset($photo['src']) && Str::startsWith($photo['src'], '/storage/')) {
                            $photosArray[$index]['src'] = Str::after($photo['src'], '/storage/');
                            $photosChanged = true;
                        }
                    }
                    if ($photosChanged) {
                        $area->updateQuietly(['photos' => $photosArray]);
                        $this->line("Fixed photos for ID {$area->id}");
                        $changes = true;
                    }
                }
            }

            if ($changes) {
                $fixed++;
            }
        }

        $this->info("Fixed {$fixed} wrapping area(s).");
        return 0;
    }
}
