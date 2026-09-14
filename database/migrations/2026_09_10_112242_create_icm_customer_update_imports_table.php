<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIcmCustomerUpdateImportsTable extends Migration
{
    public function up()
    {
        Schema::create('icm_customer_update_imports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('original_name', 255);
            $table->string('file_path', 500);
            $table->char('delimiter', 1)->default(';');
            $table->string('status', 30)->default('PENDING');
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('processed_rows')->default(0);
            $table->unsignedInteger('valid_rows')->default(0);
            $table->unsignedInteger('error_rows')->default(0);
            $table->unsignedInteger('updated_rows')->default(0);
            $table->unsignedTinyInteger('progress')->default(0);
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('user_created');
            $table->dateTime('validation_started_at')->nullable();
            $table->dateTime('validated_at')->nullable();
            $table->dateTime('apply_started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('user_created');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('icm_customer_update_imports');
    }
}
