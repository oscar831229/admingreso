<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Definitions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('definitions', function (Blueprint $table) {

            $table->id();
            $table->string('code', 150);
            $table->string('name');
            $table->string('details');

            $table->unsignedBigInteger('user_created');

            $table->foreign('user_created', 'fk_definitions_users_created')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->unsignedBigInteger('user_updated')->nullable();

            $table->foreign('user_updated', 'fk_definitions_users_updated')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->timestamps();

        });

        Schema::create('detail_definitions', function (Blueprint $table) {

            $table->id();
            $table->string('code', 30);
            $table->string('alternative_code', 30)->nullable()->comment('Se utiliza como alterno con el fin de realizar homologacion del tipo de documento');
            $table->string('name');
            $table->string('details');

            $table->unsignedBigInteger('definition_id');
            $table->foreign('definition_id', 'fk_detaildefinitions_definitions_id')
                ->references('id')
                ->on('definitions')
                ->onDelete('restrict');

            $table->unsignedBigInteger('user_created');

            $table->foreign('user_created', 'fk_detaildefinitions_users_created')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->unsignedBigInteger('user_updated')->nullable();

            $table->foreign('user_updated', 'fk_detaildefinitions_users_updated')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

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
        Schema::dropIfExists('detail_definitions');
        Schema::dropIfExists('definitions');
    }
}
