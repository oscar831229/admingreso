<?php


use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;


class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $permissions = [
            [
                'name' => 'role-list',
                'module' => 'core-role',
                'descripcion' => 'Permisos listar roles'
            ],
            [
                'name' => 'role-create',
                'module' => 'core-role',
                'descripcion' => 'Permisos crear roles'
            ],
            [
                'name' => 'role-edit',
                'module' => 'core-role',
                'descripcion' => 'Permisos editar roles'
            ],
            [
                'name' => 'user-list',
                'module' => 'core-user',
                'descripcion' => 'Permisos listar usuarios'
            ],
            [
                'name' => 'user-create',
                'module' => 'core-user',
                'descripcion' => 'Permisos crear usuarios'
            ],
            [
                'name' => 'user-edit',
                'module' => 'core-user',
                'descripcion' => 'Permisos editar usuarios'
            ],
            [
                'name' => 'user-generate-token',
                'module' => 'core-user',
                'descripcion' => 'Generar token a usuario'
            ],
            [
                'name' => 'menu-rol-list',
                'module' => 'core-user',
                'descripcion' => 'Permisos listar menu rol'
            ],
            [
                'name' => 'emails-list',
                'module' => 'core-user',
                'descripcion' => 'Listar emails'
            ],
            [
                'name' => 'emails-create',
                'module' => 'core-user',
                'descripcion' => ''
            ],
            [
                'name' => 'emails-edit',
                'module' => 'core-user',
                'descripcion' => 'Editar emails'
            ],
            [
                'name' => 'emails-delete',
                'module' => 'core-user',
                'descripcion' => 'Borrar emails'
            ],
            [
                'name' => 'plantillas-create',
                'module' => 'core-user',
                'descripcion' => 'Crear plantillas'
            ],
            [
                'name' => 'plantillas-edit',
                'module' => 'core-user',
                'descripcion' => 'Editar plantilla'
            ],
            [
                'name' => 'menu-rol-edit',
                'module' => 'core-user',
                'descripcion' => 'Permisos editar menu rol'
            ],
            [
                'module'      => 'api-services',
                'name'        => 'dictionary-synchronization',
                'descripcion' => 'Permisos sincronización de datos compartidos POS'
            ],
            [
                'module'      => 'api-services',
                'name'        => 'rate-synchronization',
                'descripcion' => 'Permisos sincronización de tarifas con centralizador'
            ]
        ];


        foreach ($permissions as $permission) {

            $permision = Permission::where('name', $permission['name'])->first();

            if(!$permision){
                Permission::create($permission);
            }


        }
    }
}
