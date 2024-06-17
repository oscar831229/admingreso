<?php

use Illuminate\Database\Seeder;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('login', 'admin')->first();

        if(!$user){

            $user           = new User;
            $user->login    = 'admin';
            $user->password = md5("2fe05187361");
            $user->name     = 'Administrador de soluciones';
            $user->email    = 'oscar831229@hotmail.com';
            $user->active   = 1;
            $user->save();

        }

        $role = Role::where('name','Administrador')->first();

        if(!$role){
            $role = Role::create(['name' => 'Administrador']);
        }


        $role->syncPermissions([]);
        $permissions = Permission::pluck('id','id')->all();
        $role->syncPermissions($permissions);
        $user->assignRole([$role->id]);


        # Usuario general ingresos sedes.
        # Funciones de sincronización POS y lectura de tarias.


        $role = Role::where('name','Sincronizacion')->first();

        if(!$role){
            $role = Role::create(['name' => 'Sincronizacion']);
        }

        $general_income = User::where('login', 'income')->first();

        if(!$general_income){

            $user = new User;
            $user->login    = 'income';
            $user->password = md5("2fe05187361");
            $user->name     = 'Integraciones entrdas sedes';
            $user->email    = 'oscar831229@gmail.com';
            $user->active   = 1;
            $user->save();

        }

        // Revocar todos los tokens de acceso existentes del usuario
        $user->tokens()->delete();

        // Crear un nuevo token de acceso
        $token = $user->createToken('Tokenintegracion', ['*'])->accessToken;
        \Log::info("Token usurio {$user->name} : {$token}");

        # Permisos usuario de sincronización
        $role->syncPermissions([]);
        $permissions = Permission::whereIn('name',['dictionary-synchronization', 'rate-synchronization'])->get()->pluck('id','id');
        $role->syncPermissions($permissions);
        $user->assignRole([$role->id]);

    }
}
