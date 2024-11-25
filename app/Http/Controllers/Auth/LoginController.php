<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Admin\AuthenticationLog;
use Illuminate\Support\Facades\Session;
use App\Models\Admin\IcmSystemConfiguration;

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
        $configuration = IcmSystemConfiguration::first();

        $browser_family = BrowserDetect::browserFamily();
        if($browser_family == 'Internet Explorer'){
            return view('auth.navegador');
        }

        if(empty($configuration->background)){

            $imagePath = public_path('img/fondo.jpg'); // Asegúrate de que esta ruta sea correcta

            // Comprobar si la imagen existe
            if (file_exists($imagePath)) {

                // Obtener el tipo MIME del archivo
                $mimeType = mime_content_type($imagePath);

                // Leer el contenido del archivo
                $imageContent = file_get_contents($imagePath);

                // Convertir el contenido a base64
                $base64Image = base64_encode($imageContent);

                // Crear el prefijo adecuado para el base64, con el tipo MIME
                $background = 'data:' . $mimeType . ';base64,' . $base64Image;

                $configuration->background = $background;
                $configuration->save();

            }

        }

        return view('auth.login', compact('configuration'));
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
