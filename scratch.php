<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('email', 'customer@flexispace.com')->first();
if (!$user) { echo "No user\n"; exit; }

$request = Illuminate\Http\Request::create('/api/profile', 'PUT', [
    'first_name' => 'Ed',
    'last_name' => 'Sabijon',
    'phone' => '01414',
    'address' => 'Ampayon, Butuan City',
    'latitude' => 8.9475,
    'longitude' => 125.5406
]);
$request->setUserResolver(function() use ($user) { return $user; });
$controller = new App\Http\Controllers\ProfileController();
try {
    $response = $controller->update($request);
    echo $response->getContent();
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
