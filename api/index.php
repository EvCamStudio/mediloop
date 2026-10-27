<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$storagePath = $_ENV['LARAVEL_STORAGE_PATH'] ?? $_SERVER['LARAVEL_STORAGE_PATH'] ?? '/tmp/storage';

$writableDirectories = [
    $storagePath.'/app/public',
    $storagePath.'/cache',
    $storagePath.'/framework/cache/data',
    $storagePath.'/framework/sessions',
    $storagePath.'/framework/views',
    $storagePath.'/logs',
    '/tmp/views',
];

foreach ($writableDirectories as $directory) {
    if (! is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
}

$databaseConnection = $_ENV['DB_CONNECTION'] ?? $_SERVER['DB_CONNECTION'] ?? 'sqlite';
$needsDatabaseSetup = false;

if ($databaseConnection === 'sqlite') {
    $sqlitePath = '/tmp/database.sqlite';

    if (! file_exists($sqlitePath) || filesize($sqlitePath) === 0) {
        touch($sqlitePath);
        $needsDatabaseSetup = true;
    }

    putenv("DB_DATABASE={$sqlitePath}");
    $_ENV['DB_DATABASE'] = $sqlitePath;
    $_SERVER['DB_DATABASE'] = $sqlitePath;
}

require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$migrationSignature = md5(implode('|', array_map(
    static fn (string $file): string => basename($file).':'.(string) md5_file($file),
    glob(__DIR__.'/../database/migrations/*.php') ?: [],
)));
$schemaMarker = $storagePath.'/schema-ready-'.$migrationSignature;

if ($needsDatabaseSetup || ! is_file($schemaMarker)) {
    try {
        $console = $app->make(Kernel::class);

        $console->call('migrate', ['--force' => true]);

        if ($needsDatabaseSetup) {
            $console->call('db:seed', ['--force' => true]);
        }

        touch($schemaMarker);
    } catch (Throwable $exception) {
        error_log('[mediloop] database bootstrap failed: '.$exception->getMessage());
    }
}

$app->handleRequest(Request::capture());
