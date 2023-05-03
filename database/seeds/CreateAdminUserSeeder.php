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
            
            $user = new User;
            $user->login = 'admin';
            $user->password = md5("2fe05187361");
            $user->name = 'Administrador de soluciones';
            $user->email = 'oscar831229@hotmail.com';
            $user->active = 1;
            $user->save();

        }

        $role = Role::where('name','Administrador')->first();

        if(!$role){
            $role = Role::create(['name' => 'Administrador']);
        }

        $permissions = Permission::pluck('id','id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
   
    }
}