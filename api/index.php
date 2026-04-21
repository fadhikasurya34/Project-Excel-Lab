<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Muat Autoloader (Wajib paling atas)
require __DIR__ . '/../vendor/autoload.php';

// 2. Muat Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 3. Jalankan Aplikasi
$app->handleRequest(Request::capture());