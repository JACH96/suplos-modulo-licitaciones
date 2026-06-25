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

switch ($route){
    case 'api/ofertas':
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
    
    case 'api/actividades':
        $controller = new App\Controllers\ActividadController();
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $controller->index();
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Método no permitido"]);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["error" => "Ruta no encontrada en la API"]);
        break;
}