<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueIndexToSisafiPersonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sisafi_seac_personas', function (Blueprint $table) {
            $table->unique(['tipo_id', 'identificacion']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sisafi_seac_personas', function (Blueprint $table) {
            $table->dropUnique(['tipo_id', 'identificacion']);
        });
    }
}
