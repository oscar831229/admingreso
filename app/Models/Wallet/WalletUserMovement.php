<?php

namespace App\Models\Wallet;

use Illuminate\Database\Eloquent\Model;
use App\Clases\DataTable\SSP;

class WalletUserMovement extends Model
{
    
    private $columnsdatatable = array(
        array( 'db' => 'm.id', 'dt' => 0 ),
        array( 'db' => "CONCAT(ep.code, ' ', ep.name)",  'dt' => 1 ),
        array( 'db' => 'ws.document_number', 'dt' => 2),
        array( 'db' => "CONCAT(IFNULL(mt.code,''), ' ', IFNULL(mt.name, ''))", 'dt' => 3),
        array( 'db' => "m.value", 'dt' => 4),
        array( 'db' => 'm.user_code', 'dt' => 5),
        array( 'db' => 's.name', 'dt' => 6),
        array( 'db' => 'm.cus', 'dt' => 7),
        array( 'db' => 'm.created_at', 'dt' => 8),
        array( 'db' => '', 'dt' => 9)
    );

    public function getDataTable($param){

        $asset =
            \DB::table('movements AS m')
            ->selectRaw("
                m.id,
                CONCAT(ep.code, ' ', ep.name) AS electrical_pocket_code,
                ws.document_number AS document_number,
                CONCAT(IFNULL(mt.code,''), ' ', IFNULL(mt.name, '')) AS movement_type_name,
                m.value,
                m.user_code,
                s.name AS store_name,
                m.cus,
                m.created_at,
                '' as action
            ")
            ->join('electrical_pocket_wallet_user AS wuep', 'wuep.id', '=','m.electrical_pocket_wallet_user_id')
            ->join('electrical_pockets AS ep', 'ep.id', '=','wuep.electrical_pocket_id')
            ->join('movement_types AS mt', 'mt.id', '=','m.movement_type_id')
            ->join('stores AS s', 's.id', '=','m.store_id')
            ->join('wallet_users AS ws', 'ws.id', '=','m.wallet_user_id')
            ->leftJoin('detail_definitions AS tdt', 'tdt.id', '=','m.transaction_document_type_id')
            ->where('wuep.id', '=', $param['extradata']['electrical_pocket_wallet_user_id'])
            ->whereDate('m.movement_date', '>=', $param['extradata']['from_date'])
            ->whereDate('m.movement_date', '<=', $param['extradata']['to_date'])
            ->orderBy('m.id', 'desc');

        if(isset($param['extradata']['store_id']) && !empty($param['extradata']['store_id'])){
            $asset->where(['s.id' => $param['extradata']['store_id']]);
        }

        if(isset($param['extradata']['movement_type_id']) && !empty($param['extradata']['movement_type_id'])){
            $asset->where(['mt.id' => $param['extradata']['movement_type_id']]);
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
        
        $asset =
            \DB::table('movements AS m')
            ->selectRaw("
                m.id,
                CONCAT(ep.code, ' ', ep.name) AS electrical_pocket_code,
                ws.document_number AS document_number,
                CONCAT(IFNULL(mt.code,''), ' ', IFNULL(mt.name, '')) AS movement_type_name,
                m.value,
                m.user_code,
                s.name AS store_name,
                m.cus,
                m.created_at,
                '' as action
            ")
            ->join('electrical_pocket_wallet_user AS wuep', 'wuep.id', '=','m.electrical_pocket_wallet_user_id')
            ->join('electrical_pockets AS ep', 'ep.id', '=','wuep.electrical_pocket_id')
            ->join('movement_types AS mt', 'mt.id', '=','m.movement_type_id')
            ->join('stores AS s', 's.id', '=','m.store_id')
            ->join('wallet_users AS ws', 'ws.id', '=','m.wallet_user_id')
            ->leftJoin('detail_definitions AS tdt', 'tdt.id', '=','m.transaction_document_type_id')
            ->where('wuep.id', '=', $param['extradata']['electrical_pocket_wallet_user_id'])
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
                CONCAT(ep.code, ' ', ep.name) AS electrical_pocket_code,
                ws.document_number AS document_number,
                CONCAT(IFNULL(mt.code,''), ' ', IFNULL(mt.name, '')) AS movement_type_name,
                m.value,
                m.user_code,
                s.name AS store_name,
                m.cus,
                m.created_at,
                '' as action
            ")
            ->join('electrical_pocket_wallet_user AS wuep', 'wuep.id', '=','m.electrical_pocket_wallet_user_id')
            ->join('electrical_pockets AS ep', 'ep.id', '=','wuep.electrical_pocket_id')
            ->join('movement_types AS mt', 'mt.id', '=','m.movement_type_id')
            ->join('stores AS s', 's.id', '=','m.store_id')
            ->join('wallet_users AS ws', 'ws.id', '=','m.wallet_user_id')
            ->leftJoin('detail_definitions AS tdt', 'tdt.id', '=','m.transaction_document_type_id')
            ->where('wuep.id', '=', $param['extradata']['electrical_pocket_wallet_user_id'])
            ->orderBy('m.id', 'desc');

        $datares['cantotal'] = $asset->count();;
        
        return $datares;

    }
}
