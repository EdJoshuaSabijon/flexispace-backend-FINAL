<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$products = Product::orderBy('id', 'asc')->get();
foreach ($products as $product) {
    echo "ID: {$product->id} | Name: [{$product->name}] | Description: [{$product->description}] | Image Path: [{$product->image_path}]\n";
}
