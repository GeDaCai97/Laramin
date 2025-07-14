<?php
namespace App\Controllers;

use MVC\Router;

class IndexController 
{
    public function index(Router $router)
    {

        $router->view('home.index', []);
    }

    public function show(Router $router)
    {
        // Ejemplo de consulta con QueryBuilder

        // $data = Usuario::query()
        //     ->select(['usuarios.nombre', 'usuarios.apellido', 'domicilios.calle AS domicilio_calle'])
        //     ->join('domicilios', 'usuarios.id', 'domicilios.usuario_id')
        //     ->where('usuarios.id', $id)
        //     ->get();

        //Ejemplo de respuesta JSON

        // $router->json([
        //     'id' => $id,
        //     'data' => $data
        // ]);
    }

    public function create (Router $router)
    {
  
    }

    public function edit (Router $router)
    {

    }

    public function store(Router $router)
    {
        //Obtener los datos del cuerpo de la solicitud
        // $data = $router->body();
        // Lógica para almacenar un nuevo recurso
        // Aquí podrías manejar la lógica de validación y almacenamiento
        // $router->json([
        //     'status' => 'success',
        //     'message' => 'Recurso creado exitosamente',
        //     'data' => $data
        // ], 201);
    }

    public function update(Router $router)
    {
        // Lógica para actualizar un recurso existente

        // Aquí podrías manejar la lógica de validación y actualización

    }

    public function patch(Router $router)
    {
        // Lógica para actualizar un recurso existente

        // Aquí podrías manejar la lógica de validación y actualización

    }

    public function destroy(Router $router)
    {
        // Lógica para eliminar un recurso existente

        // Aquí podrías manejar la lógica de validación y eliminación
    }
}