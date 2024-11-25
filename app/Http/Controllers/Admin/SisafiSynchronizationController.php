<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Sisafi\SisafiSyncTracer;
use App\Clases\DataTable\TableServer;
use App\Clases\Cajasan\Parameters;
use App\Jobs\SincronizarAfiliados;
use App\Clases\Cajasan\SynchronizeAffiliates;

class SisafiSynchronizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.sisafi-synchronization.index');
    }

    public function datatableSisafiSynchronization(Request $request){

        $extradata = [];

        if(!empty($request->type_synchronization)){
            $extradata['type_synchronization'] = $request->type_synchronization;
        }

        if(!empty($request->type_execution)){
            $extradata['type_execution'] = $request->type_execution;
        }

        if(!empty($request->date_from)){
            $extradata['date_from'] = $request->date_from;
        }

        if(!empty($request->date_to)){
            $extradata['date_to'] = $request->date_to;
        }

        $param = array(
            'model'=> new SisafiSyncTracer,
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

        $user_id   = auth()->user()->id;
        $parameter = new Parameters;
        $parameter->type_synchronization = 'T';
        $parameter->type_execution       = 'M';
        $parameter->user_id              = $user_id;

        $syncafiliados = new SynchronizeAffiliates;

        $syncafiliados->execute($parameter);

        return response()->json([
            'success' => true,
            'message' => '',
            'data'    => []
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
