<?php

namespace Config;

class Cors
{
    public static function handle()
    {
        $origin = $_ENV['CORS_ALLOWED_ORIGIN'] ?? '*'; // Puedes ajustar el origen permitido según tus necesidades
        header("Access-Control-Allow-Origin: $origin"); // Ajusta esto en producción
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        // Manejar solicitud OPTIONS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}