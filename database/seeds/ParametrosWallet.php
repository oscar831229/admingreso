<?php

use Illuminate\Database\Seeder;
use App\Models\Wallet\ElectricalPocket;

class ParametrosWallet extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        # CREACION DE BOLSILLOS DISPONIBLES
        $pockets = [
            [
                'code' => '001',
                'name' => 'Tiqutera',
                'description' => 'Bolsillo principal del usuario sobre el cual trabaja la tiquetera',
                'main' => 1,
                'user_created' => 1
            ]
        ];

        foreach ($pockets as $key => $pocket) {
            try {
                ElectricalPocket::create($pocket);
            } catch (\Throwable $th) {
                //throw $th;
            }
        }


    }
}
