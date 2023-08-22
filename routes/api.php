<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signUp')->middleware(['auth.token']);
  
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});



# API V1 -> PLANTILLAS DE TURNO
Route::group([
    'prefix' => 'v1'
], function () {

    # SERVICIOS  BILLETERA ELECTRÓNICA
    Route::group([
        'prefix' => 'wallet',
        'middleware' => 'auth:api'
    ], function () {

        # Consutla definiciones
        Route::apiResource('listings', 'wallet\ListingController');
               
        # Consulta de agendas medicas parametrizadas en indigo
        Route::apiResource('transaction', 'wallet\TransactionsController');
        Route::post('transaction/{movement_type}', 'wallet\TransactionsController@store');

        # Validar datos transaccion de abono 
        Route::post('validate-transaction/{movement_type}', 'wallet\TransactionsController@validateTransaction');
        
        
        # Consulta de parametros
        Route::apiResource('wallet-users', 'wallet\WalletUsersController');
        Route::post('wallet-user-transactions', 'wallet\WalletUsersController@getTransactions');

        # REGENERAR TOKEN Y NOTIFICARLOS
        Route::get('generate-token/{document_number}', 'wallet\WalletUsersController@generateToken');

        # CONSULTAR SALDO BILLETERA Y TIQUETERA DISPONIBLE
        Route::get('electronic-wallet-balance/{document_number}/{pocket}', 'wallet\WalletUsersController@getElectronicWalletBalance');

        Route::get('print-voucher-wallet/{document_number}/{cus}', 'wallet\TransactionsController@printVoucherWallet');

    });




});