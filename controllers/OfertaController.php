<?php
namespace App\Controllers;

use App\Models\Actividad;
use App\Models\Oferta;

class OfertaController
{
    public function index()
    {
        try {

            // Filtros
            $consecutivo = trim($_GET['consecutivo'] ?? '');
            $objeto      = trim($_GET['objeto'] ?? '');
            $descripcion = trim($_GET['descripcion'] ?? '');

            // Paginación
            $paginaActual = max(1, (int)($_GET['page'] ?? 1));
            $porPagina = 5;

            // Consulta base
            $query = Oferta::with('actividad');

            // Filtro consecutivo
            if ($consecutivo !== '') {
                $query->where('consecutivo', 'LIKE', "%{$consecutivo}%");
            }

            // Filtro objeto
            if ($objeto !== '') {
                $query->where('objeto', 'LIKE', "%{$objeto}%");
            }

            // Filtro descripción
            if ($descripcion !== '') {
                $query->where('descripcion', 'LIKE', "%{$descripcion}%");
            }

            // Orden descendente
            $query->orderBy('id', 'DESC');

            // Paginación
            $total = $query->count();

            $ofertas = $query
                ->skip(($paginaActual - 1) * $porPagina)
                ->take($porPagina)
                ->get();

            $totalPaginas = ceil($total / $porPagina);

            echo json_encode([
                'status' => 'success',
                'data' => $ofertas,
                'current_page' => $paginaActual,
                'last_page' => $totalPaginas,
                'total' => $total
            ]);

        } catch (\Exception $e) {

            http_response_code(500);

            echo json_encode([
                'status' => 'error',
                'error'  => $e->getMessage()
            ]);
        }
    }

    public function store(){
        $data = json_decode(file_get_contents("php://input"),true);

        $data['objeto'] = isset($data['objeto']) ? trim($data['objeto']) : '';
        $data['descripcion'] = isset($data['descripcion']) ? trim($data['descripcion']) : '';

        if (empty($data['objeto']) || strlen($data['objeto']) > 150 || empty($data['descripcion']) || strlen($data['descripcion']) > 400) {
            http_response_code(422);
            echo json_encode(["error" => "El objeto (máx 150) y la descripción (máx 400) son obligatorios y no pueden estar vacíos."]);
            return;
        }

        if (!$data) {
            http_response_code(400);
            echo json_encode(["error" => "Datos del formulario inválidos"]);
            return;
        }

        $inicio = strtotime($data['fecha_inicio'] . ' ' . $data['hora_inicio']);
        $cierre = strtotime($data['fecha_cierre'] . ' ' . $data['hora_cierre']);

         if ($cierre <= $inicio) {
            http_response_code(422);
            echo json_encode(["error" => "La fecha/hora de cierre debe ser posterior a la de inicio."]);
            return;
        }

        $actividadExiste = Actividad::find($data['actividad_id']);

        if (!$actividadExiste) {
            http_response_code(422);
            echo json_encode(["error" => "La actividad seleccionada no es válida o no existe en el sistema."]);
            return;
        }

        try {
            $anioActual = date('y');

            $ultimaOferta = Oferta::where('consecutivo', 'LIKE', "PO-%-$anioActual")
                ->orderBy('id', 'desc')
                ->first();

            $nuevoNumero = 1;

            if($ultimaOferta){
                $partes = explode('-', $ultimaOferta->consecutivo);
                $nuevoNumero = intval($partes[1])+1;
            }

            $numeroFormateado = str_pad($nuevoNumero, 4, '0', STR_PAD_LEFT);
            $consecutivoFinal = "PO-{$numeroFormateado}-{$anioActual}";

            $nuevaOferta = Oferta::create([
                'consecutivo' => $consecutivoFinal,
                'objeto' => $data['objeto'],
                'descripcion' => $data['descripcion'],
                'moneda' => $data['moneda'],
                'presupuesto' => $data['presupuesto'],
                'actividad_id' => $data['actividad_id'],
                'fecha_inicio' => $data['fecha_inicio'],
                'hora_inicio' => $data['hora_inicio'],
                'fecha_cierre' => $data['fecha_cierre'],
                'hora_cierre' => $data['hora_cierre'],
                'estado' => 'Abierta'
            ]);

            http_response_code(201);
            echo json_encode([
                "message" => "Oferta publicada con éxito",
                "data" => $nuevaOferta
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error interno del servidor: " . $e->getMessage()]);
        }
    }
    
    public function update($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $oferta = Oferta::find($id);

        if (!$oferta) {
            http_response_code(404);
            echo json_encode(["error" => "Oferta no encontrada"]);
            return;
        }

        $oferta->update([
            'objeto' => $data['objeto'],
            'descripcion' => $data['descripcion'],
            'moneda' => $data['moneda'],
            'presupuesto' => $data['presupuesto'],
            'actividad_id' => $data['actividad_id'],
            'fecha_inicio' => $data['fecha_inicio'],
            'hora_inicio' => $data['hora_inicio'],
            'fecha_cierre' => $data['fecha_cierre'],
            'hora_cierre' => $data['hora_cierre']
        ]);

        echo json_encode([
            "message" => "Oferta actualizada correctamente"
        ]);
    }

}