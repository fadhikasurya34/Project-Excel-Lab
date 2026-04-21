<?php

// Mengarahkan folder storage ke /tmp agar Laravel bisa menulis cache/views
$storagePath = '/tmp/storage/framework';
foreach (['/sessions', '/views', '/cache'] as $path) {
    if (!is_dir($storagePath . $path)) {
        mkdir($storagePath . $path, 0755, true);
    }
}

// Paksa Laravel menggunakan folder tmp untuk kompilasi view
putenv("VIEW_COMPILED_PATH=/tmp/storage/framework/views");

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());