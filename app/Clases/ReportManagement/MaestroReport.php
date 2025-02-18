<?php 
namespace App\Clases\ReportManagement;

use App\Models\Officials\Person;
use App\Models\Officials\SurveyForm;

# REPORTES
use App\Clases\ReportManagement\SubClass\OfficialsReport;


class MaestroReport 
{
	private $request;
	private $class;
	
	public function __construct($request){
		
		# CLASS NAME REPORT
		$classname = explode('-', $request->input('report'));
		$classname = implode('',array_map('self::nameUcfirst', $classname));

		# space class reporte
		$name = "App\Clases\ReportManagement\SubClass\\".$classname;

		$this->class = new $name($request);

	}
		
	public function __call($methodName, $args)
    {
		return $this->class->$methodName();
    }
	
	
	public static function nameUcfirst($name){
		return ucfirst($name);
	}

	public function existFunction($method){
		return method_exists($this->class, $method);
	}
	
}