<?php 
namespace App\Clases\ReportManagement\SubClass;
use App\Models\Wallet\MovementType;
use App\Models\Wallet\Store;

class TicketOfficeReport
{
	public static $filename = 'tickets_tiquetera_electronica.xlsx';

    public static $columns = [
        'ID TICKET',
        'DOCUMENTO',
        'CLIENTE',
        'DOCUMENTO ORIGEN MOVIMIENTO',
        'CUS',
        'USUARIO SISTEMA ORIGEN',
        'COMERCIO',
        'ESTADO TICKET',
        'NUMERO TICKET',
        'VALOR',
        'FECHA CREACION',
        'CUS ESTADO',
        'USUARIO MOV ESTADO',
        'FECHA ESTADO' 
    ];

	public static $data;
	
    # PARAMETRO PETICION

    public function __construct($request){
        $this->request = $request;
    }
	
	public function getData(){

        $statement = \DB::table("wallet_user_tickets AS wut")->selectRaw("
                    wut.id,
                    wu.document_number,
                    CONCAT(IFNULL(wu.first_name, ''), ' ', IFNULL(wu.second_name, ''), ' ', IFNULL(wu.first_surname, ''), ' ', IFNULL(wu.second_surname,'')) AS customer_name,
                    m.transaction_document_number,
                    m.cus,
                    m.user_code,
                    CONCAT(s.code, ' ', s.name) AS store,
                    CASE
                        WHEN wut.state = 'P' THEN 'PENDIENTE REDIMIR'
                        WHEN wut.state = 'R' THEN 'REDIMIDO'
                        WHEN wut.state = 'A' THEN 'TICKET ANULADO'
                    ELSE ''
                    END AS state,
                    wut.number_ticket,
                    wut.value,
                    wut.created_at,
                    sm.cus as cus_state,
                    sm.user_code as user_code_state,
                    sm.created_at AS  state_movement_date
                ")        
            ->join('wallet_users AS wu', 'wu.id', '=', 'wut.wallet_user_id')
            ->join('movements AS m', 'm.id', '=', 'wut.movement_id')
            ->join('stores AS s', 's.id', '=', 'm.store_id')
            ->leftJoin('movements AS sm', 'sm.id', '=', 'wut.state_movement_id')
            ->whereDate('wut.created_at','>=', $this->request->movement_date_from)
            ->whereDate('wut.created_at','<=', $this->request->movement_date_to)
            ->orderBy('wut.id', 'ASC');

        if($this->request->has('wallet_user_id') && !empty($this->request->wallet_user_id)){
            $statement->where(['wut.wallet_user_id'=>$this->request->wallet_user_id]);
        }

        if($this->request->has('cus') && !empty($this->request->cus)){
            $statement->where(['m.cus'=>$this->request->cus]);
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
        return 'wallet.wallet-reports.struct.ticket-office-report';
    }
	
	
}