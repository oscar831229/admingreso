<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Admin\Servicio;
use App\Models\Admin\Negocio;
use App\Models\Admin\Sucursal;

class ServiciosController extends Controller
{

    function __construct()
    {
         $this->middleware('permission:servicios-list|servicios-create|servicios-edit|servicios-delete', ['only' => ['index','store']]);
         $this->middleware('permission:servicios-create', ['only' => ['create','store']]);
         $this->middleware('permission:servicios-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:servicios-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $servicios = Servicio::OrderBy('id')->get();

        return view('admin.servicios.index',compact('servicios'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $unidades =  Negocio::orderBy('id_un')
                    ->get()
                    ->pluck('nombre_un','id_un')
                    ->all();

        return view('admin.servicios.create',compact('unidades'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Servicio::create($request->all());
        
    
        return redirect()->route('servicios.index')
                        ->with('success','Servicio creado con exito');
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
        $servicio = Servicio::findOrFail($id);

        $negocios = Negocio::pluck('nombre_un','id_un')->all();

        $sucursales =  Sucursal::where('id_un',$servicio->unidades_negocio_id)
                       ->orderBy('id_sucursal')
                       ->get()
                       ->pluck('nombre_sucursal','id_sucursal')
                       ->all();


        $usuarios  = Sucursal::find($servicio->sucursal_id)
                    ->usuarios
                    ->pluck('name','id')
                    ->all();

        return view('admin.servicios.edit',compact('servicio','negocios','sucursales','usuarios'));

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
   
        $servicio = Servicio::findOrFail($id);
        $servicio->update($request->all());
    
        return redirect()->route('servicios.index')
                        ->with('success','Servicio actualizados con exito.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Servicio::findOrFail($id)->delete();

        return redirect()->route('servicios.index')
                        ->with('success','El servicio se anulo con exito.');
    }
}
