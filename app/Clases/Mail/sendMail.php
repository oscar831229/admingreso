<?php 
namespace App\Clases\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class sendMail 
{

    private $cuenta;

    private $correo;

    private $debug = SMTP::DEBUG_OFF;

    public $error = '';

    public function setCuenta(Cuenta $cuenta){
        $this->cuenta = $cuenta;
    }

    public function setCorreo(Correo $correo){
        $this->correo = $correo;
    }

    public function debug(){
        $this->debug = SMTP::DEBUG_SERVER;
    }



    public function send(){

        if(empty($this->cuenta))
            throw new Exception("Error no se ha indicado la cuenta de correo");

        if(empty($this->correo))
            throw new Exception("Error no se ha definido correo a enviar");

        $mail = new PHPMailer(true);

        try {

            //Server settings
            $mail->SMTPDebug = $this->debug;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = $this->cuenta->getServer();             // Set the SMTP server to send through
            
            # AUTENTICACION PREVIA
            $password = $this->cuenta->getPassword();
            $mail->SMTPAuth   = true;
            if(empty($password)){
                $mail->SMTPAuth   = false;
                $password = '';
            }    

            # USUARIO Y CONTRASEÑA
            $mail->Username   = $this->cuenta->getEmail();
            $mail->Password   = $password;

            # TIPO ENCRIPTACIÓN
            $encryption = $this->cuenta->getEncryption();
            if(empty($encryption)){
                $mail->SMTPSecure = false;
                $mail->SMTPAutoTLS = false;
            }else{
                $mail->SMTPSecure = $this->cuenta->getEncryption();
            }

            # PUERTO
            $mail->Port       = $this->cuenta->getPuerto();


            $mail->setFrom($this->cuenta->getEmail(),'');

            # Para
            foreach ($this->correo->getPara() as $key => $para) {
                $mail->AddAddress($para);
            }

            # CC
            foreach ($this->correo->getCC() as $key => $cc) {
                $mail->addCC($cc);
            }

            # CCO
            foreach ($this->correo->getCCO() as $key => $cco) {
                $mail->addBCC($cco);
            }

            // $mail->addReplyTo('info@example.com', 'Information');

            # Adjuntos
            foreach ($this->correo->getAdjuntos() as $key => $adjunto) {
                $mail->addAttachment($adjunto); 
            }
      
            // Content
            $mail->isHTML(true);                                  // Set correo format to HTML
            // Activo condificacción utf-8
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $this->correo->getAsunto();
            $mail->Body    = $this->correo->getMensaje();

            if(!$mail->send())
                $this->error = $mail->ErrorInfo;
            
        } catch (Exception $e) {
            $this->error = $mail->ErrorInfo;
        }

    }
}