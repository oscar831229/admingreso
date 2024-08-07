<?php

namespace App\Http\Controllers\income;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Income\IcmTypeSubsidy;
use App\Clases\DataTable\TableServer;
use Illuminate\Support\Facades\Validator;
use App\Models\Amadeus\TiposSubsidio;


class SubsidyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('income.subsidies.index');
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
            'name' => "required",
            'code' => 'required|max:3',
            'state' => 'required'
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
            $type_subsidy = IcmTypeSubsidy::find($data['id']);
            $type_subsidy->update(array_merge($request->all(), ['user_updated' => $user->id ]));
        }else{
            $type_subsidy = IcmTypeSubsidy::create(array_merge($request->all(), ['user_created' => $user->id ]));
        }

        # Registrar en tabla del POS
        $subsidio = TiposSubsidio::find($type_subsidy->id);
        if(!$subsidio){
            $subsidio = new TiposSubsidio;
            $subsidio->id = $type_subsidy->id;
            $subsidio->codigo = $type_subsidy->code;
            $subsidio->nombre = $type_subsidy->name;
            $subsidio->estado = $type_subsidy->state;
            $subsidio->save();
        }else{
            $subsidio->codigo = $type_subsidy->code;
            $subsidio->nombre = $type_subsidy->name;
            $subsidio->estado = $type_subsidy->state;
            $subsidio->update();
        }

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => []
        ]);

    }

    public function datatableSubsidies(Request $request){

        $param = array(
            'model'=> new IcmTypeSubsidy,
            'method_consulta'=>'getDataTable',
            'method_cantidad'=>'getCountDatatable',
            'extradata' => []
        );

        $tableserver = new TableServer($param);
        $datos = $tableserver->getDatos();

        return response()->json($datos);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $subsidies = IcmTypeSubsidy::find($id);

        return response()->json([
            'success' => true,
            'message' => '',
            'subsidies' => $subsidies
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
