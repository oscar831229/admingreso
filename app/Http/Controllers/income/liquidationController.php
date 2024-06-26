<?php

namespace App\Http\Controllers\income;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Clases\DataTable\TableServer;
use Illuminate\Support\Facades\Validator;

use App\Models\Income\IcmLiquidation;

class liquidationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('income.liquidations.index');
    }

    public function datatableLiquidations(Request $request){

        $extradata = [];

        if(!empty($request->state)){
            $extradata['state'] = $request->state;
        }

        if(!empty($request->date_from)){
            $extradata['date_from'] = $request->date_from;
        }

        if(!empty($request->date_to)){
            $extradata['date_to'] = $request->date_to;
        }

        $param = array(
            'model'=> new IcmLiquidation,
            'method_consulta'=>'getDataTable',
            'method_cantidad'=>'getCountDatatable',
            'extradata' => $extradata
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
        //
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
