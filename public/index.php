<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, authorization, X-Requested-with");

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
require_once __DIR__ . '/../config/database.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (strpos($requestUri, $_SERVER['SCRIPT_NAME']) === 0) {
    $route = substr($requestUri, strlen($_SERVER['SCRIPT_NAME']));
} else {
    $route = $requestUri;
}
$route = trim($route, '/');

if (preg_match('#^api/ofertas/(\d+)$#', $route, $matches)) {

    $id = $matches[1];

    $controller = new App\Controllers\OfertaController();

    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $controller->update($id);
    }

    exit;
}

switch ($route){
    case '':
    case '/':
        header_remove("Content-Type");
        header("Content-Type: text/html; charset=UTF-8");
        require_once __DIR__ . '/../views/index.php';
        break;

    case 'api/ofertas':
        header("Content-Type: application/json; charset=UTF-8");
        $controller = new App\Controllers\OfertaController();
        if($_SERVER['REQUEST_METHOD'] === 'GET') {
            $controller->index();
        }elseif($_SERVER['REQUEST_METHOD'] === 'POST'){
            $controller->store();
        }else {
            http_response_code(405);
            echo json_encode(["error"=> "Método no permitido"]);
        }
        break;

    case 'api/ofertas/documentos':
        header("Content-Type: application/json; charset=UTF-8");
        $controller = new App\Controllers\OfertaDocumentoController();
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $controller->index();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->store();
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Método no permitido"]);
        }
        break;
    
    case 'api/actividades':
        header("Content-Type: application/json; charset=UTF-8");
        $controller = new App\Controllers\ActividadController();
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $controller->index();
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Método no permitido"]);
        }
        break;

    default:
        header("Content-Type: application/json; charset=UTF-8");
        http_response_code(404);
        echo json_encode(["error" => "Ruta no encontrada en la API"]);
        break;
}