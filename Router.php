<?php

namespace MVC;

class Router {
    // Array de rutas por method
    public array $getRoutes = [];
    public array $postRoutes = [];
    public array $patchRoutes = [];
    public array $putRoutes = [];
    public array $deleteRoutes = [];

    //Array de rutas por middleware
    public array $middlewares = [];

    //Funcion para agregar middleware a una ruta
    // Esta función permite agregar middlewares a una ruta específica
    public function middleware($url, $middlewareFn)
    {
        $this->middlewares[$url][] = $middlewareFn;
    }



    // Funciones para agregar rutas
    // Estas funciones permiten agregar rutas para diferentes métodos HTTP
    public function get($url, $fn)
    {
        $this->getRoutes[$url] = $fn;
    }

    public function post($url, $fn)
    {
        $this->postRoutes[$url] = $fn;
    }

        public function put($url, $fn)
    {
        $this->putRoutes[$url] = $fn;
    }

        public function patch($url, $fn)
    {
        $this->patchRoutes[$url] = $fn;
    }

        public function delete($url, $fn)
    {
        $this->deleteRoutes[$url] = $fn;
    }

    public function comprobarRutas()
    {

        $currentUrl = strtok($_SERVER['REQUEST_URI'], '?') ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        // if ($method === 'GET') {
        //     $fn = $this->getRoutes[$currentUrl] ?? null;
        // } else {
        //     $fn = $this->postRoutes[$currentUrl] ?? null;
        // }
        switch ($method) {
            case 'GET':
                $fn = $this->getRoutes[$currentUrl] ?? null;
                break;
            case 'POST':
                $fn = $this->postRoutes[$currentUrl] ?? null;
                break;
            case 'PUT':
                $fn = $this->putRoutes[$currentUrl] ?? null;
                break;
            case 'PATCH':
                $fn = $this->patchRoutes[$currentUrl] ?? null;
                break;
            case 'DELETE':
                $fn = $this->deleteRoutes[$currentUrl] ?? null;
                break;
            default:
                $fn = null;
        }


        if ( $fn ) {
            // Call user fn va a llamar una función cuando no sabemos cual sera
            call_user_func($fn, $this); // This es para pasar argumentos
        } else {
            header('Location: /404');
        }
    }

    public function render($view, $datos = [])
    {

        // Leer lo que le pasamos  a la vista
        foreach ($datos as $key => $value) {
            $$key = $value;  // Doble signo de dolar significa: variable variable, básicamente nuestra variable sigue siendo la original, pero al asignarla a otra no la reescribe, mantiene su valor, de esta forma el nombre de la variable se asigna dinamicamente
        }

        ob_start(); // Almacenamiento en memoria durante un momento...

        // entonces incluimos la vista en el layout
        include_once __DIR__ . "/src/views/$view.php";

        $contenido = ob_get_clean(); // Limpia el Buffer

        $currentUrl = strtok($_SERVER['REQUEST_URI'], '?') ?? '/';

        if(str_contains($currentUrl, '/admin')) {
            include_once __DIR__ . '/app/View/admin-layout.php';
        } else {
            include_once __DIR__ . '/app/View/index.php';
        }

        // include_once __DIR__ . '/views/layout.php';
    }
}