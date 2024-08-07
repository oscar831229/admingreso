<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;
use App\Clases\DataTable\SSP;

class IcmCompaniesAgreement extends Model
{
    protected $fillable = ['document_type', 'document_number', 'name', 'phone', 'address', 'email', 'state', 'user_created', 'user_updated'];

    private $columnsdatatable = array(
        array( 'db' => 'ica.id' , 'dt' => 0),
        array( 'db' => 'dd.name' , 'dt' => 1),
        array( 'db' => 'ica.document_number' , 'dt' => 2),
        array( 'db' => 'ica.name' , 'dt' => 3),
        array( 'db' => 'ica.phone' , 'dt' => 4),
        array( 'db' => "CASE WHEN ica.state = 'A' THEN 'Activa' ELSE 'Inactiva' END", 'dt' => 4),
    );

    public function getDataTable($param){

        $asset = \DB::table('icm_companies_agreements AS ica')
        ->selectRaw("
            ica.id,
            dd.name as document_type,
            ica.document_number,
            ica.name,
            ica.phone,
            u.name as user_created,
            ica.state,
            '' as action
        ")
        ->join('users AS u', 'u.id', '=','ica.user_created')
        ->join('detail_definitions AS dd', 'dd.id', '=','ica.document_type')
        ->orderBy('ica.name', 'ASC');

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

        $asset = \DB::table('icm_companies_agreements AS ica')
        ->selectRaw("
            ica.id,
            dd.name as document_type,
            ica.document_number,
            ica.name,
            ica.phone,
            ica.address,
            ica.email,
            u.name as user_created,
            ica.state,
            '' as action
        ")
        ->join('users AS u', 'u.id', '=','ica.user_created')
        ->join('detail_definitions AS dd', 'dd.id', '=','ica.document_type')
        ->orderBy('ica.name', 'DESC');

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
        $asset = \DB::table('icm_companies_agreements AS ica')
        ->selectRaw("
            ica.id,
            dd.name as document_type,
            ica.document_number,
            ica.name,
            ica.phone,
            ica.address,
            ica.email,
            u.name as user_created,
            ica.state,
            '' as action
        ")
        ->join('users AS u', 'u.id', '=','ica.user_created')
        ->join('detail_definitions AS dd', 'dd.id', '=','ica.document_type')
        ->orderBy('ica.name', 'DESC');

        $datares['cantotal'] = $asset->count();

        return $datares;

    }

    public function icm_agreements(){
        return $this->hasMany(IcmAgreement::class);
    }

}
