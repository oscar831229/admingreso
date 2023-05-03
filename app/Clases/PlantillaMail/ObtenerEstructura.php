<?php


namespace App\Clases\PlantillaMail;


class ObtenerEstructura
{
    public static function getDatos($name){

        # Cargar variables dinamicas
        if(file_exists(__DIR__.'/estructuras/'.$name.'.php')){

            include __DIR__.'/estructuras/'.$name.'.php';
            $estructura = $$name;
            
        }
        else{
            include __DIR__.'/estructuras/default.php';
            $estructura = $default;
        }

        return $estructura;

    }

}