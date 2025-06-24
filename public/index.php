<?php

use App\Controllers\IndexController;
use App\Middlewares\TestMethodMiddleware;
use App\Middlewares\TestMiddleware;
use MVC\Router;

require_once __DIR__ . '/../config/app.php';
$router = new Router();

//Listar las rutas

$router->get('/', [IndexController::class, 'index']);
$router->get('/show', [IndexController::class, 'show']);

// $router->middleware('/', [TestMiddleware::class, 'handle']);

$router->group([
    'prefix' => '/home',
    'middleware' => TestMiddleware::class
], function($router, $prefix) {
    $router->get("$prefix/create", [IndexController::class, 'create']);
    $router->get("$prefix/edit", [IndexController::class, 'edit']);
});

$router->group([
    'prefix' => '/api',
    'middleware' => TestMethodMiddleware::class
], function($router, $prefix) {
    $router->post("$prefix/store", [IndexController::class, 'store']);
    $router->put("$prefix/update", [IndexController::class, 'update']);    
});


$router->patch('/patch', [IndexController::class, 'patch']);
$router->delete('/delete', [IndexController::class, 'destroy']);




// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();