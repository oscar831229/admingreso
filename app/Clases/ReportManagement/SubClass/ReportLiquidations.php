<?php
namespace App\Clases\ReportManagement\SubClass;
use App\Models\Wallet\MovementType;
use App\Models\Wallet\Store;

class ReportLiquidations
{
	public static $filename = 'liquidaciones_entradas.xlsx';

    public static $columns = [
        'ID',
        'NUMERO DE LIQUIDACION',
        'AMBIENTE',
        'DOCUMENTO CLIENTE',
        'NOMBRE CLIENTE',
        'FECHA LIQUIDACIÓN',
        'ESTADO',
        'NUMERO DE FACTURA',
        'VALOR LIQUIDACIÓN',
        'SUBSIDIO LIQUIDACIÓN',
        'USUARIO LIQUIDA'
    ];

	public static $data;

    # PARAMETRO PETICION

    public function __construct($request){
        $this->request = $request;
    }

	public function getData(){

        $statement = \DB::table("icm_liquidations AS il")->selectRaw("
            il.id,
            LPAD(il.id, 10, '0') AS number_liquidation,
            ie.name AS environment_name,
            il.document_number,
            CONCAT(IFNULL(il.first_name, ''), ' ', IFNULL(il.second_name, ''), ' ', IFNULL(il.first_surname, ''), ' ', IFNULL(il.second_surname, '')) AS customer_name,
            il.liquidation_date,
            CASE
            WHEN il.state = 'F' THEN 'FACTURADO'
            WHEN il.state = 'P' THEN 'EN PROCESO'
            WHEN il.state = 'B' THEN 'ELIMINADO'
            END AS state,
            CONCAT(IFNULL(il.billing_prefix, ''), IFNULL(il.consecutive_billing, '')) AS invoice_number,
            il.total,
            il.total_subsidy,
            u.name AS user_created
        ")
        ->join('icm_environments AS ie', 'ie.id', '=', 'il.icm_environment_id')
        ->join('users AS u', 'u.id', '=', 'il.user_created')
        ->where(['il.state' => 'F'])
        ->orderBy('il.id', 'ASC');


        if($this->request->has('date_from') && !empty($this->request->date_from)){
            $statement->whereDate('il.liquidation_date','>=', $this->request->date_from);
        }

        if($this->request->has('date_to') && !empty($this->request->date_to)){
            $statement->whereDate('il.liquidation_date','<=', $this->request->date_to);
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
        return view('income.income-reports.reports.'.$code, compact('code'));
    }

    public function getEstructView(){
        return 'income.income-reports.struct.report-liquidations';
    }


}
