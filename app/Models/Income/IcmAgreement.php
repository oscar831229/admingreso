<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;
use App\Clases\DataTable\SSP;

class IcmAgreement extends Model
{
    protected $fillable = [
        'icm_environment_id',
        'icm_companies_agreement_id',
        'date_from',
        'date_to',
        'observations',
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
    );

    public function getDataTable($param){

        $asset = \DB::table('icm_agreements AS ig')
        ->selectRaw("
            ig.id,
            ica.document_number,
            ica.name as icm_companies_agreement_name,
            ig.date_from,
            ig.date_to,
            u.name as user_created,
            ig.state,
            '' as action
        ")
        ->join('users AS u', 'u.id', '=','ig.user_created')
        ->join('icm_environments AS ie', 'ie.id', '=','ig.icm_environment_id')
        ->join('icm_companies_agreements AS ica', 'ica.id', '=','ig.icm_companies_agreement_id')
        ->orderBy('ica.name', 'ASC')
        ->orderBy('ig.date_from', 'ASC');

        # Estado del consumible
        if(isset($param['extradata']['icm_environment_id'])) {
            $asset->whereRaw("ig.icm_environment_id = '{$param['extradata']['icm_environment_id']}'");
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
        ->join('icm_environments AS ie', 'ie.id', '=','ig.icm_environment_id')
        ->join('icm_companies_agreements AS ica', 'ica.id', '=','ig.icm_companies_agreement_id');

        # Estado del consumible
        if(isset($param['extradata']['icm_environment_id'])) {
            $asset->whereRaw("ig.icm_environment_id = '{$param['extradata']['icm_environment_id']}'");
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
        ->join('icm_environments AS ie', 'ie.id', '=','ig.icm_environment_id')
        ->join('icm_companies_agreements AS ica', 'ica.id', '=','ig.icm_companies_agreement_id');

        # Estado del consumible
        if(isset($param['extradata']['icm_environment_id'])) {
            $asset->whereRaw("ig.icm_environment_id = '{$param['extradata']['icm_environment_id']}'");
        }

        $datares['cantotal'] = $asset->count();

        return $datares;

    }

    public function icm_agreement_details(){
        return $this->hasMany('App\Models\Income\IcmAgreementDetail');
    }


}
