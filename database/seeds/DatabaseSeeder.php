<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        # PERMISOS
        $this->call(PermissionTableSeeder::class);

        # CREA USUARIO ADMINISTRADOR DE SOLUCIONES
        $this->call(CreateAdminUserSeeder::class);

        # CREAR MENU APLICACIÓN
        $this->call(CrearMenuSeeder::class);

        # CREAR PLANTILLAS EMAIL
        $this->call(CrearPlantillas::class);
        
		# CREAR DEFINICIONES
        $this->call(DefinitionsSeeder::class);

    }
}
