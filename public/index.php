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

// 1. Bootstrap HTTP kernel so container services (config, DB, Facades) are initialized
/** @var \Illuminate\Contracts\Http\Kernel $kernel */
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

// 2. Override storage path and SQLite database path for serverless environments (Vercel)
if (is_dir('/tmp')) {
    $app->useStoragePath('/tmp/storage');

    $tmpDb = '/tmp/database/database.sqlite';
    if (!file_exists($tmpDb) || filesize($tmpDb) === 0) {
        @mkdir('/tmp/database', 0755, true);
        @touch($tmpDb);
    }
    config(['database.connections.sqlite.database' => $tmpDb]);
    \Illuminate\Support\Facades\DB::purge('sqlite');
}

// Auto-run database migrations and seeders if tables are missing
try {
    if (!\Illuminate\Support\Facades\Schema::hasTable('users')) {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
    }
} catch (\Throwable $e) {
    throw new \RuntimeException("Migration / Seeding Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
}

$app->handleRequest(Request::capture());
