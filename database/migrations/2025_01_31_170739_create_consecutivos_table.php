<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsecutivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('icm_consecutives', function (Blueprint $table) {
            $table->id();
            $table->year('validity');
            $table->integer('consecutive')->default(1);
            $table->timestamps();
            $table->unique(['validity']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('icm_consecutives');
    }
}
