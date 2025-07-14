<?php

namespace MVC;

use Config\Cors;
use Core\BladeLite;
use CsfrMiddleware;

class Router {
    // Array de rutas por method
    public array $getRoutes = [];
    public array $postRoutes = [];
    public array $patchRoutes = [];
    public array $putRoutes = [];
    public array $deleteRoutes = [];

    //Array de rutas por middleware
    public array $middlewares = [];
    // Array de middlewares por grupo de rutas
    public array $groupMiddlewares = [];

    //Funcion para agregar middleware a una ruta
    // Esta función permite agregar middlewares a una ruta específica
    public function middleware($url, $middlewareFn)
    {
        $this->middlewares[$url][] = $middlewareFn;
    }

    // Funcion para agregar middleware a un grupo de rutas
    public function group(array $config, callable $callback)
    {
        $prefix = $config['prefix'] ?? '';
        $middleware = $config['middleware'] ?? null;
        if($middleware) {
            $this->groupMiddlewares[$prefix] = $middleware;
        }
        $callback($this, $prefix); // Llamamos al callback con el router actual
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

        // Soporte para métodos spoofing (ej. _method en formularios)
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }


        if (strpos($_SERVER['REQUEST_URI'], '/api/') === 0) {
            Cors::handle();
        }

        if(in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $csrfMiddleware = new CsfrMiddleware();
            $csrfMiddleware->handle(); // Ejecuta el middleware CSRF
        }


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

        // Ejecutar middleware por grupo de rutas
        foreach ($this->groupMiddlewares as $prefix => $middleware) {
            if (str_starts_with($currentUrl, $prefix)) {
                if (!$this->runMiddleware($middleware)) {return;} // Si el middleware devuelve false, no se ejecuta la ruta
            }
        }

        // Ejecutar middleware por ruta
        if(isset($this->middlewares[$currentUrl])) {
            foreach ($this->middlewares[$currentUrl] as $middleware) {
                if(!$this->runMiddleware($middleware)) return;
            }
        }


        // if ( $fn ) {
        //     // Call user fn va a llamar una función cuando no sabemos cual sera
        //     call_user_func($fn, $this); // This es para pasar argumentos
        // } else {
        //     header('Location: /404');
        // }

        if($fn) {
            if(is_array($fn)) {
                [$class, $method] = $fn; // Desestructuración del array
                if (class_exists($class)) {
                    $instance = new $class;
                    call_user_func([$instance, $method], $this); // Llama al método del controlador con el router actual
                } else {
                    echo "Error: Clase $class no encontrada.";
                }
            } else {
                call_user_func($fn, $this); // Llama a la función con el router actual
            }
        } else {
            header('Location: /404');
            return;
        }
    }

    private function runMiddleware($middleware): bool
    {
        if(is_callable($middleware)) {
            return call_user_func($middleware) !== false; // Si el middleware devuelve false, no se ejecuta la ruta
        }
        if(is_string($middleware) && class_exists($middleware)) {
            return call_user_func([new $middleware, 'handle']) !== false; // Si es una clase, llamamos al método handle
        }
        return true; // Si no es un middleware válido, continuamos
    }

    // Funcion para devolver response JSON
    public function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    //Funcion para leer JSON del body de la petición
    public function body(): array
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if($method === 'GET') {
            return $_GET; // Para GET, devolvemos los parámetros de la URL
        }

        if(str_contains($contentType, 'application/json')) {
            $content = file_get_contents('php://input');
            return json_decode($content, true) ?? [];
        }

        if ($method === 'POST') return $_POST;

        if (str_contains($contentType, 'application/x-www-form-urlencoded')) {
            parse_str(file_get_contents('php://input'), $data);
            return $data;
        }

        return []; // Para otros métodos, devolvemos los datos del formulario
    }

    // public function view($view, $datos = [])
    // {

    //     // Leer lo que le pasamos  a la vista
    //     foreach ($datos as $key => $value) {
    //         $$key = $value;  // Doble signo de dolar significa: variable variable, básicamente nuestra variable sigue siendo la original, pero al asignarla a otra no la reescribe, mantiene su valor, de esta forma el nombre de la variable se asigna dinamicamente
    //     }

    //     ob_start(); // Almacenamiento en memoria durante un momento...

    //     // entonces incluimos la vista en el layout
    //     include_once __DIR__ . "/src/views/$view.php";

    //     $contenido = ob_get_clean(); // Limpia el Buffer

    //     $currentUrl = strtok($_SERVER['REQUEST_URI'], '?') ?? '/';

    //     if(str_contains($currentUrl, '/admin')) {
    //         include_once __DIR__ . '/app/View/admin-layout.php';
    //     } else {
    //         include_once __DIR__ . '/app/View/index.php';
    //     }

    //     // include_once __DIR__ . '/views/layout.php';
    // }
    public function view(string $view, array $datos = [])
    {
        if (str_starts_with($_SERVER['REQUEST_URI'], '/api')) {
            // No renderiza vistas en modo API
            http_response_code(404);
            echo json_encode(['error' => 'No se permite renderizar vistas en modo API']);
            exit;
        }

        $blade = new BladeLite(
            __DIR__ . '/src/views',     // Ruta base de vistas
            __DIR__ . '/storage/cache'  // Ruta donde guardar los compilados
        );

        $blade->render($view, $datos);
    }
}