<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
<<<<<<< HEAD
  api: __DIR__.'/../routes/api.php', // 👈 VÉRIFIE BIEN QUE CETTE LIGNE EST LÀ !
=======
        api: __DIR__.'/../routes/api.php',
>>>>>>> origin/feature/abdoulaye
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {
<<<<<<< HEAD
         $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'prestataire' => \App\Http\Middleware\PrestataireMiddleware::class,
        'demandeur' => \App\Http\Middleware\DemandeurMiddleware::class,
    ]);

=======
        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);

        $middleware->statefulApi();
>>>>>>> origin/feature/abdoulaye
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();

