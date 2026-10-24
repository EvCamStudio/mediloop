<?php

use App\Http\Middleware\EnsureFacilityApprover;
use App\Http\Middleware\EnsureFacilityIsActive;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\EnsureUserIsSysadmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'active' => EnsureUserIsActive::class,
            'facility.active' => EnsureFacilityIsActive::class,
            'facility.approver' => EnsureFacilityApprover::class,
            'sysadmin' => EnsureUserIsSysadmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || isset($_ENV['LARAVEL_STORAGE_PATH']) || isset($_SERVER['LARAVEL_STORAGE_PATH'])) {
    $storagePath = (string) ($_ENV['LARAVEL_STORAGE_PATH'] ?? $_SERVER['LARAVEL_STORAGE_PATH'] ?? '/tmp/storage');
    $app->useStoragePath($storagePath);
}

return $app;
