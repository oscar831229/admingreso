<?php

use Illuminate\Database\Seeder;

use App\Models\Common\Definition;
use App\Models\Common\DetailDefinition;
use App\User;

class DefinitionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $user = User::where(['login' => 'admin'])->first();

        $definiciones = [
            [
                'code' => 'identification_document_types',
                'name' => 'Tipos de documento de identificación',
                'details' => 'Tipos de documento de identificación',
                'detaildefinitions' => [
                    ['code' => '1', 'name' => 'Cédula de Ciudadanía', 'details' => 'Cédula de Ciudadanía '],
                    ['code' => '2', 'name' => 'Cédula de Extranjería ', 'details' => 'Cédula de Ciudadanía '],
                    ['code' => '5', 'name' => 'Pasaporte', 'details' => 'Cédula de Ciudadanía'],
                    ['code' => '6', 'name' => 'Nit', 'details' => 'Nit'],
                    ['code' => '7', 'name' => 'Tarjeta de identidad', 'details' => 'Tarjeta de identidad'],
                    ['code' => '8', 'name' => 'Registro civil', 'details' => 'Registro civil'],
                    ['code' => '9', 'name' => 'No identificado', 'details' => 'No identificado'],
                ]
            ],[
                'code' => 'types_of_income',
                'name' => 'Tipos de ingreso a sedes',
                'details' => 'Tipos de ingresos',
                'detaildefinitions' => [
                    ['code' => '1', 'name' => 'AFILIADO'           , 'details' => 'AFILIADO'],
                    ['code' => '2', 'name' => 'PRESENTADO'         , 'details' => 'PRESENTADO'],
                    ['code' => '3', 'name' => 'CAJAS SIN FRONTERAS', 'details' => 'CAJAS SIN FRONTERAS'],
                    ['code' => '4', 'name' => 'PARTICULAR'         , 'details' => 'PARTICULAR']
                ]
            ]
        ];

        foreach ($definiciones as $key => $definicion) {

            $definition = Definition::where(['code' => $definicion['code']])->first();

            if(!$definition){
                $definition = Definition::create(['code'=> $definicion['code'], 'name' => $definicion['name'], 'details' => $definicion['details'], 'user_created' => $user->id]);
            }

            foreach ($definicion['detaildefinitions'] as $key => $detaildefinition) {

                $detailexist = DetailDefinition::where(['code' => $detaildefinition['code'], 'definition_id' => $definition->id])->first();;

                if(!$detailexist){
                    DetailDefinition::create(['code' => $detaildefinition['code'], 'name' => $detaildefinition['name'], 'details' => $detaildefinition['details'], 'definition_id' => $definition->id, 'user_created' => $user->id]);
                }

            }

        }

    }
}
