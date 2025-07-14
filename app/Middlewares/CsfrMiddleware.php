<?php

class CsfrMiddleware
{
    protected $methodsToProtect = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public function handle()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        if (in_array($method, $this->methodsToProtect)) {
            $token = $_SESSION['csrf_token'] ?? null;
            $inputToken = $_POST['_token'] ?? null;

            if (!$inputToken && isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                $inputToken = $_SERVER['HTTP_X_CSRF_TOKEN'];
            }

            if (!$token || !$inputToken || $token !== $inputToken) {
                http_response_code(419);
                die('CSRF token mismatch.');
            }
        }
    }
}