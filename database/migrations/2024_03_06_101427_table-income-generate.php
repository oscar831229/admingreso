<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableIncomeGenerate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('icm_environments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('state', 2);
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        Schema::create('icm_environment_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('icm_environment_id');
            $table->foreign('icm_environment_id','fk_icmenvironment_user')
                ->references('id')
                ->on('icm_environments')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id','fk_icmenvironment_user_userid')
                ->references('id')
                ->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        Schema::create('icm_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('requested_name', 150);
            $table->string('state', 2);
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        Schema::create('icm_menu_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('icm_menu_id');
            $table->foreign('icm_menu_id','fk_icmmenu_icmmenuitems')
                ->references('id')
                ->on('icm_menus')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->string('name', 150);
            $table->string('requested_name', 150);
            $table->string('barcode', 25)->nullable();
            $table->decimal('value', 20, 2)->nullable()->default(0);
            $table->decimal('percentage_iva', 5, 2)->default(0);
            $table->decimal('percentage_impoconsumo', 5, 2)->default(0);
            $table->string('state', 2);
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        Schema::create('icm_environment_icm_menu_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('icm_environment_id');
            $table->foreign('icm_environment_id','fk_icmenvironment_icmenvironmentsmenusitems')
                ->references('id')
                ->on('icm_environments')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->unsignedBigInteger('icm_menu_item_id');
            $table->foreign('icm_menu_item_id','fk_icmmenuitem_icmenvironmentsmenusitem')
                ->references('id')
                ->on('icm_menu_items')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->decimal('value', 20, 2);
            $table->string('state', 2);
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        Schema::create('icm_environment_income_items', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('code', 15);
            $table->integer('number_places');
            $table->decimal('value', 20, 2);
            $table->text('observations')->nullable();

            $table->unsignedBigInteger('icm_environment_id');
            $table->foreign('icm_environment_id','fk_icmenvironment')
                ->references('id')
                ->on('icm_environments')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->unsignedBigInteger('icm_environment_icm_menu_item_id');
            $table->foreign('icm_environment_icm_menu_item_id','fk_icmenvironmenticmmenuitem')
                ->references('id')
                ->on('icm_environment_icm_menu_items')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->string('state', 2);
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        schema::create('icm_affiliate_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name' , 150);
            $table->string('code' , 2  );
            $table->string('state', 1  )->default('A');
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        schema::create('icm_environment_income_item_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('icm_environment_income_item_id');
            $table->unsignedBigInteger('types_of_income_id');
            $table->unsignedBigInteger('icm_affiliate_category_id');
            $table->decimal('value', 20, 2);
            $table->string('state', 1  )->default('A');
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        schema::create('icm_companies_agreements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_type');
            $table->unsignedBigInteger('document_number');
            $table->string('name', 150);
            $table->string('phone', 15);
            $table->string('address', 255);
            $table->string('email', 150);
            $table->string('state', 1  )->default('A');
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });


        schema::create('icm_agreements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('icm_environment_id');
            $table->foreign('icm_environment_id','fk_icmenvironments_icmagreements')
                ->references('id')
                ->on('icm_environments')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->unsignedBigInteger('icm_companies_agreement_id');
            $table->foreign('icm_companies_agreement_id','fk_icmcompaniesagreement_icmagreements')
                ->references('id')
                ->on('icm_companies_agreements')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->date('date_from');
            $table->date('date_to');
            $table->text('observations');
            $table->string('state', 1  )->default('A');
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        schema::create('icm_agreement_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('icm_agreement_id');
            $table->foreign('icm_agreement_id','fk_icmagreement_icmagreementdetails')
                ->references('id')
                ->on('icm_agreements')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->unsignedBigInteger('icm_environment_income_item_id');
            $table->foreign('icm_environment_income_item_id','fk_icmenvironmentincomeitem_icmagreementdetails')
                ->references('id')
                ->on('icm_environment_income_items')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->decimal('value', 20, 2);
            $table->string('state', 1  )->default('A');
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
        Schema::dropIfExists('icm_environment_user');
        Schema::dropIfExists('icm_environments');

    }
}
