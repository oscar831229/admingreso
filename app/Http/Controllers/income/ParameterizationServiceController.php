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
use App\Models\Income\IcmEnvironmentIncomeItem;
use App\Models\Income\IcmAffiliateCategory;
use App\Models\Income\IcmEnvironmentIncomeItemDetail;
use App\Models\Income\IcmEnvironment;
use App\Models\Income\IcmRateType;

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
        $icm_environments = $user->icm_environments()->get();

        # Sincronizar sistema POS REST
        // synchronizePOSSystem();

        $types_of_income   = getDetailDefinitions('types_of_income');
        $affiliatecategories = IcmAffiliateCategory::where(['state' => 'A'])->get();

        $income_rates  = [];
        foreach ($types_of_income as $key => $value) {
            $rate['type_income_id']   = $key;
            $rate['type_income_name'] = $value;
            $rate['categories'] = [];
            foreach ($affiliatecategories as $key => $affiliatecategory) {
                $rate['categories'][] = $affiliatecategory;
            }
            $income_rates[] = $rate;
            $rate = [];
        }
        $rate_types = IcmRateType::where(['state' => 'A'])->get();
        return view('income.parameterization-services.index', compact('icm_environments', 'income_rates', 'affiliatecategories', 'rate_types'));

    }

    public function datatableParameterizationServices(Request $request){

        $icm_environment_id = 0;
        $request = request();
        if($request->has('icm_environment_id')){
            $icm_environment_id = $request->icm_environment_id;
        }

        $extradata['icm_environment_id'] = $icm_environment_id;

        $param = array(
            'model'=> new IcmEnvironmentIncomeItem,
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
        $incomeitem = IcmEnvironmentIncomeItem::find($id);
        $data = $request->all();
        $data['value'] = str_replace(",", "", $data['value']);
        if(!$incomeitem){
            $data['user_created'] = auth()->user()->id;
            $incomeitem = IcmEnvironmentIncomeItem::create($data);
        }else{
            $data['user_updated'] = auth()->user()->id;
            $incomeitem->update($data);
        }

        # REGISTRO DE TARIAS
        $user_id = auth()->user()->id;
        $income_rate_details = $incomeitem->icm_environment_income_item_details()->get()->pluck('value', 'id')->toArray();

        if($request->has('income_rates')){

            foreach ($request->income_rates as $key => $income_rate) {

                $icmrate = IcmEnvironmentIncomeItemDetail::where([
                    'icm_environment_income_item_id' => $incomeitem->id,
                    'types_of_income_id' => $income_rate['types_of_income_id'],
                    'icm_affiliate_category_id' => $income_rate['icm_affiliate_category_id'],
                    'icm_rate_type_id' => $income_rate['icm_rate_type_id']
                ])
                ->first();

                # Valor ingresos
                $income_rate['value'] = str_replace(",", "", $income_rate['value']);

                if($icmrate){
                    unset($income_rate_details[$icmrate->id]);
                    $icmrate->update([
                        'value' => $income_rate['value'],
                        'user_updated' => $user_id,
                        'state' => 'A'
                    ]);
                }else{
                    IcmEnvironmentIncomeItemDetail::create([
                        'icm_environment_income_item_id' => $incomeitem->id,
                        'types_of_income_id' => $income_rate['types_of_income_id'],
                        'icm_affiliate_category_id' => $income_rate['icm_affiliate_category_id'],
                        'icm_rate_type_id' => $income_rate['icm_rate_type_id'],
                        'value' => $income_rate['value'],
                        'user_created' => $user_id
                    ]);
                }
            }

        }

        # Inactivar tarifas por no información
        if(count($income_rate_details) > 0){
            IcmEnvironmentIncomeItemDetail::whereIn('id', array_keys($income_rate_details))->update([
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

        $incomeitem       = IcmEnvironmentIncomeItem::find($id);
        $incomeitemdetail = $incomeitem->icm_environment_income_item_details()->where(['state' => 'A'])->get();

        return response()->json([
            'success' => true,
            'message' => '',
            'data'    => $incomeitem,
            'income_item_detail' => $incomeitemdetail
        ]);
    }

    public function getEnvironmentIncomeServices($environment_id){

        $incomeservices = IcmEnvironment::getIncomeServices($environment_id);

        $rate_types = IcmRateType::where(['state' => 'A'])->orderBy('name', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => '',
            'data'    => [
                'incomeservices' => $incomeservices,
                'rate_types'     => $rate_types
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
