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
                'module'=>'headquarters-entrance',
                'code'=>'report-liquidations',
                'description'=>'Reportes de liquidaciones'
            ],[
                'module'=>'headquarters-entrance',
                'code'=>'report-liquidation-details',
                'description'=>'Reportes detalle de liquidaciones'
            ],[
                'module'=>'headquarters-entrance',
                'code'=>'coverage-report',
                'description'=>'Reporte de coberturas'
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
