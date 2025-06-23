<?php
namespace App\Controllers;

use MVC\Router;

class IndexController 
{
    public function index(Router $router)
    {
        $router->render('home/index', [
            'titulo' => 'PHP + MVC + Vite'
        ]);
    }

    public function create (Router $router)
    {
        $router->render('home/create', [
            'titulo' => 'Crear nuevo recurso'
        ]);
    }

    public function edit (Router $router)
    {
        $router->render('home/edit', [
            'titulo' => 'Editar recurso'
        ]);
    }
}