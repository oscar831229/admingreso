<?php

namespace App\Models\Sisafi;

use Illuminate\Database\Eloquent\Model;
use App\Clases\DataTable\SSP;

class SisafiSyncTracer extends Model
{
    protected $fillable = ['start_date', 'type_document', 'state', 'type_synchronization', 'type_execution', 'document_number', 'user_created', 'errors'];

    private $columnsdatatable = array(
        array( 'db' => "sst.id" , 'dt' => 0),
        array( 'db' => "CASE
                WHEN sst.type_execution = 'M' THEN 'Manual'
                WHEN sst.type_execution = 'A' THEN 'Automatica'
            END" , 'dt' => 1),
        array( 'db' => "sst.type_document" , 'dt' => 2),
        array( 'db' => "sst.document_number" , 'dt' => 3),
        array( 'db' => "CASE
                WHEN sst.state = 'P' THEN 'Programado'
                WHEN sst.state = 'E' THEN 'En ejecución'
                WHEN sst.state = 'F' THEN 'Finalizado'
            END" , 'dt' => 4),
        array( 'db' => "u.name" , 'dt' => 5),
        array( 'db' => "st.created_at" , 'dt' => 6),
        array( 'db' => "st.updated_at" , 'dt' => 7),
    );

    public function getDataTable($param){

        $asset = \DB::table('sisafi_sync_tracers AS sst')
        ->selectRaw("
            sst.id,
            CASE
                WHEN sst.type_synchronization = 'T' THEN 'Total'
                WHEN sst.type_synchronization = 'I' THEN 'Individual'
            END type_synchronization,
            CASE
                WHEN sst.type_execution = 'M' THEN 'Manual'
                WHEN sst.type_execution = 'A' THEN 'Automatica'
            END type_execution,
            sst.type_document,
            sst.document_number,
            CASE
                WHEN sst.state = 'P' THEN 'Programado'
                WHEN sst.state = 'E' THEN 'En ejecución'
                WHEN sst.state = 'F' THEN 'Finalizado'
                WHEN sst.state = 'B' THEN 'Finalizado con error'
            END state,
            u.name as user_created,
            sst.created_at,
            sst.updated_at
        ")
        ->join('users AS u', 'u.id', '=','sst.user_created');


        if(isset($param['extradata']['type_synchronization'])){
            $asset->where(['sst.type_synchronization' => $param['extradata']['type_synchronization']]);
        }

        if(isset($param['extradata']['type_execution'])){
            $asset->where(['sst.type_execution' => $param['extradata']['type_execution']]);
        }

        if(isset($param['extradata']['date_from'])){
            $asset->whereDate('sst.created_at', '>=', $param['extradata']['date_from']);
        }

        if(isset($param['extradata']['date_to'])){
            $asset->whereDate('sst.created_at', '<=', $param['extradata']['date_to']);
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

        $asset = \DB::table('sisafi_sync_tracers AS sst')
        ->selectRaw("
            sst.id,
            sst.type_document,
            sst.document_number,
            CASE
                WHEN sst.state = 'P' THEN 'En ejecución'
                WHEN sst.state = 'F' THEN 'Finalizado'
            END state,
            u.name as user_created,
            sst.created_at,
            sst.updated_at
        ")
        ->join('users AS u', 'u.id', '=','sst.user_created')
        ->orderBy('sst.id', 'DESC');


        if(isset($param['extradata']['state'])){
            $asset->where(['sst.state' => $param['extradata']['state']]);
        }

        if(isset($param['extradata']['date_from'])){
            $asset->whereDate('sst.created_at', '>=', $param['extradata']['date_from']);
        }

        if(isset($param['extradata']['date_to'])){
            $asset->whereDate('sst.created_at', '<=', $param['extradata']['date_to']);
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
        $asset = \DB::table('sisafi_sync_tracers AS sst')
        ->selectRaw("
            sst.id,
            sst.type_document,
            sst.document_number,
            CASE
                WHEN sst.state = 'P' THEN 'En ejecución'
                WHEN sst.state = 'F' THEN 'Finalizado'
            END state,
            u.name as user_created,
            sst.created_at,
            sst.updated_at
        ")
        ->join('users AS u', 'u.id', '=','sst.user_created')
        ->orderBy('sst.id', 'DESC');


        if(isset($param['extradata']['state'])){
            $asset->where(['sst.state' => $param['extradata']['state']]);
        }

        if(isset($param['extradata']['date_from'])){
            $asset->whereDate('sst.created_at', '>=', $param['extradata']['date_from']);
        }

        if(isset($param['extradata']['date_to'])){
            $asset->whereDate('sst.created_at', '<=', $param['extradata']['date_to']);
        }

        $datares['cantotal'] = $asset->count();

        return $datares;

    }

}
