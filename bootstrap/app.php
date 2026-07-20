<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Tambahkan pengecekan ini di bagian paling atas untuk Vercel
if (isset($_SERVER['VERCEL_BUILD_ID'])) {
    $appConfig = [
        'view.compiled' => '/tmp/storage/framework/views',
        'cache.stores.file.path' => '/tmp/storage/framework/cache/data',
    ];
    foreach ($appConfig as $key => $value) {
        config([$key => $value]);
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
