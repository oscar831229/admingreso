<?php 
namespace App\Clases\ReportManagement\SubClass;

use App\Models\Officials\Person;
use App\Models\Officials\SurveyForm;
use App\Models\Officials\SurveyFormDetail;
use App\Models\Officials\Company;


class TransplantDonorAlerts
{
	public static $filename = 'alertasdonantesorganos.xlsx';

    public static $columns = [
        'ID',
        'Tipo documento',
        'Número documento',
        'Nombre paciente',
        'Fecha nacimiento',
        'Genero',
        'Fecha de ingreso',
        'IPS',
        'Municipio',
        'Estado',
        'Estado facturacion',
        'Número de factura',
        'Valor factura',
        'Fecha factura',
        'Observación',
        'Fecha registro'
    ];

	public static $data;
	
    # PARAMETRO PETICION

    public function __construct($request){
        $this->request = $request;
    }
	
	public function getData(){

		$date_from_register = $this->request->input('date_from_register');
        $date_end_register = $this->request->input('date_end_register');

        $querySQL = "SELECT
                ppd.id,
                td.name AS type_document,
                ppd.document_number,
                ppd.name AS donor_name,
                ppd.birth_date,
                gd.name AS gender,
                ppd.admission_date,
                phpu.name AS pda_health_provider_unit,
                pm.nommunicipio,
                CASE
                    WHEN ppd.state = 'T' THEN 'En tramite' 
                    WHEN ppd.state = 'E' THEN 'Donante exitoso' 
                    WHEN ppd.state = 'D' THEN 'Donante descartado' 
                END as state,
                CASE
                    WHEN ppd.state = 'T' THEN '' 
                    WHEN ppd.is_invoiced = 0 AND ppd.state IN ('E', 'D') THEN 'Pendiente de facturar' 
                    WHEN ppd.is_invoiced = 1 AND ppd.state IN ('E', 'D') THEN 'Facturada' 
                END as is_invoice,
                ppd.invoice_number,
                ppd.invoice_value,
                ppd.invoice_date,
                ppd.medical_evolution,
                ppd.created_at
            FROM pda_possible_donors AS ppd
            INNER JOIN `pda_health_provider_units` AS phpu ON phpu.id = ppd.pda_health_provider_unit_id
            INNER JOIN `detail_definitions` AS td ON td.id = ppd.document_type_id
            INNER JOIN `detail_definitions` AS gd ON gd.id = ppd.gender_id
            INNER JOIN `pv_municipios` AS pm ON pm.idmunicipio = ppd.city_reports_alert_id
            WHERE CAST(ppd.created_at AS DATE) BETWEEN ? AND ?";

        return \DB::Select($querySQL, [$date_from_register, $date_end_register]);

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
        $collaborator_type = getDetailDefinitions('TIPOCOLAB');
        $bonding_type = getDetailDefinitions('TIPOVINCU');
        $companies = auth()->user()->companies->pluck('name', 'id');
        return view('PossibleDonor.donor-reports.reports.'.$code, compact('collaborator_type', 'bonding_type', 'companies','code'));
    }

    public function getEstructView(){
        return 'PossibleDonor.donor-reports.struct.transplant-donor-alerts';
    }
	
	
}