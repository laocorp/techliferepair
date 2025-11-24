<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckSubscription;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',     // <--- AGREGA EL /../ AQUÍ
        commands: __DIR__.'/../routes/console.php', // <--- Y AQUÍ TAMBIÉN
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('web', CheckSubscription::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
