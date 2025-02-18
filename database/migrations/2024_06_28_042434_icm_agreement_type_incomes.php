<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IcmAgreementTypeIncomes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('icm_agreement_type_incomes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('icm_agreement_id');
            $table->foreign('icm_agreement_id','fk_agreement_type_incomes_1')
                ->references('id')
                ->on('icm_agreements')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->unsignedBigInteger('icm_types_income_id');
            $table->foreign('icm_types_income_id','fk_agreement_type_incomes_2')
                ->references('id')
                ->on('icm_types_incomes')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->unsignedBigInteger('icm_affiliate_category_id');
            $table->foreign('icm_affiliate_category_id','fk_agreement_type_incomes_3')
                ->references('id')
                ->on('icm_affiliate_categories')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->string('state', 1)->default('A');
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
	    $table->unique(['icm_agreement_id', 'icm_types_income_id', 'icm_affiliate_category_id'], 'unique_convenio_afiliacion_categoria');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('icm_agreement_type_incomes');
    }
}
