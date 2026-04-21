<?php

// 1. Paksa penggunaan direktori /tmp untuk semua cache
putenv('VIEW_COMPILED_PATH=/tmp');
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_EVENTS_CACHE=/tmp/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('APP_SERVICES_CACHE=/tmp/services.php');

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 2. Load Autoload
require __DIR__ . '/../vendor/autoload.php';

// 3. Boot Application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 4. PAKSA PATH PUBLIC (Kunci perbaikan Vite & Logo)
// Kita beri tahu Laravel bahwa folder public ada satu tingkat di atas folder api
$app->instance('path.public', __DIR__ . '/../public');

// 5. Paksa Laravel menggunakan /tmp untuk storage
$app->useStoragePath('/tmp');

// 6. Handle Request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);

// 7. Send Response
$response->send();

$kernel->terminate($request, $response);