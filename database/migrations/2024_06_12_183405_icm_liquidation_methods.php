<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IcmLiquidationMethods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        schema::create('icm_liquidation_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('icm_liquidation_id');
            $table->foreign('icm_liquidation_id','fk_liqui_payment_id')
                ->references('id')
                ->on('icm_liquidations')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->unsignedBigInteger('icm_payment_method_id');
            $table->foreign('icm_payment_method_id','fk_method_payment_id')
                ->references('id')
                ->on('icm_payment_methods')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->decimal('value', 20, 2);
            $table->date('approval_date')->nullable();
            $table->string('approval_number')->nullable();
            $table->string('redeban', 100)->nullable();
            $table->string('state', 1)->default('A');
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
        Schema::dropIfExists('icm_liquidation_payments');
    }
}
