<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModuloBelectronica extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('code', '15');
            $table->string('name');
            $table->string('address');
            $table->string('phone', '15');
            $table->string('state', '1');
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable(); 
            $table->timestamps();
            $table->unique(['code']);
        });

        Schema::create('store_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable(); 
            $table->timestamps();
            $table->unique(['user_id', 'store_id']);
        });

        Schema::create('wallet_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('identification_document_type_id');
            $table->string('document_number', 20);
            $table->string('first_name', 100);
            $table->string('second_name', 100);
            $table->string('first_surname', 100);
            $table->string('second_surname', 100);
            $table->string('email', 150);
            $table->string('phone', 15);
            $table->string('uuid', 150);
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable(); 
            $table->timestamps();
            $table->unique(['document_number']);
        });

        Schema::create('history_uuids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wallet_user_id');

            $table->foreign('wallet_user_id','fk_wallet_users_historyuuids_id')
                ->references('id')
                ->on('wallet_users')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->string('uuid', 150);    
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable(); 
            $table->timestamps();
        });


        Schema::create('electrical_pockets', function (Blueprint $table) {
            $table->id();
            $table->string('code', '15');
            $table->string('name', '75');
            $table->text('description');
            $table->boolean('main');
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable(); 
            $table->timestamps();
            $table->unique(['code']);
        });
        
        

        Schema::create('wallet_user_electronic_pockets', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('electronic_pocket_id');

            $table->foreign('electronic_pocket_id','fk_electronicpockets_wselectronicpockets_id')
                ->references('id')
                ->on('electrical_pockets')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->decimal('balance', 20,2);
            $table->unsignedBigInteger('wallet_user_id');

            $table->foreign('wallet_user_id','fk_wallet_users_electricalpockets_id')
                ->references('id')
                ->on('wallet_users')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->date('last_movement_date');

            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable(); 
            $table->timestamps();
            $table->unique(['electronic_pocket_id', 'wallet_user_id'], 'unique_walletuserelectronicpockets_epid_wuid');
        });

        Schema::create('movement_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', '15');
            $table->string('name', 100);
            $table->text('observation');
            $table->string('nature_movement', 1);
            $table->string('state', 1);
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable(); 
            $table->timestamps();
            $table->unique(['code']);
        });

        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('electrical_pocket_id');

            $table->foreign('electrical_pocket_id','fk_electricalpocket_movements_id')
                ->references('id')
                ->on('electrical_pockets')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->unsignedBigInteger('transaction_document_type_id');
            $table->string('document_number', 20);
            $table->unsignedBigInteger('movement_type_id');
            $table->foreign('movement_type_id','fk_movementtypes_movements_id')
                ->references('id')
                ->on('movement_types')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->unsignedBigInteger('nature_movement');
            $table->decimal('value', 20,2);
            $table->unsignedBigInteger('user_code');

            $table->unsignedBigInteger('store_id');
            $table->foreign('store_id','fk_stores_movements_id')
                ->references('id')
                ->on('stores')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->string('cus', 15);
            $table->string('cus_transaction', 15);
            $table->date('movement_date');
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable(); 
            $table->timestamps();
            $table->unique(['cus']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movements');
        Schema::dropIfExists('movement_types');
        Schema::dropIfExists('wallet_user_electronic_pockets');
        Schema::dropIfExists('electrical_pockets');
        Schema::dropIfExists('history_uuids');
        Schema::dropIfExists('wallet_users');
        Schema::dropIfExists('store_user');
        Schema::dropIfExists('stores');
    }
}