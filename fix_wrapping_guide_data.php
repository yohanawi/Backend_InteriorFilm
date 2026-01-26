<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$areas = App\Models\WrappingArea::all();

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
        echo "Updated: {$area->title}\n";
    }
}

echo "\nDone! Updated " . $areas->count() . " wrapping areas.\n";
