<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;
use App\Clases\DataTable\SSP;

class IcmLiquidation extends Model
{
    protected $fillable = [
        'uuid',
        'sales_icm_environment_id',
        'icm_environment_id',
        'document_type',
        'document_number',
        'first_name',
        'second_name',
        'first_surname',
        'second_surname',
        /*'birthday_date',
        'gender', */
        'total',
        'liquidation_date',
        'state',
        'icm_resolution_id',
        'billing_prefix',
        'consecutive_billing',
        'user_created',
        'user_updated'
    ];

    private $columnsdatatable = array(
        array( 'db' => "il.id" , 'dt' => 0),
        array( 'db' => "LPAD(il.id, 10, '0')" , 'dt' => 1),
        array( 'db' => "il.liquidation_date" , 'dt' => 2),
        array( 'db' => "CONCAT(IFNULL(ic.first_name, ''), ' ', IFNULL(ic.second_name, ''), ' ', IFNULL(ic.first_surname, ''), ' ', IFNULL(ic.second_surname, '') )" , 'dt' => 3),
        array( 'db' => "ie.name" , 'dt' => 4),
        array( 'db' => "il.state" , 'dt' => 5),
        array( 'db' => "CONCAT(IFNULL(il.billing_prefix, ''), IFNULL(il.consecutive_billing, ''))" , 'dt' => 6),
    );

    public function getDataTable($param){

        $asset = \DB::table('icm_liquidations AS il')
        ->selectRaw("
            il.id,
            LPAD(il.id, 10, '0') AS number_liquidation,
            il.liquidation_date,
            CONCAT(IFNULL(ic.first_name, ''), ' ', IFNULL(ic.second_name, ''), ' ', IFNULL(ic.first_surname, ''), ' ', IFNULL(ic.second_surname, '') ) AS customer_name,
            ie.name AS environment_name,
            il.state,
            CONCAT(IFNULL(il.billing_prefix, ''), IFNULL(il.consecutive_billing, '')) AS invoice,
            '' as action
        ")
        ->join('icm_customers AS ic', 'ic.document_number', '=','il.document_number')
        ->join('icm_environments AS ie', 'ie.id', '=','il.sales_icm_environment_id')
        ->where(['is_deleted' => 0])
        ->orderBy('il.id', 'ASC');


        if(isset($param['extradata']['state'])){
            $asset->where(['il.state' => $param['extradata']['state']]);
        }

        if(isset($param['extradata']['date_from']) && isset($param['extradata']['date_to'])){
            $asset->whereBetween('il.liquidation_date', [$param['extradata']['date_from'], $param['extradata']['date_to']]);
        }

        if(isset($param['extradata']['date_from']) && !isset($param['extradata']['date_to'])){
            $asset->whereBetween('il.liquidation_date', [$param['extradata']['date_from'], $param['extradata']['date_from']]);
        }

        if(!isset($param['extradata']['date_from']) && isset($param['extradata']['date_to'])){
            $asset->whereBetween('il.liquidation_date', [$param['extradata']['date_to'], $param['extradata']['date_to']]);
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

        $asset = \DB::table('icm_liquidations AS il')
        ->selectRaw("
            il.id,
            LPAD(il.id, 10, '0') AS number_liquidation,
            il.liquidation_date,
            CONCAT(IFNULL(ic.first_name, ''), ' ', IFNULL(ic.second_name, ''), ' ', IFNULL(ic.first_surname, ''), ' ', IFNULL(ic.second_surname, '') ) AS customer_name,
            ie.name AS environment_name,
            il.state,
            CONCAT(IFNULL(il.billing_prefix, ''), IFNULL(il.consecutive_billing, '')) AS invoice
            '' as action
        ")
        ->join('icm_customers AS ic', 'ic.document_number', '=','il.document_number')
        ->join('icm_environments AS ie', 'ie.id', '=','il.sales_icm_environment_id')
        ->where(['is_deleted' => 0])
        ->orderBy('iac.name', 'ASC');

        if(isset($param['extradata']['state'])){
            $asset->where(['il.state' => $param['extradata']['state']]);
        }

        if(isset($param['extradata']['date_from']) && isset($param['extradata']['date_to'])){
            $asset->whereBetween('il.liquidation_date', [$param['extradata']['date_from'], $param['extradata']['date_to']]);
        }

        if(isset($param['extradata']['date_from']) && !isset($param['extradata']['date_to'])){
            $asset->whereBetween('il.liquidation_date', [$param['extradata']['date_from'], $param['extradata']['date_from']]);
        }

        if(!isset($param['extradata']['date_from']) && isset($param['extradata']['date_to'])){
            $asset->whereBetween('il.liquidation_date', [$param['extradata']['date_to'], $param['extradata']['date_to']]);
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
        $asset = \DB::table('icm_liquidations AS il')
        ->selectRaw("
            il.id,
            LPAD(il.id, 10, '0') AS number_liquidation,
            il.liquidation_date,
            CONCAT(IFNULL(ic.first_name, ''), ' ', IFNULL(ic.second_name, ''), ' ', IFNULL(ic.first_surname, ''), ' ', IFNULL(ic.second_surname, '') ) AS customer_name,
            ie.name AS environment_name,
            il.state,
            CONCAT(IFNULL(il.billing_prefix, ''), IFNULL(il.consecutive_billing, '')) AS invoice
            '' as action
        ")
        ->join('icm_customers AS ic', 'ic.document_number', '=','il.document_number')
        ->join('icm_environments AS ie', 'ie.id', '=','il.sales_icm_environment_id')
        ->where(['is_deleted' => 0])
        ->orderBy('iac.name', 'ASC');

        if(isset($param['extradata']['state'])){
            $asset->where(['il.state' => $param['extradata']['state']]);
        }

        if(isset($param['extradata']['date_from']) && isset($param['extradata']['date_to'])){
            $asset->whereBetween('il.liquidation_date', [$param['extradata']['date_from'], $param['extradata']['date_to']]);
        }

        if(isset($param['extradata']['date_from']) && !isset($param['extradata']['date_to'])){
            $asset->whereBetween('il.liquidation_date', [$param['extradata']['date_from'], $param['extradata']['date_from']]);
        }

        if(!isset($param['extradata']['date_from']) && isset($param['extradata']['date_to'])){
            $asset->whereBetween('il.liquidation_date', [$param['extradata']['date_to'], $param['extradata']['date_to']]);
        }

        $datares['cantotal'] = $asset->count();

        return $datares;

    }

    public static function getDetailsServices($icm_liquidacion_id){
        $querySQL = "SELECT
                    ils.id,
                    ils.applied_rate_code,
                    ieii.name AS icm_environment_income_item_name,
                    ils.number_places,
                    ils.base,
                    ils.iva,
                    ils.impoconsumo,
                    ils.total,
                    ils.icm_type_subsidy_id,
                    ils.discount,
                    ils.subsidy
            FROM `icm_liquidation_services` AS ils
            INNER JOIN icm_income_items AS ieii ON ieii.id = ils.icm_income_item_id
            INNER JOIN icm_environment_icm_menu_items AS ieimi ON ieimi.id = ils.icm_environment_icm_menu_item_id
            INNER JOIN icm_menu_items AS imi ON imi.id = ieimi.icm_menu_item_id
            WHERE ils.icm_liquidation_id = ? AND ils.is_deleted = 0";

        return \DB::select($querySQL, [$icm_liquidacion_id]);

    }

    public function icm_liquidation_services(){
        return $this->hasMany('App\Models\Income\IcmLiquidationService');
    }

    public function icm_liquidation_details(){
        return $this->hasMany('App\Models\Income\IcmLiquidationDetail');
    }

    public function icm_liquidation_payments(){
        return $this->hasMany('App\Models\Income\IcmLiquidationPayment');
    }

    public function icm_customer(){
        return $this->belongsTo('App\Models\Income\IcmCustomer', 'document_number', 'document_number');
    }







}
