<?php
namespace App\Controllers;

use App\Models\Actividad;

class ActividadController
{
    public function index(){
        try{
            $actividades = Actividad::orderBy('producto','asc')->get();

            echo json_encode($actividades);
        }catch (\Exception $e){
            http_response_code(500);
            echo json_encode(["error" => "Error al consultar las actividades: " . $e->getMessage()]);
        }
    }
}