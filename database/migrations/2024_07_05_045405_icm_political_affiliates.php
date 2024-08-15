<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IcmPoliticalAffiliates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('icm_political_affiliates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('icm_income_item_id');
            $table->string('type_register', 4);
            $table->string('relationship', 4);
            $table->string('type_link', 4);
            $table->string('type_sublink', 4)->nullable();
            $table->unsignedBigInteger('document_type');
            $table->string('document_number', 20);
            $table->string('first_name', 100);
            $table->string('second_name', 100)->nullable();
            $table->string('first_surname', 100);
            $table->string('second_surname', 100)->nullable();
            $table->date('birthday_date');
            $table->unsignedBigInteger('gender');
            $table->unsignedBigInteger('icm_affiliate_category_id');
            $table->unsignedBigInteger('affiliated_type_document')->nullable();
            $table->string('affiliated_document', 20);
            $table->string('affiliated_name', 150);
            $table->string('nit_company_affiliates', 20);
            $table->string('name_company_affiliates', 150);
            $table->date('political_date');
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
        Schema::dropIfExists('icm_political_affiliates');
    }
}
