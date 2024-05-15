<?php

namespace App\Http\Controllers\income;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;
use App\Models\Income\IcmAffiliateCategory;
use App\Models\Income\IcmFamilyCompensationFund;
use App\Models\Income\IcmCompaniesAgreement;
use App\Models\Income\IcmIncomeItem;
use App\Models\Income\IcmLiquidationService;
use App\Models\Income\IcmLiquidationDetail;

use App\Models\Income\IcmCustomer;
use App\Models\Income\IcmTypesIncome;
use App\Models\Income\IcmLiquidation;

use App\Clases\Cajasan\Afiliacion;
use App\Clases\Cajasan\Compute;



class BillingIncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user = auth()->user();

        # Ambientes ingreso.
        $icm_environments = $user->icm_environments()->get();

        # Tipos documento identificación
        $identification_document_types = getDetailDefinitions('identification_document_types');

        # Generos
        $genders = getDetailDefinitions('gender');

        # Tipos de ingreso
        $types_of_income = IcmTypesIncome::where(['state' => 'A'])->pluck('name', 'id');

        # Cajas de compensación
        $icm_family_compensation_funds = IcmFamilyCompensationFund::where(['state' => 'A'])->get()->pluck('name', 'id');

        # Categoria
        $icm_affiliate_categories = IcmAffiliateCategory::where(['state' => 'A'])->get()->pluck('name', 'id');

        return view('income.billing-incomes.index', compact('icm_environments', 'identification_document_types', 'types_of_income', 'icm_affiliate_categories', 'icm_family_compensation_funds', 'genders'));

    }

    public function searchClientDocument($document_number){

        # Consultar cliente
        $customer = IcmCustomer::where(['document_number' => $document_number])->first();
        $client   = $customer ? $customer->toArray() : [];
        $grupo_afiliado = null;

        # Consultar cliente CAJASAN;
        $afiliacion = new Afiliacion();
        $response   = $afiliacion->consultarCategoria($document_number);

        $affiliate_group = [];
        if($response['success'] && $response['data']['CODIGO'] == 1){
            $grupo_afiliado = $response['data']['DATOS'];

            $trabajador = [];
            foreach ($grupo_afiliado as $key => $afiliado) {
                if($afiliado['tipo_registro'] == 'TR')
                    $trabajador = $afiliado;
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

            $icm_types_income       =  IcmTypesIncome::where(['code' => 'AFI'])->first();
            $icm_affiliate_category =  IcmAffiliateCategory::where(['code' => $trabajador['categoria']])->first();
            $genders                =  getDetailHomologationDefinitions('gender');
            $document_types         =  getDetailHomologationDefinitions('identification_document_types');

            // OJOOOOOOOOOOOOOOOO  SOLICITAR TIPO DE DOCUMENTO QUE ENTREGA EL SERVICIO

            foreach ($grupo_afiliado as $key => $afiliado) {

                $gender_code        = homologacionDatosAfiliado('genero', $afiliado['genero']);
                $document_type_code = homologacionDatosAfiliado('tipo_dcto_beneficiario', $afiliado['tipo_dcto_beneficiario']);
                $edad               = calcularEdad($afiliado['fecha_nacimiento']);

                $affiliate_group[] = [
                    'document_number'                 => $afiliado['dcto_beneficiario'],
                    'document_type'                   => $document_types[$document_type_code],
                    'first_name'                      => $afiliado['primer_nombre'],
                    'second_name'                     => isset($afiliado['segundo_nombre']) ? $afiliado['segundo_nombre'] : '',
                    'first_surname'                   => $afiliado['primer_apellido'],
                    'second_surname'                  => $afiliado['segundo_apellido'],
                    'birth_date'                      => $afiliado['fecha_nacimiento'],
                    'gender'                          => $genders[$gender_code],
                    'gender_code'                     => $afiliado['genero'],
                    'affiliated_document'             => $trabajador['dcto_beneficiario'],
                    'affiliated_name'                 => implode(' ', $nombre_trabajador),
                    'icm_types_income_id'             => $icm_types_income->id,
                    'icm_affiliate_category_id'       => $icm_affiliate_category->id,
                    'icm_affiliate_category_code'     => $icm_affiliate_category->code,
                    'nit_company_affiliates'          => $trabajador['nit_empresa'],
                    'name_company_affiliates'         => $trabajador['razon_social'],
                    'icm_family_compensation_fund_id' => null,
                    'icm_agreement_id'                => null,
                    'number_years'                    => $edad
                ];
            }
        };

        return response()->json([
            'success'         => true,
            'message'         => '',
            'client'          => $client,
            'grupo_afilaido'  => $affiliate_group,
            'document_number' => $document_number,
            'control_service' => $response['success'],
            'error_service'   => !$response['success'] ? $response['message'] : ''
        ]);

    }

    public function billingIncomesCategory($icm_types_income_id){
        $type_income = IcmTypesIncome::find($icm_types_income_id);
        $categories = IcmAffiliateCategory::where(['code' => 'D'])->pluck('name', 'id');
        if($type_income){
            $categories = $type_income->icm_affiliate_categories()->pluck('icm_affiliate_categories.name', 'icm_affiliate_categories.id');
        }
        return response()->json($categories);
    }

    public function billingCompanyAgreement($icm_companies_agreement_id){
        $company_agreement = IcmCompaniesAgreement::find($icm_companies_agreement_id);
        $date_system = date_system();
        $icm_agreements = $company_agreement->icm_agreements()->orderBy('id', 'asc')->whereRaw("{$date_system} Between date_from and date_to")->get()->pluck('name', 'id');
        return response()->json($icm_agreements);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        # Validar infomración
        $validator = Validator::make(
            $request->all(),
            [
                'icm_income_item_id' => 'required',
                'clients'            => 'required'
            ],
            [
                'icm_income_item_id.required' => 'Servicio de ingreso requerido',
                'clients'                     => 'Clientes requeridos'
            ]
        );

        if ($validator->fails()) {
            $errors = array_values($validator->errors()->toArray());
            $expection = '';
            foreach ($errors as $key => $error) {
                $separador = empty($expection) ? '':', ';
                $expection .= $separador.implode(', ', $error);
            }
            throw new \Exception($expection, 1);
        }

        # Procesar solicitud
        $icm_liquidation_id = $request->icm_liquidation_id;
        $icm_income_item_id = $request->icm_income_item_id;
        $clients = $request->clients;
        $user_id = auth()->user()->id;

        $icmliquidation = IcmLiquidation::find($icm_liquidation_id);
        if(empty($icmliquidation)){
            $client_liquidation = $clients[0];
            $icmliquidation = IcmLiquidation::create([
                'document_type'   => $client_liquidation['document_number'],
                'document_number' => $client_liquidation['document_type'],
                'first_name'      => $client_liquidation['first_name'],
                'second_name'     => $client_liquidation['second_name'],
                'first_surname'   => $client_liquidation['first_surname'],
                'second_surname'  => $client_liquidation['second_surname'],
                'birthday_date'   => $client_liquidation['birth_date'],
                'gender'          => $client_liquidation['gender'],
                'total'           => 0,
                'state'           => 'P',
                'user_created'    => $user_id
            ]);
        }

        # Registrar servicios
        $income_item = IcmIncomeItem::find($icm_income_item_id);
        foreach ($clients as $key => $client) {

            # Servicio liquidado para ingreso de varias persona
            $cantidad_disponible = 0;
            $liquidationservices = IcmLiquidationService::where([
                'icm_liquidation_id'             => $icmliquidation->id,
                'icm_income_item_id' => $income_item->id
            ])->get();

            foreach ($liquidationservices as $key => $service) {
                $details = IcmLiquidationDetail::where(['icm_liquidation_service_id' => $service->id])->get();
                $cantidad_disponible = $income_item->number_places - $details->count();
                if($cantidad_disponible > 0){
                    $liquidationservice = $service;
                    break;
                }
            }

            # Si el servicio no tiene disponible cupos
            if($cantidad_disponible == 0){

                # Calcular valor sercio
                $system_date   = getSystemDate();
                $valorservicio = Compute::calcularValorServicio($income_item, (object) $client, $system_date);



                # Calcular impuesto producto
                $icm_menu_items = $income_item->icm_environment_icm_menu_item->icm_menu_item;
                $discriminated_value = Compute::calculateTaxes($icm_menu_items, $valorservicio[0]['value']);

                $liquidationservice  = IcmLiquidationService::create([
                    'icm_liquidation_id'               => $icmliquidation->id,
                    'icm_income_item_id'   => $income_item->id,
                    'icm_environment_id'               => 1,  // PENDIENTE
                    'icm_environment_icm_menu_item_id' => $income_item->icm_environment_icm_menu_item_id,
                    'number_places'                    => $income_item->number_places,
                    'applied_rate_code'                => $valorservicio[0]['code'],
                    'base'                             => $discriminated_value->base,
                    'percentage_iva'                   => $icm_menu_items->percentage_iva,
                    'iva'                              => $discriminated_value->iva,
                    'percentage_impoconsumo'           => $icm_menu_items->percentage_impoconsumo,
                    'impoconsumo'                      => $discriminated_value->impoconsumo,
                    'total'                            => $valorservicio[0]['value'],
                    'user_created'                     => $user_id,
                    'general_price'                    => $income_item->value
                ]);

            }

            $icm_affiliate_category = IcmAffiliateCategory::find($client['icm_affiliate_category_id']);
            # Registrar cliente
            $liquidationdetail  = IcmLiquidationDetail::create([
                'icm_liquidation_service_id'      => $liquidationservice->id,
                'document_type'                   => $client['document_type'],
                'document_number'                 => $client['document_number'],
                'first_name'                      => $client['first_name'],
                'second_name'                     => $client['second_name'],
                'first_surname'                   => $client['first_surname'],
                'second_surname'                  => $client['second_surname'],
                'icm_types_income_id'             => $client['icm_types_income_id'],
                'icm_affiliate_category_id'       => $client['icm_affiliate_category_id'],
                'category_presented_code'         => $icm_affiliate_category->code,
                'icm_family_compensation_fund_id' => $client['icm_family_compensation_fund_id'],
                'nit_company_affiliates'          => isset($client['nit_company_affiliates']) ? $client['nit_company_affiliates'] : NULL,
                'name_company_affiliates'         => isset($client['name_company_affiliates']) ? $client['name_company_affiliates'] : NULL,
                'nit_company_agreement'           => isset($client['nit_company_agreement']) ? $client['nit_company_agreement'] : NULL,
                'name_company_agreement'          => isset($client['name_company_agreement']) ? $client['name_company_agreement'] : NULL,
                'icm_agreement_id'                => isset($client['icm_agreement_id']) ? $client['icm_agreement_id'] : 0,
                'icm_liquidation_id'              => $icmliquidation->id,
                'state'                           => 'A',
                'user_created'                    => $user_id,
            ]);


        }

        # Actualizar totales liquidacion
        $querySQL = "UPDATE icm_liquidations
        INNER JOIN (
            SELECT il.id, SUM(ils.base) AS base, SUM(ils.iva) AS iva, SUM(ils.impoconsumo) AS impoconsumo, SUM(ils.total) AS total  FROM `icm_liquidations` AS il
            INNER JOIN `icm_liquidation_services` AS ils ON ils.icm_liquidation_id = il.id
            WHERE il.id = ?
            GROUP BY il.id
        ) AS subconsulta ON subconsulta.id = icm_liquidations.id
        SET
            icm_liquidations.base        = subconsulta.base,
            icm_liquidations.iva         = subconsulta.iva,
            icm_liquidations.impoconsumo = subconsulta.impoconsumo,
            icm_liquidations.total       = subconsulta.total ";

        \DB::select($querySQL, [$icmliquidation->id]);

        return response()->json([
            'success' => true,
            'message' => '',
            'data'    => $icmliquidation
        ]);

    }

    public function getBillingPeopleServices($icm_liquidation_id){

        $services = IcmLiquidation::find($icm_liquidation_id)->icm_liquidation_services()->where(['state' => 'A'])->get();

        foreach ($services as $key => $service) {
            # Personas vinculadas con el servicio
            $service['people'] = IcmLiquidationService::getPeopleService($service);
        }

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $services
        ]);

    }

    public function viewLiquidationTotals($icm_liquidation_id){

        $icm_liquidation = IcmLiquidation::find($icm_liquidation_id);

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $icm_liquidation
        ]);
    }

    public function billingIncomesDetails($icm_liquidation_id){
        $liquidation_services = IcmLiquidation::getDetailsServices($icm_liquidation_id);
        return response()->json([
            'success' => true,
            'message' => '',
            'data'    => $liquidation_services
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
