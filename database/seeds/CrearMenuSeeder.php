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
            array('id' => '1' , 'menu_id' => '0' , 'nombre' => 'Administrador'              , 'url' => '#'                           , 'orden' => '1' , 'icono' => 'icon ion-wrench', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '4' , 'menu_id' => '1' , 'nombre' => 'Roles'                      , 'url' => 'Admin/roles'                 , 'orden' => '4' , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
            array('id' => '5' , 'menu_id' => '1' , 'nombre' => 'Usuarios'                   , 'url' => 'Admin/users'                 , 'orden' => '5' , 'icono' => NULL, 'created_at' => $now, 'updated_at' => $now),
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
