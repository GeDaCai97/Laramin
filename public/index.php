<?php

use App\Controllers\IndexController;
use App\Middlewares\TestMiddleware;
use MVC\Router;

require_once __DIR__ . '/../config/app.php';
$router = new Router();

//Listar las rutas

$router->get('/', [IndexController::class, 'index']);

// $router->middleware('/', [TestMiddleware::class, 'handle']);

$router->group([
    'prefix' => '/api',
    'middleware' => [TestMiddleware::class, 'handle']
], function($router, $prefix) {
    $router->get("$prefix/create", [IndexController::class, 'create']);
    $router->get("$prefix/edit", [IndexController::class, 'edit']);
});




// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();