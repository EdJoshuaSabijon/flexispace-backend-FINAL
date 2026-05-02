<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = Illuminate\Http\Request::create('/api/admin/financial/export', 'GET', ['period' => 'weekly']);
$controller = new App\Http\Controllers\Admin\FinancialController();
try {
    $response = $controller->getExportData($request);
    echo $response->getContent();
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
