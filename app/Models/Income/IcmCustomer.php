<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;
use App\Clases\DataTable\SSP;

class IcmCustomer extends Model
{
    protected $fillable = [
        'document_number',
        'document_type',
        'first_name',
        'second_name',
        'first_surname',
        'second_surname',
        'birthday_date',
        'gender',
        'icm_municipality_id',
        'address',
        'phone',
        'email',
        'type_regime_id',
        'type_liability_id',
        'tax_detail_id',
        'type_organization_id',
        'last_liquidation_date',
        'icm_types_income_id',
        'icm_affiliate_category_id',
        'user_created',
        'user_updated'
    ];

    private $columnsdatatable = array(
        array( 'db' => 'c.id' , 'dt' => 0),
        array( 'db' => 'c.document_number' , 'dt' => 1),
        array( 'db' => "CONCAT(IFNULL(c.first_name, ''), ' ', IFNULL(c.second_name, ''), ' ', IFNULL(c.first_surname, ''), ' ', IFNULL(c.second_surname, ''), ' ')" , 'dt' => 2),
        array( 'db' => 'c.phone' , 'dt' => 3),
        array( 'db' => 'c.email' , 'dt' => 4)
    );

    public function getDataTable($param){

        $asset = \DB::table('icm_customers as c')
        ->selectRaw("
            c.id,
            c.document_number,
            CONCAT(IFNULL(c.first_name, ''), ' ', IFNULL(c.second_name, ''), ' ', IFNULL(c.first_surname, ''), ' ', IFNULL(c.second_surname, ''), ' ') as name,
            c.phone,
            c.email,
            '' as action
        ")
        ->join('users AS u', 'u.id', '=','c.user_created')
        ->orderByRaw("CONCAT(IFNULL(c.first_name, ''), ' ', IFNULL(c.second_name, ''), ' ', IFNULL(c.first_surname, ''), ' ', IFNULL(c.second_surname, ''), ' ') asc");


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

        $asset = \DB::table('icm_customers as c')
        ->selectRaw("
            c.id,
            c.document_number,
            CONCAT(IFNULL(c.first_name, ''), ' ', IFNULL(c.second_name, ''), ' ', IFNULL(c.first_surname, ''), ' ', IFNULL(c.second_surname, ''), ' ') as name,
            c.phone,
            c.email,
            '' as action
        ")
        ->join('users AS u', 'u.id', '=','c.user_created')
        ->orderByRaw("CONCAT(IFNULL(c.first_name, ''), ' ', IFNULL(c.second_name, ''), ' ', IFNULL(c.first_surname, ''), ' ', IFNULL(c.second_surname, ''), ' ') asc");

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
        $asset = \DB::table('icm_customers as c')
        ->selectRaw("
            c.id,
            c.document_number,
            CONCAT(IFNULL(c.first_name, ''), ' ', IFNULL(c.second_name, ''), ' ', IFNULL(c.first_surname, ''), ' ', IFNULL(c.second_surname, ''), ' ') as name,
            c.phone,
            c.email,
            '' as action
        ")
        ->join('users AS u', 'u.id', '=','c.user_created')
        ->orderByRaw("CONCAT(IFNULL(c.first_name, ''), ' ', IFNULL(c.second_name, ''), ' ', IFNULL(c.first_surname, ''), ' ', IFNULL(c.second_surname, ''), ' ') asc");


        $datares['cantotal'] = $asset->count();

        return $datares;

    }

}
