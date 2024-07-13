<?php


namespace App\Clases\Cajasan;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

use App\Models\Income\IcmRateType;
use App\Models\Income\IcmSpecialRate;
use App\Models\Income\IcmAgreement;


class Compute
{

    public static function calcularValorServicio($income_items, $client, $date){



        // $objdate = Carbon::createFromFormat('Y-m-d', $date);
        // $year    = $objdate->format('Y');

        // # Festivo
        // $holidays       = getHolidays($year);
        // $tempodada_alta = isset($holidays[$date]) ? 'A' : 'V';

        // # Temporada alta
        // if(!isset($holidays[$date])){
        //     $special_rate   = IcmSpecialRate::where(['date' => $date])->first();
        //     $tempodada_alta = $special_rate ? 'A' : 'V';
        // }

        $tempodada_alta = obtenerTemporadaDate($date);

        $temporada = IcmRateType::where(['code' => $tempodada_alta])->first();

        if($tempodada_alta == 'V'){
            # Tarifa general
            $service_value[] = [
                'value'              => $income_items->value,
                'class'              => 'default',
                'code'               => 'GENERAL_'.$tempodada_alta,
                'alterno'            => 'GEN',
                'subsidy'            => 0,
                'icm_rate_type_id'   => $temporada->id,
                'icm_rate_type_code' => $tempodada_alta,
            ];

        }else{

            $service_value[] = [
                'value'              => $income_items->value_high,
                'class'              => 'default',
                'code'               => 'GENERAL_'.$tempodada_alta,
                'alterno'            => 'GEN',
                'subsidy'            => 0,
                'icm_rate_type_id'   => $temporada->id,
                'icm_rate_type_code' => $tempodada_alta,
            ];

        }

        # TARIFA POR PARAMETRIZACIÓN DE VALORES
        $items_details = $income_items->icm_income_item_details()->where([
            'icm_types_income_id'       => $client->icm_types_income_id,
            'icm_affiliate_category_id' => $client->icm_affiliate_category_id,
            'icm_rate_type_id'          => $temporada->id
        ])->first();

        if($items_details){

            $income_type = $items_details->icm_types_income->code ? $items_details->icm_types_income->code             : 'REV';
            $category    = $items_details->icm_affiliate_category->code ? $items_details->icm_affiliate_category->code : 'NE';
            $code        = $income_type.'_'.$category.'_'.$tempodada_alta;
            $subsidy     = $income_type == 'AFI' ? $items_details->subsidy : 0;

            $service_value[] = [
                'value'              => $items_details->value,
                'class'              => 'parametrizacion',
                'code'               => $code,
                'alterno'            => $income_type,
                'subsidy'            => $subsidy,
                'icm_rate_type_id'   => $temporada->id,
                'icm_rate_type_code' => $tempodada_alta,
            ];

        }

        # TARIFA POR CONVENIO PROVENIENTES FORMULARIO AFILIADO
        if(isset($client->icm_agreements) && is_array($client->icm_agreements) && count($client->icm_agreements) > 0 ){

            foreach ($client->icm_agreements as $key => $agreement) {

                # Convenio enviado
                $icm_agreement = IcmAgreement::find($agreement['icm_agreement_id']);

                # Consulta servicio de ingreso en la temporada
                $detail        = $icm_agreement->icm_agreement_details()
                    ->where([
                        'icm_rate_type_id'               => $temporada->id,
                        'icm_environment_income_item_id' => $income_items->id
                    ])
                    ->first();

                if($detail && $detail->state == 'A'){
                    $service_value[] = [
                        'value'              =>  $detail->value,
                        'class'              => 'convenio',
                        'code'               => 'CONVENIO_'.$tempodada_alta.'_'.$icm_agreement->code,
                        'alterno'            => 'CONV',
                        'subsidy'            => 0,
                        'icm_rate_type_id'   => $temporada->id,
                        'icm_rate_type_code' => $tempodada_alta,
                        'icm_agreement'      => $icm_agreement
                    ];
                }

            }

        }

        array_multisort(array_column($service_value, 'value'), SORT_ASC, $service_value);
        return $service_value;

    }

    public static function calculateTaxes($icm_menu_items, $total_value){

        $response = new \stdClass();
        $response->base        = 0;
        $response->iva         = 0;
        $response->impoconsumo = 0;

        if($icm_menu_items->percentage_iva > 0 && $icm_menu_items->porcentage_impoconsumo > 0){
            throw new \Exception("Error producto con parametros de iva e impoconsumo {$icm_menu_items->name}", 1);
        }

        # Grabado con iva
        if($icm_menu_items->percentage_iva > 0){
            $porcentaje     = $icm_menu_items->percentage_iva / 100;
            $response->base = round(($total_value / (1 + $porcentaje)), 2);
            $response->iva  = $total_value - $response->base;
        }

        # Grabado con impoconsumo
        if($icm_menu_items->percentage_impoconsumo > 0){
            $porcentaje             = $icm_menu_items->percentage_impoconsumo / 100;
            $response->base         = round(($total_value / (1 + $porcentaje)), 2);
            $response->impoconsumo  = $total_value - $response->base;
        }

        return $response;

    }

}
