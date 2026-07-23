<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Override storage path for serverless environments (Vercel)
if (is_dir('/tmp')) {
    $app->useStoragePath('/tmp/storage');
}

// Auto-run database migrations and seeders if tables are missing
try {
    if (!\Illuminate\Support\Facades\Schema::hasTable('users')) {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
    }
} catch (\Throwable $e) {
    // Continue if migration check fails
}

$app->handleRequest(Request::capture());
