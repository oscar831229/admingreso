<?php


namespace App\Clases\PlantillaMail;
use DB;

/**
 *  Clase que carga los datos dinamicos standar
 */
class ObtenerDatosMapa
{

	private static $estructura;

	public function __construct($codigo_planilla){
		self::$estructura = ObtenerEstructura::getDatos($codigo_planilla);
	}

	public function  getSelect($components){

		# Validamos que la estructura no este vacía
		if(empty($components))
			throw new Exception("No se ha definido la estructura de la consulta");

		# Cargar los select
		foreach($components as $campo => $param){
			$select[] = $param['detailField'].' as '.$campo;
		}

		return $select;	

	}


	# return joins
	public function getJoin($mapa){
		return $mapa['relationTable'];
	}

	# return details
	public function getDetails(){
		return $this->estructure['details'];
	}

	# asignar el where
	public function setWhere($where){
		$this->where = $where;
	}


	public function consultarDatos($where = array()){

		$estructura = self::$estructura;

		$nivel1 = array();

		foreach ($estructura['default'] as $key => $value) {
		    switch ($value['type']) {
		    	case 'SQL':
					
					$res = DB::table($value['from'])->select($value['select'])->where($value['where'])->get();

		    		if(!empty($res)){
		    			$res = isset($value['firts']) && $value['firts'] ? $res[0]:$res;
		    		}

		    		if(isset($value['detailField']))
		    			$res = $res[$value['detailField']];

		    		$nivel1[$key] = $res;

		    		break;
		    		
		    	default:
		    		$nivel1[$key] = $value['value'];
		    }
		}

        # Primer nivel
		if(isset($estructura['source']) && isset($estructura['components'])){

			# Nivel 1 estructura
			$head  = $this->getDatosEstructura($estructura, $where);

			
			if(!empty($head) && $head->count()){

				$head = (Array) $head->first();

				$nivel1 = $nivel1 != null ? array_merge($nivel1,$head):$head;
				$values_1 = array_values($nivel1);
				$keys_1 = array_keys($nivel1);
				
				array_walk($keys_1,'self::ajustarPrefixValue', '@');
				
 			    # Segundo nivel
				foreach ($estructura['details'] as $key => $detail) {

					# Se remplazan las variables de los where
					$detail = json_decode(str_replace($keys_1, $values_1, json_encode($detail)),true);
					$nivel2 = $this->getDatosEstructura($detail)->toArray();

					# Cargar datos en nive 1
					$nivel1[$detail['alias']] = $nivel2 == null? array():$nivel2;

				}
			}

		}
		
		$result = array();
		array_walk($nivel1, function ($elment,$key) use (&$result) {
			$result['@'.$key] = (String) $elment;
		});

		
		return $result;

	}


	public function getIdPrincipal($estructura){

		# idprincipal
		$idprincipal = array();

		# Componentes
		foreach ($estructura['components'] as $key => $component) {
			if(isset($component['masterDetailRelation']) && $component['masterDetailRelation']){
				$idprincipal[] = $key;
			}
		}

		return $idprincipal;
	}


	public function getDatosEstructura($estructura,$where = array()){

		$response = null;

		if(isset($estructura['source']) && $estructura['components']){


			if(isset($estructura['connection']) && !empty($estructura['connection'])){
				$DB = DB::connection($estructura['connection'])->table($estructura['source'])
			   		->selectRaw(implode(', ',$this->getSelect($estructura['components'])));
			}else{
				$DB = DB::table($estructura['source'])
			   		->selectRaw(implode(', ',$this->getSelect($estructura['components'])));
			}

			

			if(isset($estructura['relationTable'])){
				foreach ($estructura['relationTable'] as $key => $join) {

					switch ($join['direccion']) {
						case 'LEFT':
							$DB->leftJoin($join['tabla'], $join['enlace_left'], '=', $join['enlace_right']);
							break;
						case 'RIGHT':
							$DB->rightJoin($join['tabla'], $join['enlace_left'], '=', $join['enlace_right']);
							break;
						default:
							$DB->Join($join['tabla'], $join['enlace_left'], '=', $join['enlace_right']);
							break;
					}
				}
			}		

			if(count($where) == 0)
				$DB->where($estructura['where']);
			else
				$DB->where($where);

			$response = $DB->get();

		}else
			$response =  null;


		return $response;
		

	}


	/**
	 *  Obtiene atributos plantilla
	 */
	public static function obtenerMapCaption($codigo_planilla){

		$estructura = ObtenerEstructura::getDatos($codigo_planilla);

		$mapa = array();

		# Datos por default
		foreach ($estructura['default'] as $key => $detail) {
			$mapa[$key] = $detail['caption'];
		}

	    # Componentes
		foreach ($estructura['components'] as $key => $component) {
			$mapa[$key] = $component['caption'];
		}

		# Details
		foreach ($estructura['details'] as $key => $detail) {
			
			foreach ($detail['components'] as $key => $component) {
				$mapa[$detail['alias']][$key] = $component['caption'];
			}

			foreach ($detail['details'] as $key => $detail_2) {
				foreach ($detail_2['components'] as $key => $component_2) {
					$mapa[$detail['alias']][$detail_2['alias']][$key] = $component['caption'];
				}
			}

		}

		return $mapa;

	}

	public static function ajustarPrefixValue(&$elemento1, $clave, $prefijo){
		$elemento1 = '@'.$elemento1;
	}



}

?>