<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\ProductSpecification;

$products = Product::count();
$specs = ProductSpecification::count();

echo "products={$products}\n";
echo "specs={$specs}\n";

$example = ProductSpecification::query()->with('product')->latest('id')->first();
if ($example) {
    echo "latest_spec_id={$example->id}\n";
    echo "latest_spec_product_id={$example->product_id}\n";
    echo "latest_spec_product_slug=" . ($example->product->slug ?? '') . "\n";
    echo "latest_spec_surface_finish=" . ($example->surface_finish ?? '') . "\n";
} else {
    echo "latest_spec_id=\n";
}
