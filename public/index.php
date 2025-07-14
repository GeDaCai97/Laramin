<?php

use App\Controllers\IndexController;
use MVC\Router;

require_once __DIR__ . '/../config/app.php';
$router = new Router();

//Listar las rutas

$router->get('/', [IndexController::class, 'index']);

// Ejemplo de middlewares a una ruta específica o a un grupo de rutas

// $router->middleware('/', TestMiddleware::class);

// $router->group([
//     'prefix' => '/home',
//     'middleware' => TestMiddleware::class
// ], function($router, $prefix) {
//     $router->get("$prefix/create", [IndexController::class, 'create']);
//     $router->get("$prefix/edit", [IndexController::class, 'edit']);
// });


//Rutas que empiecen con /api no cargan BladeLite "reservado para API"

// $router->post("/api/store", [IndexController::class, 'store']);
// $router->put("/api/update", [IndexController::class, 'update']);




// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();