<?php

namespace App\Http\Controllers\income;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Amadeus\Menu;
use App\Models\Amadeus\MenuItem;
use App\Models\Amadeus\SalonMenuItem;

use App\Models\Income\IcmMenu;
use App\Models\Income\IcmMenuItem;
use App\Models\Income\IcmEnvironmentIcmMenuItem;
use App\Models\Income\IcmIncomeItem;
use App\Models\Income\IcmAffiliateCategory;
use App\Models\Income\IcmIncomeItemDetail;
use App\Models\Income\IcmEnvironment;
use App\Models\Income\IcmRateType;
use App\Models\Income\IcmTypeSubsidy;
use App\Models\Income\IcmTypesIncome;
use App\Models\Income\IcmEnvirontmentIcmIncomeItem;
use App\Models\Income\IcmResolution;

use App\Clases\DataTable\TableServer;

class ParameterizationServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $icm_environments = IcmEnvironment::all();

        # Sincronizar sistema POS REST
        // synchronizePOSSystem('all');

        $types_of_income     = IcmTypesIncome::where(['state' => 'A'])->get();
        $affiliatecategories = IcmAffiliateCategory::where(['state' => 'A'])->orderby('code', 'asc')->get();

        $income_rates  = [];
        foreach ($types_of_income as $key => $type_income) {
            $rate['type_income_id']   = $type_income->id;
            $rate['type_income_name'] = $type_income->name;
            $rate['categories'] = [];
            foreach ($affiliatecategories as $key => $affiliatecategory) {
                $control = $type_income->icm_affiliate_categories()->where(['icm_affiliate_category_id' => $affiliatecategory->id])->first();
                $rate['categories'][] = [
                    'control'           => $control ? true : false,
                    'affiliatecategory' => $affiliatecategory
                ];
            }
            $income_rates[] = $rate;
            $rate = [];
        }

        $rate_types = IcmRateType::where(['state' => 'A'])->get();
        $subsidies  = IcmTypeSubsidy::where(['state' => 'A'])->get()->pluck('name', 'id');

        return view('income.parameterization-services.index', compact('icm_environments', 'income_rates', 'affiliatecategories', 'rate_types', 'subsidies'));

    }

    public function datatableParameterizationServices(Request $request){

        $extradata = [];
        if($request->has('icm_environment_id')){
            $icm_environment_id              = $request->icm_environment_id;
            $extradata['icm_environment_id'] = $icm_environment_id;
        }

        $param = array(
            'model'=> new IcmIncomeItem,
            'method_consulta'=>'getDataTable',
            'method_cantidad'=>'getCountDatatable',
            'extradata' => $extradata
        );

        $tableserver = new TableServer($param);
        $datos = $tableserver->getDatos();

        return response()->json($datos);

    }

    public function getEnvironmentMenusItems($environment_id){
        $menus_items = IcmEnvironmentIcmMenuItem::getEnvironmentMenusItems($environment_id);
        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $menus_items
        ]);
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
        $id = empty($request->id) ? 0 : $request->id;
        $incomeitem = IcmIncomeItem::find($id);
        $data = $request->all();
        $data['value']      = str_replace(",", "", $data['value']);
        $data['value_high'] = str_replace(",", "", $data['value_high']);
        if(!$incomeitem){
            $data['user_created'] = auth()->user()->id;
            $incomeitem = IcmIncomeItem::create($data);
        }else{
            $data['user_updated'] = auth()->user()->id;
            $incomeitem->update($data);
        }

        # REGISTRO DE TARIAS
        $user_id = auth()->user()->id;
        $income_rate_details = $incomeitem->icm_income_item_details()->get()->pluck('value', 'id')->toArray();

        if($request->has('income_rates')){

            foreach ($request->income_rates as $key => $income_rate) {

                $icmrate = IcmIncomeItemDetail::where([
                    'icm_income_item_id' => $incomeitem->id,
                    'icm_types_income_id' => $income_rate['icm_types_income_id'],
                    'icm_affiliate_category_id' => $income_rate['icm_affiliate_category_id'],
                    'icm_rate_type_id' => $income_rate['icm_rate_type_id']
                ])
                ->first();

                # Valor ingresos
                $income_rate['value']   = str_replace(",", "", $income_rate['value']);
                $income_rate['subsidy'] = str_replace(",", "", $income_rate['subsidy']);
                $income_rate['subsidy'] = $income_rate['subsidy'] == '' ? 0 : $income_rate['subsidy'];

                if($icmrate){
                    unset($income_rate_details[$icmrate->id]);
                    $icmrate->update([
                        'value'        => $income_rate['value'],
                        'subsidy'      => $income_rate['subsidy'],
                        'user_updated' => $user_id,
                        'state'        => 'A'
                    ]);
                }else{
                    IcmIncomeItemDetail::create([
                        'icm_income_item_id'        => $incomeitem->id,
                        'icm_types_income_id'       => $income_rate['icm_types_income_id'],
                        'icm_affiliate_category_id' => $income_rate['icm_affiliate_category_id'],
                        'icm_rate_type_id'          => $income_rate['icm_rate_type_id'],
                        'value'                     => $income_rate['value'],
                        'subsidy'                   => $income_rate['subsidy'],
                        'user_created'              => $user_id
                    ]);
                }
            }

        }

        # Inactivar tarifas por no información
        if(count($income_rate_details) > 0){
            IcmIncomeItemDetail::whereIn('id', array_keys($income_rate_details))->update([
                'state' => 'I',
                'value' => 0
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => '',
            'data'  => []
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

        $incomeitem             = IcmIncomeItem::find($id);
        $incomeitemdetail       = $incomeitem->icm_income_item_details()->where(['state' => 'A'])->get();
        $request                = request();

        $environments_enabled   = [];
        if($request->has('enable_sale')){

            # Ambiente principal para venta
            $environments_enabled[] = [
                'maestro'                          => true,
                'icm_environment_id'               => $incomeitem->icm_environment_id,
                'icm_environment_name'             => $incomeitem->icm_environment->name,
                'icm_environment_icm_menu_item_id' => $incomeitem->icm_environment_icm_menu_item_id,
                'state'                            => 'A'
            ];

            $icm_environment_incomes = IcmEnvirontmentIcmIncomeItem::where('icm_income_item_id', '=', $incomeitem->id)->get();
            foreach ($icm_environment_incomes as $key => $icm_environment_income) {
                $environments_enabled[] = [
                    'maestro'                          => false,
                    'icm_environment_id'               => $icm_environment_income->icm_environment_id,
                    'icm_environment_name'             => $icm_environment_income->icm_environment->name,
                    'icm_environment_icm_menu_item_id' => $icm_environment_income->icm_environment_icm_menu_item_id,
                    'state'                            => $icm_environment_income->state
                ];
            }

        }

        return response()->json([
            'success'               => true,
            'message'               => '',
            'data'                  => $incomeitem,
            'income_item_detail'    => $incomeitemdetail,
            'environments_enabled'  => $environments_enabled
        ]);

    }

    public function getEnvironmentIncomeServices($environment_id){

        # Controlar servicios de ambiente o venta de otros ambientes
        /**
         * A all - todos los servicios disponibles por el ambiente
         * P allowed(Permitidos) los permitidos para venta de otros ambientes
         */
        $user = auth()->user();

        # Ambiente autorizado
        $icm_environment = $user->icm_environments()->first();

        # Servicios disponibles
        $incomeservices  = IcmEnvironment::getIncomeServices($environment_id, $icm_environment);

        # Temporada
        $rate_types = IcmRateType::where(['state' => 'A'])->orderBy('name', 'desc')->get();

        # Resolociones autorizadas.
        $resolutions = IcmResolution::where(['icm_environment_id' => $icm_environment->id, 'state' => 'A'])->get();

        return response()->json([
            'success' => true,
            'message' => '',
            'data'    => [
                'incomeservices'           => $incomeservices,
                'rate_types'               => $rate_types,
                'resolutions_environtment' => $resolutions
            ]
        ]);

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

        $icm_income_item     = IcmIncomeItem::find($id);
        $environment_enabled = $request->environment_enabled;
        $user_id             = auth()->user()->id;

        foreach ($environment_enabled as $key => $environment) {

            if($icm_income_item->icm_environment_id == $environment['environment_id']){
                continue;
            }

            $environment_income = IcmEnvirontmentIcmIncomeItem::where([
                'icm_environment_id' => $environment['environment_id'],
                'icm_income_item_id' => $icm_income_item->id
            ])->first();

            if(!$environment_income && !empty($environment['icm_environment_icm_menu_item_id'])){
                $environment_income = IcmEnvirontmentIcmIncomeItem::create([
                    'icm_environment_id'               => $environment['environment_id'],
                    'icm_income_item_id'               => $icm_income_item->id,
                    'icm_environment_icm_menu_item_id' => $environment['icm_environment_icm_menu_item_id'],
                    'user_created'                     => $user_id
                ]);
            }else if($environment_income){
                $state = $environment['enabled'] == 'true' ? 'A' : 'I';
                $environment_income->state        = $state;
                $environment_income->user_updated = $user_id;
                $environment_income->update();
            }


        }

        return response()->json([
            'success' => true,
            'message' => '',
            'data'    => []
        ]);


    }

    public function consecutiveCodes($code){

        $data = '';
        switch ($code) {

            case 'services':
                $prefix = 'SER';
                $amount = icmIncomeItem::count() + 1;
                $data   = $prefix.$amount;
                break;

            default:
                # code...
                break;
        }
        return response()->json([
            'success' => true,
            'message' => '',
            'data'    => $data
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
        //
    }
}
