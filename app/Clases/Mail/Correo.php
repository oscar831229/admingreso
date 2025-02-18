<?php 
namespace App\Clases\Mail;

class Correo 
{

    private $asunto;
    private $mensaje;

    private $para = [];
    private $cc = [];
    private $cco = [];

    private $adjuntos = [];


    public function setAsunto($asunto)
    {
        $this->asunto = $asunto;
        return $this;
    }

    public function setMensaje($mensaje)
    {   
        $this->mensaje = $mensaje;
        return $this;
    }

    public function setPara($para){

        if(is_array($para)){
            $this->para = array_merge($this->para,$para);
        }else{
            $this->para[] = $para;
        }
        return $this;

    }


    public function setCC($cc)
    {
        if(is_array($cc)){
            $this->cc = array_merge($this->cc, $cc);
        }else{
            $this->cc[] = $cc;
        }
        return $this;
    }

    public function setCCO($cco)
    {
        if(is_array($cco)){
            $this->cco = array_merge($this->cco, $cco);
        }else{
            $this->cco[] = $cco;
        }
        return $this;

    }

    public function setAdjunto($adjunto)
    {
        if(is_array($adjunto)){
            $this->adjuntos = array_merge($this->adjuntos, $adjunto);
        }else{
            $this->adjuntos[] = $adjunto;
        }
        return $this;

    }

    public function getAsunto()
    {
        return $this->asunto;
    }

    public function getMensaje()
    {
        return $this->mensaje;
    }

    public function getPara()
    {
        return $this->para;
    }

    public function getCC()
    {
        return $this->cc;        
    }

    public function getCCO()
    {
        return $this->cco;
    }

    public function getAdjuntos(){
        return $this->adjuntos;
    }
   
}