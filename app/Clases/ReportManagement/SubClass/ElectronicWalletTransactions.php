<?php 
namespace App\Clases\ReportManagement\SubClass;
use App\Models\Wallet\MovementType;
use App\Models\Wallet\Store;

class ElectronicWalletTransactions
{
	public static $filename = 'movimientobilleteraelec.xlsx';

    public static $columns = [
        'ID MOVIMIENTO',
        'NUMERO DOCUMENTO',
        'NOMBRE CLIENTE',
        'EMAIL',
        'TELEFONO',
        'DOCUMENTO ORIGEN TRANSACCIÓN',
        'MOVIMIENTO',
        'BOLSILLO',
        'VALOR',
        'NATURALEZA MOVIMIENTO',
        'CUS',
        'USUARIO SISTEMA ORIGEN',
        'COMERCIO',
        'CUS ASOCIADO',
        'FECHA'
    ];

	public static $data;
	
    # PARAMETRO PETICION

    public function __construct($request){
        $this->request = $request;
    }
	
	public function getData(){

        $statement = \DB::table("movements AS m")->selectRaw("
                m.id,
                wu.document_number,
                CONCAT(IFNULL(wu.first_name, ''), ' ', IFNULL(wu.second_name, ''), ' ', IFNULL(wu.first_surname, ''), ' ', IFNULL(wu.second_surname,'')) AS customer_name,
                wu.email,
                wu.phone,
                m.transaction_document_number,
                CONCAT(mt.code, ' ', mt.name) AS movement_type,
                CONCAT(ep.code, ' ', ep.name) AS electrical_pockets,
                m.value,
                CASE 
                    WHEN m.nature_movement = 'C' THEN 'Credito'
                    WHEN m.nature_movement = 'D' THEN 'Debito'
                END AS nature_movement,
                m.cus,
                m.user_code,
                CONCAT(s.code, ' ', s.name) AS store,
                m.cus_transaction,
                m.created_at")        
        ->join('wallet_users AS wu', 'wu.id', '=', 'm.wallet_user_id')
        ->join('electrical_pockets AS ep', 'ep.id', '=', 'm.electrical_pocket_id')
        ->join('movement_types AS mt', 'mt.id', '=', 'm.movement_type_id')
        ->join('stores AS s', 's.id', '=', 'm.store_id')
        ->whereDate('m.created_at','>=', $this->request->movement_date_from)
        ->whereDate('m.created_at','<=', $this->request->movement_date_to)
        ->orderBy('m.id', 'ASC');

        if($this->request->has('wallet_user_id') && !empty($this->request->wallet_user_id)){
            $statement->where(['m.wallet_user_id'=>$this->request->wallet_user_id]);
        }

        if($this->request->has('store_id') && !empty($this->request->store_id)){
            $statement->where(['m.store_id'=>$this->request->store_id]);
        }

        if($this->request->has('movement_type_id') && !empty($this->request->movement_type_id)){
            $statement->where(['m.movement_type_id'=>$this->request->movement_type_id]);
        }

        return $statement->get();
            
	}

    /** Retorna nombres de columnas  */
    public function getColumns(){
        return self::$columns;
    }

    /** Retorna el nombre del archivo */
    public function getFileName(){
        return self::$filename;
    }

    public function view(){
        $code = $this->request->input('report');

        # Tipos de movimientos.
        $movement_types = MovementType::all()->pluck('name', 'id');
        $stores = Store::all()->pluck('name', 'id');

        return view('wallet.wallet-reports.reports.'.$code, compact('code', 'movement_types', 'stores'));
    }

    public function getEstructView(){
        return 'wallet.wallet-reports.struct.electronic-wallet-transactions';
    }
	
	
}