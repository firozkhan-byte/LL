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

// Auto-run database migrations and seeders if activity_log table or default admin user is missing
$app->booted(function () {
    try {
        if (!\Illuminate\Support\Facades\Schema::hasTable('activity_log')) {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        }
        
        if (!\App\Models\User::where('email', 'admin@livingliquidz.com')->exists()) {
            \Illuminate\Support\Facades\Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\RolesAndPermissionsSeeder',
                '--force' => true
            ]);
        }
    } catch (\Throwable $e) {
        // Silently handle if seeding check passes
    }
});

$app->handleRequest(Request::capture());
