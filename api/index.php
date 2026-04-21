<?php

// 1. Paksa penggunaan direktori /tmp untuk semua cache di lingkungan Vercel
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

// 4. Paksa Laravel untuk menggunakan path /tmp pada instance aplikasi
$app->useStoragePath('/tmp');

// 5. Handle Request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);

// 6. Send Response
$response->send();

$kernel->terminate($request, $response);