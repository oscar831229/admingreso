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

        ];



        foreach ($procesos as $key => $proceso) {

            $plantilla = Plantillas::where('codigo',$proceso['codigo'])->first();

            if(!$plantilla){
                Plantillas::create($proceso);
            }
        }

    }
}
