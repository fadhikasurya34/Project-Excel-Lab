<?php
// 1. Setup path untuk Vercel
putenv('VIEW_COMPILED_PATH=/tmp');
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');

use Illuminate\Http\Request;
define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 2. Deteksi folder public untuk laravel
$app->instance('path.public', __DIR__ . '/../public');

$app->useStoragePath('/tmp');

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Request::capture());
$response->send();
$kernel->terminate($request, $response);