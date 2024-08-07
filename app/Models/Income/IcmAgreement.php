<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;
use App\Clases\DataTable\SSP;

class IcmAgreement extends Model
{
    protected $fillable = [
        'code',
        'name',
        'icm_companies_agreement_id',
        'date_from',
        'date_to',
        'observations',
        'state',
        'user_created',
        'user_updated'
    ];

    private $columnsdatatable = array(
        array( 'db' => 'ig.id' , 'dt' => 0),
        array( 'db' => "CONCAT(IFNULL(ica.document_number,'') , ' ', IFNULL(ica.name, ''))" , 'dt' => 1),
        array( 'db' => 'ig.code' , 'dt' => 2),
        array( 'db' => 'ig.name' , 'dt' => 3),
        array( 'db' => 'ig.date_from' , 'dt' => 4),
        array( 'db' => 'ig.date_to' , 'dt' => 5),
    );

    public function getDataTable($param){

        $asset = \DB::table('icm_agreements AS ig')
        ->selectRaw("
            ig.id,
            CONCAT(IFNULL(ica.document_number,'') , ' ', IFNULL(ica.name, '')) AS icm_companies_agreement_name,
            ig.code,
            ig.name as icm_agreement_name,
            ig.date_from,
            ig.date_to,
            u.name as user_created,
            ig.state,
            '' as action
        ")
        ->join('users AS u', 'u.id', '=','ig.user_created')
        // ->join('icm_environments AS ie', 'ie.id', '=','ig.icm_environment_id')
        ->join('icm_companies_agreements AS ica', 'ica.id', '=','ig.icm_companies_agreement_id')
        ->orderBy('ica.name', 'ASC')
        ->orderBy('ig.date_from', 'ASC');

        # Filtrando por estados del convenio
        if(isset($param['extradata']['state'])) {
            $asset->whereRaw("ig.state = '{$param['extradata']['state']}'");
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

        $asset = \DB::table('icm_agreements AS ig')
        ->selectRaw("
            ig.id,
            ica.name as icm_companies_agreement_name,
            ig.date_from,
            ig.date_to,
            ig.state,
            '' as action
        ")
        ->join('users AS u', 'u.id', '=','ig.user_created')
        // ->join('icm_environments AS ie', 'ie.id', '=','ig.icm_environment_id')
        ->join('icm_companies_agreements AS ica', 'ica.id', '=','ig.icm_companies_agreement_id');

        # Filtrando por estados del convenio
        if(isset($param['extradata']['state'])) {
            $asset->whereRaw("ig.state = '{$param['extradata']['state']}'");
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
        $asset = \DB::table('icm_agreements AS ig')
        ->selectRaw("
            ig.id,
            ica.document_number,
            ica.name as icm_companies_agreement_name,
            ig.date_from,
            ig.date_to,
            ig.state,
            '' as action
        ")
        ->join('users AS u', 'u.id', '=','ig.user_created')
        // ->join('icm_environments AS ie', 'ie.id', '=','ig.icm_environment_id')
        ->join('icm_companies_agreements AS ica', 'ica.id', '=','ig.icm_companies_agreement_id');

        # Filtrando por estados del convenio
        if(isset($param['extradata']['state'])) {
            $asset->whereRaw("ig.state = '{$param['extradata']['state']}'");
        }

        $datares['cantotal'] = $asset->count();

        return $datares;

    }

    public function icm_agreement_details(){
        return $this->hasMany('App\Models\Income\IcmAgreementDetail');
    }

    public function icm_agreement_type_incomes(){
        return $this->hasMany('App\Models\Income\IcmAgreementTypeIncome');
    }

    public function icm_companies_agreement(){
        return $this->belongsTo('App\Models\Income\IcmCompaniesAgreement');
    }



}
