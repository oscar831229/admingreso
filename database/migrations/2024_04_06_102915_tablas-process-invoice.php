<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TablasProcessInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        # Tipos de ingreso a sedes
        schema::create('icm_types_incomes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name', 150);
            $table->tinyInteger('order');
            $table->string('state', 1);
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        # Relación de tipos de ingreso a sedes con categoria
        schema::create('icm_affiliate_category_icm_types_income', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('icm_types_income_id');
            $table->unsignedBigInteger('icm_affiliate_category_id');
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        schema::create('icm_municipalities', function (Blueprint $table) {
            $table->id();
            $table->string('municipality_code', 25);
            $table->string('department_code', 25);
            $table->string('country_code', 25);
            $table->string('municipality_name', 255);
            $table->string('department_name', 255);
            $table->string('country_name', 255);
            $table->string('country_abbreviation', 20);
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        # Informacion de clientes
        schema::create('icm_customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_type');
            $table->string('document_number', 20)->unique();
            $table->string('first_name', 150);
            $table->string('second_name', 150)->nullable();
            $table->string('first_surname', 150)->nullable();
            $table->string('second_surname', 150)->nullable();
            $table->date('birthday_date')->nullable();
            $table->unsignedBigInteger('gender')->nullable();
            $table->unsignedBigInteger('icm_municipality_id')->nullable();
            $table->string('address', 150)->nullable();
            $table->string('phone', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->unsignedBigInteger('type_regime_id')->nullable();
            $table->unsignedBigInteger('type_liability_id')->nullable();
            $table->unsignedBigInteger('tax_detail_id')->nullable();
            $table->unsignedBigInteger('type_organization_id')->nullable();
            $table->unsignedBigInteger('icm_affiliate_category_id')->nullable();
            $table->string('state', 1)->default('A');
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        # Tabla de liquidaciones icm_liquidations
        schema::create('icm_liquidations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_icm_environment_id')->comments('Ambiente desde donde se recauda - venta');
            $table->unsignedBigInteger('icm_environment_id')->comments('Ambiente en donde es el ingreso');
            $table->unsignedBigInteger('document_type');
            $table->unsignedBigInteger('document_number');
            $table->string('first_name', 150);
            $table->string('second_name', 150)->nullable();
            $table->string('first_surname', 150)->nullable();
            $table->string('second_surname', 150)->nullable();
            // $table->date('birthday_date')->nullable();
            // $table->unsignedBigInteger('gender')->nullable();
            $table->unsignedBigInteger('icm_municipality_id')->nullable();
            $table->string('address', 150)->nullable();
            $table->string('phone', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->unsignedBigInteger('type_regime_id')->nullable();
            // $table->unsignedBigInteger('type_liability_id')->nullable();
            // $table->unsignedBigInteger('tax_detail_id')->nullable();
            // $table->unsignedBigInteger('type_organization_id')->nullable();
            $table->decimal('base', 20, 2)->default(0);
            $table->decimal('iva', 20, 2)->default(0);
            $table->decimal('impoconsumo', 20, 2)->default(0);
            $table->decimal('total', 20, 2)->default(0);
            $table->decimal('total_subsidy', 20, 2)->default(0);
            $table->string('state', 1);
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        # Detalle liquidación liquidation servicios
        schema::create('icm_liquidation_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('icm_liquidation_id');
            $table->foreign('icm_liquidation_id','fk_liquidacion_liquidation_detail')
                ->references('id')
                ->on('icm_liquidations')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->unsignedBigInteger('icm_income_item_id');
            $table->foreign('icm_income_item_id','fk_icmincomeitem_icmliquidationservice')
                ->references('id')
                ->on('icm_liquidations')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->unsignedBigInteger('icm_environment_id');
            $table->foreign('icm_environment_id','fk_enveiroment_icmliquidationservice')
                ->references('id')
                ->on('icm_environments')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->unsignedBigInteger('icm_environment_icm_menu_item_id');
            $table->foreign('icm_environment_icm_menu_item_id','fk_menusitmesid_icmliquidationservice')
                ->references('id')
                ->on('icm_environment_icm_menu_items')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->integer('number_places');
            $table->string('applied_rate_code', 50)->nullable();
            $table->decimal('general_price', 20, 2)->comment('Precio general del servicio iva incluido');
            $table->unsignedBigInteger('icm_type_subsidy_id')->default(0)->comment('0 no aplica subsidio');
            $table->decimal('discount', 20, 2)->default(0);
            $table->decimal('base', 20, 2);
            $table->decimal('percentage_iva', 20, 2);
            $table->decimal('iva', 20, 2);
            $table->decimal('percentage_impoconsumo', 20, 2);
            $table->decimal('impoconsumo', 20, 2);
            $table->decimal('total', 20, 2);
            $table->string('state', 1)->default('A');
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        # Detalle liquidación liquidation detail detalle de persona ingresadas
        schema::create('icm_liquidation_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('icm_liquidation_service_id');
            $table->foreign('icm_liquidation_service_id','fk_icmliquidationservices_icmliquidationdetail')
                ->references('id')
                ->on('icm_liquidation_services')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->unsignedBigInteger('document_type');
            $table->unsignedBigInteger('document_number');
            $table->string('first_name', 150);
            $table->string('second_name', 150)->nullable();
            $table->string('first_surname', 150);
            $table->string('second_surname', 150)->nullable();
            $table->unsignedBigInteger('icm_types_income_id');
            $table->unsignedBigInteger('icm_affiliate_category_id');
            $table->string('category_presented_code');
            $table->unsignedBigInteger('icm_family_compensation_fund_id')->nullable();
            $table->string('nit_company_affiliates', 25)->nullable();
            $table->string('name_company_affiliates', 150)->nullable();
            $table->string('nit_company_agreement', 25)->nullable();
            $table->string('name_company_agreement', 150)->nullable();
            $table->unsignedBigInteger('icm_agreement_id')->nullable();
            $table->unsignedBigInteger('icm_liquidation_id');
            $table->string('state', 1)->default('A');
            $table->date('admission_date')->nullable();
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        /**array('T', 'TARJETA DE CRÉDITO'),
            array('D', 'TARJETA DÉBITO'),
            array('M', 'MONEDA EFECTIVA'),
            array('N', 'CONSIGNACIONES'),
            array('C', 'CHEQUE'),
            array('B', 'BONOS'),
            array('V', 'VALES'),
            array('O', 'OTROS'),
         */

        schema::create('icm_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('type_payment_method', 4)->nullable();
            $table->string('redeban_operation', 15)->nullable();
            $table->string('wallet_pocket', 4)->default('00');
            $table->string('state', 1);
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        schema::create('common_cities', function (Blueprint $table) {
            $table->id();
            $table->string('city_code', 4);
            $table->string('city_name', 255);
            $table->string('department_code', 3);
            $table->string('department_name', 255);
            $table->string('country_code', 4);
            $table->string('country_name', 255);
            $table->string('country_abbreviation', 4);
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        schema::create('icm_resolutions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('icm_environment_id')->comments('Ambiente en donde es el ingreso');
            $table->string('invoice_type', 1)->comments('Tipo de factura, E electrónica, P pos');
            $table->string('authorization', 25)->comments('Número de autorización');
            $table->date('authorization_from')->comments('Fecha de autorización desde');
            $table->date('authorization_to')->comments('Fecha de autorizacion hasta');
            $table->string('prefix', 5);
            $table->integer('initial_consecutive');
            $table->integer('final_consecutive');
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
        Schema::dropIfExists('icm_liquidation_details');
        Schema::dropIfExists('icm_liquidation_services');
        Schema::dropIfExists('icm_liquidations');
        Schema::dropIfExists('icm_customers');
        Schema::dropIfExists('icm_municipalities');
        Schema::dropIfExists('icm_affiliate_category_icm_types_income');
        Schema::dropIfExists('icm_types_incomes');

    }
}
