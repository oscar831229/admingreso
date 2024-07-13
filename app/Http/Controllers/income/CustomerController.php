<?php

namespace App\Http\Controllers\income;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Clases\DataTable\TableServer;
use Illuminate\Support\Facades\Validator;
use App\Models\Income\IcmCustomer;
use App\Models\Income\CommonCity;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $identification_document_types = getDetailDefinitions('identification_document_types');
        $genders = getDetailDefinitions('gender');
        $common_cities = CommonCity::orderBy('city_name')->get()->pluck('city_name', 'id');
        $tax_regime   = ['49' => 'No responsables del IVA', '48' => 'Impuestos sobre la venta del IVA'];

        return view('income.customers.index', compact('identification_document_types', 'genders', 'common_cities', 'tax_regime'));
    }

    public function datatableCustomers(Request $request){

        $param = array(
            'model'=> new IcmCustomer,
            'method_consulta'=>'getDataTable',
            'method_cantidad'=>'getCountDatatable',
            'extradata' => []
        );

        $tableserver = new TableServer($param);
        $datos = $tableserver->getDatos();

        return response()->json($datos);

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
        $validator = Validator::make($request->all(), [
            'document_type'   => "required",
            'id'              => 'required',
            'first_name'      => 'required',
            'first_surname'   => 'required',
            'birthday_date'   => 'required',
            'gender'          => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->messages(),
                'data' => []
            ]);
        }

        $data = $request->all();
        $user = auth()->user();
        if(isset($data['id']) && !empty($data['id'])){
            IcmCustomer::find($data['id'])->update(array_merge($request->all(), ['user_updated' => $user->id ]));
        }else{
            IcmCustomer::create(array_merge($request->all(), ['user_created' => $user->id ]));
        }

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => []
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
        $ratetype = IcmCustomer::find($id);

        return response()->json([
            'success' => true,
            'message' => '',
            'ratetype' => $ratetype
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
