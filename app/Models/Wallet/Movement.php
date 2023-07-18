<?php

namespace App\Models\Wallet;

use Illuminate\Database\Eloquent\Model;
use App\Clases\DataTable\SSP;

class Movement extends Model
{

    protected $fillable = ['electrical_pocket_id', 'document_type', 'document_number', 'movement_type_id', 'nature_movement', 'store_id', 'cus', 'cus_transaction', 'movement_date', 'user_created', 'user_updated'];

    private $columnsdatatable = array(
        array( 'db' => 'm.id', 'dt' => 0 ),
        array( 'db' => 'ep.code AS electrical_pocket_code',  'dt' => 1 ),
        array( 'db' => 'ep.name AS electrical_pocket_name', 'dt' => 2),
        array( 'db' => 'tdt.name AS transaction_document_type_name', 'dt' => 3),
        array( 'db' => 'm.document_number', 'dt' => 4),
        array( 'db' => 'mt.name AS movement_type_name', 'dt' => 5),
        array( 'db' => 'm.value', 'dt' => 6),
        array( 'db' => 'm.user_code', 'dt' => 6),
        array( 'db' => 's.name AS store_name', 'dt' => 6),
        array( 'db' => 'm.cus', 'dt' => 6),
        array( 'db' => 'm.movement_date', 'dt' => 6)
    );

    public function getDataTable($param){

        $asset =
            \DB::table('movements AS m')
            ->selectRaw("
                m.id,
                ep.code AS electrical_pocket_code,
                ep.name AS electrical_pocket_name,
                tdt.name AS transaction_document_type_name,
                m.document_number,
                mt.name AS movement_type_name,
                m.value,
                m.user_code,
                s.name AS store_name,
                m.cus,
                m.movement_date
            ")
            ->join('wallet_user_electronic_pockets AS wuep', 'wuep.id', '=','m.electrical_pocket_id')
            ->join('electrical_pockets AS ep', 'ep.id', '=','wuep.electronic_pocket_id')
            ->join('movement_types AS mt', 'mt.id', '=','m.movement_type_id')
            ->join('stores AS s', 's.id', '=','m.store_id')
            ->leftJoin('detail_definitions AS tdt', 'tdt.id', '=','m.transaction_document_type_id')
            ->whereDate('m.movement_date', '>=', $param['extradata']['from_date'])
            ->whereDate('m.movement_date', '<=', $param['extradata']['to_date'])
            ->orderBy('m.id', 'desc');

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
        
        $asset =
            \DB::table('movements AS m')
            ->selectRaw("
                m.id,
                ep.code AS electrical_pocket_code,
                ep.name AS electrical_pocket_name,
                tdt.name AS transaction_document_type_name,
                m.document_number,
                mt.name AS movement_type_name,
                m.value,
                m.user_code,
                s.name AS store_name,
                m.cus,
                m.movement_date
            ")
            ->join('wallet_user_electronic_pockets AS wuep', 'wuep.id', '=','m.electrical_pocket_id')
            ->join('electrical_pockets AS ep', 'ep.id', '=','wuep.electronic_pocket_id')
            ->join('movement_types AS mt', 'mt.id', '=','m.movement_type_id')
            ->join('stores AS s', 's.id', '=','m.store_id')
            ->leftJoin('detail_definitions AS tdt', 'tdt.id', '=','m.transaction_document_type_id')
            ->whereDate('m.movement_date', '>=', $param['extradata']['from_date'])
            ->whereDate('m.movement_date', '<=', $param['extradata']['to_date'])
            ->orderBy('m.id', 'desc');

        $where = '';
        $bindings = array();
		$wheretable = SSP::filter( $_POST, $this->columnsdatatable, $bindings );

        if($wheretable != ''){
            $where = $wheretable;
        }

        if(!empty($where)){
            $asset->whereRaw($where);
        }

        $datares['canfiltered'] = $asset->count();

        # CANTIDAD TOTAL
        $asset =
            \DB::table('movements AS m')
            ->selectRaw("
                m.id,
                ep.code AS electrical_pocket_code,
                ep.name AS electrical_pocket_name,
                tdt.name AS transaction_document_type_name,
                m.document_number,
                mt.name AS movement_type_name,
                m.value,
                m.user_code,
                s.name AS store_name,
                m.cus,
                m.movement_date
            ")
            ->join('wallet_user_electronic_pockets AS wuep', 'wuep.id', '=','m.electrical_pocket_id')
            ->join('electrical_pockets AS ep', 'ep.id', '=','wuep.electronic_pocket_id')
            ->join('movement_types AS mt', 'mt.id', '=','m.movement_type_id')
            ->join('stores AS s', 's.id', '=','m.store_id')
            ->leftJoin('detail_definitions AS tdt', 'tdt.id', '=','m.transaction_document_type_id')
            ->whereDate('m.movement_date', '>=', $param['extradata']['from_date'])
            ->whereDate('m.movement_date', '<=', $param['extradata']['to_date'])
            ->orderBy('m.id', 'desc');

        $datares['cantotal'] = $asset->count();;
        
        return $datares;

    }

}
