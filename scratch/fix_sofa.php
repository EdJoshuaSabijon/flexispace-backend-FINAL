<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$product = Product::find(2);
if ($product) {
    $product->name = 'Sofa';
    $product->description = 'wowowow';
    $product->image_path = null;
    $product->save();
    echo "Product ID 2 updated to 'Sofa' with description 'wowowow'\n";
} else {
    echo "Product ID 2 not found\n";
}
