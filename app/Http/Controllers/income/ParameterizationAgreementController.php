<?php

namespace App\Http\Controllers\income;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Income\IcmAffiliateCategory;
use App\Models\Income\IcmAgreement;
use App\Models\Income\IcmAgreementDetail;
use App\Models\Income\IcmCompaniesAgreement;
use App\Models\Income\IcmRateType;

use App\Clases\DataTable\TableServer;

class ParameterizationAgreementController extends Controller
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
        $types_of_income   = getDetailDefinitions('types_of_income');
        $affiliatecategories = IcmAffiliateCategory::where(['state' => 'A'])->get();

        $income_rates = [];
        foreach ($types_of_income as $key => $value) {
            $rate['type_income_id']   = $key;
            $rate['type_income_name'] = $value;
            foreach ($affiliatecategories as $key => $affiliatecategory) {
                $rate['categories'][] = $affiliatecategory;
            }
            $income_rates[] = $rate;
            $rate = [];
        }

        return view('income.parameterization-agreements.index', compact('icm_environments', 'affiliatecategories', 'income_rates'));

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

    public function datatableParameterizationAgreements(Request $request){

        $icm_environment_id = 0;
        $request = request();
        if($request->has('icm_environment_id')){
            $icm_environment_id = $request->icm_environment_id;
        }

        $extradata['icm_environment_id'] = $icm_environment_id;

        $param = array(
            'model'=> new IcmAgreement,
            'method_consulta'=>'getDataTable',
            'method_cantidad'=>'getCountDatatable',
            'extradata' => $extradata
        );

        $tableserver = new TableServer($param);
        $datos = $tableserver->getDatos();

        return response()->json($datos);

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
        $agreement = IcmAgreement::find($id);
        $data = $request->all();
        if(!$agreement){

            $codeexist = IcmAgreement::where(['code' => $data['code']])->first();
            if($codeexist){
                return response()->json([
                    'succes'  => false,
                    'message' => 'Ya existe convenio con el mismo código ('.$data['code'].')',
                    'data'    => []
                ]);
            }

            $data['user_created'] = auth()->user()->id;
            $agreement = IcmAgreement::create($data);
        }else{
            $data['user_updated'] = auth()->user()->id;
            $agreement->update($data);
        }

        # REGISTRO DE TARIAS
        $user_id = auth()->user()->id;
        $agreement_details = $agreement->icm_agreement_details()->get()->pluck('value', 'id')->toArray();

        if($request->has('income_rates')){
            foreach ($request->income_rates as $key => $income_rate) {

                $icmrate = IcmAgreementDetail::where([
                    'icm_agreement_id'               => $agreement->id,
                    'icm_environment_income_item_id' => $income_rate['icm_environment_income_item_id'],
                    'icm_rate_type_id'               => $income_rate['icm_rate_type_id']
                ])
                ->first();

                # Valor ingresos
                $income_rate['value'] = str_replace(",", "", $income_rate['value']);

                if($icmrate){
                    unset($agreement_details[$icmrate->id]);
                    $icmrate->update([
                        'value'        => $income_rate['value'],
                        'user_updated' => $user_id,
                        'state'        => 'A'
                    ]);
                }else{

                    IcmAgreementDetail::create([
                        'icm_agreement_id'               => $agreement->id,
                        'icm_environment_income_item_id' => $income_rate['icm_environment_income_item_id'],
                        'icm_rate_type_id'               => $income_rate['icm_rate_type_id'],
                        'value'                          => $income_rate['value'],
                        'user_created'                   => $user_id
                    ]);
                }
            }

        }

        # Inactivar tarifas por no información
        if(count($agreement_details) > 0){
            IcmAgreementDetail::whereIn('id', array_keys($agreement_details))->update([
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
        $agreement = IcmAgreement::find($id);
        $company   = IcmCompaniesAgreement::find($agreement['icm_companies_agreement_id']);
        $agreement['icm_companies_agreement_name'] = $company->name;

        $agreementdetail = $agreement->icm_agreement_details()->where(['state' => 'A'])->get();

        return response()->json([
            'success' => true,
            'message' => '',
            'data'    => $agreement->toArray(),
            'income_item_detail' => $agreementdetail
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
