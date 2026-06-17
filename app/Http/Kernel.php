// app/Http/Kernel.php
protected $routeMiddleware = [
    // ... autres middlewares
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
    'prestataire' => \App\Http\Middleware\PrestataireMiddleware::class,
    'demandeur' => \App\Http\Middleware\DemandeurMiddleware::class,
];