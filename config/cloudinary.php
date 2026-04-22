<?php

return [
    'cloud_url' => env('CLOUDINARY_URL'),

    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET', 'ml_default'),

    /*
    | Tambahkan ini agar sistem tidak menganggap array ini kosong
    */
    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),
    'secure_url' => env('CLOUDINARY_SECURE_URL', true),
];