<?php
namespace App\Middlewares;

class TestMiddleware
{
    public static function handle()
    {
        // Aquí puedes agregar la lógica del middleware
        //
        //
        // if (!isset($_SESSION['usuario'])) {
        //     redirect('/login');
        //     return false; // opcional, porque redirect() ya hace exit
        // }
        return true; // Redirige a la ruta raíz como ejemplo
    }
}