<?php

use App\Models\Admin\Permiso;
use App\Models\Common\Definition;

# EMAIL
use App\Clases\Mail\Cuenta;
use App\Clases\Mail\Correo;
use App\Clases\Mail\sendMail;

use App\Models\Amadeus\Menu;
use App\Models\Amadeus\MenuItem;
use App\Models\Amadeus\SalonMenuItem;

use App\Models\Income\IcmMenu;
use App\Models\Income\IcmMenuItem;
use App\Models\Income\IcmEnvironmentIcmMenuItem;
use App\Models\Income\IcmEnvironmentIncomeItem;
use App\Models\Income\IcmAffiliateCategory;
use App\Models\Income\IcmEnvironmentIncomeItemDetail;
use App\Models\Income\IcmEnvironment;

use Carbon\Carbon;


use \Mpdf\Mpdf as PDF;


if (!function_exists('getMenuActivo')) {
    function getMenuActivo($ruta)
    {
        if (request()->is($ruta) /*|| request()->is($ruta . '/*')*/) {
            return 'active';
        } else {
            return '';
        }
    }
}

if (!function_exists('getReporteActivo')) {
    function getReporteActivo($ruta)
    {

        $fullurl = Request::fullUrl();

        if (strpos($fullurl, $ruta) !== false ) {
            return 'active';
        } else {
            return '';
        }
    }
}


if (! function_exists('sanear_string')) {
    function sanear_string($string)
    {
        $string = trim($string);

        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );

        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );

        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );

        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );

        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );

        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $string
        );

        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
            array("\\", "¨", "º", "-", "~",
                 "#", "@", "|", "!", "\"",
                 "·", "$", "%", "&", "/",
                 "(", ")", "?", "'", "¡",
                 "¿", "[", "^", "`", "]",
                 "+", "}", "{", "¨", "´",
                 ">", "< ", ";", ",", ":",
                 ".", " "),
            '',
            $string
        );

        return $string;
    }
}

if (!function_exists('is_valid_email')) {
    function is_valid_email($str)
    {
        $matches = null;
        return (1 === preg_match('/^[A-z0-9\\._-]+@[A-z0-9][A-z0-9-]*(\\.[A-z0-9_-]+)*\\.([A-z]{2,6})$/', $str, $matches));
    }
}

if (!function_exists('findmunicipality')) {
    function findmunicipality($municipio)
    {
        $names = explode(' ', $municipio);
        $where = "";

        $bd = DB::connection()
            ->table('pv_municipios');

        foreach ($names as $index=>$value){
            $bd->where('nommunicipio', 'like', '%'.$value.'%');
        }

        return  $bd->get();

    }
}

if (!function_exists('findUser')) {
    function findUser($name)
    {
        $names = explode(' ', $name);
        $where = "";

        $bd = DB::connection()
            ->table('users');

        foreach ($names as $index=>$value){
            $bd->whereRaw("name like '%{$value}%'  || login like '%{$value}%'");
        }

        return  $bd->get();

    }
}

if (!function_exists('findCompanyAgreement')) {
    function findCompanyAgreement($name)
    {
        $names = explode(' ', $name);
        $where = "";

        $bd = DB::connection()
            ->table('icm_companies_agreements');

        foreach ($names as $index=>$value){
            $bd->whereRaw("name like '%{$value}%'  || document_number like '%{$value}%'");
        }

        return  $bd->get();

    }
}

if (!function_exists('generarCodigo')) {
    function generarCodigo($longitud)
    {
        $key = '';
        $pattern = '1234567890';
        $max = strlen($pattern)-1;
        for($i=0;$i < $longitud;$i++) $key .= $pattern{mt_rand(0,$max)};
        return $key;
    }
}

if (!function_exists('findNationality')) {
    function findNationality($nationality)
    {
        $names = explode(' ', $nationality);
        $where = "";

        $bd = DB::connection()
            ->table('countries');

        foreach ($names as $index=>$value){
            $bd->where('name', 'like', '%'.$value.'%');
        }

        return  $bd->get();

    }
}

if (!function_exists('imgperfil')) {
    function imgperfil()
    {
        if(auth()->user()){
            $imgperfil = 'img'.auth()->user()->id.'.jpg';

            if(file_exists(public_path('/img/perfiles/'.$imgperfil))) {
                return '/img/perfiles/'.$imgperfil;
            } else {
                return 'img/img1.jpg';
            }
        }else{
            return 'img/img1.jpg';
        }
    }
}

if (!function_exists('getDetailDefinitions')) {
    function getDetailDefinitions($code)
    {
        $defintion = new Definition;
        return $defintion->getDetailDefinitions($code);
    }
}

if (!function_exists('getDetailHomologationDefinitions')) {
    function getDetailHomologationDefinitions($code)
    {
        $defintion = new Definition;
        return $defintion->getDetailHomologationDefinitions($code);
    }
}

if (!function_exists('getDetailDefinition')) {
    function getDetailDefinition($codedefinition, $codedetail)
    {
        return DB::table('definitions AS d')
            ->select(['dd.id','dd.code'])
            ->join('detail_definitions AS dd', 'dd.definition_id', '=', 'd.id')
            ->where(['d.code' => $codedefinition, 'dd.code' => $codedetail])
            ->first();
    }
}

if (!function_exists('getUserIpAddr')) {

    function getUserIpAddr(){
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}


if (!function_exists('GenerarPdfRemovablePayment')) {
    function GenerarPdfRemovablePayment($document)
    {

        // Create the mPDF document
        $mpdf = new PDF( [
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'margin_header' => '3',
            'margin_top' => '20',
            'margin_bottom' => '20',
            'margin_footer' => '2',
        ]);


        $mpdf->WriteHTML($document->content_html);

        $path_file = storage_path('removable/').$document->file_name;
        $mpdf->Output($path_file);

        return $path_file;

    }
}

if (!function_exists('GenerarPdfDocument')) {
    function GenerarPdfDocument($document)
    {

        // Create the mPDF document
        $mpdf = new PDF( [
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'margin_header' => '3',
            'margin_top' => '20',
            'margin_bottom' => '20',
            'margin_footer' => '2',
        ]);


        $mpdf->WriteHTML($document->content_html);

        $path_file = storage_path('temp');
        if(!file_exists($path_file)){
            File::makeDirectory($path_file, $mode = 0777, true, true);
        }

        $path_file = $path_file.'/'.$document->file_name;
        $mpdf->Output($path_file);

        return $path_file;

    }
}

# TEXT CONEXION SMTP
if (!function_exists('testConnectionEmail')) {
    function testConnectionEmail($parameters)
    {

        $response = [
            'success' => true,
            'error' => ''
        ];


        # Cuenta SMTP
        $cuenta = new Cuenta;
        $cuenta->setServer($parameters->server)
            ->setPuerto($parameters->puerto)
            ->setEncryption($parameters->encryption)
            ->setEmail($parameters->email)
            ->setPassword($parameters->password);


        # Mensaje prueba de conexión
        $correo = new Correo;
        $correo->setAsunto('prueba conexion cuenta')
            ->setMensaje('Correo de verificación datos de autenticación email.')
            ->setPara($parameters->email);

        # Envio correo de prubas
        $sendMail = new sendMail;
        $sendMail->setCuenta($cuenta);
        $sendMail->setCorreo($correo);

        $sendMail->send();



        if(!empty($sendMail->error)){
            $response['success'] = false;
            $response['error'] = $sendMail->error;
        }

        return $response;

    }
}

if (!function_exists('getDateSpanish')) {
    function getDateSpanish()
    {

        $diassemana = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

        return  $diassemana[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y').' '.strftime("%H:%M") ; exit;
        //Salida: Miercoles 05 de Septiembre del 2016

    }
}


if (!function_exists('validateDate')) {
    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}


if (!function_exists('generarPdfHtml')) {
    function generarPdfHtml($documento)
    {

        // Create the mPDF document
        $mpdf = new PDF( [
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_header' => '3',
            'margin_top' => '20',
            'margin_bottom' => '20',
            'margin_footer' => '2',
        ]);

        $mpdf->WriteHTML($documento->html);
        $path_file = storage_path('app/cotizaciones/').$documento->nombre_archivo;
        $mpdf->Output($path_file);

        return $path_file;

    }
}

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
    }
}



if (!function_exists('getHighSeasonDays')) {
    function getHighSeasonDays($year)
    {

        $days_high_season = Config::get('days_week_high_season.days_high_season');
        $days_high_season = array_map('sanear_string', $days_high_season);
        $days_high_season = array_map('strtolower', $days_high_season);

        $name_days_spanish = array(
            'domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'
        );

        $alldays = [];

        for ($month=1; $month <= 12; $month++) {
            $Lastdaymonth = date('t', strtotime("$year-$month-01"));
            for ($day = 1; $day <= $Lastdaymonth; $day++) {
                $day_current = "$year-$month-$day";
                $number_day_week = date('w', strtotime($day_current));
                $day_week = $name_days_spanish[$number_day_week];

                $alldays[] = [
                    'day'  => $day_current,
                    'name' => $day_week,
                    'is_high_season' => in_array($day_week, $days_high_season) ? true : false
                ];
            }
        }

        return $alldays;


    }
}


if (!function_exists('date_system')) {
    function date_system()
    {
        return date('Ymd');
    }
}

if (!function_exists('getHolidays')) {
    function getHolidays($year)
    {
        $holidays[$year] = [];
        $appStoragePath = storage_path('holidays/'.$year.'.php');
        if(file_exists($appStoragePath)){
            require $appStoragePath;
        }
        return $holidays[$year];
    }
}

if (!function_exists('homologacionDatosAfiliado')) {
    function homologacionDatosAfiliado($grupo_data, $dato_original)
    {
        $homologacion = [
            'genero'  => [
                'F'       => 'F',
                'M'       => 'M',
                'default' => 'M'
            ],
            'tipo_dcto_beneficiario' => [
                'CC'      => 'CC',
                'RC'      => 'RC',
                'default' => 'CC'
            ]
        ];

        $control = isset($homologacion[$grupo_data][$dato_original]);
        if($control){
            $response = $homologacion[$grupo_data][$dato_original];
        }else{
            $response = $homologacion[$grupo_data]['default'];
        }
        return  $response;
    }
}

if (!function_exists('calcularEdad')) {
    function calcularEdad($fechaNacimiento)
    {
        $fechaNacimiento = Carbon::parse($fechaNacimiento);
    $edad = $fechaNacimiento->age;

    return $edad;
    }
}

if (!function_exists('getSystemDate')) {
    function getSystemDate()
    {
        return '20240424';
    }
}



