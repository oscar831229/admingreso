<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalsToSisafiSyncTracersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sisafi_sync_tracers', function (Blueprint $table) {
            $table->bigInteger('total_records')->default(0);
            $table->bigInteger('total_processed')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sisafi_sync_tracers', function (Blueprint $table) {
            $table->dropColumn(['total_records', 'total_processed']);
        });
    }
}
