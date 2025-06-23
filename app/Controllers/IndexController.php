<?php
namespace App\Controllers;

use MVC\Router;

class IndexController 
{
    public static function index(Router $router)
    {
        $router->render('home/index', [
            'titulo' => 'PHP + MVC + Vite'
        ]);
    }

}