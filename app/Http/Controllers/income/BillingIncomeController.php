<?php

namespace App\Http\Controllers\income;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;
use App\Models\Income\IcmAffiliateCategory;
use App\Models\Income\IcmFamilyCompensationFund;
use App\Models\Income\IcmCompaniesAgreement;
use App\Models\Income\IcmIncomeItem;
use App\Models\Income\IcmEnvirontmentIcmIncomeItem;
use App\Models\Income\IcmEnvironmentIcmMenuItem;
use App\Models\Income\IcmLiquidationPayment;


use App\Models\Income\IcmLiquidationService;
use App\Models\Income\IcmLiquidationDetail;

use App\Models\Income\IcmCustomer;
use App\Models\Income\IcmTypesIncome;
use App\Models\Income\IcmLiquidation;
use App\Models\Income\IcmPaymentMethod;
use App\Models\Income\CommonCity;

use App\Models\Common\DetailDefinition;

use App\Clases\Cajasan\Afiliacion;
use App\Clases\Cajasan\Compute;

use Illuminate\Support\Facades\DB;
use App\Services\AmadeusPosApiService;
use Ramsey\Uuid\Uuid;



class BillingIncomeController extends Controller
{


    protected $AmadeusPosApiService;

    public function __construct(AmadeusPosApiService $AmadeusPosApiService)
    {
        $this->AmadeusPosApiService = $AmadeusPosApiService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user = auth()->user();

        # Ambiente usuario default - Facturacion ingresos
        $icm_environment                        = $user->icm_environments()->first();
        $icm_environments[$icm_environment->id] = $icm_environment;

        # Facturación de ingresos otras sedes habilitado
        $other_services = IcmEnvirontmentIcmIncomeItem::where(['icm_environment_id' => $icm_environment->id, 'state' => 'A'])->get();
        foreach ($other_services as $key => $other_services) {
            $icm_income_item = $other_services->icm_income_item;
            if(!isset($icm_environments[$icm_income_item->icm_environment_id])){
                $icm_environments[$icm_income_item->icm_environment_id] = $icm_income_item->icm_environment;
            }
        }

        # Tipos documento identificación
        $identification_document_types = getDetailDefinitions('identification_document_types');

        # Tipos persona
        $types_person = ['N' => 'Natural', 'J' => 'Jurídica'];

        # Regiment fiscal
        $tax_regime   = ['49' => 'No responsables del IVA', '48' => 'Impuestos sobre la venta del IVA'];

        # Generos
        $genders = getDetailDefinitions('gender');

        # Ciudades DIAN.
        $common_cities = CommonCity::orderBy('city_name')->get()->pluck('city_name', 'id');

        # Formas de pago
        $icmpaymentmethod = IcmPaymentMethod::where(['state' => 'A'])->orderBY('type_payment_method')->orderBY('name')->get();
        $icmpaymentmethod = preparedMethoPayment($icmpaymentmethod);

        # Tipos de ingreso
        $types_of_income = IcmTypesIncome::where(['state' => 'A'])->pluck('name', 'id');

        # Cajas de compensación
        $icm_family_compensation_funds = IcmFamilyCompensationFund::where(['state' => 'A'])->get()->pluck('name', 'id');

        # Categoria
        $icm_affiliate_categories = IcmAffiliateCategory::where(['state' => 'A'])->get()->pluck('name', 'id');

        $auth_pos_amadeus = 0;
        $user_id          = auth()->user()->id;

        if (\Session::has('auth_amadeus_pos'.$user_id)){
            $auth_pos_amadeus = 1;
        }

        return view('income.billing-incomes.index', compact('icm_environments', 'identification_document_types', 'types_of_income', 'icm_affiliate_categories', 'icm_family_compensation_funds', 'genders', 'types_person', 'tax_regime', 'icmpaymentmethod', 'common_cities', 'auth_pos_amadeus'));

    }

    public function searchClientDocument($document_number){

        # Consultar cliente
        $customer = IcmCustomer::where(['document_number' => $document_number])->first();
        $client   = $customer ? $customer->toArray() : false;

        $request         = request();
        $grupo_afiliado  = null;
        $affiliate_group = [];

        $response_master = [
            'success' => true,
            'message' => '',
            'client'  => $client
        ];

        if($request->has('notcategory')){
            return response()->json($response_master);
        }

        # Consultar afiliados CAJASAN
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
                    'birthday_date'                   => $afiliado['fecha_nacimiento'],
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

        $response_master['grupo_afilaido']   = $affiliate_group;
        $response_master['document_number']  = $document_number;
        $response_master['control_service' ] = $response['success'];
        $response_master['error_service']    = !$response['success'] ? $response['message'] : '';

        return response()->json($response_master);

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
        $clients            = $request->clients;
        $user               = auth()->user();
        $icm_environment    = $user->icm_environments()->first();
        $user_id            = $user->id;

        $income_item    = IcmIncomeItem::find($icm_income_item_id);
        $icmliquidation = IcmLiquidation::find($icm_liquidation_id);
        if(empty($icmliquidation)){

            $client_liquidation = $clients[0];

            # UUID identificación unica para facturación.
            $uuid = Uuid::uuid4()->toString();
            $liquidation_date = getSystemDate();

            $icmliquidation = IcmLiquidation::create([
                'uuid'                     => $uuid,
                'sales_icm_environment_id' => $icm_environment->id,
                'icm_environment_id'       => $income_item->icm_environment_id,
                'document_type'            => $client_liquidation['document_type'],
                'document_number'          => $client_liquidation['document_number'],
                'first_name'               => $client_liquidation['first_name'],
                'second_name'              => $client_liquidation['second_name'],
                'first_surname'            => $client_liquidation['first_surname'],
                'second_surname'           => $client_liquidation['second_surname'],
                'birthday_date'            => $client_liquidation['birthday_date'],
                'gender'                   => $client_liquidation['gender'],
                'total'                    => 0,
                'liquidation_date'         => $liquidation_date,
                'state'                    => 'P',
                'user_created'             => $user_id
            ]);
        }

        # Registrar servicios
        $icm_affiliate_category_d = IcmAffiliateCategory::where(['code' => 'D'])->first();
        foreach ($clients as $key => $client) {

            $customer = IcmCustomer::where(['document_number' => $client['document_number']])->first();

            if(!$customer){

                # Tipo ingreso
                $icm_types_income       = IcmTypesIncome::find($client['icm_types_income_id']);

                # Asignar categoria si el ingreso es de afiliado
                $icm_affiliate_category_id = $icm_types_income->code == 'AFI' ? $client['icm_affiliate_category_id'] : $icm_affiliate_category_d->id;

                IcmCustomer::create([
                    'document_type'             => $client['document_type'],
                    'document_number'           => $client['document_number'],
                    'first_name'                => $client['first_name'],
                    'second_name'               => $client['second_name'],
                    'first_surname'             => $client['first_surname'],
                    'second_surname'            => $client['second_surname'],
                    'birthday_date'             => $client['birthday_date'],
                    'gender'                    => $client['gender'],
                    'icm_affiliate_category_id' => $client['icm_affiliate_category_id'],
                    'user_created'              => $user_id
                ]);
            } else {
                $customer->update([
                    'birthday_date' => $client['birthday_date'],
                    'gender'        => $client['gender'],
                    'user_updated'  => $user_id
                ]);
            }

            # Servicio liquidado para ingreso de varias persona
            $cantidad_disponible = 0;
            $liquidationservices = IcmLiquidationService::where([
                'icm_liquidation_id' => $icmliquidation->id,
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

                # Servicios autorizados para venta desde otros ambientes
                $icm_environtment_icm_income_item = IcmEnvirontmentIcmIncomeItem::where(['icm_income_item_id' => $income_item->id, 'icm_environment_id' => $icm_environment->id])->first();

                # Obtener servicio con el cual se liquida depende del ambiente de venta
                $icm_environment_icm_menu_item_id = $income_item->icm_environment_id != $icm_environment->id
                    ?
                    $icm_environtment_icm_income_item->icm_environment_icm_menu_item_id :
                    $income_item->icm_environment_icm_menu_item_id;

                # Menus items
                $icm_environment_icm_menu_item = IcmEnvironmentIcmMenuItem::find($icm_environment_icm_menu_item_id);
                $icm_menu_items                = $icm_environment_icm_menu_item->icm_menu_item;

                # Calcular impuesto producto
                $discriminated_value           = Compute::calculateTaxes($icm_menu_items, $valorservicio[0]['value']);

                # Identificar descuento - tipo de descuento
                $discount = $valorservicio[0]['alterno'] != 'AFI' ? $income_item->value - $valorservicio[0]['value'] : 0;

                # Identificar si el servicio aplica subsidio
                $icm_types_income    = IcmTypesIncome::find($client['icm_types_income_id']);
                $icm_type_subsidy_id = $valorservicio[0]['alterno'] == 'AFI' && $valorservicio[0]['subsidy'] > 0 ? 1 : 0;

                $liquidationservice  = IcmLiquidationService::create([
                    'icm_liquidation_id'               => $icmliquidation->id,
                    'icm_income_item_id'               => $income_item->id,
                    'icm_environment_id'               => $income_item->icm_environment_id,  // PENDIENTE
                    'icm_environment_icm_menu_item_id' => $icm_environment_icm_menu_item_id,
                    'number_places'                    => $income_item->number_places,
                    'icm_rate_type_id'                 => $valorservicio[0]['icm_rate_type_id'],
                    'applied_rate_code'                => $valorservicio[0]['code'],
                    'base'                             => $discriminated_value->base,
                    'percentage_iva'                   => $icm_menu_items->percentage_iva,
                    'iva'                              => $discriminated_value->iva,
                    'percentage_impoconsumo'           => $icm_menu_items->percentage_impoconsumo,
                    'impoconsumo'                      => $discriminated_value->impoconsumo,
                    'total'                            => $valorservicio[0]['value'],
                    'user_created'                     => $user_id,
                    'general_price'                    => $income_item->value,
                    'discount'                         => $discount,
                    'icm_type_subsidy_id'              => $icm_type_subsidy_id,
                    'subsidy'                          => $valorservicio[0]['subsidy']
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
            SELECT
                il.id,
                SUM(ils.base) AS base,
                SUM(ils.iva) AS iva,
                SUM(ils.impoconsumo) AS impoconsumo,
                SUM(ils.total) AS total,
                SUM(ils.subsidy) AS total_subsidy
            FROM `icm_liquidations` AS il
            INNER JOIN `icm_liquidation_services` AS ils ON ils.icm_liquidation_id = il.id
            WHERE il.id = ?
            GROUP BY il.id
        ) AS subconsulta ON subconsulta.id = icm_liquidations.id
        SET
            icm_liquidations.base          = subconsulta.base,
            icm_liquidations.iva           = subconsulta.iva,
            icm_liquidations.impoconsumo   = subconsulta.impoconsumo,
            icm_liquidations.total         = subconsulta.total,
            icm_liquidations.total_subsidy = subconsulta.total_subsidy";

        \DB::select($querySQL, [$icmliquidation->id]);

        return response()->json([
            'success' => true,
            'message' => '',
            'data'    => $icmliquidation
        ]);


    }

    public function billingIncomesPrint($icm_liquidation_id){

        $icm_liquidation = IcmLiquidation::find($icm_liquidation_id);
        $user_id = auth()->user()->id;
        $password = \Session::get('auth_amadeus_pos'.$user_id);

        // Llamar al servicio REST con los datos de la factura
        $this->AmadeusPosApiService->setHeader([
            'autorization'  => $password,
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json'
        ]);

        $response = $this->AmadeusPosApiService->imprimirFactura([
            "environment_id"      => $icm_liquidation->sales_icm_environment_id,
            "billing_prefix"      => $icm_liquidation->billing_prefix,
            "consecutive_billing" => $icm_liquidation->consecutive_billing,
	        "document_type"       => 'F',
        ]);


        if(!$response['success']){
            throw new \Exception($response['message'], 1);
        }

        $facturabase64 = $response['name'];
        $facturahtml   = base64_decode($facturabase64);

        echo $facturahtml;


    }

    public function payBillingIncomes(Request $request){

        DB::beginTransaction();

        $billing_prefix      = '';
        $consecutive_billing = '';

        try {

            $user_id            = auth()->user()->id;
            $icm_resolution_id  = $request->icm_resolution_id;
            $icm_liquidation_id = $request->icm_liquidation_id;
            $payment_methods    = $request->payment_methods;
            $password           = $request->password;

            # Liquidacion
            $icm_liquidation = IcmLiquidation::where(['id' => $icm_liquidation_id])->first();

            if(!$icm_liquidation){
                throw new Exception("No existe la liquidación a facturar.", 1);
            }

            # Marcar los pagos como anulados
            DB::table('icm_liquidation_payments')->where(['icm_liquidation_id' => $icm_liquidation_id])->update(['state' => 'B']);

            foreach ($payment_methods as $key => $payment_method) {
                IcmLiquidationPayment::create([
                    'icm_liquidation_id'    => $icm_liquidation_id,
                    'icm_payment_method_id' => $payment_method['payment_method'],
                    'approval_date'         => $payment_method['approval_date'],
                    'approval_number'       => $payment_method['approval_number'],
                    'value'                 => $payment_method['value'],
                    'user_created'          => $user_id
                ]);
            }


            # Consumo servicio REST
            if (!\Session::has('auth_amadeus_pos'.$user_id)){
                $password = AmadeusPosApiService::encrypt($password, '');
            }else{
                $password = \Session::get('auth_amadeus_pos'.$user_id);
            }


            // Llamar al servicio REST con los datos de la factura
            $this->AmadeusPosApiService->setHeader([
                'autorization'  => $password,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json'
            ]);

            $response = $this->AmadeusPosApiService->facturarLiquidacion(self::liquitacionToJson($icm_liquidation_id, $icm_resolution_id));
            if(!$response['success']){
                throw new \Exception($response['message'], 1);
            }

            \Session::put('auth_amadeus_pos'.$user_id, $password);

            $icm_liquidation->update([
                'state'               => 'F',
                'icm_resolution_id'   => $icm_resolution_id,
                'billing_prefix'      => $response['factura']['prefijo_facturacion'],
                'consecutive_billing' => $response['factura']['consecutivo_facturacion'],
                'user_updated'        => $user_id
            ]);

            $billing_prefix      = $response['factura']['prefijo_facturacion'];
            $consecutive_billing = $response['factura']['consecutivo_facturacion'];

            DB::commit();

        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'success' =>  false,
                'message' => $e->getMessage(),
                'data'    => []
            ]);

        }

        return response()->json([
            'success'             =>  true,
            'message'             => '',
            'data'                => [],
            'billing_prefix'      => $billing_prefix,
            'consecutive_billing' => $consecutive_billing
        ]);

    }

    public static function liquitacionToJson($icm_liquidation_id, $icm_resolution_id){

        $icm_liquidation = IcmLiquidation::find($icm_liquidation_id);

        $data = [
            'uuid'              => $icm_liquidation->uuid,
            'liquidation_id'    => $icm_liquidation->id,
            'environment_id'    => $icm_liquidation->sales_icm_environment_id,
            'resolution_id'     => $icm_resolution_id,
            'customer'          => [],
            'payment_methods'   => [],
            'liquidation_lines' => []
        ];

        # Cliente factura
        $icmcustomer   = $icm_liquidation->icm_customer;
        $document_type = DetailDefinition::find($icmcustomer->document_type);


        $data['customer'] = [
            'type_document_identification_id' => $document_type->code,
            'identification_number'           => $icmcustomer->document_number,
            'type_organization_id'            => 1,
            'first_name'                      => $icmcustomer->first_name,
            'second_name'                     => $icmcustomer->second_name,
            'first_surname'                   => $icmcustomer->first_surname,
            'second_surname'                  => $icmcustomer->second_surname,
            'phone'                           => $icmcustomer->phone,
            'email'                           => $icmcustomer->email,
            'municipality_id'                 => $icmcustomer->icm_municipality_id,
            'address'                         => $icmcustomer->address,
            'type_regime_id'                  => $icmcustomer->type_regime_id,
            'postal_code'                     => '680001',
            'birth_date'                      => $icmcustomer->birthday_date,
        ];

        # Metodos de Pago liquidación
        $payment_methods = $icm_liquidation->icm_liquidation_payments;
        foreach ($payment_methods as $key => $payment_method) {
            $data['payment_methods'][] = [
                "icm_payment_method_id" => $payment_method->icm_payment_method_id,
                "approval_date"         => $payment_method->approval_date,
                "approval_number"       => $payment_method->approval_number,
                "value"                 => $payment_method->value
            ];
        }

        # Servicio facturados
        $liquidation_lines = $icm_liquidation->icm_liquidation_services;
        foreach ($liquidation_lines as $key => $liquidation_line) {

            $discount = $liquidation_line->icm_type_subsidy_id == 0 ? 0 : $liquidation_line->discount;

            $icm_environment_icm_menu_item = IcmEnvironmentIcmMenuItem::find($liquidation_line->icm_environment_icm_menu_item_id);

            $data['liquidation_lines'][] = [
                "menus_items_id" => $icm_environment_icm_menu_item->icm_menu_item_id,
                "amount"         => "1",
                "value"          => $liquidation_line->base,
                "iva"            => $liquidation_line->iva,
                "impo"           => $liquidation_line->impoconsumo,
                "total"          => $liquidation_line->total,
                "subsidy"        => $liquidation_line->subsidy,
                "type_subsidy"   => $liquidation_line->icm_type_subsidy_id
            ];
        }

        return $data;

    }

    public function getBillingPeopleServices($icm_liquidation_id){

        if($icm_liquidation_id == 0){
            return response()->json([
                'success' => true,
                'message' => '',
                'data' => []
            ]);
        }


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

        if($icm_liquidation_id == 0){

            $base          = 0;
            $iva           = 0;
            $impoconsumo   = 0;
            $total         = 0;
            $total_subsidy = 0;

            return response()->json([
                'success' => true,
                'message' => '',
                'data' => [
                    'base'          => $base,
                    'iva'           => $iva,
                    'impoconsumo'   => $impoconsumo,
                    'total'         => $total,
                    'total_subsidy' => $total_subsidy,
                ]
            ]);
        }
        $icm_liquidation = IcmLiquidation::find($icm_liquidation_id);

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $icm_liquidation
        ]);

    }

    public function viewLiquidationPayment($icm_liquidation_id){

        $icm_liquidation = IcmLiquidation::find($icm_liquidation_id);
        $customer        = IcmCustomer::where(['document_number' => $icm_liquidation->document_number])->first();

        return response()->json([
            'success'         => true,
            'message'         => '',
            'icm_liquidation' => $icm_liquidation,
            'customer'        => $customer
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
        # Consultamos cliente
        $document_number = $request->document_number;
        $customer = IcmCustomer::where(['document_number' => $document_number])->first();
        $user_id  = auth()->user()->id;
        if(!$customer){
            $customer = IcmCustomer::create([
                'document_number'     => $request->document_number,
                'document_type'       => $request->document_type,
                'first_name'          => $request->first_name,
                'second_name'         => $request->second_name,
                'first_surname'       => $request->first_surname,
                'second_surname'      => $request->second_surname,
                'phone'               => $request->phone,
                'email'               => $request->email,
                'icm_municipality_id' => $request->icm_municipality_id,
                'address'             => $request->address,
                'type_regime_id'      => $request->type_regime_id,
                'user_created'        => $user_id
            ]);
        }else{
            $customer->update([
                'phone'               => $request->phone,
                'email'               => $request->email,
                'icm_municipality_id' => $request->icm_municipality_id,
                'address'             => $request->address,
                'type_regime_id'      => $request->type_regime_id,
                'user_update'         => $user_id
            ]);
        }

        $icm_liquidation = IcmLiquidation::find($id);
        $icm_liquidation->update([
            'document_type'       => $customer->document_type,
            'document_number'     => $customer->document_number,
            'first_name'          => $customer->first_name,
            'second_name'         => $customer->second_name,
            'first_surname'       => $customer->first_surname,
            'second_surname'      => $customer->second_surname,
            'icm_municipality_id' => $customer->icm_municipality_id,
            'address'             => $customer->address,
            'phone'               => $customer->phone,
            'type_regime_id'      => $customer->type_regime_id
        ]);

        return response()->json([
            'success' => true,
            'message' => '',
            'data'    => []
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        DB::beginTransaction();

        try {

            $request = request();
            $icm_liquidation_details  = IcmLiquidationDetail::where(['icm_liquidation_id' => $request->icm_liquidation_id, 'id' => $id ])->first();
            $icm_liquidation_services = IcmLiquidationService::find($icm_liquidation_details->icm_liquidation_service_id);

            $icm_liquidation_details->delete();
            $count = $icm_liquidation_services->icm_liquidation_details()->count();
            if($count == 0){
                $icm_liquidation_services->delete();
            }

            DB::commit();

            # Actualizar totales liquidacion
            $querySQL = "UPDATE icm_liquidations
            INNER JOIN (
                SELECT
                    il.id,
                    SUM(IFNULL(ils.base, 0)) AS base,
                    SUM(IFNULL(ils.iva, 0)) AS iva,
                    SUM(IFNULL(ils.impoconsumo, 0)) AS impoconsumo,
                    SUM(IFNULL(ils.total, 0)) AS total,
                    SUM(IFNULL(ils.subsidy, 0)) AS total_subsidy
                FROM `icm_liquidations` AS il
                LEFT JOIN `icm_liquidation_services` AS ils ON ils.icm_liquidation_id = il.id
                WHERE il.id = ?
                GROUP BY il.id
            ) AS subconsulta ON subconsulta.id = icm_liquidations.id
            SET
                icm_liquidations.base          = subconsulta.base,
                icm_liquidations.iva           = subconsulta.iva,
                icm_liquidations.impoconsumo   = subconsulta.impoconsumo,
                icm_liquidations.total         = subconsulta.total,
                icm_liquidations.total_subsidy = subconsulta.total_subsidy";

            \DB::select($querySQL, [$request->icm_liquidation_id]);

        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'success' =>  false,
                'message' => $e->getMessage(),
                'data'    => []
            ]);

        }

        return response()->json([
            'success'             =>  true,
            'message'             => '',
            'data'                => [],
        ]);

    }

}
