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
            array('id' => '1' , 'menu_id' => '0' , 'nombre' => 'Administrador'              , 'url' => '#'                           , 'orden' => '1' , 'icono' => 'fa fa-cogs', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '2' , 'menu_id' => '1' , 'nombre' => 'Roles'                      , 'url' => 'Admin/roles'                 , 'orden' => '2' , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '3' , 'menu_id' => '1' , 'nombre' => 'Usuarios'                   , 'url' => 'Admin/users'                 , 'orden' => '3' , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '4' , 'menu_id' => '1' , 'nombre' => 'Menu Rol'                   , 'url' => 'Admin/menu-rol'              , 'orden' => '4' , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '5' , 'menu_id' => '1' , 'nombre' => 'Emails'                     , 'url' => 'Admin/emails'                , 'orden' => '5' , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '6' , 'menu_id' => '1' , 'nombre' => 'Plantillas'                 , 'url' => 'Admin/plantillas'            , 'orden' => '6' , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '7', 'menu_id' =>  '0' , 'nombre' => 'Billetera'                  , 'url' => '#'                           , 'orden' => '7' , 'icono' => 'fa fa-money', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '8' , 'menu_id' => '7' , 'nombre' => 'Comercios'                  , 'url' => 'wallet/business'             , 'orden' => '8' , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '9' , 'menu_id' => '7' , 'nombre' => 'Permisos comercios'         , 'url' => 'wallet/business-users'       , 'orden' => '9' , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '14','menu_id' =>  '7', 'nombre'  => 'Bolsillos electrónicos'     , 'url' => 'wallet/electrical-pockets'   , 'orden' => '10' , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '15','menu_id' =>  '7', 'nombre'  => 'Consecutivos tiquetera'     , 'url' => 'wallet/consecutive-tickets'  , 'orden' => '9' , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '10' , 'menu_id' => '7' , 'nombre' => 'Tipos de movimientos'       , 'url' => 'wallet/movement-types'      , 'orden' => '12' , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '11', 'menu_id' => '7' , 'nombre' => 'Usuarios'                   , 'url' => 'wallet/wallet-users'         , 'orden' => '13' , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '12', 'menu_id' => '7' , 'nombre' => 'Transacciones'              , 'url' => 'wallet/transactions'         , 'orden' => '14' , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '13','menu_id' =>  '7', 'nombre'  => 'Reportes'                   , 'url' => 'wallet/wallet-reports'       , 'orden' => '15' , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
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
