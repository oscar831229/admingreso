<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Admin\Servicio;  
use App\Models\Definicion;
use App\Models\Categoria;  

use App\Providers\RouteServiceProvider;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:custom');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
}
