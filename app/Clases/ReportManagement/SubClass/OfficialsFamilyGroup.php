<?php 
namespace App\Clases\ReportManagement\SubClass;

use App\Models\Officials\Person;
use App\Models\Officials\SurveyForm;
use App\Models\Officials\SurveyFormDetail;
use App\Models\Officials\Company;


class OfficialsFamilyGroup
{
	public static $filename = 'funcionarios_grupo_familiar.xlsx';

    public static $columns = [
        'ID',
        'Tipo documento',
        'Número documento',
        'Nombre',
        'Edad',
        'Genero',
        'Nacionalidad',
        'Telefono',
        'Departamento residencia',
        'Municipio residencia',
        'Dirección residencia',
        'Email',
        'Formación academica',
        'Profesión',
        'Especialización',
        'Número contratos activos',
        'Empresas contrato activos',
        'Nombre hijo',
        'Parentesco',
        'Fecha de nacimiento',
        'Edad',
        'Discapacidad',
        'Descripción discapacidad',
        'Grado escolaridad'
    ];

	public static $data;
	
    # PARAMETRO PETICION

    public function __construct($request){
        $this->request = $request;
    }
	
	public function getData(){
		
		$people = new Person;

        # DATOS PERSONA
        $people = $people->getPeopleGroupFamilyFilters($this->request)->toArray();

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
        $companies = Company::all()->pluck('name', 'id');
        return view('officials.report-management.reports.'.$code, compact('collaborator_type', 'bonding_type', 'companies','code'));
    }

    public function getEstructView(){
        return 'Officials.report-management.struct.officials-report';
    }
	
}