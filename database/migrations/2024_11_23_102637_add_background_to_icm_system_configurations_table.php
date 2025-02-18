<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBackgroundToIcmSystemConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('icm_system_configurations', function (Blueprint $table) {
            $table->longText('background')->nullable(); // Campo para almacenar la imagen en Base64
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('icm_system_configurations', function (Blueprint $table) {
            $table->dropColumn('background'); // Eliminar la columna si se revierte la migración
        });
    }
}
