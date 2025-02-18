<?php

namespace App\Models\Seac;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sisafi\SisafiSeacPersonas;

class ClientesSeac extends Model
{
    protected $connection = 'oracle_second';

    protected $table = 'SEAC.V_SEAC_PERSONAS';

    protected $fillable = ['identificacion', 'id_principal'];

    public function getDataTable($param){

        if(!isset($param['extradata']['identificacion']) && !isset($param['extradata']['id_principal'])){
            return [];
        }

        $query = \DB::connection('oracle_second')->table('SEAC.V_SEAC_PERSONAS')->selectRaw("
            identificacion as id,
            tipo_id,
            identificacion,
            primer_nombre || ' ' || segundo_nombre || ' ' || primer_apellido || ' ' || segundo_apellido AS nombre_completo,
            tipoid_ppal,
            id_principal,
            primer_nombre_ppal || ' ' || segundo_nombre_ppal || ' ' || primer_apellido_ppal || ' ' || segundo_apellido_ppal AS nombre_completo_ppal,
            nit_empresa,
            razon_social,
            '' as categoria_ingresos,
            categoria as categoria_sisafi
        ");

        if(isset($param['extradata']['identificacion'])){
            $query->where('identificacion', '=', $param['extradata']['identificacion']);
        }

        if(isset($param['extradata']['id_principal'])){
            $query->where('id_principal', '=', $param['extradata']['id_principal']);
        }

        $data = $query->get();

        # Asignar categoria ingresos
        foreach ($data as $key => &$value) {

            $afiliado = SisafiSeacPersonas::where([
                'tipo_id'        => $value->tipo_id,
                'identificacion' => $value->identificacion,
                'tipoid_ppal'    => $value->tipoid_ppal,
                'id_principal'   => $value->id_principal
            ])->first();

            $value->categoria_ingresos = $afiliado ? $afiliado->categoria : '';

        }

        $datares = array();
        $number = $param['start'] + 1 ;
        foreach ($data as $key => $value) {
            $value->number = $number;
            $datares[] = $value;
            $number++;
        }

        return $datares;

    }

    public function getCountDatatable($param) {

        if(!isset($param['extradata']['identificacion']) && !isset($param['extradata']['id_principal'])){
            $datares['canfiltered'] = 0;
            $datares['cantotal']    = 0;
            return $datares;
        }

        $query = \DB::connection('oracle_second')->table('SEAC.V_SEAC_PERSONAS');

        if(isset($param['extradata']['identificacion'])){
            $query->where('identificacion', '=', $param['extradata']['identificacion']);
        }

        if(isset($param['extradata']['id_principal'])){
            $query->where('id_principal', '=', $param['extradata']['id_principal']);
        }

        $datares['canfiltered'] = $query->count();
        $datares['cantotal']    = $datares['canfiltered'];

        return $datares;

    }

}
