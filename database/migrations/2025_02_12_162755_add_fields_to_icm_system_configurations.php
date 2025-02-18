<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToIcmSystemConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('icm_system_configurations', function (Blueprint $table) {
            // Añadir los nuevos campos
            $table->string('company_name', 150)->nullable();  // Campo company_name
            $table->unsignedBigInteger('document_type')->nullable();  // Campo document_type
            $table->string('identification_number', 20)->nullable();  // Campo identification_number
            $table->string('address', 150)->nullable();  // Campo address
            $table->string('phone', 15)->nullable();  // Campo phone
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
            // Eliminar los campos en caso de revertir la migración
            $table->dropColumn('company_name');
            $table->dropColumn('document_type');
            $table->dropColumn('identification_number');
            $table->dropColumn('address');
            $table->dropColumn('phone');
        });
    }
}
