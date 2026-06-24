<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

use App\Http\Middleware\AdminMiddleware;


return Application::configure(
    basePath: dirname(__DIR__)
)

->withRouting(

    web: __DIR__.'/../routes/web.php',

    api: __DIR__.'/../routes/api.php',

    commands: __DIR__.'/../routes/console.php',

    health: '/up',

)


->withMiddleware(function (Middleware $middleware): void {


    // Alias des middlewares
    $middleware->alias([

        'admin' =>
            AdminMiddleware::class,

        'prestataire' =>
            \App\Http\Middleware\PrestataireMiddleware::class,

        'demandeur' =>
            \App\Http\Middleware\DemandeurMiddleware::class,

    ]);



    // Sanctum SPA
    $middleware->statefulApi();



    // IMPORTANT :
    // Empêche Laravel de chercher une route "connexion"
    $middleware->redirectGuestsTo(function (
        Request $request
    ) {


        if ($request->is('api/*') || $request->expectsJson()) {

            return null;

        }


        return '/login';


    });


})


->withExceptions(function (Exceptions $exceptions): void {

    //

})

->create();