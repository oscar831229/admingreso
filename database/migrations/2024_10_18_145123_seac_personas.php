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
            $table->string('type_synchronization', 1)->comment('T: Total, I: Individual')->default('T');
            $table->string('type_execution', 1)->comment('A: Automatica, M: Manual');
            $table->string('type_document', 3)->nullable();
            $table->string('document_number', 20)->nullable();
            $table->string('state')->default('P')->comment('P: Procesando, F: Finalizado, B: Finalizado con error');
            $table->Text('errors')->nullable();
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        Schema::create('sisafi_seac_personas', function (Blueprint $table) {
            $table->id();
            $table->string('relacion', 2)->nullable();
            $table->string('tipo_reg', 2)->nullable();
            $table->bigInteger('consecutivo_dep')->nullable();
            $table->string('tipo_id', 2);
            $table->string('identificacion', 15);
            $table->string('primer_apellido', 100)->nullable();
            $table->string('segundo_apellido', 100)->nullable();
            $table->string('primer_nombre', 100);
            $table->string('segundo_nombre', 100)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('genero', 1)->nullable();
            $table->string('direccion', 150)->nullable();
            $table->string('barrio', 150)->nullable();
            $table->Integer('cod_municipio')->nullable();
            $table->Integer('cod_depto')->nullable();
            $table->string('celular', 15)->nullable();
            $table->string('tel_fijo', 15)->nullable();
            $table->string('tipo_persona', 2)->nullable();
            $table->string('correo', 150)->nullable();
            $table->string('vinculacion', 1)->nullable();
            $table->Integer('subvinculacion')->nullable();
            $table->string('categoria', 1)->nullable();
            $table->string('fuente_creacion', 50)->nullable();
            $table->date('fecha_creacion')->nullable();
            $table->string('fuente_actualizacion', 50)->nullable();
            $table->date('fecha_actualizacion')->nullable();
            $table->Integer('consecutivo_ppal')->nullable();
            $table->string('tipoid_ppal', 2)->nullable();
            $table->string('id_principal', 15)->nullable();
            $table->string('primer_apellido_ppal', 100)->nullable();
            $table->string('segundo_apellido_ppal', 100)->nullable();
            $table->string('primer_nombre_ppal', 100)->nullable();
            $table->string('segundo_nombre_ppal', 100)->nullable();
            $table->string('nombre_ppal', 255)->nullable();
            $table->string('tipo_id_empresa', 2)->nullable();
            $table->string('nit_empresa', 20)->nullable();
            $table->string('razon_social', 150)->nullable();
            $table->string('estado_afi', 1)->nullable();
            $table->date('fecha_ret')->nullable();
            $table->unsignedBigInteger('sisafi_sync_tracer_id')->nullable();
            $table->timestamps();
            $table->index(['tipo_id', 'identificacion']);
            $table->index(['tipoid_ppal', 'id_principal']);
        });

        Schema::create('sisafi_seac_temporal', function (Blueprint $table) {
            $table->id();
            $table->string('relacion', 2)->nullable();
            $table->string('tipo_reg', 2)->nullable();
            $table->bigInteger('consecutivo_dep')->nullable();
            $table->string('tipo_id', 2);
            $table->string('identificacion', 15);
            $table->string('primer_apellido', 100)->nullable();
            $table->string('segundo_apellido', 100)->nullable();
            $table->string('primer_nombre', 100);
            $table->string('segundo_nombre', 100)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('genero', 1)->nullable();
            $table->string('direccion', 150)->nullable();
            $table->string('barrio', 150)->nullable();
            $table->Integer('cod_municipio')->nullable();
            $table->Integer('cod_depto')->nullable();
            $table->string('celular', 15)->nullable();
            $table->string('tel_fijo', 15)->nullable();
            $table->string('tipo_persona', 2)->nullable();
            $table->string('correo', 150)->nullable();
            $table->string('vinculacion', 1)->nullable();
            $table->Integer('subvinculacion')->nullable();
            $table->string('categoria', 1)->nullable();
            $table->string('fuente_creacion', 50)->nullable();
            $table->date('fecha_creacion')->nullable();
            $table->string('fuente_actualizacion', 50)->nullable();
            $table->date('fecha_actualizacion')->nullable();
            $table->Integer('consecutivo_ppal')->nullable();
            $table->string('tipoid_ppal', 2)->nullable();
            $table->string('id_principal', 15)->nullable();
            $table->string('primer_apellido_ppal', 100)->nullable();
            $table->string('segundo_apellido_ppal', 100)->nullable();
            $table->string('primer_nombre_ppal', 100)->nullable();
            $table->string('segundo_nombre_ppal', 100)->nullable();
            $table->string('nombre_ppal', 255)->nullable();
            $table->string('tipo_id_empresa', 2)->nullable();
            $table->string('nit_empresa', 20)->nullable();
            $table->string('razon_social', 150)->nullable();
            $table->string('estado_afi', 1)->nullable();
            $table->date('fecha_ret')->nullable();
            $table->unsignedBigInteger('sisafi_sync_tracer_id')->nullable();
            $table->timestamps();
            $table->index(['tipo_id', 'identificacion']);
            $table->index(['tipoid_ppal', 'id_principal']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sisafi_sync_tracers');
        Schema::dropIfExists('sisafi_seac_personas');
        Schema::dropIfExists('sisafi_seac_temporal');
    }
}
