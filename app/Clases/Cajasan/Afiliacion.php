<?php


namespace App\Clases\Cajasan;
use Illuminate\Support\Facades\Http;


class Afiliacion
{

    private $urlapi;
	private $request;
	private $usuario;
    private $password;

    public function __construct(){
        $this->urlapi   = env('CAJASAN_CATEGORIA_API');
        $this->usuario  = env('USUARIO_API_SISAFI');
        $this->password = env('PASSWORD_API_SISAFI');
    }

    public function generarToken(){

        $url_method = $this->urlapi.'/auth/token';

        $response = Http::withOptions([
            'verify' => false,
        ])->post($url_method, [
            'username' => $this->usuario,
            'password' => $this->password
        ]);

        if ($response->ok()) {
            $response_json = $response->json();
            return $response_json['access_token'];
        } else {
            return false;
        }

    }

	public function consultarCategoria($numero_documento, $tipo_documento, $tipo_operacion = 1){

        $return = [
            'success' => false,
            'message' => '',
            'data'    => []
        ];

        try {

            $token = $this->generarToken();

            if(empty($this->urlapi))
			    throw new Exception("No se ha definido la url del servicio");

            $this->request = [
                'tipo_documento' => $tipo_documento,
                'nro_documento'  => $numero_documento,
			    'tipo'           => $tipo_operacion
		    ];

            # Consultar sercicion post afiliaciones
            $url_method = $this->urlapi.'/src1/v1/pack_funciones_cobertura/f_afiliado_ws_recrea';

            $response = Http::withOptions([
                'verify' => false,
            ])->withToken($token)->post($url_method, $this->request);

            if ($response->ok()) {

                $response_data = $response->json();
                $return['success'] = true;
                if($response_data['respuesta']['codigo'] == '-200'){
                    $return['data']    = [
                        'CODIGO'  => '0',
                        'MENSAJE' => 'No existe',
                        'DATOS'   => [],
                    ];
                }else{

                    # Entregar categoria
                    foreach ($response_data['respuesta']['mensaje'] as $key => &$value) {
                        $value['categoria'] = substr($value['categoria'], 0, 1);
                    }

                    $return['data']    = [
                        'CODIGO'  => '1',
                        'MENSAJE' => 'OK',
                        'DATOS'   => $response_data['respuesta']['mensaje'],
                    ];
                }

                return $return;

            } else {
                return false;
            }

        } catch (\Exception $e) {
            $return['success'] = false;
            $return['message'] = "La URL no está disponible. Error: " . $e->getMessage();
            return $return;
        }

	}


}
