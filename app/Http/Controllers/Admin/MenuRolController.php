<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use App\Models\Admin\Menu;

class MenuRolController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:menu-rol-list|menu-rol-create|menu-rol-edit|menu-rol-delete', ['only' => ['index','store']]);
         $this->middleware('permission:menu-rol-create', ['only' => ['create','store']]);
         $this->middleware('permission:menu-rol-edit', ['only' => ['edit','update','guardar']]);
         $this->middleware('permission:menu-rol-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rols = Role::orderBy('id')->pluck('name', 'id')->toArray();
        $menus = Menu::getMenu();
        $menusRols = Menu::with('roles')->get()->pluck('roles', 'id')->toArray();

        return view('admin.menu-rol.index', compact('rols', 'menus', 'menusRols'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        if ($request->ajax()) {
            $menus = new Menu();
            if ($request->input('estado') == 1) {
                $menus->find($request->input('menu_id'))->roles()->attach($request->input('rol_id'));
                return response()->json(['respuesta' => 'El rol se asigno correctamente']);
            } else {
                $menus->find($request->input('menu_id'))->roles()->detach($request->input('rol_id'));
                return response()->json(['respuesta' => 'El rol se elimino correctamente']);
            }
        } else {
            abort(404);
        }
    }
}
