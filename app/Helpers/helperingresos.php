<?php

use App\Models\Amadeus\Datos;
use App\Models\Amadeus\DatosHotel;
use App\Models\Amadeus\Menu;
use App\Models\Amadeus\MenuItem;
use App\Models\Amadeus\SalonMenuItem;
use App\Models\Amadeus\FormasPago;
use App\Models\Amadeus\CiudadesDian;
use App\Models\Amadeus\ResolucionFactura;
use App\Models\Amadeus\Clientes;

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
use App\Models\Income\IcmCustomer;
use App\Models\Admin\IcmSystemConfiguration;

use App\Models\Seac\ClientesSeac;

use Carbon\Carbon;
use App\Models\Income\IcmRateType;
use App\Models\Income\IcmSpecialRate;
use App\Models\Income\IcmTypesIncome;

use Illuminate\Support\Facades\DB;

use App\Jobs\SynchronizationTask;
use App\Jobs\ClosingTasks;
use App\Jobs\ExecuteCoverage;



if (!function_exists('synchronizePOSSystem')) {

    function synchronizePOSSystem($component, $document_number = '')
    {

        # Parametros
        /**
         * description component
         * items               : Actualiza solo información de items_menus, menus, salon_menus_items
         * all                 : Actualizan informacion de items, payment-methods, dian-cities, invoice-resolutions
         * payment-methods     : Actualizar metodos de pago
         * dian-cities         : Actualizar ciudades DIAN.
         * invoice-resolutions : Actualizar resoluciones de factura
         * customers           : Actualiza informacion de todos los usurios (solo crea los que no existen)
         * customer            : Crea o actualiza un usuario en particular, se debe recibir el numero de documento
         * initialization      : Se actualiza toda la informacion del sistema requerida para el inicio de la aplicación.
         */


        if($component == 'items' || $component == 'all' || $component == 'initialization' ){

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
                        'state'                  => $menusitem->estado,
                        'name'                   => $menusitem->nombre
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

            \Log::info("sincronización de menus items {$component}");

        }


        if($component == 'payment-methods' || $component == 'all' || $component == 'initialization'){

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

            \Log::info("sincronización medios de pago {$component}");

        }


        if($component == 'dian-cities' || $component == 'all' || $component == 'initialization'){

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

            \Log::info("sincronización de ciudades dian {$component}");

        }

        if($component == 'invoice-resolutions' || $component == 'all' || $component == 'initialization'){

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

            \Log::info("sincronización de resoluciones {$component}");

        }


        # Sincronizar base de datos SISAFI
        if($component == 'all' || $component == 'initialization'){

            $codetypedocument = getDetailHomologationAlternativeDefinitions('identification_document_types');

            $generos          = getDetailHomologationDefinitions('gender');

            $ciudades         = CommonCity::selectRaw("CONCAT(TRIM(LEADING '0' FROM department_code), city_code) as codigo, id")->get()->pluck('id', 'codigo')->toArray();

            ClientesSeac::cursor()->each(function ($cliente) use ($codetypedocument, $ciudades, $generos){

                if($cliente->identificacion == 1)
                    return; // Salta esta iteración y pasa al siguiente cliente

                $customer = IcmCustomer::where(['document_number' => $cliente->identificacion])->first();
                if($customer)
                    return; // Salta esta iteración y pasa al siguiente cliente

                if(empty($cliente->primer_nombre) || empty($cliente->primer_apellido))
                    return;

                $document_type = isset($codetypedocument[$cliente['tipo_id']]) ? $codetypedocument[$cliente['tipo_id']] : $codetypedocument['CC'];
                $ciudades_dian = isset($ciudades[$cliente->cod_municipio]) ? $ciudades[$cliente->cod_municipio] : NULL;
                $gender        = isset($generos[$cliente->genero]) ? $generos[$cliente->genero] : NULL;

                $clientenew = IcmCustomer::create([
                    'document_type'       => $document_type,
                    'document_number'     => $cliente->identificacion,
                    'first_name'          => $cliente->primer_nombre,
                    'second_name'         => $cliente->segundo_nombre,
                    'first_surname'       => $cliente->primer_apellido,
                    'second_surname'      => $cliente->segundo_apellido,
                    'birthday_date'       => $cliente->fecha_nacimiento,
                    'phone'               => $cliente->celular,
                    'email'               => $cliente->correo,
                    'icm_municipality_id' => $ciudades_dian,
                    'address'             => $cliente->direccion,
                    'gender'              => $gender,
                    'type_regime_id'      => 49,
                    'user_created'        => 1
                ]);

            });

        }

        if($component == 'clientes-sisafi'){

            $codetypedocument = getDetailHomologationAlternativeDefinitions('identification_document_types');

            $generos          = getDetailHomologationDefinitions('gender');

            $ciudades         = CommonCity::selectRaw("CONCAT(TRIM(LEADING '0' FROM department_code), city_code) as codigo, id")->get()->pluck('id', 'codigo')->toArray();

            $fechaActual = new DateTime();  // Fecha y hora actual
            $fechaActual->sub(new DateInterval('P2D'));  // Restar 2 días

            $fecha_proceso = $fechaActual->format('Y-m-d');  // Formato de salida: YYYY-MM-DD

            ClientesSeac::whereDate('fecha_creacion', '>=', $fecha_proceso)->cursor()->each(function ($cliente) use ($codetypedocument, $ciudades,  $generos){

                if($cliente->identificacion == 1)
                    return; // Salta esta iteración y pasa al siguiente cliente

                $customer = IcmCustomer::where(['document_number' => $cliente->identificacion])->first();
                if($customer)
                    return; // Salta esta iteración y pasa al siguiente cliente

                if(empty($cliente->primer_nombre) || empty($cliente->primer_apellido))
                    return;

                $document_type = isset($codetypedocument[$cliente['tipo_id']]) ? $codetypedocument[$cliente['tipo_id']] : $codetypedocument['CC'];
                $ciudades_dian = isset($ciudades[$cliente->cod_municipio]) ? $ciudades[$cliente->cod_municipio] : NULL;
                $gender        = isset($generos[$cliente->genero]) ? $generos[$cliente->genero] : NULL;

                $clientenew = IcmCustomer::create([
                    'document_type'       => $document_type,
                    'document_number'     => $cliente->identificacion,
                    'first_name'          => $cliente->primer_nombre,
                    'second_name'         => $cliente->segundo_nombre,
                    'first_surname'       => $cliente->primer_apellido,
                    'second_surname'      => $cliente->segundo_apellido,
                    'birthday_date'       => $cliente->fecha_nacimiento,
                    'phone'               => $cliente->celular,
                    'email'               => $cliente->correo,
                    'icm_municipality_id' => $ciudades_dian,
                    'address'             => $cliente->direccion,
                    'gender'              => $gender,
                    'type_regime_id'      => 49,
                    'user_created'        => 1
                ]);

            });

        }


        // if($component == 'customers' || $component == 'initialization'){

        //     $codetypedocument = getDetailHomologationDefinitions('identification_document_types');

        //     $clientes = Clientes::selectRaw("
        //             cedula,
        //             tipdoc,
        //             digitov,
        //             nombre,
        //             primer_nombre,
        //             segundo_nombre,
        //             primer_apellido,
        //             segundo_apellido,
        //             direccion,
        //             IFNULL(telefono1, telefono2) AS telefono,
        //             IFNULL(emailfe, email) AS email,
        //             ciudades_dian,
        //             regimen_fiscal"
        //         )
        //         ->whereRaw("primer_nombre IS NOT NULL AND primer_apellido IS NOT  NULL")
        //         ->get();

        //     foreach ($clientes as $key => $cliente) {

        //         $cliente->cedula = trim($cliente->cedula);
        //         $customer = IcmCustomer::where(['document_number' => $cliente->cedula])->first();

        //         if(!$customer){

        //             $document_type = isset($codetypedocument[$cliente->tipdoc]) ?  $codetypedocument[$cliente->tipdoc] : $codetypedocument[13];

        //             IcmCustomer::create([
        //                 'document_type'             => $document_type,
        //                 'document_number'           => $cliente->cedula,
        //                 'first_name'                => $cliente->primer_nombre,
        //                 'second_name'               => $cliente->segundo_nombre,
        //                 'first_surname'             => $cliente->primer_apellido,
        //                 'second_surname'            => $cliente->segundo_apellido,
        //                 'phone'                     => $cliente->telefono,
        //                 'email'                     => $cliente->email,
        //                 'icm_municipality_id'       => $cliente->ciudades_dian,
        //                 'address'                   => $cliente->direccion,
        //                 'type_regime_id'            => $cliente->regimen_fiscal,
        //                 'user_created'              => 1
        //             ]);

        //         }

        //     }

        //     \Log::info("finalizdo sincronización cliente {$component}");

        // }

        # Debe venir identificación;
        if($component == 'customer' && !empty($document_number)){

            $codetypedocument = getDetailHomologationDefinitions('identification_document_types');

            $clientes = Clientes::selectRaw("cedula, tipdoc, digitov, nombre, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, direccion, IFNULL(telefono1, telefono2) AS telefono, IFNULL(email, emailfe) AS email, ciudades_dian, regimen_fiscal")
                ->where(['cedula' => $document_number])
                ->get();

            foreach ($clientes as $key => $cliente) {

                $cliente->cedula = trim($cliente->cedula);
                $customer = IcmCustomer::where(['document_number' => $cliente->cedula])->first();

                if(!$customer){

                    $document_type = isset($codetypedocument[$cliente->tipdoc]) ?  $codetypedocument[$cliente->tipdoc] : $codetypedocument[13];

                    IcmCustomer::create([
                        'document_type'             => $document_type,
                        'document_number'           => $cliente->cedula,
                        'first_name'                => $cliente->primer_nombre,
                        'second_name'               => $cliente->segundo_nombre,
                        'first_surname'             => $cliente->primer_apellido,
                        'second_surname'            => $cliente->segundo_apellido,
                        'phone'                     => $cliente->telefono,
                        'email'                     => $cliente->email,
                        'icm_municipality_id'       => $cliente->ciudades_dian,
                        'address'                   => $cliente->direccion,
                        'type_regime_id'            => $cliente->regimen_fiscal,
                        'user_created'              => 1
                    ]);

                } else {
                    $customer->update([
                        'first_name'                => $cliente->primer_nombre,
                        'second_name'               => $cliente->segundo_nombre,
                        'first_surname'             => $cliente->primer_apellido,
                        'second_surname'            => $cliente->segundo_apellido,
                        'phone'                     => $cliente->telefono,
                        'email'                     => $cliente->email,
                        'icm_municipality_id'       => $cliente->ciudades_dian,
                        'address'                   => $cliente->direccion,
                        'type_regime_id'            => $cliente->regimen_fiscal,
                        'user_created'              => 1,
                        'user_updated'              => 1
                    ]);
                }

            }

            \Log::info("finalizdo sincronización cliente {$component}  cedula: $document_number");
        }


        \Log::info("finalizdo proceso");


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


if (!function_exists('obtenerTemporadaDate')) {
    function obtenerTemporadaDate($date)
    {
        $objdate = Carbon::createFromFormat('Y-m-d', $date);
        $year    = $objdate->format('Y');

        # Festivo
        $holidays       = getHolidays($year);
        $tempodada_alta = isset($holidays[$date]) ? 'A' : 'V';

        # Temporada alta
        if(!isset($holidays[$date])){
            $special_rate   = IcmSpecialRate::where(['date' => $date])->first();
            $tempodada_alta = $special_rate ? 'A' : 'V';
        }

        return $tempodada_alta;

    }
}


if (!function_exists('obtenerTemporadaNameDate')) {
    function obtenerTemporadaNameDate($date)
    {
        $tempodada_alta = obtenerTemporadaDate($date);

        $icm_rate_type  = IcmRateType::where(['code' => $tempodada_alta])->first();
        if($icm_rate_type){
            $string = $tempodada_alta == 'A' ? '<span class="badge badge-primary" style="background-color: #2a59a5 !important;">'.$icm_rate_type->name.'</span>' :
            '<span class="badge badge-secondary">'.$icm_rate_type->name.'</span>';
            return $string;
        }else{
            return $tempodada_alta;
        }


    }
}


if (!function_exists('getIncomeCategoryHtml')) {
    function getIncomeCategoryHtml()
    {
        $types_of_incomes = IcmTypesIncome::where(['code' => 'AFI'])->orderBy('order', 'asc')->get();
        $html_item = '<ul>';
        foreach ($types_of_incomes as $types_of_income) {
            $name_type_income = ucwords($types_of_income['name']);
            $html_item .= "<li><input type='checkbox'><span>&nbsp;&nbsp;{$name_type_income}</span>";
            $categories  = $types_of_income->icm_affiliate_categories()->orderBy('code', 'asc')->get();
            foreach ($categories as $category) {
                $html_item .= "    <ul>";
                $html_item .= "        <li><input type='checkbox' class='capture' data-type_income_id='{$types_of_income['id']}' data-category_id='{$category['id']}'><span>&nbsp;&nbsp;{$category['name']}</span>";
                $html_item .= "        </li>";
                $html_item .= "    </ul>";
            }
            $html_item .= "</li>";
        }
        return $html_item.'</ul>';

    }
}

if (!function_exists('validateClosure')) {
    function validateClosure()
    {
        # Obtener informacion del hotel
        $hotel = DatosHotel::first();

        # Obtener informacion del pos
        $pos   = Datos::first();

        if($hotel->fecha != $pos->fecha){
            return [
                'success' => false,
                'message' => "No estan sincronizadas las fecha de sistema HOTEL({$hotel->fecha}) Y POS({$pos->fecha})"
            ];
        }

        $system = IcmSystemConfiguration::first();

        if(empty($system->system_date) || (!empty($system->system_date) && $system->system_date < $pos->fecha) ){

            try {

                $coverage_date = $system->system_date;

                DB::beginTransaction();

                # Actualizar fecha sistema
                $system->system_date = $pos->fecha;
                $system->update();

                # Ejecutar tareas de mantenimiento de cierre
                ClosingTasks::dispatch($system->system_date);

                // Ejecutar covertura en segundo plano
                ExecuteCoverage::dispatch($coverage_date);

                # Ejecutar creacion de usuarios sisafi
                SynchronizationTask::dispatch('clientes-sisafi');

                DB::commit();



                Session::forget('fecha_pos');
                getSystemDate();

            } catch (\Exception $e) {
                DB::rollback();
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }


        }

        return [
            'success' => true,
            'message' => ''
        ];

    }
}


