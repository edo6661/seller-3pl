<?php

use App\Http\Middleware\Api\ApiAdminMiddleware;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\PasswordValidationMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        apiPrefix: 'api',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'isAdmin' => AuthMiddleware::class,
            'passwordValidation' => PasswordValidationMiddleware::class,
            'apiIsAdmin' => ApiAdminMiddleware::class,
        ]);    
        $middleware->redirectGuestsTo(function ($request) {
           
            return route('guest.auth.login');
        });
        
      

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
