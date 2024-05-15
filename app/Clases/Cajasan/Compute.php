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

        # Tarifa general
        $service_value[] = [
            'value' => $income_items->value,
            'class' => 'default',
            'code'  => 'GENERAL'
        ];

        $objdate = Carbon::createFromFormat('Ymd', $date);
        $year    = $objdate->format('Y');

        # Festivo
        $holidays = getHolidays($year);
        $tempodada_alta = isset($holidays[$date]) ? 'A' : 'V';

        # Temporada alta
        if(!$tempodada_alta){
            $special_rate   = IcmSpecialRate::where(['date' => $date])->first();
            $tempodada_alta = $special_rate ? 'A' : 'V';
        }

        $temporada = IcmRateType::where(['code' => $tempodada_alta])->first();

        # TARIFA POR PARAMETRIZACIÓN DE VALORES
        $items_details = $income_items->icm_income_item_details()->where([
            'icm_types_income_id'        => $client->icm_types_income_id,
            'icm_affiliate_category_id' => $client->icm_affiliate_category_id,
            'icm_rate_type_id'          => $temporada->id
        ])->first();

        if($items_details){

            $income_type = $items_details->icm_types_income->code ? $items_details->icm_types_income->code : 'REV';
            $category    = $items_details->icm_affiliate_category->code ? $items_details->icm_affiliate_category->code : 'NE';
            $code        = $income_type.'_'.$category.'_'.$tempodada_alta;

            $service_value[] = [
                'value' => $items_details->value,
                'class' => 'parametrizacion',
                'code'  => $code
            ];
        }

        # TARIFA POR CONVENIO
        if(isset($client->icm_agreement_id) && !empty($client->icm_agreement_id)){
            $icm_agreement = IcmAgreement::find($client->icm_agreement_id);
            $detail        = $icm_agreement->icm_agreement_details()->where(['icm_rate_type_id' => $temporada->id])->first();
            if($detail){
                $service_value[] = [
                    'value' =>  $detail->value,
                    'class' => 'convenio',
                    'code'  => 'CONVENIO_'.$icm_agreement->code
                ];
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

        if($icm_menu_items->porcentage_iva > 0 && $icm_menu_items->porcentage_impoconsumo > 0){
            throw new \Exception("Error producto con parametros de iva e impoconsumo {$icm_menu_items->name}", 1);
        }

        # Grabado con iva
        if($icm_menu_items->porcentage_iva > 0){
            $porcentaje     = $icm_menu_items->porcentage_iva / 100;
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
