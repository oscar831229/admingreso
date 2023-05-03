<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\MedicalUnit;
use App\Models\Entity;

class UnitController extends Controller
{

    function __construct()
    {
         $this->middleware('permission:unidad-list|unidad-create|unidad-edit|unidad-delete', ['only' => ['index','store']]);
         $this->middleware('permission:unidad-create', ['only' => ['create','store']]);
         $this->middleware('permission:unidad-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:unidad-delete', ['only' => ['destroy']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $medicalUnit = MedicalUnit::OrderBy('id')->get();
        return view('admin.unit.index',compact('medicalUnit'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.unit.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:App\Models\Admin\MedicalUnit|max:45',
            'name' => 'required',
        ]);
      
        $data = $request->all();
        $data['user_created'] = auth()->user()->id;

        MedicalUnit::create($data);

        return redirect()->route('units.index')
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
        $MedicalUnit = MedicalUnit::findOrFail($id);
        return view('admin.unit.edit',compact('MedicalUnit'));
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
        $request->validate([
            'code' => 'required|unique:App\Models\Admin\MedicalUnit|max:45',
            'name' => 'required',
        ]);
      
        $data = $request->all();
        $data['user_updated'] = auth()->user()->id;

        MedicalUnit::create($data);

        return redirect()->route('units.index')
                        ->with('success','Servicio actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        MedicalUnit::find($id)->delete();
        return redirect()->route('units.index')
                        ->with('success','Usuario eliminado con exito');
    }

    public function sincronizar(){

        # ADICIONAR LAS UNIDADES FUNCIONALES -> CRISTAL
        $entidades = \DB::connection('INDIGO019')->select("SELECT * FROM INENTIDAD");

        foreach ($entidades as $key => $entidad) {

            $exits = Entity::where('code',$entidad->CODENTIDA)->first();

            if(!$exits){
                Entity::create([
                    'code' => trim($entidad->CODENTIDA),
                    'name' => $entidad->NOMENTIDA,
                    'nit' => $entidad->CODIGONIT,
                    'user_created_at' => auth()->user()->id
                ]);
            }

        }


        # ADICIONAR LAS UNIDADES FUNCIONALES -> CRISTAL
        $unidades = \DB::connection('INDIGO019')->select("SELECT * FROM INUNIFUNC");

        foreach ($unidades as $key => $unidad) {

            $exits = \DB::table('medical_units')->where('code',$unidad->UFUCODIGO)->first();

            if(!$exits){
               \DB::table('medical_units')->insert([
                    'code' => $unidad->UFUCODIGO,
                    'name' => $unidad->UFUDESCRI,
                    'user_created' => 1
                ]);
            }

        }

        return response()->json(['success'=>true, 'message'=>'', 'data'=>[]]);
    }
}
