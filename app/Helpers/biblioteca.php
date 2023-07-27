<?php

use App\Models\Admin\Permiso;
use App\Models\Common\Definition;
use App\Models\BudgetManagement\VIEBudgetBudgetaryValidity;

# EMAIL
use App\Clases\Mail\Cuenta;
use App\Clases\Mail\Correo;
use App\Clases\Mail\sendMail;


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




if (!function_exists('findEntity')) {
    function findEntity($nameEntity)
    {
        $names = explode(' ', $nameEntity);
        $where = "";

        $bd = DB::connection('INDIGO019')
            ->table('INENTIDAD');
        
        foreach ($names as $index=>$value){
            $bd->where('NOMENTIDA', 'like', '%'.$value.'%');
        }
       
        return  $bd->get();

    }
}


if (!function_exists('findUnity')) {
    function findUnity($nameEntity)
    {
        $names = explode(' ', $nameEntity);
        $where = "";

        $bd = DB::connection('INDIGO019')
            ->table('INUNIFUNC');
        
        foreach ($names as $index=>$value){
            $bd->where('UFUDESCRI', 'like', '%'.$value.'%');
        }
       
        return  $bd->get();

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

if (!function_exists('findWalletUsers')) {
    function findWalletUsers($name)
    {
        $names = explode(' ', $name);
        $where = "";

        $bd = DB::table('wallet_users')
            ->selectRaw("id, CONCAT(first_name, ' ', second_name, ' ', first_surname, ' ', second_surname) as name");
        
        foreach ($names as $index=>$value){
            $bd->whereRaw("CONCAT(first_name, ' ', second_name, ' ', first_surname, ' ', second_surname) like '%{$value}%'");
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

if (!function_exists('findentidad')) {
    function findentidad($entidad)
    {
        $names = explode(' ', $entidad);
        $where = "";

        $bd = DB::connection()
            ->table('entities');
        
        foreach ($names as $index=>$value){
            $bd->where('name', 'like', '%'.$value.'%');
        }
       
        return  $bd->get();

    }
}

if (!function_exists('findMedicalUnity')) {
    function findMedicalUnity($entidad)
    {
        $names = explode(' ', $entidad);
        $where = "";

        $bd = DB::connection()
            ->table('medical_units');
        
        foreach ($names as $index=>$value){
            $bd->where('name', 'like', '%'.$value.'%');
        }
       
        return  $bd->get();

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





if (!function_exists('findRiskManagement')) {
    function findRiskManagement($name)
    {
        $names = explode(' ', $name);
        $where = "";

        $bd = DB::connection()
            ->table('occupational_risk_managers');
        
        foreach ($names as $index=>$value){
            $bd->where('name', 'like', '%'.$value.'%');
        }
       
        return  $bd->get();

    }
}

if (!function_exists('findPensionManagers')) {
    
    function findPensionManagers($name)
    {
        $names = explode(' ', $name);
        $where = "";

        $bd = DB::connection()
            ->table('pension_fund_managers');
        
        foreach ($names as $index=>$value){
            $bd->where('name', 'like', '%'.$value.'%');
        }
       
        return  $bd->get();

    }
}

if (!function_exists('findProcessLeader')) {
    
    function findProcessLeader($name)
    {
        $names = explode(' ', $name);
        $where = "";

        $bd = DB::connection()
            ->table('people')
            ->select('people.*')
            ->join('contracts','contracts.person_id','people.id')
            ->where(['contracts.is_boss' => 'S', ]);
        
        foreach ($names as $index=>$value){
            $bd->whereRaw("concat(people.first_name,' ',people.second_name,' ',people.first_surname,' ',people.second_surname) like '%{$value}%'");
        }
       
        return  $bd->get();

    }
}

if (!function_exists('findCostCenter')) {
    
    function findCostCenter($name)
    {
        $names = explode(' ', $name);
        $where = "";

        $bd = DB::connection()
            ->table('cost_centers');

        
        foreach ($names as $index=>$value){
            $bd->whereRaw("name like '%{$value}%'");
        }
       
        return  $bd->get();

    }
}


if (!function_exists('findDocumentElectronicState')) {
    
    function findDocumentElectronicState($state)
    {
        $sql = "SELECT * FROM electronic_document_traceabilities 
                WHERE 
                    CASE
                        WHEN cisi_process_number = 0 THEN 'noqpar'
                        WHEN carvajal_legal_status = 'ACCEPTED' THEN 'accept'
                        ELSE 'noprocess'
                    END = '{$state}'";

        return DB::select($sql);
    }
}

if (!function_exists('findSupervidorName')) {
    
    function findSupervidorName($name)
    {
        $names = explode(' ', $name);
        $where = "";

        $bd = DB::connection()
            ->table('supervisors as s')
            ->selectRaw("
                s.id as supervisor_id, 
                concat(IFNULL(p.first_name,''),' ',IFNULL(p.second_name,''),' ',IFNULL(p.first_surname,''),' ', IFNULL(p.second_surname,'')) as supervidor,
                p.document_number
            ")
            ->join('people as p', 'p.id', '=', 's.person_id');
        
        foreach ($names as $index=>$value){
            $bd->where(DB::raw("concat(IFNULL(p.first_name,''),' ',IFNULL(p.second_name,''),' ',IFNULL(p.first_surname,''),' ', IFNULL(p.second_surname,''))"), 'like', '%'.$value.'%');
        }
       
        return  $bd->get();
        
    }
}


if (!function_exists('findOfficialNameVie')) {
    
    function findOfficialNameVie($name)
    {
        $names = explode(' ', $name);
        $where = "";

        $bd = DB::connection('SIGH')
            ->table('VIE19.Payroll.Employee AS pe')
            ->selectRaw("
                pe.Id, 
                ct.Nit,
                ct.Name 
            ")
            ->join('VIE19.Common.ThirdParty AS ct', 'ct.Id', '=', 'pe.ThirdPartyId');
        
        foreach ($names as $index=>$value){
            $bd->where(DB::raw("ct.Name"), 'like', '%'.$value.'%');
        }
       
        return  $bd->get();
        
    }
}

if (!function_exists('findPeopleName')) {
    
    function findPeopleName($name)
    {

        $names = explode(' ', $name);
        $where = "";

        $querySQL = "SELECT
                san.value
            FROM survey_forms sf
            INNER JOIN survey_form_details sfd ON sfd.survey_form_id = sf.id AND sfd.state = 'A'
            INNER JOIN detail_definitions dd ON dd.id = sfd.type
            LEFT JOIN survey_applications sa ON sa.survey_form_id  = sf.id AND sa.person_id = '800'
            LEFT JOIN survey_answers san ON san.survey_application_id = sa.id AND san.survey_form_detail_id = sfd.id
            LEFT JOIN survey_form_detail_values sfdv ON sfdv.id = san.value
            WHERE sf.state = 'P' AND sf.id = '2'";

        $bd = DB::connection()
            ->table('contracts AS c')
            ->selectRaw("
                p.id AS people_id,
                p.document_number,
                CONCAT(IFNULL(p.first_name,''), ' ', IFNULL(p.second_name,''), ' ', IFNULL(p.first_surname,''), ' ', IFNULL(p.second_surname,'')) people_name,
                c.cost_center_id,
                cc.name AS cost_center_name,
                CASE
                    WHEN p.email_notification IS NOT NULL THEN p.email_notification
                    WHEN (SELECT
                                san.value
                        FROM survey_forms sf
                        INNER JOIN survey_form_details sfd ON sfd.survey_form_id = sf.id AND sfd.state = 'A'
                        INNER JOIN detail_definitions dd ON dd.id = sfd.type
                        LEFT JOIN survey_applications sa ON sa.survey_form_id  = sf.id
                        LEFT JOIN survey_answers san ON san.survey_application_id = sa.id AND san.survey_form_detail_id = sfd.id
                        LEFT JOIN survey_form_detail_values sfdv ON sfdv.id = san.value
                        WHERE sa.person_id = p.id AND sf.state = 'P' AND sf.name = 'CORREO ASIGNADO POR EL HOSPITAL' AND sfd.name = 'CORREO ASIGNADO POR LA  INSTITUCION'
                    ) IS NOT NULL THEN (
                        SELECT
                                san.value
                        FROM survey_forms sf
                        INNER JOIN survey_form_details sfd ON sfd.survey_form_id = sf.id AND sfd.state = 'A'
                        INNER JOIN detail_definitions dd ON dd.id = sfd.type
                        LEFT JOIN survey_applications sa ON sa.survey_form_id  = sf.id
                        LEFT JOIN survey_answers san ON san.survey_application_id = sa.id AND san.survey_form_detail_id = sfd.id
                        LEFT JOIN survey_form_detail_values sfdv ON sfdv.id = san.value
                        WHERE sa.person_id = p.id AND sf.state = 'P' AND sf.name = 'CORREO ASIGNADO POR EL HOSPITAL' AND sfd.name = 'CORREO ASIGNADO POR LA  INSTITUCION'
                        limit 1
                    )
                    ELSE p.email
                END as email_notification
            ")
            ->join('people as p', 'p.id', '=', 'c.person_id')
            ->join('cost_centers AS cc', 'cc.id', '=', 'c.cost_center_id')
            ->where(['c.state' => 'A']);
        
        foreach ($names as $index=>$value){
            $bd->where(DB::raw("concat(IFNULL(p.first_name,''),' ',IFNULL(p.second_name,''),' ',IFNULL(p.first_surname,''),' ', IFNULL(p.second_surname,''))"), 'like', '%'.$value.'%');
        }
       
        return  $bd->get();
        
    }
}

if (!function_exists('findCodeDane')) {
    
    function findCodeDane($code)
    {
        $codes = explode(' ', $code);
        $where = "";

        $bd = DB::connection('SIGH')
            ->table('budget.dane_codes as dc')
            ->selectRaw('dc.*, dcp.id as dane_code_id, dcp.code as dane_code, dcp.title as dane_code_title')
            ->leftJoin('budget.dane_codes as dcp', 'dcp.id', '=', 'dc.dane_code_id');
        
        foreach ($codes as $index=>$value){
            $bd->where('dc.code', 'like', '%'.$value.'%');
        }

        $bd->orderByRaw('dc.code');
       
        return  $bd->get();

    }
}


if (!function_exists('BuildTree')) {
    
    function BuildTree($data, $parent = 0, $k=false) 
    {
        static $i = 1;
        if ($data[$parent]) {
            $html .= "\n<ul" . ($parent == 0 ? ' class="tree"' : '') . ">\n";
            $i++;
            foreach ($data[$parent] as $key => $v) {
                $child = BuildTree($data, $v->Parent, $k.$key);
                $html .= "<li>[" . $k.$key . ']';
                $html .= '<span>' . $v->Title . "</span>";
                if ($child) {
                    $i–;
                    $html .= $child;
                }
                $html .= "</li>\n";
            }
            $html .= "</ul>\n";
            return $html;
        } else {
            return false;
        }
    }
}


// 

if (!function_exists('findCodeCategory')) {
    
    function findCodeCategory($code, $year) 
    {
        $codes = explode(' ', $code);
        $where = "";

        $bd = DB::connection('SIGH')
            ->table('vie19.budget.category AS c')
            ->selectRaw('c.Id AS category_id,
                c.Code AS category_code,
                c.Name AS category_name,
                bv.year AS category_year,
                fs.Name as Category_financial_source
            ')
            ->join('vie19.budget.budgetaryvalidity AS bv', 'bv.Id', '=', 'c.BudgetaryValidityId')
            ->join('vie19.Budget.FinancialSource as fs', 'fs.Id', '=', 'c.FinancialSourceId')
            ->where(['c.auxiliary' => 1, 'bv.year' => $year, 'c.itemtype' => 2]);

        foreach ($codes as $index=>$value){
            $bd->where('c.Code', 'like', '%'.$value.'%');
        }
       
        return  $bd->get();

    }
}

if (!function_exists('findNameCategory')) {
    
    function findNameCategory($name, $year) 
    {
        $codes = explode(' ', $name);
        $where = "";

        $bd = DB::connection('SIGH')
            ->table('vie19.budget.category AS c')
            ->selectRaw('c.Id AS category_id,
                c.Code AS category_code,
                c.Name AS category_name,
                bv.year AS category_year,
                fs.Name as Category_financial_source
            ')
            ->join('vie19.budget.budgetaryvalidity AS bv', 'bv.Id', '=', 'c.BudgetaryValidityId')
            ->join('vie19.Budget.FinancialSource as fs', 'fs.Id', '=', 'c.FinancialSourceId')
            ->where(['c.auxiliary' => 1, 'bv.year' => $year, 'c.itemtype' => 2]);

        foreach ($codes as $index=>$value){
            $bd->where('c.Name', 'like', '%'.$value.'%');
        }
       
        return  $bd->get();

    }
}

if (!function_exists('findCodeDaneAuto')) {
    
    function findCodeDaneAuto($code) 
    {
        $codes = explode(' ', $code);
        $where = "";

        $bd = DB::connection('SIGH')
            ->table('budget.dane_codes AS dc')
            ->selectRaw('
                dc.id as dane_code_id,
                dc.code as dane_code,
                dc.title as dane_title
            ')
            ->where(['dc.code_type' => 'C']);

        foreach ($codes as $index=>$value){
            $bd->where('dc.code', 'like', '%'.$value.'%');
        }
       
        return  $bd->get();

    }
}


if (!function_exists('findNameDane')) {
    
    function findNameDane($name) 
    {
        $codes = explode(' ', $name);
        $where = "";

        $bd = DB::connection('SIGH')
            ->table('budget.dane_codes AS dc')
            ->selectRaw('
                dc.id as dane_code_id,
                dc.code as dane_code,
                dc.title as dane_title
            ')
            ->where(['dc.code_type' => 'C']);

        foreach ($codes as $index=>$value){
            $bd->where('dc.title', 'like', '%'.$value.'%');
        }
       
        return  $bd->get();

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

/**
 * Helper para traer la vigencia activa (Abiertas VIE19)
 * requerida en el modulo de presupuesto
 */
if (!function_exists('getBudgetaryValidity')) {
    
    function getBudgetaryValidity() 
    {
        if (request()->session()->exists('budgetary_validity')) {
            return Session::get('budgetary_validity');
        }

        # Consultamos la vigencia abierta
        $validity = VIEBudgetBudgetaryValidity::where(['status' => 2])->first();
        if(!$validity){
            throw new \Exception("Error no existe vigencia activa.", 1);
        }

        Session::put('budgetary_validity', $validity);

        return Session::get('budgetary_validity');

    }
}


/**
 * Helper para traer la vigencia activa (Abiertas VIE19)
 * requerida en el modulo de presupuesto
 */
if (!function_exists('getBudgetaryValidityObligationProcessed')) {
    
    function getBudgetaryValidityObligationProcessed() 
    {

        if (request()->session()->exists('budgetary_validity_obligation_processed')) {
            return Session::get('budgetary_validity_obligation_processed');
        }

        # Consultamos la vigencia abierta
        $validity = collect(VIEBudgetBudgetaryValidity::getBudgetaryValidityObligationProcessed());

        if(!$validity){
            throw new \Exception("Error no existe vigencia activa.", 1);
        }

        Session::put('budgetary_validity_obligation_processed', $validity);

        return Session::get('budgetary_validity_obligation_processed');

    }
}

if (!function_exists('getBudgetaryValidityCommitmentProcessed')) {
    
    function getBudgetaryValidityCommitmentProcessed() 
    {

        if (request()->session()->exists('budgetary_validity_commitment_processed')) {
            return Session::get('budgetary_validity_commitment_processed');
        }

        # Consultamos la vigencia abierta
        $validity = collect(VIEBudgetBudgetaryValidity::getBudgetaryValidityCommitmentProcessed());

        if(!$validity){
            throw new \Exception("Error no existe vigencia activa.", 1);
        }

        Session::put('budgetary_validity_commitment_processed', $validity);

        return Session::get('budgetary_validity_commitment_processed');

    }
}

if (!function_exists('getBudgetaryValidityPaymentProcessed')) {
    
    function getBudgetaryValidityPaymentProcessed() 
    {

        if (request()->session()->exists('budgetary_validity_payment_processed')) {
            return Session::get('budgetary_validity_payment_processed');
        }

        # Consultamos la vigencia abierta
        $validity = collect(VIEBudgetBudgetaryValidity::getBudgetaryValidityPaymentProcessed());

        if(!$validity){
            throw new \Exception("Error no existe vigencia activa.", 1);
        }

        Session::put('budgetary_validity_payment_processed', $validity);

        return Session::get('budgetary_validity_payment_processed');

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
 
