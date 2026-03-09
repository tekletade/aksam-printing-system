protected $routeMiddleware = [
    // ...
    'permission' => \App\Http\Middleware\CheckPermission::class,
    'api.key' => \App\Http\Middleware\ApiKeyMiddleware::class,
];
