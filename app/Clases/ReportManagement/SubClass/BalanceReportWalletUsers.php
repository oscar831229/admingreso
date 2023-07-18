<?php 
namespace App\Clases\ReportManagement\SubClass;
use App\Models\Wallet\MovementType;
use App\Models\Wallet\Store;


class BalanceReportWalletUsers
{
	public static $filename = 'desagregado_cierre_cuipo.xlsx';

    public static $columns = [
        'NIT',
        'NOMBRE TERCERO',
        'CHIP',
        'RUBRO',
        'NOMBRE RUBRO',
        'CÓDIGO FUENTE DE FINANCIAMIENTO',
        'FUENTE DE FINANCIAMIENTO',
        'VIGENCIA',
        'VIGENCIA CODE',
        'CÓDIGO DANE',
        'DESC DANE',
        'VALOR COMPROMETIDO',
        'VALOR OBLIGADO',
        'VALOR PAGADO' 
    ];

	public static $data;
	
    # PARAMETRO PETICION

    public function __construct($request){
        $this->request = $request;
    }
	
	public function getData(){
        dd($this->request->all());
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
        return view('wallet.wallet-reports.reports.'.$code, compact('code'));
    }

    public function getEstructView(){
        return 'BudgetManagement.report-management.struct.report-disaggregated-budget-closing';
    }
	
	
}