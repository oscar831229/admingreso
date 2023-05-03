<?php
namespace App\Http\Controllers\Admin;
    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;
use App\Models\Admin\Servicio;
use App\Models\Admin\MedicalUnit;
use App\Http\Requests\ValidacionUsuario;
use App\Http\Requests\ValiarUpdateUser;
use App\Models\Admin\AuthenticationLog;

    
class UserController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        //  $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','store']]);
        //  $this->middleware('permission:user-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = User::orderBy('name')->get();
        return view('admin.usuarios.index',compact('data'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::OrderBy('name')->pluck('name','name')->all();
        $MedicalUnit = MedicalUnit::OrderBy('name')->pluck('name','name')->all();

        return view('admin.usuarios.create',['roles' => $roles, 'MedicalUnit' => $MedicalUnit]);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ValidacionUsuario $request)
    {
    
        $input = $request->all();
        $input['password'] = md5($input['password']);
        $input['active'] = 1;

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        if($request->input('Units')){
            $medical_units = DB::table('medical_units')->whereIn('name',$request->input('Units'))->get()->pluck('id');
            $user->units()->attach($medical_units);
        }
    
        return redirect()->route('users.index')
                        ->with('success','Usuarios creado con exito');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        $autenticate_logs = AuthenticationLog::where(['user_id' => $id])->orderBy('created_at','desc')->get();

        return view('admin.usuarios.show',compact('user','autenticate_logs'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        $roles = Role::OrderBy('name')->pluck('name','name')->all();
        $MedicalUnit = MedicalUnit::OrderBy('name')->pluck('name','name')->all();

        $user = User::find($id);
        $userRole = $user->roles->pluck('name','name')->all();
        $userUnits = $user->units->pluck('name','name')->all();

        return view('admin.usuarios.edit',[
            'user'=>$user,
            'roles' => $roles,
            'userRole' => $userRole, 
            'MedicalUnit' => $MedicalUnit, 
            'userUnits' => $userUnits
        ]);

    }

    public function getSucursales(Request $reques, $unidad_id)
    {
        $unidades = Negocio::findOrFail($unidad_id)->getSucursales->where('estado','A');
        return $unidades->pluck('nombre_sucursal','id_sucursal')->all();
    }

    public function getUsuarios($servicio_id)
    {
        
        $usuario = Servicio::findOrFail($servicio_id)
                    ->usuario;
        $usuarios = [
            $usuario->id => $usuario->name
        ];
        

        return $usuarios;
    }

    public function usuariosSucursal($sucursal_id){

        $usuarios   = Sucursal::findOrFail($sucursal_id)
                    ->usuarios
                    ->where('active','Y')
                    ->pluck('name','id')
                    ->all();
        
        return $usuarios;
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ValiarUpdateUser $request, $id)
    {
        $input = $request->all();

        $user = User::find($id);
        $user->update($input);

        # Actualizar roles
        DB::table('model_has_roles')->where('model_id',$id)->delete();
        $user->assignRole($request->input('roles'));

        # Actualizar unidad
        DB::table('medical_units_users')->where('user_id',$id)->delete();

        if($request->input('Units')){
            $medical_units = DB::table('medical_units')->whereIn('name',$request->input('Units'))->get()->pluck('id');
            $user->units()->attach($medical_units);
        }
    
        return redirect()->route('users.index')
                        ->with('success','Usuario actualizados con exito.');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->route('users.index')
                        ->with('success','Usuario eliminado con exito');
    }
}