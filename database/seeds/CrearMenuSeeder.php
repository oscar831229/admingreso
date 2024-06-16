<?php
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\PlantillasEmail as Plantillas;

class CrearMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $now = Carbon::now()->toDateTimeString();
        $menus = [
            array('id' => '1'  , 'menu_id'  => '0' , 'nombre' => 'Administrador'              , 'url' => '#'                                   , 'orden' => '1'  , 'icono' => 'fa fa-cogs', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '2'  , 'menu_id'  => '1' , 'nombre' => 'Roles'                      , 'url' => 'Admin/roles'                         , 'orden' => '2'  , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '3'  , 'menu_id'  => '1' , 'nombre' => 'Usuarios'                   , 'url' => 'Admin/users'                         , 'orden' => '3'  , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '4'  , 'menu_id'  => '1' , 'nombre' => 'Menu Rol'                   , 'url' => 'Admin/menu-rol'                      , 'orden' => '4'  , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '5'  , 'menu_id'  => '1' , 'nombre' => 'Emails'                     , 'url' => 'Admin/emails'                        , 'orden' => '5'  , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '6'  , 'menu_id'  => '1' , 'nombre' => 'Plantillas'                 , 'url' => 'Admin/plantillas'                    , 'orden' => '6'  , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '7'  , 'menu_id'  => '0' , 'nombre' => 'Parametros ingresos' , 'url' => '#'                                   , 'orden' => '7'  , 'icono' => 'fa fa-arrow-right', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '8'  , 'menu_id'  => '7' , 'nombre' => 'Ambientes'                  , 'url' => 'income/environments'                 , 'orden' => '8'  , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '9'  , 'menu_id'  => '7' , 'nombre' => 'Tipos de tarifa'            , 'url' => 'income/rate-types'                   , 'orden' => '9'  , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '10' , 'menu_id'  => '7' , 'nombre' => 'Categorias cajasan'         , 'url' => 'income/affiliate-categories'         , 'orden' => '11'  , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '11' , 'menu_id'  => '7' , 'nombre' => 'Calendario temporada'       , 'url' => 'income/special-rates'                , 'orden' => '12'  , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '12' , 'menu_id'  => '7' , 'nombre' => 'Usuarios - ambientes'       , 'url' => 'income/users-environments'           , 'orden' => '13'  , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '13' , 'menu_id'  => '7' , 'nombre' => 'Servicios de ingreso'  , 'url' => 'income/parameterization-services'    , 'orden' => '14'  , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '14' , 'menu_id'  => '7' , 'nombre' => 'Empresas convenio'   , 'url' => 'income/parameterization-companies'   , 'orden' => '15'  , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '15' , 'menu_id'  => '7' , 'nombre' => 'Convenios'  , 'url' => 'income/parameterization-agreements'  , 'orden' => '16'  , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '16' , 'menu_id'  => '0' , 'nombre' => 'Ingreso a sedes'            , 'url' => '#'                                   , 'orden' => '7'  , 'icono' => 'fa fa-arrow-right', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '17' , 'menu_id'  => '16' , 'nombre' => 'Facturación y ingreso'      , 'url' => 'income/billing-incomes'              , 'orden' => '17' , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
           // array('id' => '18' , 'menu_id'  => '16' , 'nombre' => 'Proceso ingreso'            , 'url' => 'income/incomes'                      , 'orden' => '18' , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '19' , 'menu_id'  => '16' , 'nombre' => 'Reportes'                   , 'url' => 'income/income-reports'               , 'orden' => '19' , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
        ];

        foreach ($menus as $key => $menu) {

            $exits = DB::table('menu')->where('id',$menu['id'])->first();

            if(!$exits){
                DB::table('menu')->insert($menu);
            }

            $menu_rol = DB::table('menu_rol')->where(['roles_id'=> 1,'menu_id' => $menu['id']])->first();

            if(!$menu_rol){
                DB::table('menu_rol')->insert(['roles_id'=>1, 'menu_id' =>$menu['id'] ]);
            }

        }


    }
}
