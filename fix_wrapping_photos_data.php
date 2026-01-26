<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$areas = App\Models\WrappingArea::all();

foreach ($areas as $area) {
    $photos = $area->photos ?? [];
    $updated = false;

    foreach ($photos as $idx => $photo) {
        if (!isset($photo['alt'])) {
            $photos[$idx]['alt'] = $area->title . ' - Image ' . ($idx + 1);
            $updated = true;
        }
    }

    if ($updated) {
        $area->photos = $photos;
        $area->save();
        echo "Updated photos: {$area->title}\n";
    }
}

echo "\nDone! Checked " . $areas->count() . " wrapping areas.\n";
