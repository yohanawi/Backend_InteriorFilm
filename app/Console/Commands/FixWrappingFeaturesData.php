<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixWrappingFeaturesData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wrapping:fix-features-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix wrapping area features data structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $areas = \App\Models\WrappingArea::all();

        foreach ($areas as $area) {
            $features = $area->features ?? [];
            $updated = false;

            foreach ($features as $idx => $feature) {
                if (!isset($feature['title'])) {
                    $features[$idx]['title'] = '';
                    $updated = true;
                }
                if (!isset($feature['description'])) {
                    $features[$idx]['description'] = '';
                    $updated = true;
                }
            }

            $photos = $area->photos ?? [];
            foreach ($photos as $idx => $photo) {
                if (!isset($photo['alt'])) {
                    $photos[$idx]['alt'] = $area->title . ' - Image ' . ($idx + 1);
                    $updated = true;
                }
                if (!isset($photo['src'])) {
                    $photos[$idx]['src'] = '';
                    $updated = true;
                }
            }

            if ($updated) {
                if (count($features) > 0) {
                    $area->features = $features;
                }
                if (count($photos) > 0) {
                    $area->photos = $photos;
                }
                $area->save();
                $this->info("Updated: {$area->title}");
            }
        }

        $this->info("Done! Checked {$areas->count()} wrapping areas.");
        return 0;
    }
}
