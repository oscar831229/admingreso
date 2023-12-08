<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableHistoryToken extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_user_token_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wallet_user_id');
            $table->foreign('wallet_user_id','fk_wallet_users_wuth_id')
                ->references('id')
                ->on('wallet_users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->string('email', 255);
            $table->string('token', 255);
            $table->string('user_code', 150)->nullable();
            $table->string('store_code', 20)->nullable();
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        schema::create('wallet_user_email_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wallet_user_id');
            $table->foreign('wallet_user_id','fk_wallet_users_wueh_id')
                ->references('id')
                ->on('wallet_users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->string('email', 255);
            $table->string('user_code', 150)->nullable();
            $table->string('store_code', 20)->nullable();
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
        Schema::dropIfExists('wallet_user_token_histories');
        Schema::dropIfExists('wallet_user_email_histories');
    }
}
