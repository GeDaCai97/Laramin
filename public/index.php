<?php

use App\Controllers\IndexController;
use MVC\Router;

require_once __DIR__ . '/../config/app.php';

$router = new Router();

//Listar las rutas

$router->get('/', [IndexController::class, 'index']);



// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();