<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SystemConfiguration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('icm_system_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('url_pos_system', 255)->nullable();
            $table->string('pos_system_token', 255)->nullable();
            $table->date('system_date')->nullable();
            $table->tinyInteger('policy_enabled')->default(0)->comment('Campo que identifica si la politica de reportar todo el grupo esta activa coberturas');
            $table->string('infrastructure_code', 100)->comment('Código de infraestructura');
            $table->string('system_names')->comment('Nombre del sistema');
            $table->string('state', 1)->default('A');
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
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
        Schema::dropIfExists('icm_system_configuration');
    }
}
