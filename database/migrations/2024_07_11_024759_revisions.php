<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Revisions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        # Detalle liquidación liquidation detail detalle de persona ingresadas
        schema::create('icm_liquidacion_detail_revisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('icm_coverage_id');
            $table->string('step', 10);
            $table->unsignedBigInteger('icm_liquidation_service_id');
            $table->unsignedBigInteger('icm_liquidation_detail_id');
            $table->tinyInteger('is_processed_affiliate')->comment('Identifica si los datos se completaron con sisafi, aplica solo para afilaidos coberturas');
            $table->string('type_register', 2)->nullable();
            $table->string('relationship', 4)->nullable();
            $table->string('type_link', 4)->nullable();
            $table->unsignedBigInteger('affiliated_type_document')->nullable();
            $table->string('affiliated_document_number', 45)->nullable();
            $table->string('affiliated_name', 255)->nullable();
            $table->unsignedBigInteger('document_type');
            $table->unsignedBigInteger('document_number');
            $table->string('first_name', 150);
            $table->string('second_name', 150)->nullable();
            $table->string('first_surname', 150);
            $table->string('second_surname', 150)->nullable();
            $table->string('business_name', 150)->nullable();
            $table->date('birthday_date')->nullable();
            $table->unsignedBigInteger('gender')->nullable();
            $table->string('address', 150)->nullable();
            $table->unsignedBigInteger('icm_municipality_id')->nullable();
            $table->string('phone', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->decimal('code_seac', 12, 0);
            $table->unsignedBigInteger('icm_income_item_id');
            $table->string('icm_income_item_code', 15);
            $table->string('infrastructure_code', 100)->comment('Código de infraestructura');
            $table->date('liquidation_date');
            $table->string('nit_company_affiliates', 25)->nullable();
            $table->string('name_company_affiliates', 150)->nullable();
            $table->unsignedBigInteger('icm_types_income_id');
            $table->unsignedBigInteger('icm_affiliate_category_id');
            $table->string('category_presented_code');
            $table->decimal('total', 20, 2)->default(0);
            $table->unsignedBigInteger('icm_type_subsidy_id');
            $table->decimal('subsidy', 20, 2)->default(0);
            $table->integer('number_places');
            $table->unsignedBigInteger('icm_family_compensation_fund_id')->nullable();
            $table->string('system_names')->comment('Nombre del sistema');
            $table->unsignedBigInteger('icm_liquidation_id');
            $table->string('billing_prefix', 10)->nullable();
            $table->unsignedBigInteger('consecutive_billing')->nullable();
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
        Schema::dropIfExists('icm_liquidacion_detail_revisions');
    }
}
