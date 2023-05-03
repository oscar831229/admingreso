<?php 
namespace App\Clases\ReportManagement\SubClass;

use App\Models\Officials\Person;
use App\Models\Officials\SurveyForm;
use App\Models\Officials\SurveyFormDetail;
use App\Models\Officials\Company;
use App\Models\ElectronicDocument\ElectronicDocumentTraceability;


class ElectronicDocumentReport
{
	public static $filename = 'documentos_electronicos.xlsx';

    public static $columns = [
        'Electronic document id',
        'Tipo documento',
        'Número documento',
        'valor',
        'Factura nota',
        'Fecha documento',
        'Identificación cliente',
        'Nombre cliente',
        'Identificación paciente',
        'Nombre paciente',
        'Números proceso conector CISI',
        'Fecha primer proceso CISI',
        'Fecha ultimo proceso CISI',
        'Estado legal cavajal',
        'Fecha aceptación Dian',
        'Mensaje',
        'Ultimo proceso carvajal',
        'Nombre proceso carvajal',
        'Estado proceso',
        'Estado documentos',
        'Estado documento',
        'Usuario anula',
        'Fecha anula'
    ];

	public static $data;
	
    # PARAMETRO PETICION

    public function __construct($request){
        $this->request = $request;
    }
	
	public function getData(){

		$eletronicdocument = new ElectronicDocumentTraceability;

        # DATOS PERSONA
        $eletronicdocument = $eletronicdocument->getDocuments($this->request)->toArray();

        return $eletronicdocument;

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
        return view('ControlDocuments.report-management.reports.'.$code, compact('collaborator_type', 'bonding_type', 'companies','code'));
    }

    public function getEstructView(){
        return 'ControlDocuments.report-management.struct.electronic-document-report';
    }
	
	
}