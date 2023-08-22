<?php

namespace App\Models\Wallet;

use Illuminate\Database\Eloquent\Model;
use App\Clases\DataTable\SSP;

class WalletUser extends Model
{
    
    protected $fillable = ['identification_document_type_id','document_type', 'document_number', 'first_name', 'second_name', 'first_surname', 'second_surname', 'email', 'phone', 'uuid', 'user_created', 'user_updated', 'token', 'user_code_create', 'user_code_update'];

    private $columnsdatatable = array(
        array( 'db' => 'wu.id', 'dt' => 0 ),
        array( 'db' => 'wu.document_number',  'dt' => 1 ),
        array( 'db' => "CONCAT(IFNULL(wu.first_name, ''), ' ',  IFNULL(wu.second_name, ''), ' ',IFNULL(wu.first_surname, ''), ' ',IFNULL(wu.second_surname, ''))", 'dt' => 2),
        array( 'db' => "wu.email", 'dt' => 3),
        array( 'db' => "wu.phone", 'dt' => 4),
        array( 'db' => "wu.created_at", 'dt' => 5),
        array( 'db' => "u.name", 'dt' => 6),
    );

    public function getDataTable($param){

        $asset =
            \DB::table('wallet_users AS wu')
            ->selectRaw("
                wu.id,
                wu.document_number,
                CONCAT(IFNULL(wu.first_name, ''), ' ',  IFNULL(wu.second_name, ''), ' ',IFNULL(wu.first_surname, ''), ' ',IFNULL(wu.second_surname, '')) AS user_name,
                wu.email,
                wu.phone,
                wu.created_at,
                u.name AS user_created
            ")
            ->join('users AS u', 'u.id', '=','wu.user_created')
            ->orderBy('wu.id', 'desc');

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
            \DB::table('wallet_users AS wu')
            ->selectRaw("
                wu.id,
                wu.document_number,
                CONCAT(IFNULL(wu.first_name, ''), ' ',  IFNULL(wu.second_name, ''), ' ',IFNULL(wu.first_surname, ''), ' ',IFNULL(wu.second_surname, '')) AS user_name,
                wu.email,
                wu.phone,
                wu.created_at,
                u.name AS user_created
            ")
            ->join('users AS u', 'u.id', '=','wu.user_created')
            ->orderBy('wu.id', 'desc');

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
            \DB::table('wallet_users AS wu')
            ->selectRaw("
                wu.id,
                wu.document_number,
                CONCAT(IFNULL(wu.first_name, ''), ' ',  IFNULL(wu.second_name, ''), ' ',IFNULL(wu.first_surname, ''), ' ',IFNULL(wu.second_surname, '')) AS user_name,
                wu.email,
                wu.phone,
                wu.created_at,
                u.name AS user_created
            ")
            ->join('users AS u', 'u.id', '=','wu.user_created')
            ->orderBy('wu.id', 'desc');

        $datares['cantotal'] = $asset->count();
        
        return $datares;

    }

    public function ElectronicPockets()
    {
        return $this->belongsToMany('App\Models\Wallet\ElectricalPocket')->withPivot('id', 'balance', 'last_movement_date');
    }

    public function identification_document_type()
    {
        return $this->belongsTo('App\Models\Common\DetailDefinition');
    }

    public function WalletUserTicket($electrical_pocket_wallet_user_id, $state){
        return $this->selectRaw('wallet_user_tickets.*')->join('wallet_user_tickets', 'wallet_user_tickets.wallet_user_id', '=', 'wallet_users.id')
            ->join('movements', 'movements.id', '=', 'wallet_user_tickets.movement_id')
            ->where(['electrical_pocket_wallet_user_id' => $electrical_pocket_wallet_user_id, 'state' => $state ])->get();
    }



}