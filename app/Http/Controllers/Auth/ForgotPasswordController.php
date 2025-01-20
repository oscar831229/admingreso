<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Models\Admin\IcmSystemConfiguration;

use BrowserDetect;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function showLinkRequestForm()
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

        return view('auth.passwords.email', compact('configuration'));
    }
}
