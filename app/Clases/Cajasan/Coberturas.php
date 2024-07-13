<?php


namespace App\Clases\Cajasan;
use Illuminate\Support\Facades\Http;

use App\Models\Income\IcmCoverage;
use Illuminate\Support\Facades\DB;

use App\Models\Income\IcmLiquidacionDetailRevision;
use App\Models\Income\IcmTypesIncome;
use App\Models\Income\IcmAffiliateCategory;
use App\Models\Income\IcmPoliticalAffiliate;
use App\Models\Income\IcmCoverageDetail;
use App\Models\Admin\IcmSystemConfiguration;
use App\Models\Seac\SeacMdeIngresoSedes;


use Illuminate\Support\Facades\Auth;


class Coberturas
{

    private $services_sisafi;

    public function __construct(){
        $this->services_sisafi = new Afiliacion;
    }

    private $steps = [
        [
            'code'   => 'P1',
            'name'   => 'Iniciar proceso de coberturas',
            'method' => 'startProcess',
        ],[
            'code'   => 'P2',
            'name'   => 'Completar información afiliado y politicas grupo familiar',
            'method' => 'completeAffiliateInformation',
        ],[
            'code'   => 'P3',
            'name'   => 'Estructurar informacion final',
            'method' => 'finalInformationStructure',
        ],[
            'code'   => 'P4',
            'name'   => 'Transferir información SEAC',
            'method' => 'transferSEACinformation',
        ]
    ];

    private $icm_coverage;

    public function executeProccessId($icm_coverage_id){

        # Consular ultimo proceso
        $this->icm_coverage = IcmCoverage::find($icm_coverage_id);

        $this->step  = $this->getLastProcess($icm_coverage_id);

        $error      = false;
        $last_setp  = false;
        while ($this->step) {

            $last_setp = $this->step;

            try {

                if (method_exists($this, $this->step['method'])) {

                    # Correr proceso
                    call_user_func([$this, $this->step['method']]);

                    # Registrar proceso realizado
                    $this->icm_coverage->state     = 'E';
                    $this->icm_coverage->step      = $this->step['code'];
                    $this->icm_coverage->step_name = $this->step['name'];
                    $this->icm_coverage->save();

                    # Nuevo paso a procesar
                    $this->step = $this->getStepNext($this->step);

                } else {
                    throw new \Exception("No existe el metodo programado en el paso: {$this->step['code']}", 1);
                }


            } catch (\Throwable $th) {
                $this->icm_coverage->errors    = $th->getMessage();
                $this->icm_coverage->events++;;
                $this->icm_coverage->state     = 'D';
                $this->icm_coverage->step      = $this->step['code'];
                $this->icm_coverage->step_name = $this->step['name'];
                $this->icm_coverage->save();
                $error = true;
                break;
            }
        }

        # Si no se presento error finaliza el proceso
        if(!$error){
            $this->icm_coverage->state      = 'T';
            $this->icm_coverage->errors     = NULL;
            $this->icm_coverage->step       = $last_setp['code'];
            $this->icm_coverage->step_name  = $last_setp['name'];
            $this->icm_coverage->save();
        }

    }

    public function transferSEACinformation(){

        $system_configurations = IcmSystemConfiguration::first();

        # Limpiar informacion en seac del día segun codigo de cobertura.
        $seac_ingresos = SeacMdeIngresoSedes::where([
            'MDECOB_FECHA_SERVICIO' => $this->icm_coverage->coverage_date,
            'MDECOB_INFRAESTRUCTURA' => $system_configurations->infrastructure_code
        ])->delete();

        $coverage_detail = IcmCoverageDetail::where(['icm_coverage_id' => $this->icm_coverage->id])->get()->toArray();

        foreach ($coverage_detail as $key => $detail) {
            unset($detail['MDESIS_FECHACARGUE']);
            SeacMdeIngresoSedes::create($detail);
        }

    }

    public function deleteConsolidatedCoverage($coverage){
        return IcmCoverageDetail::where(['MDECOB_FECHA_SERVICIO' => $coverage->coverage_date])->delete();
    }

    public function finalInformationStructure(){

        $this->deleteConsolidatedCoverage($this->icm_coverage);

        $querySQL = "SELECT
            ildr.icm_coverage_id,
            tda.alternative_code AS MDEPER_COT_TIPOID,
            ildr.affiliated_document_number AS MDEPER_COT_IDENTIF,
            tdb.alternative_code AS MDEPER_BEN_TIPOID,
            ildr.document_number AS MDEPER_BEN_IDENTIF,
            ildr.first_surname AS MDEPER_PRIAPE,
            ildr.second_surname AS MDEPER_SEGAPE,
            ildr.first_name AS MDEPER_PRINOM,
            ildr.second_name AS MDEPER_SEGNOM,
            NULL AS MDEPER_RAZSOC,
            ildr.birthday_date AS MDEPER_NACIMIENTO,
            gb.code AS MDEPER_GENERO,
            ildr.address AS MDEPER_DIRECCION_RES,
            cc.department_code AS MDEPER_CODDPTO_RES,
            CONCAT(cc.department_code,cc.city_code) AS MDEPER_CODMUN_RES,
            NULL AS MDEPER_BARRIO_RES,
            ildr.phone AS MDEPER_CELULAR,
            '' AS MDEPER_TEL_FIJO_RES,
            '' AS MDEPER_HABEAS_DATA,
            'PN' AS MDEPER_TIPO_PERSONA,
            ildr.email AS MDEPER_CORREO,
            ildr.code_seac AS MDECOB_PRODUCTO_SEAC,
            ildr.icm_income_item_code AS MDECOB_PRODUCTO_ORIGEN,
            ildr.infrastructure_code AS MDECOB_INFRAESTRUCTURA,
            ildr.liquidation_date AS MDECOB_FECHA_SERVICIO,
            ildr.nit_company_affiliates AS MDECOB_NITEMP,
            'PX' AS MDECOB_ROL_CLIENTE,
            ildr.type_register AS MDECOB_VINCULACION,
            0 AS MDECOB_SUBVIN,
            ildr.type_link AS MDECOB_RELACION,
            CASE
                WHEN iti.code IN ('AFI', 'CAJ', 'PAR') THEN iac.code
                ELSE 'D'
            END AS MDECOB_CATEGORIA,
            SUM(ildr.total) AS MDECOB_VALOR_VENTA,
            its.code AS MDECOB_TIPO_SUB,
            SUM(ildr.subsidy) AS MDECOB_SUBSIDIO,
            COUNT(ildr.id) AS MDECOB_USOS,
            ildr.number_places AS MDECOB_PARTICIPANTES,
            'VNT' AS MDECOB_POLITICA,
            ifcf.name AS MDECOB_CAJA,
            ildr.system_names AS MDECOB_SISTEMAFUENTE,
            NULL AS MDEPRO_PROCESO,
            NULL AS MDECOB_TARIFA_PROMO,
            MAX(ildr.icm_liquidation_id) AS MDECOB_FOLIO,
            GROUP_CONCAT(IFNULL(ildr.billing_prefix, ''), ildr.consecutive_billing SEPARATOR ', ') AS MDECOB_FACTURA,
            NULL AS MDESIS_FECHACARGUE,
            CASE
                WHEN iti.code = 'AFI' AND iac.code = 'A' THEN 1
                WHEN iti.code = 'AFI' AND iac.code = 'B' THEN 2
                WHEN iti.code = 'AFI' AND iac.code = 'C' AND IFNULL(ils.icm_agreement_id, 0) = 0 THEN 3
                WHEN iti.code = 'AFI' AND iac.code = 'C' AND IFNULL(ils.icm_agreement_id, 0) <> 0 THEN 10
                WHEN iti.code = 'CAJ' AND iac.code = 'A' THEN 7
                WHEN iti.code = 'CAJ' AND iac.code = 'B' THEN 8
                WHEN iti.code = 'CAJ' AND iac.code = 'C' THEN 9
                ELSE 4
            END AS MDECOB_CATEGORIA_SSF
        FROM `icm_liquidacion_detail_revisions` AS ildr
        INNER JOIN icm_liquidation_services AS ils on ils.id = ildr.icm_liquidation_service_id
        LEFT JOIN `detail_definitions` AS tda ON tda.id = ildr.affiliated_type_document
        LEFT JOIN `detail_definitions` AS tdb ON tdb.id = ildr.document_type
        LEFT JOIN `common_cities` AS cc ON cc.id = ildr.icm_municipality_id
        LEFT JOIN icm_types_incomes AS iti ON iti.id = ildr.icm_types_income_id
        LEFT JOIN icm_affiliate_categories AS iac ON iac.id = ildr.icm_affiliate_category_id
        LEFT JOIN icm_type_subsidies AS its ON its.id = ildr.icm_type_subsidy_id
        LEFT JOIN icm_family_compensation_funds AS ifcf ON ifcf.id = ildr.icm_family_compensation_fund_id
        LEFT JOIN detail_definitions AS gb ON gb.id = ildr.gender
        WHERE ildr.icm_coverage_id = ?
        GROUP BY
                ildr.icm_coverage_id,
                ils.icm_agreement_id,
                tda.alternative_code,
                ildr.affiliated_document_number,
                tdb.alternative_code,
                ildr.document_number,
                ildr.first_surname,
                ildr.second_surname,
                ildr.first_name,
                ildr.second_name,
                ildr.birthday_date,
                gb.code,
                ildr.address,
                cc.department_code,
                cc.city_code,
                ildr.phone,
                ildr.email,
                ildr.code_seac,
                ildr.icm_income_item_code,
                ildr.infrastructure_code,
                ildr.liquidation_date,
                ildr.nit_company_affiliates,
                ildr.type_register,
                ildr.type_link ,
                iti.code,
                iac.code,
                its.code,
                ildr.number_places,
                ifcf.name,
                ildr.system_names";

        $consolidate = DB::select($querySQL, [$this->icm_coverage->id]);

        $political_affiliates    = [];
        $political_beneficiaries = [];
        foreach ($consolidate as $key => $values) {
            if(!empty($values->MDEPER_COT_IDENTIF)){
                $political_affiliates[$values->MDECOB_PRODUCTO_ORIGEN][$values->MDEPER_COT_IDENTIF] = true;
            }
            $political_beneficiaries[$values->MDECOB_PRODUCTO_ORIGEN][$values->MDEPER_BEN_IDENTIF] = true;
            $values = (Array) $values;
            IcmCoverageDetail::create($values);
        }

        # llenar información por activación de politicas
        $system_configurations = IcmSystemConfiguration::first();

        if($system_configurations->policy_enabled == 1){
            # Recorrer cedula afiliados
            foreach ($political_affiliates as $key => $all_affiliates) {

                $icm_income_item_code = $key;
                # Recorrer afiliaciones
                foreach ($all_affiliates as $document_number_affiliate => $value) {
                    # code...
                    $querySQL = "SELECT
                            '{$this->icm_coverage->id}' as icm_coverage_id,
                            tdb.alternative_code AS MDEPER_COT_TIPOID,
                            ipa.affiliated_document AS MDEPER_COT_IDENTIF,
                            tdb.alternative_code AS MDEPER_BEN_TIPOID,
                            ipa.document_number AS MDEPER_BEN_IDENTIF,
                            ipa.first_surname AS MDEPER_PRIAPE,
                            ipa.second_surname AS MDEPER_SEGAPE,
                            ipa.first_name AS MDEPER_PRINOM,
                            ipa.second_name AS MDEPER_SEGNOM,
                            NULL AS MDEPER_RAZSOC,
                            ipa.birthday_date AS MDEPER_NACIMIENTO,
                            gb.code AS MDEPER_GENERO,
                            ic.address AS MDEPER_DIRECCION_RES,
                            cc.department_code AS MDEPER_CODDPTO_RES,
                            CONCAT(cc.department_code,cc.city_code) AS MDEPER_CODMUN_RES,
                            NULL AS MDEPER_BARRIO_RES,
                            ic.phone AS MDEPER_CELULAR,
                            NULL AS MDEPER_TEL_FIJO_RES,
                            NULL AS MDEPER_HABEAS_DATA,
                            'PN' AS MDEPER_TIPO_PERSONA,
                            ic.email AS MDEPER_CORREO,
                            iii.code_seac AS MDECOB_PRODUCTO_SEAC,
                            iii.code AS MDECOB_PRODUCTO_ORIGEN,
                            isc.infrastructure_code AS MDECOB_INFRAESTRUCTURA,
                            ipa.political_date AS MDECOB_FECHA_SERVICIO,
                            ipa.nit_company_affiliates AS MDECOB_NITEMP,
                            'PX' AS MDECOB_ROL_CLIENTE,
                            ipa.type_register AS MDECOB_VINCULACION,
                            1 AS MDECOB_SUBVIN,
                            ipa.type_link AS MDECOB_RELACION,
                            iac.code AS MDECOB_CATEGORIA,
                            NULL AS MDECOB_VALOR_VENTA,
                            NULL AS MDECOB_TIPO_SUB,
                            NULL AS MDECOB_SUBSIDIO,
                            1 AS MDECOB_USOS,
                            1 AS MDECOB_PARTICIPANTES,
                            'POL' AS MDECOB_POLITICA,
                            NULL AS MDECOB_CAJA,
                            isc.system_names AS MDECOB_SISTEMAFUENTE,
                            NULL AS MDEPRO_PROCESO,
                            NULL AS MDECOB_TARIFA_PROMO,
                            NULL AS MDECOB_FOLIO,
                            NULL AS MDECOB_FACTURA,
                            NULL AS MDESIS_FECHACARGUE,
                            CASE
                                WHEN iac.code = 'A' THEN 1
                                WHEN iac.code = 'B' THEN 2
                                WHEN iac.code = 'C' THEN 3
                                ELSE 4
                            END AS MDECOB_CATEGORIA_SSF
                        FROM icm_political_affiliates AS ipa
                        INNER JOIN icm_system_configurations AS isc ON 1 = 1
                        INNER JOIN icm_income_items AS iii ON iii.id = ipa.icm_income_item_id
                        LEFT JOIN detail_definitions AS tda ON tda.id = ipa.affiliated_type_document
                        LEFT JOIN detail_definitions AS tdb ON tdb.id = ipa.document_type
                        LEFT JOIN detail_definitions AS gb ON gb.id = ipa.gender
                        LEFT JOIN icm_customers AS ic ON ic.document_number = ipa.document_number
                        LEFT JOIN common_cities AS cc ON cc.id = ic.icm_municipality_id
                        INNER JOIN icm_affiliate_categories AS iac ON iac.id = ipa.icm_affiliate_category_id
                        LEFT JOIN icm_liquidacion_detail_revisions AS ildr ON ildr.document_number = ipa.document_number AND ildr.liquidation_date = ipa.political_date AND ildr.icm_income_item_id = ipa.icm_income_item_id
                        WHERE ipa.affiliated_document = '{$document_number_affiliate}' AND iii.code = '{$icm_income_item_code}' AND ipa.political_date = '{$this->icm_coverage->coverage_date}' AND ildr.id IS NULL";

                    $political_people = DB::select($querySQL, []);
                    foreach ($political_people as $key => $person) {
                        IcmCoverageDetail::create((Array) $person);
                    }
                }

            }

        }

    }

    public function completeAffiliateInformation(){


        $step_next = $this->getStepNext($this->step);

        # Obtener información a procesar
        /**
         * Ingreso por afiliación con bandera is_processed_affiliate en cero
         * Corresponde a los afiliados que no tuviero ingreso a traves del servicio web
         * se debe alimentar el grupo familiar por politica activa.
         */
        $affiliates = IcmLiquidacionDetailRevision::where([
            'icm_liquidacion_detail_revisions.icm_coverage_id'        => $this->icm_coverage->id,
            'icm_liquidacion_detail_revisions.is_processed_affiliate' => 0,
            'icm_types_incomes.code'                                  => 'AFI'
        ])->join('icm_types_incomes', 'icm_types_incomes.id', '=','icm_liquidacion_detail_revisions.icm_types_income_id')
        ->selectRaw("icm_liquidacion_detail_revisions.*")
        ->get();

        # Tipo de ingreso particular
        $types_of_incomes =  IcmTypesIncome::all()->pluck('id', 'code');
        $categories       =  IcmAffiliateCategory::all()->pluck('id', 'code');
        $control_politica = [];
        $afiliado_grupo   = []; // servicio, afiliado

        foreach ($affiliates as $key => $affiliate) {

            $affiliates_group = [];

            # Consultar servicio SISAFI
            $response = $this->services_sisafi->consultarCategoria($affiliate->document_number);
            if(!$response['success'])
                throw new \Exception($response['message'], 1);

            $user_id = Auth::check() ? auth()->user()->id : 1;

            # Respuesta exitosa servicicio web
            if($response['success'] && $response['data']['CODIGO'] == 1){

                $grupo_afiliado = $response['data']['DATOS'];

                $trabajador = [];
                # Consultamos al afiliado principal
                foreach ($grupo_afiliado as $key => $afiliado) {
                    if($afiliado['tipo_registro'] == 'TR'){
                        $trabajador = $afiliado;
                        break;
                    }
                }

                $nombre_trabajador = [];
                if(isset($trabajador['primer_nombre'])  &&  !empty($trabajador['primer_nombre'])){
                    $nombre_trabajador[] = $trabajador['primer_nombre'];
                }
                if(isset($trabajador['segundo_nombre'])  &&  !empty($trabajador['segundo_nombre'])){
                    $nombre_trabajador[] = $trabajador['segundo_nombre'];
                }
                if(isset($trabajador['primer_apellido'])  &&  !empty($trabajador['primer_apellido'])){
                    $nombre_trabajador[] = $trabajador['primer_apellido'];
                }
                if(isset($trabajador['segundo_apellido'])  &&  !empty($trabajador['segundo_apellido'])){
                    $nombre_trabajador[] = $trabajador['segundo_apellido'];
                }

                $icm_types_income       =  $types_of_incomes['AFI'];
                $icm_affiliate_category =  isset($categories[$trabajador['categoria']]) ? $categories[$trabajador['categoria']] : null;

                if(empty($icm_affiliate_category)){
                    throw new \Exception("Error categoria trabajador desconocidad {$trabajador['categoria']}", 1);
                }

                $genders                  = getDetailHomologationDefinitions('gender');
                $document_types           = getDetailHomologationDefinitions('identification_document_types');
                $affiiliate_name          = implode(' ', $nombre_trabajador);
                $affiliated_type_document = homologacionDatosAfiliado('tipo_dcto_beneficiario', $trabajador['tipo_dcto_trabajador']);

                foreach ($grupo_afiliado as $key => $afiliado) {

                    $gender_code        = homologacionDatosAfiliado('genero', $afiliado['genero']);
                    $document_type_code = homologacionDatosAfiliado('tipo_dcto_beneficiario', $afiliado['tipo_dcto_beneficiario']);
                    $edad               = calcularEdad($afiliado['fecha_nacimiento']);

                    $affiliates_group[] = [
                        'is_processed_affiliate'          => 1,
                        'type_register'                   => $afiliado['tipo_registro'],
                        'relationship'                    => $afiliado['parentesco'],
                        'type_link'                       => $afiliado['tipo_vinculacion'],
                        'document_number'                 => $afiliado['dcto_beneficiario'],
                        'document_type'                   => $document_types[$document_type_code],
                        'first_name'                      => $afiliado['primer_nombre'],
                        'second_name'                     => isset($afiliado['segundo_nombre']) ? $afiliado['segundo_nombre'] : '',
                        'first_surname'                   => $afiliado['primer_apellido'],
                        'second_surname'                  => $afiliado['segundo_apellido'],
                        'birthday_date'                   => $afiliado['fecha_nacimiento'],
                        'gender'                          => $genders[$gender_code],
                        'gender_code'                     => $afiliado['genero'],
                        'affiliated_type_document'        => $document_types[$affiliated_type_document],
                        'affiliated_document'             => $trabajador['dcto_beneficiario'],
                        'affiliated_name'                 => $affiiliate_name,
                        'icm_types_income_id'             => $icm_types_income,
                        'icm_affiliate_category_id'       => $icm_affiliate_category,
                        'icm_affiliate_category_code'     => $trabajador['categoria'],
                        'nit_company_affiliates'          => $trabajador['nit_empresa'],
                        'name_company_affiliates'         => $trabajador['razon_social'],
                        'icm_family_compensation_fund_id' => null,
                        'icm_agreement_id'                => null,
                        'number_years'                    => $edad,
                        'political_date'                  => $this->icm_coverage->coverage_date,
                        'user_created'                    => $user_id,
                        'icm_income_item_id'              => $affiliate->icm_income_item_id
                    ];

                    # Actualizar registro persona ingresada
                    if($affiliate->document_number == $afiliado['dcto_beneficiario'] && $affiliate->is_processed_affiliate == 0){
                        $affiliate->is_processed_affiliate     = 1;
                        $affiliate->type_register              = $afiliado['tipo_registro'];
                        $affiliate->relationship               = $afiliado['parentesco'];
                        $affiliate->type_link                  = $afiliado['tipo_vinculacion'];
                        $affiliate->affiliated_type_document   = $document_types[$affiliated_type_document];
                        $affiliate->affiliated_document_number = $trabajador['dcto_beneficiario'];
                        $affiliate->affiliated_name            = $affiiliate_name;
                        $affiliate->nit_company_affiliates     = $trabajador['nit_empresa'];
                        $affiliate->name_company_affiliates    = $trabajador['razon_social'];
                        $affiliate->icm_types_income_id        = $icm_types_income;  // Particular
                        $affiliate->icm_affiliate_category_id  = $icm_affiliate_category;  // Categoria D
                        $affiliate->step                       = $step_next['code'];  // Categoria D
                        $affiliate->update();
                    }

                }

                # Validar si el grupo del afiliado ya se encuentra almacenado
                if(isset($afiliado_grupo[$affiliate->icm_income_item_id][$trabajador['dcto_beneficiario']])){
                    continue;
                }

                # Borrar grupo del trabajador * servicio
                IcmPoliticalAffiliate::where([
                    'affiliated_document'    => $trabajador['dcto_beneficiario'],
                    'political_date'         => $this->icm_coverage->coverage_date,
                    'icm_income_item_id'     => $affiliate->icm_income_item_id
                ])->delete();

                # Crear politica de afiliacion
                foreach ($affiliates_group as $key => $affiliate_group) {

                    $exits = IcmPoliticalAffiliate::where([
                        'document_number'    => $affiliate_group['document_number'],
                        'political_date'     => $this->icm_coverage->coverage_date,
                        'icm_income_item_id' => $affiliate->icm_income_item_id
                    ])->first();

                    IcmPoliticalAffiliate::create($affiliate_group);

                }

                # Marcar como todo el grupo procesado
                $afiliado_grupo[$affiliate->icm_income_item_id][$trabajador['dcto_beneficiario']] = true;


            }else{
                $affiliate->is_processed_affiliate     = 1;
                $affiliate->icm_types_income_id        = $types_of_income['PAR'];  // Particular
                $affiliate->icm_affiliate_category_id  = $categories['D'];         // Categoria D
                $affiliate->step                       = $step_next['code'];       // Categoria D
                $affiliate->update();
            };

        }

    }


    public function getLastProcess($icm_coverage_id){
        $step = IcmLiquidacionDetailRevision::where(['icm_coverage_id' => $icm_coverage_id])->max('step');
        return !$step ? $this->steps[0] : $this->steps[$step];
    }


    public function generatesNewProcess($date){

        $user_id = Auth::check() ? auth()->user()->id : 1;

        return IcmCoverage::create([
            'coverage_date' => $date,
            'user_created'  => $user_id
        ]);

    }

    public function startProcess(){

        # Suprimir data detail
        $this->resetLiquidationDetails($this->icm_coverage->coverage_date);

        # Leer informacion para procesamiento
        $liquidation_details = $this->getLiquidationDetails($this->icm_coverage->coverage_date);

        # Usuario procesa
        $user_id = auth()->check() ? auth()->user()->id : 1;

        // Comenzar la transacción
        DB::beginTransaction();

        # Asignar paso siguiente
        $step_next = $this->getStepNext($this->step);

        try {

            foreach ($liquidation_details as $key => $detail) {
                $data = (Array) $detail;
                $data['icm_coverage_id'] = $this->icm_coverage->id;
                $data['user_created']    = $user_id;
                $data['step']            = $step_next['code'];

                IcmLiquidacionDetailRevision::create((Array) $data);
            }

            // Commit de la transacción si todo ha ido bien
            DB::commit();

        } catch (\Exception $e) {
            // En caso de error, revertir la transacción
            DB::rollback();
            throw new \Exception($e->getMessage(), 1);

        }

    }

    public function getStepNext($step){
        $steps     = array_column($this->steps, 'code');
        $index     = array_search($step['code'], $steps);
        $index_new = $index+1;
        return isset($this->steps[$index_new]) ? $this->steps[$index_new] : false;
    }


    public function resetLiquidationDetails($liquidation_date){
        return IcmLiquidacionDetailRevision::where(['liquidation_date' => $liquidation_date])->delete();
    }

    public function getLiquidationDetails($liquidation_date){

        # Consultar datos coberturas día
        $querySQL = "SELECT
            ils.id AS icm_liquidation_service_id,
            ild.id AS icm_liquidation_detail_id,
            ild.is_processed_affiliate,
            ild.type_register,
            ild.relationship,
            ild.type_link,
            ild.affiliated_type_document,  /* PENDIENTE DE ALMACENAR */
            ild.affiliated_document AS affiliated_document_number,
            ild.affiliated_name,
            ild.document_type,
            ild.document_number,
            ild.first_name,
            ild.second_name,
            ild.first_surname,
            ild.second_surname,
            '' AS business_name,
            ic.birthday_date,
            ic.gender,
            ic.address,
            ic.icm_municipality_id,
            ic.phone,
            ic.email,
            iii.code_seac,
            iii.id AS icm_income_item_id,
            iii.code AS icm_income_item_code,
            isc.infrastructure_code AS infrastructure_code,
            il.liquidation_date,
            ild.nit_company_affiliates,
            ild.name_company_affiliates,
            ild.icm_types_income_id,
            ild.icm_affiliate_category_id,
            ild.category_presented_code,
            ils.total,
            ils.icm_type_subsidy_id,
            ils.subsidy,
            iii.number_places,
            ild.icm_family_compensation_fund_id,
            isc.system_names AS system_names,
            il.id AS icm_liquidation_id,
            il.billing_prefix,
            il.consecutive_billing
        FROM icm_liquidations AS il
        INNER JOIN icm_system_configurations AS isc ON 1 = 1
        INNER JOIN `icm_liquidation_services` AS ils ON ils.icm_liquidation_id = il.id
        INNER JOIN icm_income_items AS iii ON iii.id = ils.icm_income_item_id
        LEFT JOIN icm_type_subsidies AS its ON its.id = ils.icm_type_subsidy_id
        INNER JOIN `icm_liquidation_details` AS ild ON ild.icm_liquidation_service_id = ils.id
        INNER JOIN icm_customers AS ic ON ic.document_number = ild.document_number
        INNER JOIN icm_types_incomes AS iti ON iti.id = ild.icm_types_income_id
        INNER JOIN icm_affiliate_categories AS iac ON iac.id = ild.icm_affiliate_category_id
        LEFT JOIN icm_family_compensation_funds AS ifcf ON ifcf.id = ild.icm_family_compensation_fund_id
        WHERE
            il.liquidation_date = ?
            AND il.state = 'F'
            AND ils.is_deleted = 0
            AND ild.is_deleted = 0";

        return DB::select($querySQL, [$liquidation_date]);

    }


}
