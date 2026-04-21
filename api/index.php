<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Paksa Laravel buat pake folder /tmp (Satu-satunya tempat yang boleh ditulis di Vercel)
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->useStoragePath('/tmp');

// 2. Pastikan folder cache view ada di /tmp agar tidak error "view does not exist"
if (!is_dir('/tmp/framework/views')) {
    mkdir('/tmp/framework/views', 0755, true);
}

// 3. Jalankan Autoload & Handle Request
require __DIR__.'/../vendor/autoload.php';

$app->handleRequest(Request::capture());