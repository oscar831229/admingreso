<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

use App\Clases\DataTable\SSP;

class IcmSystemConfiguration extends Model
{

    protected $fillable = ['url_pos_system', 'pos_system_token', 'state', 'user_created', 'user_updated'];

    private $columnsdatatable = array(
        array( 'db' => 'isc.id' , 'dt' => 0),
        array( 'db' => 'isc.url_pos_system' , 'dt' => 1),
    );

    public function getDataTable($param){

        $asset = \DB::table('icm_system_configurations as isc')
        ->selectRaw("
            isc.id,
            isc.url_pos_system,
            '' as action
        ")
        ->join('users AS u', 'u.id', '=','isc.user_created')
        ->orderBy('isc.id', 'DESC');

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

        $asset = \DB::table('icm_system_configurations as isc')
        ->selectRaw("
            isc.id,
            isc.url_pos_system,
            '' as action
        ")
        ->join('users AS u', 'u.id', '=','isc.user_created')
        ->orderBy('isc.id', 'DESC');

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
        $asset = \DB::table('icm_system_configurations as isc')
        ->selectRaw("
            isc.id,
            isc.url_pos_system,
            '' as action
        ")
        ->join('users AS u', 'u.id', '=','isc.user_created')
        ->orderBy('isc.id', 'DESC');

        $datares['cantotal'] = $asset->count();

        return $datares;

    }

}
