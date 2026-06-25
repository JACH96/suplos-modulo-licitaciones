<?php
namespace App\Controllers;

use App\Models\OfertaDocumento;

class OfertaDocumentoController
{
    //listar los documentos de una oferta específica
    public function index()
    {
        //parámetro id enviado desde la URL
        $licitacionId = $_GET['licitacion_id'] ?? null;

        if (!$licitacionId) {
            http_response_code(400);
            echo json_encode(["error" => "Falta el parámetro licitacion_id"]);
            return;
        }

        try {
            $documentos = OfertaDocumento::where('licitacion_id', $licitacionId)->get();
            echo json_encode($documentos);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al consultar documentos: " . $e->getMessage()]);
        }
    }

    // recibir y guardar el archivo
    public function store()
    {
        $licitacionId = $_POST['licitacion_id'] ?? null;
        $titulo       = $_POST['titulo'] ?? null;
        $descripcion  = $_POST['descripcion'] ?? null;

        if (!$licitacionId || !$titulo || !$descripcion || !isset($_FILES['archivo'])) {
            http_response_code(400);
            echo json_encode(["error" => "Todos los campos son obligatorios, incluyendo el archivo."]);
            return;
        }

        $file = $_FILES['archivo'];

        //validacion archivo
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $extensionesPermitidas = ['pdf', 'zip'];

        if (!in_array($extension, $extensionesPermitidas)) {
            http_response_code(422);
            echo json_encode(["error" => "Formato no permitido. Solo se aceptan archivos PDF o ZIP."]);
            return;
        }

        //ruta donde se almacenara
        $targetDir = __DIR__ . '/../public/uploads/';
        
        //crear carpeta si no existe en el XAMPP
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        //generacion de nombre único
        $nuevoNombreArchivo = 'doc_' . uniqid() . '.' . $extension;
        $targetFile = $targetDir . $nuevoNombreArchivo;

        try {
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                
                //guardar en la base de datos
                $documento = OfertaDocumento::create([
                    'licitacion_id' => $licitacionId,
                    'titulo'        => $titulo,
                    'descripcion'   => $descripcion,
                    'archivo'       => $nuevoNombreArchivo
                ]);

                http_response_code(201);
                echo json_encode([
                    "message" => "Documento adjuntado e indexado con éxito",
                    "data" => $documento
                ]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "No se pudo guardar el archivo físico en el servidor."]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error interno del servidor: " . $e->getMessage()]);
        }
    }
}