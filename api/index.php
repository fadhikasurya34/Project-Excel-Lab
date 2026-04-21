<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Muat Autoloader
require __DIR__.'/../vendor/autoload.php';

// 2. Jalankan Bootstrap Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

// 3. Atur jalur Storage ke /tmp agar bisa menulis file di Vercel
$app->useStoragePath('/tmp');

// 4. Pastikan folder yang dibutuhkan ada di /tmp
$folders = [
    '/tmp/framework/views',
    '/tmp/framework/sessions',
    '/tmp/framework/cache',
];

foreach ($folders as $folder) {
    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }
}

// 5. Tangani Permintaan (Request)
$app->handleRequest(Request::capture());