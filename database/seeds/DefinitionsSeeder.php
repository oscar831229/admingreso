<?php

use Illuminate\Database\Seeder;

use App\Models\Officials\Definition;
use App\Models\Officials\DetailDefinition;
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
    
        $definiciones = [];

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
