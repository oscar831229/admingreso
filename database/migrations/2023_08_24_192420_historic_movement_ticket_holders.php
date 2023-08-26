<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HistoricMovementTicketHolders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historic_movement_ticket_holders', function (Blueprint $table) {
            $table->id();
            $table->string('movement_id', 100);
            $table->string('wallet_user_ticket_id');
            $table->string('number_ticket', 20);
            $table->decimal('value', 20,2)->nullable();
            $table->string('state', 2)->default('P');
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
        Schema::dropIfExists('historic_movement_ticket_holders');
    }
}
