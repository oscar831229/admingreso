<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIcmCustomerExportsTable extends Migration
{
    public function up()
    {
        Schema::create('icm_customer_exports',function(Blueprint $table){
            $table->bigIncrements('id');
            $table->string('file_name',255)->nullable();
            $table->string('file_path',500)->nullable();
            $table->string('status',20)->default('PENDING');
            $table->unsignedBigInteger('total_rows')->default(0);
            $table->unsignedBigInteger('processed_rows')->default(0);
            $table->unsignedTinyInteger('progress')->default(0);
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('user_created');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->timestamps();
            $table->index(['user_created','status']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('icm_customer_exports');
    }
}
