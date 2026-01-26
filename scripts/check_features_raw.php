<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductSpecification;

$spec = ProductSpecification::query()->with('product')->latest('id')->first();

if (!$spec) {
    echo "no_spec\n";
    exit(0);
}

echo "product_slug=" . ($spec->product->slug ?? '') . "\n";

$raw = $spec->getRawOriginal('features');
if (is_string($raw)) {
    echo "features_raw={$raw}\n";
} else {
    echo "features_raw=" . json_encode($raw, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
}
