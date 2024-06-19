<?php
namespace App\Clases\ReportManagement\SubClass;
use App\Models\Wallet\MovementType;
use App\Models\Wallet\Store;

class ReportLiquidationDetails
{
	public static $filename = 'detallado_liquidaciones.xlsx';

    public static $columns = [
        'ID',
        'NUMERO DE LIQUIDACION',
        'AMBIENTE',
        'CLIENTE FACTURA',
        'NOMBRE FACTURA',
        'FECHA LIQUIDACIÓN',
        'ESTADO',
        'NUMERO DE FACTURA',
        'VALOR LIQUIDACIÓN',
        'SUBSIDIO LIQUIDACIÓN',
        'SERVICIO SISTEMA INGRESO',
        'SERVICIO POS',
        'NÚMERO PERSONAS SERVICIO',
        'CÓDIGO TARIFA APLICADA',
        'BASE',
        'IVA',
        'IMPOCONSUMO',
        'SUBSIDIO',
        'TOTAL',
        'DOCUMENTO INGRESA',
        'NOMBRE INGRESA',
        'TIPO INGRESO',
        'CATEGORIA',
        'CAJA SIN FRONTERAS',
        'NIT EMPRESA AFILIADO',
        'NOMBRE EMPRESA AFILIADO',
        'NIT EMPRESA CONVENION',
        'NOMBRE EMPRESA CONVENIO',
        'CÓDIGO CONVENIO',
        'NOMBRE CONVENIO'
    ];

	public static $data;

    # PARAMETRO PETICION

    public function __construct($request){
        $this->request = $request;
    }

	public function getData(){

        $querySQL = "SELECT

            FROM
            INNER JOIN  ON
            INNER JOIN
            INNER JOIN
            INNER JOIN
            INNER JOIN
            INNER JOIN
            INNER JOIN
            INNER JOIN
            LEFT JOIN
            LEFT JOIN
            INNER JOIN ";

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
            iii.name AS icm_income_item_name,
            imi.name AS icm_menu_item_name,
            ils.number_places,
            ils.applied_rate_code,
            ils.base,
            ils.iva,
            ils.impoconsumo,
            ils.subsidy,
            ils.total AS total_detalle,
            ild.document_number AS document_number_ingresa,
            CONCAT(IFNULL(ild.first_name, ''), ' ', IFNULL(ild.second_name, ''), ' ', IFNULL(ild.first_surname, ''), ' ', IFNULL(ild.second_surname, '')) AS customer_ingreso_name,
            iti.name AS icm_types_income_name,
            iac.name AS icm_affiliate_category_name,
            ifc.name AS icm_family_compensation_fund_name,
            ild.nit_company_affiliates,
            ild.name_company_affiliates,
            ild.nit_company_agreement,
            name_company_agreement,
            ia.code AS icm_agreement_code,
            ia.name AS icm_agreement_name
        ")
        ->join('icm_environments AS ie', 'ie.id', '=', 'il.icm_environment_id')
        ->join('icm_liquidation_services AS ils', 'ils.icm_liquidation_id', '=', 'il.id')
        ->join('icm_income_items AS iii', 'iii.id', '=', 'ils.icm_income_item_id')
        ->join('icm_environment_icm_menu_items AS ieimi', 'ieimi.id', '=', 'ils.icm_environment_icm_menu_item_id')
        ->join('icm_menu_items AS imi', 'imi.id', '=', 'ieimi.icm_menu_item_id')
        ->join('icm_liquidation_details AS ild', 'ild.icm_liquidation_service_id', '=', 'ils.id')
        ->join('icm_types_incomes AS iti', 'iti.id', '=', 'ild.icm_types_income_id')
        ->join('icm_affiliate_categories AS iac', 'iac.id', '=', 'ild.icm_affiliate_category_id')
        ->leftJoin('icm_family_compensation_funds AS ifc', 'ifc.id', '=', 'ild.icm_family_compensation_fund_id')
        ->leftJoin('icm_agreements AS ia', 'ia.id', '=', 'ild.icm_agreement_id')
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
        return 'income.income-reports.struct.report-liquidation-details';
    }


}
