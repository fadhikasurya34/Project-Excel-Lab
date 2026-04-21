<?php

// 1. Set path untuk cache (Vercel cuma kasih izin nulis di /tmp)
putenv('VIEW_COMPILED_PATH=/tmp');
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('APP_SERVICES_CACHE=/tmp/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 2. Load Autoload & Boot Application
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 3. PAKSA PATH PUBLIC (Kunci biar Vite & Gambar muncul)
// Baris ini ngasih tahu Laravel kalau folder public ada di luar folder api
$app->instance('path.public', __DIR__ . '/../public');

// 4. Paksa storage pakai /tmp
$app->useStoragePath('/tmp');

// 5. Jalankan Kernel
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);

$response->send();

$kernel->terminate($request, $response);