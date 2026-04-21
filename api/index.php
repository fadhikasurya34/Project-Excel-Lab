<?php
// 1. Path Cache & Environment
putenv('VIEW_COMPILED_PATH=/tmp');
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 2. PAKSA PATH (Kunci Utama)
$app->instance('path.public', __DIR__ . '/../public');

// 3. Tambahkan ini agar Vite tidak bingung di Serverless
$app->bind('path.public', function () {
    return __DIR__ . '/../public';
});

$app->useStoragePath('/tmp');

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);

$response->send();
$kernel->terminate($request, $response);