<?php
namespace App\Controllers;

use MVC\Router;

class IndexController 
{
    public function index(Router $router)
    {
        // $router->view('home/index', [
        //     'titulo' => 'PHP + MVC + Vite'
        // ]);
        $router->view('home.index', [
            'variable' => 'Valor de la variable',
        ]);
    }

    public function create (Router $router)
    {
        $router->view('home/create', [
            'titulo' => 'Crear nuevo recurso'
        ]);
    }

    public function edit (Router $router)
    {
        $router->view('home/edit', [
            'titulo' => 'Editar recurso'
        ]);
    }

    public function store(Router $router)
    {
        $data = $router->body();
        // Lógica para almacenar un nuevo recurso
        // Aquí podrías manejar la lógica de validación y almacenamiento
        $router->json([
            'status' => 'success',
            'message' => 'Recurso creado exitosamente',
            'data' => $data
        ], 201);
    }

    public function update(Router $router)
    {
        $data = $router->body();
        // Lógica para actualizar un recurso existente
        // Aquí podrías manejar la lógica de validación y actualización
        $router->json([
            'status' => 'success',
            'message' => 'Recurso actualizado exitosamente',
            'data' => $data
        ], 200);
    }

    public function patch(Router $router)
    {
        $data = $router->body();
        // Lógica para actualizar un recurso existente
        // Aquí podrías manejar la lógica de validación y actualización
        $router->json([
            'status' => 'success',
            'message' => 'Recurso actualizado exitosamente PATCH',
            'data' => $data
        ], 200);
    }

    public function destroy(Router $router)
    {
        // Lógica para eliminar un recurso existente
        // Aquí podrías manejar la lógica de validación y eliminación
        $router->json([
            'status' => 'success',
            'message' => 'Recurso eliminado exitosamente',
        ], 200);
    }
}