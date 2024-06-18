<?php

namespace App\Http\Controllers\Income;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Jobs\SynchronizationTask;
use Illuminate\Support\Facades\Bus;
use Carbon\Carbon;
use App\Jobs\ProcessMessage;

class SynchronizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
    public function show($component)
    {
        $request = request();
        if($request->has('document_number')){
            SynchronizationTask::dispatch($component, $request->document_number);
        }else{
            SynchronizationTask::dispatch($component);
        }

        \Log::info("Programada sincronización sistema POS {$component}");

        return response()->json([
            'success' => true,
            'message' => 'Trabajo en cola programado con exito',
            'data'    => []
        ]);
    }

    public function tokenValidation(){
        return response()->json([
            'success' => true,
            'message' => 'Token valido',
            'data'    => []
        ]);
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
