<?php

use Illuminate\Database\Seeder;

use App\Models\Common\Definition;
use App\Models\Common\DetailDefinition;
use App\User;

use App\Models\Income\IcmAffiliateCategory;
use App\Models\Income\IcmRateType;
use App\Models\Income\IcmFamilyCompensationFund;
use App\Models\Income\IcmTypesIncome;

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
                    ['code' => 'CC', 'name' => 'Cédula de Ciudadanía', 'details' => 'Cédula de Ciudadanía '],
                    ['code' => 'CE', 'name' => 'Cédula de Extranjería ', 'details' => 'Cédula de Ciudadanía '],
                    ['code' => 'PA', 'name' => 'Pasaporte', 'details' => 'Cédula de Ciudadanía'],
                    ['code' => 'NI', 'name' => 'Nit', 'details' => 'Nit'],
                    ['code' => 'TI', 'name' => 'Tarjeta de identidad', 'details' => 'Tarjeta de identidad'],
                    ['code' => 'TC', 'name' => 'Registro civil', 'details' => 'Registro civil']
                ]
                ], [
                    'code' => 'gender',
                    'name' => 'Definiciones de generos',
                    'details' => 'Definicion de generos',
                    'detaildefinitions' => [
                        ['code' => 'F', 'name' => 'Femenino', 'details' => 'Femenino'],
                        ['code' => 'M', 'name' => 'Masculino', 'details' => 'Masculino']
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

        # Migración  tipos de tarifa.
        $tipos = [
            [
                'code'         => 'V',
                'name'         => 'TEMPORADA BAJA',
                'state'        => 'A',
                'icon'         => 'fa fa-arrow-down',
                'user_created' => $user->id,
            ],[
                'code'         => 'A',
                'name'         => 'TEMPORADA ALTA',
                'state'        => 'A',
                'icon'         => 'fa fa-arrow-up',
                'user_created' => $user->id,
            ]
        ];

        foreach ($tipos as $key => $tipo) {
            $find =  IcmRateType::where(['code' => $tipo['code']])->first();
            if(!$find){
                IcmRateType::create($tipo);
            }
        }


        # Migracion categorias afiliados
        $categories = [[
            'code'         => 'A',
            'name'         => 'CATEGORIA A',
            'state'        => 'A',
            'user_created' => $user->id,
        ],[
            'code'         => 'B',
            'name'         => 'CATEGORIA B',
            'state'        => 'A',
            'user_created' => $user->id,
        ],[
            'code'         => 'C',
            'name'         => 'CATEGORIA C',
            'state'        => 'A',
            'user_created' => $user->id,
        ],[
            'code'         => 'D',
            'name'         => 'CATEGORIA D',
            'state'        => 'A',
            'user_created' => $user->id,
        ]];

        foreach ($categories as $key => $category) {
            $find =  IcmAffiliateCategory::where(['code' => $category['code']])->first();
            if(!$find){
                IcmAffiliateCategory::create($category);
            }
        }



        # Cajas de compensación familiar
        $icm_family_compensation_funds = [
            [
                'code'            => 'CCF01',
                'document_number' => '891190047',
                'name'            => 'COMFACA'
            ],[
                'code'            => 'CCF02',
                'document_number' => '844003392',
                'name'            => 'COMFACASANARE'
            ],[
                'code'            => 'CCF03',
                'document_number' => '891500182',
                'name'            => 'COMFACAUCA'
            ],[
                'code'            => 'CCF04',
                'document_number' => '892399989',
                'name'            => 'COMFACESAR'
            ],[
                'code'            => 'CCF05',
                'document_number' => '891600091',
                'name'            => 'COMFACHOCO'
            ],[
                'code'            => 'CCF06',
                'document_number' => '891080005',
                'name'            => 'COMFACOR'
            ],[
                'code'            => 'CCF07',
                'document_number' => '800231969',
                'name'            => 'COMCAJA'
            ],[
                'code'            => 'CCF08',
                'document_number' => '892115006',
                'name'            => 'COMFAGUAJIRA'
            ],[
                'code'            => 'CCF09',
                'document_number' => '891180008',
                'name'            => 'COMFAMILIAR HUILA'
            ],[
                'code'            => 'CCF10',
                'document_number' => '891780093',
                'name'            => 'CAJAMAG'
            ],[
                'code'            => 'CCF11',
                'document_number' => '892000146',
                'name'            => 'COFREM'
            ],[
                'code'            => 'CCF12',
                'document_number' => '891280008',
                'name'            => 'COMFAMILIAR NARIÑO'
            ],[
                'code'            => 'CCF13',
                'document_number' => '890500516',
                'name'            => 'COMFANORTE'
            ],[
                'code'            => 'CCF14',
                'document_number' => '891200337',
                'name'            => 'COMFAMILIAR PUTUMAYO'
            ],[
                'code'            => 'CCF15',
                'document_number' => '891480000',
                'name'            => 'COMFAMILIAR RISARALDA'
            ],[
                'code'            => 'CCF16',
                'document_number' => '892400320',
                'name'            => 'CAJASAI'
            ],[
                'code'            => 'CCF17',
                'document_number' => '8892200015',
                'name'            => 'COMFASUCRE'
            ],[
                'code'            => 'CCF18',
                'document_number' => '800211025',
                'name'            => 'COMFATOLIMA'
            ],[
                'code'            => 'CCF19',
                'document_number' => '890303093',
                'name'            => 'COMFENALCO VALLE'
            ],[
                'code'            => 'CCF20',
                'document_number' => '860066942',
                'name'            => 'COMPENSAR'
            ],[
                'code'            => 'CCF21',
                'document_number' => '860013570',
                'name'            => 'CAFAM'
            ],[
                'code'            => 'CCF22',
                'document_number' => '891800213',
                'name'            => 'COMFABOY'
            ],[
                'code'            => 'CCF23',
                'document_number' => '890480110',
                'name'            => 'COMFAMILIAR CARTAGENA'
            ],[
                'code'            => 'CCF24',
                'document_number' => '890102002',
                'name'            => 'COMBARRANQUILLA'
            ],[
                'code'            => 'CCF25',
                'document_number' => '800219488',
                'name'            => 'COMFIAR'
            ],[
                'code'            => 'CCF26',
                'document_number' => '890900842',
                'name'            => 'COMFENALCO ANTIOQUIA'
            ],[
                'code'            => 'CCF27',
                'document_number' => '800003122',
                'name'            => 'CAMFAMAZ'
            ]
        ];

        foreach ($icm_family_compensation_funds as $key => $compensation) {
            $find =  IcmFamilyCompensationFund::where(['code' => $compensation['code']])->first();
            $compensation['user_created'] = 1;
            if(!$find){
                IcmFamilyCompensationFund::create($compensation);
            }
        }


        # Tipos de ingreso a sedes
        $types_incomes = [[
                'code'  => 'AFI',
                'name'  => 'AFILIADO',
                'order' => '1',
                'icm_affiliate_categories' => [
                    ['code' => 'A', 'name' => 'CATEGORIA A'],
                    ['code' => 'B', 'name' => 'CATEGORIA B'],
                    ['code' => 'C', 'name' => 'CATEGORIA C'],
                ]
            ], [
                'code'  => 'PRE',
                'name'  => 'PRESENTADO',
                'order' => '2',
                'icm_affiliate_categories' => [
                    ['code' => 'A', 'name' => 'CATEGORIA A'],
                    ['code' => 'B', 'name' => 'CATEGORIA B'],
                    ['code' => 'C', 'name' => 'CATEGORIA C'],
                ]
            ], [
                'code'  => 'CAJ',
                'name'  => 'CAJAS SIN FRONTERAS',
                'order' => '3',
                'icm_affiliate_categories' => [
                    ['code' => 'A', 'name' => 'CATEGORIA A'],
                    ['code' => 'B', 'name' => 'CATEGORIA B'],
                    ['code' => 'C', 'name' => 'CATEGORIA C'],
                ]
            ], [
                'code'  => 'PAR',
                'name'  => 'PARTICULAR',
                'order' => '4',
                'icm_affiliate_categories' => [
                    ['code' => 'D', 'name' => 'CATEGORIA D']
                ]
            ]];

        foreach ($types_incomes as $key => $types_income) {

            $income = IcmTypesIncome::where(['code' => $types_income['code']])->first();
            if(!$income){
                $income = IcmTypesIncome::create([
                    'code'         => $types_income['code'],
                    'name'         => $types_income['name'],
                    'order'        => $types_income['order'],
                    'state'        => 'A',
                    'user_created' => 1
                ]);
            }

            foreach ( $types_income['icm_affiliate_categories'] as $key => $category) {
                $icm_category = IcmAffiliateCategory::where(['code' => $category['code']])->first();
                $categories = $income->icm_affiliate_categories()->where(['icm_affiliate_category_id' => $icm_category->id])->first();
                if(!$categories){
                    $income->icm_affiliate_categories()->attach($icm_category, ['user_created' => 1]);
                }
            }

        }


    }
}
