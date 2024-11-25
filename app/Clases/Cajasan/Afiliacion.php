<?php


namespace App\Clases\Cajasan;
use Illuminate\Support\Facades\Http;
use App\Models\Sisafi\SisafiSeacPersonas;

use App\Models\Admin\IcmSystemConfiguration;


class Afiliacion
{

    private $urlapi;
	private $request;
	private $usuario;
    private $password;

    private $medio_utilizado = 'Local';

    public function __construct(){

        $this->urlapi   = env('CAJASAN_CATEGORIA_API');
        $this->usuario  = env('USUARIO_API_SISAFI');
        $this->password = env('PASSWORD_API_SISAFI');

        $system_configuration  = IcmSystemConfiguration::first();
        $this->medio_utilizado = $system_configuration->query_type_category;

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

            switch (strtolower($this->medio_utilizado)) {

                case 'local':

                    $response_data = $this->consultaLocal($numero_documento, $tipo_documento, $tipo_operacion = 1);

                    break;

                case 'servicio':

                    $response_data = $this->servicioRest($numero_documento, $tipo_documento, $tipo_operacion = 1);

                    if(!$response_data['success'])
                        throw new \Exception($response_data['message'], 1);

                    $response_data = $response_data['data'];
                    break;

                default:
                    throw new \Exception("Error no se ha definido el metodo de consulta categoria", 1);
                    break;

            }

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


        } catch (\Exception $e) {
            $return['success'] = false;
            $return['message'] = $e->getMessage();
        }

        return $return;

	}

    public function servicioRest($numero_documento, $tipo_documento, $tipo_operacion = 1){

        $token = $this->generarToken();

        if(empty($this->urlapi))
            throw new Exception("No se ha definido la url del servicio");

        $this->request = [
            'tipo_documento' => $tipo_documento,
            'nro_documento'  => $numero_documento,
            'tipo'           => $tipo_operacion
        ];

        $message = '';

        # Consultar sercicion post afiliaciones
        $url_method = $this->urlapi.'/src1/v1/pack_funciones_cobertura/f_afiliado_ws_recrea';

        $response = Http::withOptions([
            'verify' => false,
        ])->withToken($token)->post($url_method, $this->request);

        if ($response->ok()) {
            return [
                'success' => true,
                'message' => $message,
                'data'    => $response->json()
            ];
        }else{

            $errors  = $response->json();
            $message = '';

            if(isset($errors['message'])){
                $message = imploed(', ', $errors['message']);
            }

            return [
                'success' => false,
                'message' => $message,
                'data'    => []
            ];
        }

    }

    public function consultaLocal($numero_documento, $tipo_documento, $tipo_operacion = 1){

        # Consultar persona
        $seac_person = SisafiSeacPersonas::where(['tipo_id' => $tipo_documento, 'identificacion' => $numero_documento])
            ->orderBy('categoria', 'ASC')
            ->orderByRaw("CASE WHEN tipo_reg = 'TR' THEN 0 ELSE 1 END ASC")
            ->first();

        if(!$seac_person){
            return [
                'respuesta' => [
                    'codigo'  => '-200',
                    'mensaje' => 'No se encontraron datos'
                ]
            ];
        }


        $grupo_familiar = SisafiSeacPersonas::select([
            'id',
            'tipo_reg AS tipo_registro',
            'vinculacion AS tipo_vinculacion',
            'subvinculacion AS tipo_subvinculacion',
            'relacion AS parentesco',
            'tipoid_ppal AS tipo_dcto_trabajador',
            'id_principal AS dcto_trabajador',
            'tipo_id AS tipo_dcto_beneficiario',
            'identificacion AS dcto_beneficiario',
            'primer_nombre AS primer_nombre',
            'segundo_nombre AS segundo_nombre',
            'primer_apellido AS primer_apellido',
            'segundo_apellido AS segundo_apellido',
            \DB::raw("DATE_FORMAT(fecha_nacimiento, '%Y%m%d') AS fecha_nacimiento"), // Formato de fecha yyyymmdd
            'genero AS genero',
            'categoria AS categoria',
            \DB::raw("'Pendiente' AS servicio"),
            'nit_empresa AS nit_empresa',
            \DB::raw('0 AS id_interno_empresa'),
            'razon_social',
            \DB::raw("'PENDIENTE' AS fecha_afil_tr")
        ])
        ->where('id_principal', $seac_person->id_principal)
        ->get()->toArray();

        return [
            'respuesta' => [
                'codigo'  => '200',
                'mensaje' => $grupo_familiar
            ]
        ];

    }

    /**
     * Tipos de vinculación de afiliados
     */
    public static function getVinculacion(){
        return [
            'T',
            'I',
            'P',
            'S'
        ];
    }


}
