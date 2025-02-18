<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Admin\PlantillasEmail as Plantillas;
use App\Models\Admin\Email;


use App\Clases\PlantillaMail\ObtenerDatosMapa;
# use App\Clases\PlantillaMail\ObtenerEstructura;

class PlantillasController extends Controller
{
    
    
    function __construct()
    {
         $this->middleware('permission:plantillas-list|plantillas-create|plantillas-edit|plantillas-delete', ['only' => ['index','store']]);
         $this->middleware('permission:plantillas-create', ['only' => ['create','store']]);
         $this->middleware('permission:plantillas-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:plantillas-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plantillas = Plantillas::orderBy('id')->get();

        return view('admin.plantillas.index', compact('plantillas'));
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
        $emails = Email::pluck('email','id')->all();

        $plantilla = Plantillas::findOrFail($id);

        $variables = ObtenerDatosMapa::obtenerMapCaption($plantilla->codigo);

        return view('admin.plantillas.edit',compact('emails','plantilla','variables'));
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
        $input = $request->all();

        if(empty($input['emails_id']))
            $input['emails_id'] = NULL;

        $plantilla = Plantillas::findOrFail($id);

        $plantilla->update($input);
    
        return redirect()->route('plantillas.index')
                        ->with('success','La plantilla fue actualizada con exito.');
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
