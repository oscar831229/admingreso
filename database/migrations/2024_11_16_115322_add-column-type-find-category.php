<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnTypeFindCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('icm_system_configurations', function (Blueprint $table) {
            // Agregar el campo query_type_category como ENUM
            $table->enum('query_type_category', ['local', 'servicio'])
                  ->default('local'); // Opcional: asigna un valor por defecto
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
            // Eliminar el campo query_type_category
            $table->dropColumn('query_type_category');
        });
    }
}
