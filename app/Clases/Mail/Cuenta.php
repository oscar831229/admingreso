<?php 

namespace App\Clases\Mail;

use App\Models\Admin\Email;

class Cuenta 
{

   private $server;
   private $encryption;
   private $puerto;
   private $email;
   Private $password;


   public function setServer($server)
   {
       $this->server = $server;
       return $this;
   }

   public function setEncryption($encryption)
   {
       $this->encryption = $encryption;
       return $this;
   }

   public function setPuerto($puerto)
   {
       $this->puerto = $puerto;
       return $this;
   }

   public function setEmail($email)
   {
        $this->email = $email;
        return $this;
   }

   public function setPassword($password)
   {
        $this->password = $password; 
        return $this;      
   }

   public function getServer()
   {
       return $this->server;
   }

   public function getEncryption()
   {
        return $this->encryption;
   }

   public function getPuerto(){
       return $this->puerto;
   }

   public function getEmail(){
       return $this->email;
   }

   public function getPassword()
   {
        return $this->password;
   }


   public function prepararCuenta($id){

   }

   public function preparDatos($emails_id){

        $cuenta = Email::findOrFail($emails_id);

        $this->setServer($cuenta->server);
        $this->setEncryption($cuenta->encryption);
        $this->setPuerto($cuenta->puerto);
        $this->setEmail($cuenta->email);
        $this->setPassword($cuenta->password);

   }

    

   
}