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
                'name' => 'his-schedule-index',
                'module' => 'system-his',
                'descripcion' => 'Consultar agenda clinica HIS (INDIGO)' 
            ],
            [
                'name' => 'his-schedule-show',
                'module' => 'system-his',
                'descripcion' => 'Visualizar agenda clinica HIS (INDIGO)' 
            ],
            [
                'name' => 'his-schedule-created',
                'module' => 'system-his',
                'descripcion' => 'Crear agenda clinica HIS (INDIGO)' 
            ],
            [
                'name' => 'his-schedule-update',
                'module' => 'system-his',
                'descripcion' => 'Actualizar agenda clinica HIS (INDIGO)' 
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