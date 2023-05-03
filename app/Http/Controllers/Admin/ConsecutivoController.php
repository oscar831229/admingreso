<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Consecutivo;

use App\Http\Requests\Admin\ValidacionConsecutivo;
use App\Models\Solicitudes\SolicitudesCotizacion as Solicitud;

class ConsecutivoController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:consecutivo-list|consecutivo-create|consecutivo-edit|consecutivo-delete', ['only' => ['index','store']]);
         $this->middleware('permission:consecutivo-create', ['only' => ['create','store']]);
         $this->middleware('permission:consecutivo-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:consecutivo-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		
		# prueba
        $consecutivos = Consecutivo::orderBy('id','DESC')->get();

        return view('admin.consecutivo.index',compact('consecutivos'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.consecutivo.create');
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ValidacionConsecutivo $request)
    {
        $input = $request->all();
        $input['usuario_crea_id'] = auth()->user()->id;

        $role = Consecutivo::create($input);
    
        return redirect()->route('consecutivo.index')
                         ->with('success','Consecutivo creado con exito.');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();
    
        return view('admin.roles.show',compact('role','rolePermissions'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $consecutivo = Consecutivo::find($id);
   
        return view('admin.consecutivo.edit',compact('consecutivo'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ValidacionConsecutivo $request, $id)
    {
    
        $input = $request->all();
        $input['usuario_actu_id'] = auth()->user()->id;

        Consecutivo::findOrFail($id)->update($input);
    
        return redirect()->route('consecutivo.index')
                        ->with('success','Consecutivo actualizado exitosamente.');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        # Consultar consecutivo
        $consecutivo = Consecutivo::findOrFail($id);

        # Validar que no existas solicitudes de cotizacion vinculadas
        $solicitudes = Solicitud::where('prefijo',$consecutivo->prefijo);

        if($solicitudes->count()  == 0){
            
            $consecutivo->destroy();

            return redirect()->route('consecutivo.index')
                        ->with('success','El consecutivo fue anulado con exito.');

        }

        return redirect()->route('consecutivo.index')
                    ->with('errors','Consecutivo no puede ser anulado.');
        
    }
}
