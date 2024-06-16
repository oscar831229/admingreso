<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Admin\AuthenticationLog;
use Illuminate\Support\Facades\Session;

use BrowserDetect;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    public $maxAttempts = '3';


    public $decayMinutes = 5;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        $browser_family = BrowserDetect::browserFamily();
        if($browser_family == 'Internet Explorer'){
            return view('auth.navegador');
        }
        return view('auth.login');
    }

    public function username()
    {
        return 'login';
    }

    protected function authenticated(Request $request, $user)
    {

        if($user->active == 0){

            AuthenticationLog::create([
                'user_id' => $user->id,
                'ipaddress' => getUserIpAddr(),
                'observation' => 'Usuario Inactivo'
            ]);

            $this->guard()->logout();
            $request->session()->invalidate();
            return redirect('/')->withErrors(['error' => 'Usuario inactivo']);
        }

        $roles = $user->roles()->get();
        if ($roles->isNotEmpty()) {

            AuthenticationLog::create([
                'user_id' => $user->id,
                'ipaddress' => getUserIpAddr(),
                'observation' => 'Autenticación exitosa'
            ]);

            $user->setSession($roles->toArray());

            // $allSessions = Session::all();

            // // Mostrar las variables de sesión
            // dd($allSessions); // dd() imprime y termina la ejecución

        } else {

            AuthenticationLog::create([
                'user_id' => $user->id,
                'ipaddress' => getUserIpAddr(),
                'observation' => 'No tiene asignado rol el usuario autenticado'
            ]);

            $this->guard()->logout();
            $request->session()->invalidate();
            return redirect('/')->withErrors(['error' => 'Este usuario no tiene un rol activo']);
        }
    }


}
