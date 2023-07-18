<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Common\ReportManagement;
use Spatie\Permission\Models\Permission;

class reportManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        

        $reports = [
            [
                'module'=>'electronic-wallet-module',
                'code'=>'electronic-wallet-transactions',
                'description'=>'Reportes transacciones billetera electrónica'
            ],
            [
                'module'=>'electronic-wallet-module',
                'code'=>'balance-report-wallet-users',
                'description'=>'Reportes saldos usuario billetera'
            ]
        ];

        foreach ($reports as $key => $value) {

            $report = ReportManagement::where(['code'=> $value['code']])->first();

            if(!$report){

                # CREAR REPORTE
                ReportManagement::create([
                    'module' => $value['module'],
                    'code' => $value['code'],
                    'description' => $value['description'],
                    'state' => 'A'
                ]);

                # REGISTRAR PERMISO REPORTE
                $permision = Permission::where('name', $value['code'])->first();

                if(!$permision){
                    Permission::create([
                        'name' => $value['code'],
                        'module' => $value['module'],
                        'descripcion'=> $value['module'].': '.$value['description']
                    ]);
                }

            }

        }
    }
}
