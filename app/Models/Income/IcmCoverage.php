<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;
use App\Clases\DataTable\SSP;

class IcmCoverage extends Model
{
    protected $fillable = ['coverage_date', 'step', 'events', 'errors', 'state', 'user_created', 'user_updated'];


    private $columnsdatatable = array(
        array( 'db' => "ic.id" , 'dt' => 0),
        array( 'db' => "ic.coverage_date" , 'dt' => 1),
        array( 'db' => "ic.step" , 'dt' => 2),
        array( 'db' => "ic.step_name" , 'dt' => 3),
        array( 'db' => "ic.events" , 'dt' => 4),
        array( 'db' => "ic.state" , 'dt' => 5),
        array( 'db' => "ic.created_at" , 'dt' => 6),
    );

    public function getDataTable($param){

        $asset = \DB::table('icm_coverages AS ic')
        ->selectRaw("
            ic.id,
            ic.coverage_date,
            ic.step,
            ic.step_name,
            ic.events,
            ic.state,
            ic.created_at,
            ic.errors as action
        ")
        ->join('users AS uc', 'uc.id', '=','ic.user_created')
        ->where(['is_deleted' => 0])
        ->orderBy('ic.coverage_date', 'DESC');


        if(isset($param['extradata']['state']) && !empty($param['extradata']['state'])){
            $asset->where(['ic.state' => $param['extradata']['state']]);
        }

        if(isset($param['extradata']['date_from']) && isset($param['extradata']['date_to'])){
            $asset->whereBetween('ic.coverage_date', [$param['extradata']['date_from'], $param['extradata']['date_to']]);
        }

        if(isset($param['extradata']['date_from']) && !isset($param['extradata']['date_to'])){
            $asset->whereBetween('ic.coverage_date', [$param['extradata']['date_from'], $param['extradata']['date_from']]);
        }

        if(!isset($param['extradata']['date_from']) && isset($param['extradata']['date_to'])){
            $asset->whereBetween('ic.coverage_date', [$param['extradata']['date_to'], $param['extradata']['date_to']]);
        }


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

        $asset = \DB::table('icm_coverages AS ic')
        ->selectRaw("
            ic.id,
            ic.coverage_date,
            ic.step,
            ic.step_name,
            ic.events,
            ic.errors,
            ic.state,
            '' as action
        ")
        ->join('users AS uc', 'uc.id', '=','ic.user_created')
        ->where(['is_deleted' => 0])
        ->orderBy('ic.coverage_date', 'DESC');


        if(isset($param['extradata']['state'])){
            $asset->where(['ic.state' => $param['extradata']['state']]);
        }

        if(isset($param['extradata']['date_from']) && isset($param['extradata']['date_to'])){
            $asset->whereBetween('ic.coverage_date', [$param['extradata']['date_from'], $param['extradata']['date_to']]);
        }

        if(isset($param['extradata']['date_from']) && !isset($param['extradata']['date_to'])){
            $asset->whereBetween('ic.coverage_date', [$param['extradata']['date_from'], $param['extradata']['date_from']]);
        }

        if(!isset($param['extradata']['date_from']) && isset($param['extradata']['date_to'])){
            $asset->whereBetween('ic.coverage_date', [$param['extradata']['date_to'], $param['extradata']['date_to']]);
        }

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
        $asset = \DB::table('icm_coverages AS ic')
        ->selectRaw("
            ic.id,
            ic.coverage_date,
            ic.step,
            ic.step_name,
            ic.events,
            ic.errors,
            ic.state,
            '' as action
        ")
        ->join('users AS uc', 'uc.id', '=','ic.user_created')
        ->where(['is_deleted' => 0])
        ->orderBy('ic.coverage_date', 'DESC');


        if(isset($param['extradata']['state'])){
            $asset->where(['ic.state' => $param['extradata']['state']]);
        }

        if(isset($param['extradata']['date_from']) && isset($param['extradata']['date_to'])){
            $asset->whereBetween('ic.coverage_date', [$param['extradata']['date_from'], $param['extradata']['date_to']]);
        }

        if(isset($param['extradata']['date_from']) && !isset($param['extradata']['date_to'])){
            $asset->whereBetween('ic.coverage_date', [$param['extradata']['date_from'], $param['extradata']['date_from']]);
        }

        if(!isset($param['extradata']['date_from']) && isset($param['extradata']['date_to'])){
            $asset->whereBetween('ic.coverage_date', [$param['extradata']['date_to'], $param['extradata']['date_to']]);
        }

        $datares['cantotal'] = $asset->count();

        return $datares;

    }


}
