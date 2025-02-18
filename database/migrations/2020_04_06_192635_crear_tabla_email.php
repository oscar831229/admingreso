<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrearTablaEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->string('server',255);  
            $table->string('encryption',3);
            $table->string('puerto',10);
            $table->string('email',150);
            $table->string('password',150);
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
        });


        Schema::create('plantillas_email', function (Blueprint $table) {
            $table->id();
            $table->string('codigo',255);
            $table->string('nombre',255)->nullable();
            $table->string('asunto',255)->nullable();
            $table->longText('mensaje')->nullable();
            $table->unsignedBigInteger('emails_id')->nullable();
            $table->foreign('emails_id','fk_plantillasemail_emails')
                ->references('id')
                ->on('emails')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plantillas_email');
        Schema::dropIfExists('emails');
    }
}
