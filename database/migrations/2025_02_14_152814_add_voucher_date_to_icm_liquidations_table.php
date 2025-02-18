<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVoucherDateToIcmLiquidationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('icm_liquidations', function (Blueprint $table) {
            $table->dateTime('voucher_date')->nullable()->after('consecutive_billing');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('icm_liquidations', function (Blueprint $table) {
            $table->dropColumn('voucher_date');
        });
    }
}
