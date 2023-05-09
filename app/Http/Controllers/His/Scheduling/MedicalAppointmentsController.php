<?php

namespace App\Http\Controllers\His\Scheduling;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\His\Scheduling\HisMedicalScheduling;
use App\Http\Resources\His\MedicalScheduling\MedicalSchedulingResource;

use Carbon\Carbon;


class MedicalAppointmentsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $request = request();
        $filters = $request->query();

        # Verificar parametros
        

        $errors = [];

        if(!isset($filters['start_date']) || empty($filters['start_date'])){
            $errors[] = 'Debe indicar una fecha inicial de la agenda';
        }

        if(!isset($filters['end_date']) || empty($filters['end_date'])){
            $errors[] = 'Debe indicar una fecha final de la agenda';
        }

        if(!validateDate($filters['start_date'], 'Ymd')){
            $errors[] = 'Fecha inicial de agenda incorrecta';
        }

        if(!validateDate($filters['end_date'], 'Ymd')){
            $errors[] = 'Fecha final de agenda incorrecta';
        }
        
        $start_date = $filters['start_date'];
        $end_date = $filters['end_date'];

        $datetime1=new \DateTime($start_date);
        $datetime2=new \DateTime($end_date);
        
        # obtenemos la diferencia entre las dos fechas
        $interval=$datetime2->diff($datetime1);
        $nummonths = $intervalMeses=$interval->format("%a");

        if($nummonths > 29 && !isset($filters['specialty_code'])){
            $errors[] = 'Maximo consulta 1 mes cuando no se espeficica especialidad';
        }

        if($nummonths > 89 && isset($filters['specialty_code'])){
            $errors[] = 'Maximo consulta 3 meses';
        }


        if(count($errors) > 0){
            return response()->json([
                'success' => false,
                'message' => implode(', ',$errors),
                'data' => []
            ]);
        }

        # Consultar agenda medica
        $scheduling = HisMedicalScheduling::getMedicalSchedulingFilters($filters);
                
        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $scheduling
        ], 200);

    }

}
