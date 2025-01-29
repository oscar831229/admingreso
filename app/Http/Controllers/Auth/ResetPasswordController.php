<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\Admin\AuthenticationLog;

use Illuminate\Http\Request;
use App\Models\Admin\IcmSystemConfiguration;
use BrowserDetect;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    public function showResetForm(Request $request, $token = null)
    {

        $configuration = IcmSystemConfiguration::first();

        $browser_family = BrowserDetect::browserFamily();
        if($browser_family == 'Internet Explorer'){
            return view('auth.navegador');
        }

        if($configuration && empty($configuration->background)){

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

        // Pasa la variable a la vista junto con los demás datos necesarios
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $request->email,
            'configuration' => $configuration, // Pasa la variable a la vista
        ]);

    }

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;


    protected function setUserPassword($user, $password)
    {
        $user->password = md5($password);
    }


    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {

        $this->setUserPassword($user, $password);

        $user->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));

        $roles = $user->roles()->get();

        if ($roles->isNotEmpty()) {

            AuthenticationLog::create([
                'user_id' => $user->id,
                'ipaddress' => $this->getUserIpAddr(),
                'observation' => 'Autenticación exitosa'
            ]);

            $user->setSession($roles->toArray());

        }

        $this->guard()->login($user);

    }

    public function getUserIpAddr(){
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
     }


}
