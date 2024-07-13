<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;
use App\Clases\DataTable\SSP;

class IcmIncomeItem extends Model
{
    protected $fillable = [
        'name',
        'code',
        'number_places',
        'value',
        'value_high',
        'observations',
        'icm_environment_id',
        'icm_environment_icm_menu_item_id',
        'code_seac',
        'icm_type_subsidy_id',
        'state',
        'user_created',
        'user_updated'
    ];

    private $columnsdatatable = array(
        array( 'db' => 'ic.id' , 'dt' => 0),
        array( 'db' => 'icie.lot_number' , 'dt' => 1),
        array( 'db' => 'icie.invima_registration' , 'dt' => 2),
        array( 'db' => 'icie.box_number' , 'dt' => 3),
        array( 'db' => 'icie.expiration_date' , 'dt' => 4),
        array( 'db' => 'ic.date_in' , 'dt' => 5),
        array( 'db' => 'ic.date_use' , 'dt' => 5),
        array( 'db' => 'ic.date_end' , 'dt' => 5),
        array( 'db' => 'u.name' , 'dt' => 5),
        array( 'db' => "RTRIM(LTRIM(CONCAT(IFNULL(p.first_name, ''), ' ', IFNULL(p.second_name, ''), ' ', IFNULL(p.first_surname, ''), ' ', IFNULL(p.second_surname, ''))))" , 'dt' => 5),
        array( 'db' => "CASE
                    WHEN IFNULL(ic.date_use, '') = '' AND IFNULL(ic.date_end, '') = '' THEN 'P'
                    WHEN IFNULL(ic.date_use, '') <> '' AND IFNULL(ic.date_end, '') = '' THEN 'U'
                    WHEN IFNULL(ic.date_use, '') <> '' AND IFNULL(ic.date_end, '') <> '' THEN 'T'
                    ELSE 'DESCONOCIDO'
                END" , 'dt' => 5)
    );

    public function getDataTable($param){

        $asset = \DB::table('icm_income_items AS ieii')
        ->selectRaw("
            ieii.id,
            ieii.code,
            ieii.name,
            ieii.value,
            ieii.number_places,
            u.name as user_created,
            ieii.state,
            '' as action
        ")
        ->join('users AS u', 'u.id', '=','ieii.user_created')
        ->orderBy('ieii.name', 'ASC');

        # Estado del consumible
        if(isset($param['extradata']['icm_environment_id'])) {
            $asset->whereRaw("ieii.icm_environment_id = '{$param['extradata']['icm_environment_id']}'");
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

        $asset = \DB::table('icm_income_items AS ieii')
        ->selectRaw("
            ieii.code,
            ieii.name,
            ieii.number_places,
            ieii.value,
            ieii.state,
            ieii.user_created
        ")
        ->join('users AS u', 'u.id', '=','ieii.user_created')
        ->orderBy('ieii.name', 'DESC');

        # Estado del consumible
        if(isset($param['extradata']['icm_environment_id'])) {
            $asset->whereRaw("ieii.icm_environment_id = '{$param['extradata']['icm_environment_id']}'");
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
        $asset = \DB::table('icm_income_items AS ieii')
        ->selectRaw("
            ieii.code,
            ieii.name,
            ieii.number_places,
            ieii.value,
            ieii.state,
            ieii.user_created
        ")
        ->join('users AS u', 'u.id', '=','ieii.user_created')
        ->orderBy('ieii.name', 'DESC');

        # Estado del consumible
        if(isset($param['extradata']['icm_environment_id'])) {
            $asset->whereRaw("ieii.icm_environment_id = '{$param['extradata']['icm_environment_id']}'");
        }

        $datares['cantotal'] = $asset->count();

        return $datares;

    }

    public function icm_income_item_details(){
        return $this->hasMany('App\Models\Income\IcmIncomeItemDetail');
    }

    public function icm_environment_icm_menu_item(){
        return $this->belongsTo('App\Models\Income\IcmEnvironmentIcmMenuItem');
    }

    public function icm_environment(){
        return $this->belongsTo('App\Models\Income\IcmEnvironment');
    }

    public function icm_type_subsidy(){
        return $this->belongsTo('App\Models\Income\IcmTypeSubsidy');
    }
}
