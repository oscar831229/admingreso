<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSearchIndexesToIcmCustomersTable extends Migration
{
    public function up()
    {
        Schema::table('icm_customers', function (Blueprint $table) {
            $table->index('first_name', 'idx_icm_customers_first_name');
            $table->index('second_name', 'idx_icm_customers_second_name');
            $table->index('first_surname', 'idx_icm_customers_first_surname');
            $table->index('second_surname', 'idx_icm_customers_second_surname');
            $table->index('phone', 'idx_icm_customers_phone');
            $table->index('email', 'idx_icm_customers_email');
        });
    }

    public function down()
    {
        Schema::table('icm_customers', function (Blueprint $table) {
            $table->dropIndex('idx_icm_customers_first_name');
            $table->dropIndex('idx_icm_customers_second_name');
            $table->dropIndex('idx_icm_customers_first_surname');
            $table->dropIndex('idx_icm_customers_second_surname');
            $table->dropIndex('idx_icm_customers_phone');
            $table->dropIndex('idx_icm_customers_email');
        });
    }
}
