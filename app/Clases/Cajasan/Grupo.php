<?php


namespace App\Clases\Cajasan;


class Grupo
{

    private $grupofamiliar = [];

	public function serPersona($persona){
		$this->grupofamiliar[] = $persona;
	}

	public function existPersona($identificacion){

		$response = false;
		# VALIDAMOS CONTRA EL PRINCIPAL
		foreach ($this->grupofamiliar as $key => $value) {
			if($value->dcto_beneficiario == $identificacion){
				$response = $value;
				break;
			}
		}
		return $response;
	}

}
