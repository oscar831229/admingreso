<?php


namespace App\Clases\Cajasan;
use Illuminate\Support\Facades\Http;


class Afiliacion
{

    private $urlapi;
	private $urlapidatos;
	private $request;
	private $token;

    public function __construct(){
        $this->urlapi = env('CAJASAN_CATEGORIA_API');
        $this->urlapidatos = env('CAJASAN_DATOSAFILIADO_API');
        $this->token = env('CAJASAN_CATEGORIA_TOKEN');
    }

	public function setUrlAPI($urlapi){
		$this->urlapi = $urlapi;
	}

	public function setToken($token){
		$this->token = $token;
	}

	public function consultarCategoria($numero_documento, $tipo_operacion = 1){

        $return = [
            'success' => false,
            'message' => '',
            'data'    => []
        ];

        try {

            if(empty($this->urlapi))
			    throw new Exception("No se ha definido la url del servicio");

            $this->request = [
			    'token'          => $this->token,
			    'tipo_operacion' => $tipo_operacion,
			    'numero_dcto'    => $numero_documento
		    ];

            # Consultar sercicion post afiliaciones
            $response = Http::post($this->urlapi, $this->request);

            if ($response->ok()) {
                $return['success'] = true;
                $return['data']    = $response->json();
                return $return;
            } else {
                return false;
            }

        } catch (\Exception $e) {
            $return['success'] = false;
            $return['message']    = "La URL no está disponible. Error: " . $e->getMessage();
            return $return;
        }

	}


	public function consultarDatosAfiliado($numero_documento){

		if(empty($this->urlapidatos))
			throw new Exception("No se ha definido la url del servicio de consulta de datos afiliados");

		$this->request = [
			'token'       => $this->token,
			'numero_dcto' => $numero_documento
		];

		# Validamos token
		$response =  \Httpful\Request::post($this->urlapidatos)
			->body($this->request)
			->sendsJson()
			->send();

	    return $response->body;

	}

}
