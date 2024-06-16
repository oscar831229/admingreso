<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AmadeusPosApiService
{
    protected $baseUrl;

    private   $header;

    const  KEYSECRET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()-_+=~';

    public function __construct()
    {
        $this->baseUrl = env('AMADEUS_POS_API_BASE_URL');
    }

    public function setHeader($header){
        $this->header = $header;
    }

    public function facturarLiquidacion(array $factura)
    {

        $response = Http::withHeaders($this->header)->post("{$this->baseUrl}/invoice/save", $factura);

        if ($response->failed()) {
            throw new \Exception('Error al comunicar con el servicio externo: ' . $response->body());
        }

        return $response->json();
    }

    public function imprimirFactura($factura){

        $response = Http::withHeaders($this->header)->post("{$this->baseUrl}/invoice/print", $factura);

        if ($response->failed()) {
            throw new \Exception('Error al comunicar con el servicio externo: ' . $response->body());
        }

        return $response->json();

    }

    public function obtenerDatos($endpoint)
    {
        $response = Http::get("{$this->baseUrl}/$endpoint");

        if ($response->failed()) {
            throw new \Exception('Error al obtener datos del servicio externo: ' . $response->body());
        }

        return $response->json();
    }

    public static function encrypt($data) {
        $key    = self::KEYSECRET;
		$ivSize = openssl_cipher_iv_length('aes-256-cbc');
		$iv = openssl_random_pseudo_bytes($ivSize);
		$encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
		return base64_encode($iv . $encrypted);
	}


}
