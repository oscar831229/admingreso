<?php 
namespace App\Clases\ReportManagement\SubClass;
use App\Models\Wallet\MovementType;
use App\Models\Wallet\Store;


class BalanceReportWalletUsers
{
	public static $filename = 'balance_tiquetera_electronica.xlsx';

    public static $columns = [
        'ID BILETERA USUA',
        'DOCUMENTO',
        'NOMBRE',
        'CODIGO BOLSILLO',
        'SALDO'
    ];

	public static $data;
	
    # PARAMETRO PETICION

    public function __construct($request){
        $this->request = $request;
    }
	
	public function getData(){

        $statement = \DB::table("electrical_pocket_wallet_user AS epwu")->selectRaw("
                epwu.id,
                wu.document_number,
                CONCAT(IFNULL(wu.first_name, ''), ' ', IFNULL(wu.second_name, ''), ' ', IFNULL(wu.first_surname, ''), ' ', IFNULL(wu.second_surname,'')) AS customer_name,
                CONCAT(ep.code, ' ', ep.name) AS electrical_pockets,
                epwu.balance
            ")        
            ->join('wallet_users AS wu', 'wu.id', '=', 'epwu.wallet_user_id')
            ->join('electrical_pockets AS ep', 'ep.id', '=', 'epwu.electrical_pocket_id')
            ->orderBy('wu.document_number', 'ASC');

        if($this->request->has('wallet_user_id') && !empty($this->request->wallet_user_id)){
            $statement->where(['epwu.wallet_user_id'=>$this->request->wallet_user_id]);
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
        return view('wallet.wallet-reports.reports.'.$code, compact('code'));
    }

    public function getEstructView(){
        return 'wallet.wallet-reports.struct.balance-report-wallet-users';
    }
	
	
}