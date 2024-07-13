<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;
use App\Clases\DataTable\SSP;

class IcmRateType extends Model
{

    protected $fillable = [
        'name',
        'code',
        'state',
        'user_created',
        'user_updated'
    ];

    private $columnsdatatable = array(
        array( 'db' => 'irt.id' , 'dt' => 0),
        array( 'db' => 'irt.name' , 'dt' => 1),
        array( 'db' => "CASE
                    WHEN irt.code = 'V' THEN 'TEMPORADA BAJA'
                    WHEN irt.code = 'A' THEN 'TEMPORADA ALTA'
                END" , 'dt' => 2),
        array( 'db' => 'u.name' , 'dt' => 3),
        array( 'db' => 'irt.state' , 'dt' => 4)
    );

    public function getDataTable($param){

        $asset = \DB::table('icm_rate_types as irt')
        ->selectRaw("
            irt.id,
            irt.name,
            CASE
                WHEN irt.code = 'V' THEN 'TEMPORADA BAJA'
                WHEN irt.code = 'A' THEN 'TEMPORADA ALTA'
            END AS code_name,
            u.name as user_created,
            irt.state,
            '' as action
        ")
        ->join('users AS u', 'u.id', '=','irt.user_created')
        ->orderBy('irt.name', 'DESC');

        $where = '';
        $bindings = array();
		$wheretable = SSP::filter( $_POST, $this->columnsdatatable, $bindings );

        if($wheretable != ''){
            $where = $wheretable;
        }

        if(!empty($where)){
            $data = $asset->whereRaw($where)
                ->offset($param['start'])
                ->limit($param['length'])
                ->get();
        }else{
            $data = $asset
                ->offset($param['start'])
                ->limit($param['length'])
                ->get();
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

        $asset = \DB::table('icm_rate_types as irt')
        ->selectRaw("
            irt.id,
            irt.cod,
            irt.name,
            CASE
                WHEN irt.code = 'V' THEN 'TARIFA BAJA'
                WHEN irt.code = 'A' THEN 'TARIFA ALTA'
            END AS code_name,
            u.name as user_created,
            irt.state,
            '' as action
        ")
        ->join('users AS u', 'u.id', '=','irt.user_created')
        ->orderBy('irt.name', 'DESC');

        $bindings = array();
        $wheretable = SSP::filter( $_POST, $this->columnsdatatable, $bindings );

        $where = '';
        if($wheretable != ''){
            $where = $wheretable;
        }

        if(!empty($where)){
            $asset->whereRaw($where);
        }

        $datares['canfiltered'] = $asset->count();

        # CANTIDAD TOTAL
        $asset = \DB::table('icm_rate_types as irt')
        ->selectRaw("
            irt.id,
            irt.name,
            CASE
                WHEN irt.code = 'V' THEN 'TEMPORADA BAJA'
                WHEN irt.code = 'A' THEN 'TEMPORADA ALTA'
            END AS code_name,
            u.name as user_created,
            irt.state,
            '' as action
        ")
        ->join('users AS u', 'u.id', '=','irt.user_created')
        ->orderBy('irt.name', 'DESC');

        $datares['cantotal'] = $asset->count();

        return $datares;

    }


}
