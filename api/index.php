<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Muat Autoload
require __DIR__.'/../vendor/autoload.php';

// 2. Jalankan Bootstrap
$app = require_once __DIR__.'/../bootstrap/app.php';

// 3. PAKSA jalur storage ke /tmp (Satu-satunya folder yang bisa ditulis di Vercel)
$app->useStoragePath('/tmp');

// 4. Pastikan folder pendukung ada di /tmp
$storageFolders = [
    '/tmp/framework/views',
    '/tmp/framework/sessions',
    '/tmp/framework/cache',
];

foreach ($storageFolders as $folder) {
    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }
}

// 5. Paksa Laravel menggunakan folder view yang baru dibuat
config(['view.compiled' => '/tmp/framework/views']);

// 6. Jalankan Aplikasi
$app->handleRequest(Request::capture());