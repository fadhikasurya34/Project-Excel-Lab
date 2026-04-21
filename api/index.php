<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Load Autoload
require __DIR__ . '/../vendor/autoload.php';

// 2. Boot Application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 3. Handle Request Secara Eksplisit
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);

// 4. Kirim Output ke Browser
$response->send();

$kernel->terminate($request, $response);