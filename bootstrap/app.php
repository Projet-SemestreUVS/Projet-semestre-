<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;

return Application::configure(basePath: dirname(__DIR__))
<<<<<<< HEAD
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
=======
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
<<<<<<< HEAD
        api: __DIR__.'/../routes/api.php',
=======
        api: __DIR__.'/../routes/api.php',  //
>>>>>>> origin/feature/awa
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
>>>>>>> origin/feature/adama
    )

    ->withMiddleware(function (Middleware $middleware): void {
<<<<<<< HEAD
<<<<<<< HEAD
        $middleware->alias([
            'admin' => AdminMiddleware::class,
=======
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
>>>>>>> origin/feature/adama
        ]);
=======
        $middleware->statefulApi();
        //
>>>>>>> origin/feature/awa
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        //
<<<<<<< HEAD
    })->create();
=======
    })
    ->create();
>>>>>>> origin/feature/seydina
