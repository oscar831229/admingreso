<?php

use App\Models\Amadeus\Datos;
use App\Models\Amadeus\Menu;
use App\Models\Amadeus\MenuItem;
use App\Models\Amadeus\SalonMenuItem;
use App\Models\Amadeus\FormasPago;
use App\Models\Amadeus\CiudadesDian;
use App\Models\Amadeus\ResolucionFactura;

use App\Models\Income\IcmMenu;
use App\Models\Income\IcmMenuItem;
use App\Models\Income\IcmEnvironmentIcmMenuItem;
use App\Models\Income\IcmEnvironmentIncomeItem;
use App\Models\Income\IcmAffiliateCategory;
use App\Models\Income\IcmEnvironmentIncomeItemDetail;
use App\Models\Income\IcmEnvironment;
use App\Models\Income\IcmPaymentMethod;
use App\Models\Income\CommonCity;
use App\Models\Income\IcmResolution;

if (!function_exists('synchronizePOSSystem')) {
    function synchronizePOSSystem()
    {
        # Sincronizar menus, menus_itmes, salon_menus_items
        $menus = Menu::all();
        foreach ($menus as $key => $menu) {
            $icmmenu = IcmMenu::find($menu->id);
            if(!$icmmenu){
                $icmmenu = new IcmMenu;
                $icmmenu->id             = $menu->id;
                $icmmenu->name           = $menu->nombre;
                $icmmenu->requested_name = $menu->nombre_pedido;
                $icmmenu->state          = $menu->estado;
                $icmmenu->user_created  = 1;
                $icmmenu->save();
            }else{
                $icmmenu->update([
                    'requested_name' => $menu->nombre_pedido,
                    'state'          => $menu->estado
                ]);
            }

        }

        $menusitems = MenuItem::all();
        foreach ($menusitems as $key => $menusitem) {
            $icmmenusitem = IcmMenuItem::find($menusitem->id);
            if(!$icmmenusitem){
                $icmmenusitem = new IcmMenuItem;
                $icmmenusitem->id                     = $menusitem->id;
                $icmmenusitem->icm_menu_id            = $menusitem->menus_id;
                $icmmenusitem->name                   = $menusitem->nombre;
                $icmmenusitem->requested_name         = $menusitem->nombre_pedido;
                $icmmenusitem->barcode                = $menusitem->codigo_barras;
                $icmmenusitem->value                  = $menusitem->valor;
                $icmmenusitem->percentage_iva         = $menusitem->porcentaje_iva;
                $icmmenusitem->percentage_impoconsumo = $menusitem->porcentaje_impoconsumo;
                $icmmenusitem->state                  = $menusitem->estado;
                $icmmenusitem->user_created           = 1;
                $icmmenusitem->save();
            }else{
                $icmmenusitem->update([
                    'requested_name'         => $menusitem->nombre_pedido,
                    'barcode'                => $menusitem->codigo_barras,
                    'value'                  => $menusitem->valor,
                    'percentage_iva'         => $menusitem->porcentaje_iva,
                    'percentage_impoconsumo' => $menusitem->porcentaje_impoconsumo,
                    'state'                  => $menusitem->estado
                ]);
            }

        }

        # SALON MENUS ITEMS
        $salonmenuitems = SalonMenuItem::all();
        foreach ($salonmenuitems as $key => $salonmenuitem) {
            $icmmenusitem = IcmEnvironmentIcmMenuItem::find($salonmenuitem->id);
            if(!$icmmenusitem){
                $icmmenusitem = new IcmEnvironmentIcmMenuItem;
                $icmmenusitem->id                 = $salonmenuitem->id;
                $icmmenusitem->icm_environment_id = $salonmenuitem->salon_id;
                $icmmenusitem->icm_menu_item_id   = $salonmenuitem->menus_items_id;
                $icmmenusitem->value              = $salonmenuitem->valor;
                $icmmenusitem->state              = $salonmenuitem->estado;
                $icmmenusitem->user_created       = 1;
                $icmmenusitem->save();
            }else{
                $icmmenusitem->update([
                    'value' => $salonmenuitem->valor,
                    'state' => $salonmenuitem->estado,
                ]);
            }

        }

        # FORMAS PAGO
        $formaspago = FormasPago::all();
        foreach ($formaspago as $key => $formapago) {
            $paymentmethod = IcmPaymentMethod::find($formapago->forpag);
            if(!$paymentmethod){
                $paymentmethod = new IcmPaymentMethod;
                $paymentmethod->id                  = $formapago->forpag;
                $paymentmethod->name                = $formapago->detalle;
                $paymentmethod->type_payment_method = $formapago->tipfor;
                $paymentmethod->redeban_operation   = $formapago->operacion_redeban;
                $paymentmethod->wallet_pocket       = isset($formapago->bolsillo_billetera) ? $formapago->bolsillo_billetera : '00';
                $paymentmethod->state               = $formapago->estado;
                $paymentmethod->user_created        = 1;
                $paymentmethod->save();
            }else{
                $paymentmethod->update([
                    'state' => $paymentmethod->state,
                    'type_payment_method' => $paymentmethod->type_payment_method,
                    'redeban_operation'   => $paymentmethod->redeban_operation,
                    'wallet_pocket'       => isset($formapago->bolsillo_billetera) ? $formapago->bolsillo_billetera : '00'
                ]);
            }

        }

        # Ciudades DIAN
        $commoncities = CiudadesDian::all();
        foreach ($commoncities as $key => $commoncity) {
            $city = CommonCity::find($commoncity->id);
            if(!$city){
                $city                       = new CommonCity;
                $city->id                   = $commoncity->id;
                $city->city_code            = $commoncity->codCiudad;
                $city->city_name            = $commoncity->nombre_ciudad;
                $city->department_name      = $commoncity->nombre_depto;
                $city->department_code      = $commoncity->codDepto;
                $city->country_code         = $commoncity->codPais;
                $city->country_name         = $commoncity->nombre_pais;
                $city->country_abbreviation = $commoncity->abreviatura_pais;
                $city->user_created         = 1;
                $city->save();
            }
        }

        # Resoluciones POS.
        $resolutions = ResolucionFactura::all();
        foreach ($resolutions as $key => $resolution) {

            $icmresolution = IcmResolution::find($resolution->id);
            if(!$icmresolution){
                $icmresolution = new IcmResolution;
                $icmresolution->id                  = $resolution->id;
                $icmresolution->icm_environment_id  = $resolution->salon_id;
                $icmresolution->invoice_type        = $resolution->tipo_factura;
                $icmresolution->authorization       = $resolution->autorizacion;
                $icmresolution->authorization_from  = $resolution->fecha_autorizacion;
                $icmresolution->authorization_to    = $resolution->fecha_fin_autorizacion;
                $icmresolution->prefix              = $resolution->prefijo_facturacion;
                $icmresolution->initial_consecutive = $resolution->consecutivo_inicial;
                $icmresolution->final_consecutive   = $resolution->consecutivo_final;
                $icmresolution->state               = $resolution->estado;
                $icmresolution->user_created        = 1;
                $icmresolution->save();
            }else{
                $icmresolution->update([
                    'icm_environment_id' => $resolution->salon_id,
                    'invoice_type'       => $resolution->tipo_factura,
                    'authorization'      => $resolution->autorizacion,
                    'authorization_from' => $resolution->fecha_autorizacion,
                    'authorization_to'   => $resolution->fecha_fin_autorizacion,
                    'prefix'             => $resolution->prefijo_facturacion,
                    'initial_consecutive'=> $resolution->consecutivo_inicial,
                    'final_consecutive'  => $resolution->consecutivo_final,
                    'state'              => $resolution->estado,
                    'user_created'       => 1
                ]);
            }


        }

    }
}


if (!function_exists('getSystemDate')) {
    function getSystemDate()
    {
        $datos = Datos::first();
        if (!session()->has('fecha_pos')) {
            $datos = Datos::first();
            session(['fecha_pos' => $datos->fecha]);
        }

        return session('fecha_pos');

    }
}

if (!function_exists('formatDate')) {
    function formatDate($fechaString, $format = 'Y-m-d')
    {
        return date($format, strtotime($fechaString));
    }
}


