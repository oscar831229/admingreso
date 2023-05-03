<?php 
namespace App\Clases\ReportManagement\SubClass;

use App\Models\Officials\Person;
use App\Models\Officials\SurveyForm;
use App\Models\Officials\SurveyFormDetail;
use App\Models\Officials\Company;


class OfficialsReportCompany 
{
	public static $filename = 'funcionarios_empresa.xlsx';

    public static $columns = [
        'ID',
        'Tipo documento',
        'Número documento',
        'Fecha expedición',
        'Nombre',
        'Fecha nacimiento',
        'Edad',
        'Municipio nacimiento',
        'Departamento nacimiento',
        'Genero',
        'Nacionalidad',
        'Telefono',
        'Departamento residencia',
        'Municipio residencia',
        'Dirección residencia',
        'Email',
        'Estado civil',
        'Es cabeza de familia',
        'Pasa tiempos',
        'Otros idiomas habla',
        'Grupo étnico',
        'Número hijos',
        'Discapacitado',
        'Descripción discapacidad',
        'Entidad promotora de salud',
        'Administración riesgos laborales',
        'Fondo de pensión',
        'Tipo de casa',
        'Estrato',
        'Formación academica',
        'Profesión',
        'Especialización',
        'Tipo de contratación',
        'Estado contrato',
        'Fecha de admisión',
        'Fecha de retiro',
        'Observación retiro',
        'Tipo de colaborador',
        'Horas laboradas',
        'Cargo',
        'Unidad',
        'Es jefe',
        'Lider de proceso',
        'Actividades',
        'Trabajo en casa'
    ];

	public static $data;
	
    # PARAMETRO PETICION

    public function __construct($request){
        $this->request = $request;
    }
	
	public function getData(){

		$people = new Person;

        # DATOS PERSONA
        $people = $people->getPeopleCompanyFilters($this->request)->toArray();

        $forms = [];

        # DATOS EXTRA
        $survey_forms = SurveyForm::where(['state' => 'P'])->get();
        $columnnames = SurveyFormDetail::pluck('name','id');

        foreach ($people as $key => &$person) {
            foreach($survey_forms as $key => $form){
                $results = \DB::select("CALL viem_form_dynamic({$form->id}, {$person['id']})");
                $results = (Array)  $results[0];
                unset($results['person_id']);
                unset($results['survey_form_id']);
                $person = array_merge($person, $results);

                if(!in_array($form->id, $forms)){

                    # INCLUIR CABECERAS REPORTE DINAMICAS
                    $columns_result = array_keys($results);

                    foreach ($columns_result as $key => $columnresult) {
                        $id = substr($columnresult,5);
                        self::$columns[] = ucfirst($columnnames[$id]);
                    }

                    $forms[] = $form->id;

                }

            }
        }

        return $people;

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
        return view('officials.report-management.reports.'.$code, compact('collaborator_type', 'bonding_type', 'companies','code'));
    }

    public function getEstructView(){
        return 'Officials.report-management.struct.officials-report-company';
    }
	
	
}