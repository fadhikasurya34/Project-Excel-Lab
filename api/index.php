<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. MUAT AUTOLOADER (Ini wajib nomor satu!)
require __DIR__.'/../vendor/autoload.php';

// 2. JALANKAN BOOTSTRAP LARAVEL
$app = require_once __DIR__.'/../bootstrap/app.php';

// 3. PAKSA STORAGE KE /TMP (Khusus Vercel)
$app->useStoragePath('/tmp');

// 4. PASTIKAN FOLDER VIEWS ADA (Biar nggak Error 500)
if (!is_dir('/tmp/framework/views')) {
    mkdir('/tmp/framework/views', 0755, true);
}

// 5. TANGANI REQUEST
$app->handleRequest(Request::capture());