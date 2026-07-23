<?php

use Illuminate\Http\Request;

// Enable error display for debugging deployment issues
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

try {
    // 1. Create required storage and database directories in /tmp
    $storageDirs = [
        '/tmp/storage/app',
        '/tmp/storage/app/public',
        '/tmp/storage/framework/cache',
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/framework/views',
        '/tmp/storage/logs',
        '/tmp/storage/bootstrap/cache',
        '/tmp/database',
    ];

    foreach ($storageDirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    // 2. Set environment variables for Vercel writable paths
    putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
    putenv('APP_SERVICES_CACHE=/tmp/storage/bootstrap/cache/services.php');
    putenv('APP_PACKAGES_CACHE=/tmp/storage/bootstrap/cache/packages.php');
    putenv('APP_CONFIG_CACHE=/tmp/storage/bootstrap/cache/config.php');
    putenv('APP_ROUTES_CACHE=/tmp/storage/bootstrap/cache/routes.php');
    putenv('APP_EVENTS_CACHE=/tmp/storage/bootstrap/cache/events.php');

    // 3. Handle SQLite database in /tmp if sqlite driver is used
    $dbConnection = getenv('DB_CONNECTION') ?: 'sqlite';
    if ($dbConnection === 'sqlite') {
        $tmpDb = '/tmp/database/database.sqlite';
        if (!file_exists($tmpDb)) {
            $sourceDb = __DIR__ . '/../database/database.sqlite';
            if (file_exists($sourceDb)) {
                @copy($sourceDb, $tmpDb);
            } else {
                @touch($tmpDb);
            }
        }
        putenv("DB_DATABASE={$tmpDb}");
        $_ENV['DB_DATABASE'] = $tmpDb;
        $_SERVER['DB_DATABASE'] = $tmpDb;
    }

    define('LARAVEL_START', microtime(true));

    // 4. Register Composer autoloader
    $autoloader = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoloader)) {
        throw new \RuntimeException("Composer autoloader not found at {$autoloader}. Composer install may have failed during build.");
    }
    require $autoloader;

    // 5. Bootstrap Laravel Application
    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // 6. Set storage path to /tmp/storage
    $app->useStoragePath('/tmp/storage');

    // 7. Handle request
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Vercel Deployment Error</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (line " . $e->getLine() . ")</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre style='background:#f4f4f4;padding:15px;border-radius:5px;white-space:pre-wrap;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}



