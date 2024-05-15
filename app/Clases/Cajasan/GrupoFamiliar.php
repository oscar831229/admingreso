<?php


namespace App\Clases\Cajasan;


class GrupoFamiliar
{
    private $grupos = [];

	public function setGrupo($grupo){
		$this->grupos[] = $grupo;
	}

	public function existperson($identificacion){

		$response = false;

		if(count($this->grupos) > 0){
			foreach ($this->grupos as $key => $grupo) {
				$response = $grupo->existPersona($identificacion);
				if($response){
					break;
				}
			}
		}

		return $response;

	}
}
