<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EmailTraceability extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_traceabilities', function (Blueprint $table) {
            $table->id();
            $table->string('process_code', 100);
            $table->string('where');
            $table->string('destination');
            $table->text('attachments');
            $table->string('send', 1)->nullable()->default('N');
            $table->text('error')->nullable();
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable(); 
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
        Schema::dropIfExists('email_traceabilities');
    }
}
