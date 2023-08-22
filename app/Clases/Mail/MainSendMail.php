<?php 
namespace App\Clases\Mail;

use App\Models\Admin\PlantillasEmail as Plantilla;
use App\Clases\PlantillaMail\ObtenerDatosMapa;

use App\Models\Wallet\EmailTraceability;


class MainSendMail 
{
    public static function send($process_code, $where, Array $destination = [], $attachments = [], $extras = []){

        $traceability = [
            'process_code' => $process_code,
            'where' => json_encode($where),
            'destination' => is_array($destination) ? json_encode($destination) : $destination,
            'attachments' => json_encode($attachments),
            'user_created' => auth()->user()->id
        ];

        # REGISTRA TRAZABILIDAD
        $traceability = EmailTraceability::create($traceability);
        
        # CARGAR PLANTILLA NOTIFICACIÓN
        $plantilla = Plantilla::where('codigo', $process_code)->first();

        $cuenta = new Cuenta;
        $cuenta->preparDatos($plantilla->emails_id);

        # DATOS EMAIL
        $datos_mapa = new ObtenerDatosMapa($process_code);

        # CONSULTAR DATOS DEL TRABAJADOR
        $datos = $datos_mapa->consultarDatos($where);

        # CUERPO EMAIL
        $cuerpo = str_replace(array_keys($datos),array_values($datos),$plantilla->mensaje);
        $cuerpo .= count($extras) > 0 && isset($extras['numrows']) && $extras['numrows'] > 0 ? $extras['table'] : '';
        $cuerpo =  '
            <!DOCTYPE html>
            <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <meta http-equiv="X-UA-Compatible" content="ie=edge">
                    <title>Document</title>
                    <style>
                        table, th, td {
                            border: 1px solid;
                            padding: 5px;
                        }
                        table {
                            border-collapse: collapse;
                        }
                    </style>
                </head>
                <body >
                    '.$cuerpo.'
                </body>
            </html>';

        # INICIALIZAR CORREOS
        $correo = new Correo;
        $correo->setAsunto($plantilla->asunto)
                ->setMensaje($cuerpo);

        # EMAIL DESTINO
        $correo->setPara($destination);

        # ADJUNTOS 
        $correo->setAdjunto($attachments);
        

        # SONDA EMAIL
        $sendMail = new sendMail;
        $sendMail->setCuenta($cuenta);
        $sendMail->setCorreo($correo);

        try {

            # SEND EMAIL
            $sendMail->send();

            $traceability->send = 'S';
            $traceability->update();

        } catch (phpmailerException $e) {
            $message = $e->errorMessage();
            $traceability->error = $message;
            $traceability->update();
        } catch (Exception $e) {
            $message = $e->errorMessage();
            $traceability->error = $message;
            $traceability->update();
        }
        
    }

    public static function tableTickets($tickets, $title = 'Nuevos tickets'){
        # TABLA PERIODOS
        $tablehtml = "<p>{$title}</p><table style>
            <tr>
                <th>#</th>
                <th>Número</th>
                <th>Valor</th>
                <th>Fecha</th>
            </tr>";

        $number = 1;
        foreach ($tickets as $key => $ticket) {

            $fecha_ticket = $ticket->state == 'P' ? $ticket->created_at : $ticket->updated_at;
            $tablehtml .= "<tr>
                <td>{$number}</td>
                <td>{$ticket->number_ticket}</td>
                <td>".number_format($ticket->value, 2)."</td>
                <td>{$fecha_ticket}</td>
            </tr>";
            $number++;
        }

        $tablehtml .= '<table>';

        return $tablehtml;

    }
}
