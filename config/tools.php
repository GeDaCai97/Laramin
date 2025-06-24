<?php

function dd($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

function pagina_actual($path) : bool {
    return str_contains($_SERVER['PATH_INFO'] ?? '/', $path) ? true : false;
}
function is_auth() : bool {
    if(!isset($_SESSION)) {
        session_start();
    }
    return isset($_SESSION['nombre']) && !empty($_SESSION);
}
function is_admin() : bool {
    if(!isset($_SESSION)) {
        session_start();
    }
    return isset($_SESSION['admin']) && !empty($_SESSION['admin']);
}
function is_adminAdv() {
    if(!isset($_SESSION)) {
        session_start();
    }
    if(isset($_SESSION['userLevel']) && !empty($_SESSION['userLevel'])) {
        $nivel = $_SESSION['userLevel'];
        return ($nivel === '2' || $nivel === '3') ? true : false;
        // $resultado = false;
        // if($nivel === '2' || $nivel === '3') {
        //     $resultado = true;
        // } else {
        //     $resultado = false;
        // }
        
        // return $resultado;
    }
}


//muestra las alertas
function mostrarNotificacion($codigo) {
    switch($codigo) { 
        case 1:
            $mensaje = 'Creado correctamente';
            break;
        case 2:
            $mensaje = 'Actualizado correctamente';
            break;
        case 3:
            $mensaje = 'Eliminado correctamente';
            break;
        default:
            $mensaje = false;
            break;
    }
    return $mensaje;
}

function vite_asset($entry)
{
    $manifestPath = __DIR__ .  '/../public/assets/.vite/manifest.json';

    if (!file_exists($manifestPath)) {
        dd("El archivo manifest.json no existe. Ejecuta `npm run build`.");
    }

    $manifest = json_decode(file_get_contents($manifestPath), true);


    if (!isset($manifest[$entry])) {
        throw new Exception("El archivo $entry no está en el manifest.");
    }

    $url = '/assets/' . $manifest[$entry]['file'];

    $css = $manifest[$entry]['css'][0] ?? null;
    $html = '<script type="module" src="' . $url . '"></script>';

    if ($css) {
        $html = '<link rel="stylesheet" href="/assets/' . $css . '">' . "\n" . $html;
    }

    return $html;
}

function json_response(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function redirect(string $url, int $statusCode = 302): void
{
    http_response_code($statusCode);
    header("Location: $url");
    exit;
}