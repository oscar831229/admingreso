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
        'FECHA REGISTRO TICKET',
        'CUS ESTADO (REDIMIDO, ANULADO)',
        'USUARIO MOV ESTADO (REDIMIDO, ANULADO)',
        'FECHA ESTADO  (REDIMIDO, ANULADO)' 
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
            ->orderBy('wut.id', 'ASC');

        
        if($this->request->has('movement_date_from') && !empty($this->request->movement_date_from)){
            $statement->whereDate('wut.created_at','>=', $this->request->movement_date_from);
        }

        if($this->request->has('movement_date_to') && !empty($this->request->movement_date_to)){
            $statement->whereDate('wut.created_at','<=', $this->request->movement_date_to);
        }

        if($this->request->has('movement_state_from') && !empty($this->request->movement_state_from)){
            $statement->whereDate('sm.created_at','>=', $this->request->movement_state_from);
        }

        if($this->request->has('movement_estado_to') && !empty($this->request->movement_estado_to)){
            $statement->whereDate('sm.created_at','<=', $this->request->movement_estado_to);
        }

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