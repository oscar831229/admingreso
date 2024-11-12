<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeacPersonas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('sisafi_sync_tracers', function (Blueprint $table) {
            $table->id();
            $table->datetime('start_date');
            $table->datetime('end_date')->nullable();
            $table->date('start_date_synchronization')->nullable();
            $table->date('end_date_synchronization');
            $table->string('type_execution', 1)->comment('A: Automatica, M: Manual');
            $table->string('sync_type', 1)->comment('G: Sincronizacion inicial, I: Incremental dia a dia, M: Manual');
            $table->string('document_number', 20)->nullable();
            $table->string('state')->default('P')->comment('P: Procesando, F: Finalizado');
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        Schema::create('sisafi_seac_personas', function (Blueprint $table) {
            $table->id();
            $table->string('relacion', 2);
            $table->string('tipo_reg', 2);
            $table->bigInteger('consecutivo_dep');
            $table->string('tipo_id', 2);
            $table->string('identificacion', 15);
            $table->string('primer_apellido', 100);
            $table->string('segundo_apellido', 100)->nullable();
            $table->string('primer_nombre', 100);
            $table->string('segundo_nombre', 100)->nullable();
            $table->date('fecha_nacimiento');
            $table->string('genero', 1);
            $table->string('direccion', 150);
            $table->string('barrio', 150);
            $table->Integer('cod_municipio');
            $table->Integer('cod_depto');
            $table->string('celular', 15);
            $table->string('tel_fijo', 15)->nullable();
            $table->string('tipo_persona', 2);
            $table->string('correo', 150)->nullable();
            $table->string('vinculacion', 1);
            $table->string('categoria', 1);
            $table->string('fuente_creacion', 50);
            $table->date('fecha_creacion');
            $table->string('fuente_actualizacion', 50);
            $table->date('fecha_actualizacion');
            $table->Integer('consecutivo_ppal');
            $table->string('tipoid_ppal', 2);
            $table->string('id_principal', 15);
            $table->string('primer_apellido_ppal', 100);
            $table->string('segundo_apellido_ppal', 100)->nullable();
            $table->string('primer_nombre_ppal', 100);
            $table->string('segundo_nombre_ppal', 100)->nullable();
            $table->string('nombre_ppal', 255);
            $table->string('tipo_id_empresa', 2);
            $table->string('nit_empresa', 20);
            $table->string('razon_social', 150);
            $table->string('estado_afi', 1);
            $table->date('fecha_ret')->nullable();
            $table->unsignedBigInteger('sisafi_sync_tracer_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('sisafi_sync_tracers');
        // Schema::dropIfExists('sisafi_seac_personas');
    }
}
