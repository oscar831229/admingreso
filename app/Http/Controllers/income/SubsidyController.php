<?php

namespace App\Http\Controllers\income;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Income\IcmTypeSubsidy;
use App\Clases\DataTable\TableServer;
use Illuminate\Support\Facades\Validator;


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
            IcmTypeSubsidy::find($data['id'])->update(array_merge($request->all(), ['user_updated' => $user->id ]));
        }else{
            IcmTypeSubsidy::create(array_merge($request->all(), ['user_created' => $user->id ]));
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
