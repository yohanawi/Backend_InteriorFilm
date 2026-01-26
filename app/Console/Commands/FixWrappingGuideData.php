<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixWrappingGuideData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wrapping:fix-guide-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix wrapping area guide data structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $areas = \App\Models\WrappingArea::all();

        foreach ($areas as $area) {
            $guide = $area->guide ?? [];
            $updated = false;

            foreach ($guide as $idx => $item) {
                if (!isset($item['heading'])) {
                    $guide[$idx]['heading'] = 'Step ' . ($idx + 1);
                    $updated = true;
                }
                if (!isset($item['subheading'])) {
                    $guide[$idx]['subheading'] = '';
                    $updated = true;
                }
                if (!isset($item['features'])) {
                    $guide[$idx]['features'] = [];
                    $updated = true;
                }
            }

            if ($updated) {
                $area->guide = $guide;
                $area->save();
                $this->info("Updated: {$area->title}");
            }
        }

        $this->info("Done! Checked {$areas->count()} wrapping areas.");
        return 0;
    }
}
