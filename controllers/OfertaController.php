<?php
namespace App\Controllers;

use App\Models\Oferta;

class OfertaController
{
    public function index(){
        try{
            $ofertas = Oferta::with('actividad')->orderBy('id', 'desc')->get();
            echo json_encode($ofertas);
        } catch (\exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al consultar ofertas: ". $e->getMessage()]);
        }
    }

    public function store(){
        $data = json_decode(file_get_contents("php://input"),true);

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

}