<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Cek mode maintenance
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// 2. Register Autoloader (Vercel akan menginstal ini otomatis)
require __DIR__.'/../vendor/autoload.php';

// 3. Jalankan Bootstrap Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

// 4. Tangani Request
$app->handleRequest(Request::capture());