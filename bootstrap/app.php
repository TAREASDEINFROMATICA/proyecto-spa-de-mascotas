<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SessionTimeout;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
   
    $middleware->prepend(\App\Http\Middleware\AddTokenFromUrl::class);
    
    $middleware->alias([
        '2fa' => \PragmaRX\Google2FALaravel\Middleware::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
  