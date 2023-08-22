<?php

use Illuminate\Database\Seeder;

use App\Models\Admin\PlantillasEmail as Plantillas;


class CrearPlantillas extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        #Plantillas 
        $procesos = [
            [
                'codigo' => 'send_new_token', 
                'nombre' => 'Plantilla utilizada para notificar token usuario tiquetera electrónica'
            ], [
                'codigo' => 'notify_movement', 
                'nombre' => 'Plantilla para notificar movimientos generados, abono, consumos, reversos etc'
            ], [
                'codigo' => 'send_account_statement', 
                'nombre' => 'Plantilla para enviar el extracto de la cuenta del usuario'
            ]                
        ];



        foreach ($procesos as $key => $proceso) {

            $plantilla = Plantillas::where('codigo',$proceso['codigo'])->first();

            if(!$plantilla){
                Plantillas::create($proceso);
            }
        }
       
    }
}
