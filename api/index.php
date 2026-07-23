<?php

// Create required storage directories in /tmp for Vercel serverless environment
$storageDirs = [
    '/tmp/storage/app',
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Forward Vercel requests to Laravel's public/index.php
require __DIR__ . '/../public/index.php';

