<?php

namespace App\Middlewares;

class TestMethodMiddleware
{
    public function handle()
    {
        return json_response([
            'error' => 'Error',
            'message' => 'No Autorizado'
        ], 401);
        
    }
}