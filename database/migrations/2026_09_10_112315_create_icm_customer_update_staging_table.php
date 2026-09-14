<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIcmCustomerUpdateStagingTable extends Migration
{
    public function up()
    {
        Schema::create('icm_customer_update_staging', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('import_id');
            $table->unsignedInteger('row_number');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('document_type_raw', 100)->nullable();
            $table->unsignedBigInteger('document_type_id')->nullable();
            $table->string('document_number', 100)->nullable();
            $table->string('first_surname', 500)->nullable();
            $table->string('second_surname', 500)->nullable();
            $table->string('first_name', 500)->nullable();
            $table->string('second_name', 500)->nullable();
            $table->string('birthday_date_raw', 100)->nullable();
            $table->date('birthday_date')->nullable();
            $table->string('gender_raw', 100)->nullable();
            $table->unsignedBigInteger('gender_id')->nullable();
            $table->text('address')->nullable();
            $table->text('email')->nullable();
            $table->string('status', 20)->default('READY');
            $table->text('error_message')->nullable();
            $table->index(['import_id', 'status']);
            $table->index(['import_id', 'document_number']);
            $table->index(['import_id', 'customer_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('icm_customer_update_staging');
    }
}
