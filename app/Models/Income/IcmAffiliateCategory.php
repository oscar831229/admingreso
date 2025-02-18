<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;
use App\Clases\DataTable\SSP;

class IcmAffiliateCategory extends Model
{

    protected $fillable = [
        'name',
        'code',
        'state',
        'user_created',
        'user_updated'
    ];

    private $columnsdatatable = array(
        array( 'db' => 'iac.id'    , 'dt' => 0),
        array( 'db' => 'iac.code'  , 'dt' => 1),
        array( 'db' => 'iac.name'  , 'dt' => 2),
        array( 'db' => 'u.name'    , 'dt' => 3),
        array( 'db' => 'iac.state' , 'dt' => 4),
    );

    public function getDataTable($param){

        $asset = \DB::table('icm_affiliate_categories as iac')
        ->selectRaw("
            iac.id,
            iac.code,
            iac.name,
            u.name as user_created,
            iac.state,
            '' as action
        ")
        ->join('users AS u', 'u.id', '=','iac.user_created')
        ->orderBy('iac.name', 'ASC');

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

        $asset = \DB::table('icm_affiliate_categories as iac')
        ->selectRaw("
            iac.id,
            iac.code,
            iac.name,
            u.name as user_created,
            iac.state,
            '' as action
        ")
        ->join('users AS u', 'u.id', '=','iac.user_created')
        ->orderBy('iac.name', 'DESC');

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
        $asset = \DB::table('icm_affiliate_categories as iac')
        ->selectRaw("
            iac.id,
            iac.code,
            iac.name,
            u.name as user_created,
            iac.state,
            '' as action
        ")
        ->join('users AS u', 'u.id', '=','iac.user_created')
        ->orderBy('iac.name', 'DESC');

        $datares['cantotal'] = $asset->count();

        return $datares;

    }

}
