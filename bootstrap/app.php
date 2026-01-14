<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware aliases (custom)
        $middleware->alias([
            'check.subdomain' => \App\Http\Middleware\CheckSubdomain::class,
            'check.role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // Grupos de middleware
        $middleware->web(append: [
            // Aquí puedes agregar middleware adicional al grupo 'web'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();