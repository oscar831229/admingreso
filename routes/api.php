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

    Route::post('login' , 'AuthController@login');
    Route::post('signup', 'AuthController@signUp')->middleware(['auth.token']);

    Route::post('token' , 'AuthController@login');

    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user'  , 'AuthController@user');
    });
});



# API V1
Route::group([
    'prefix' => 'v1',
    'middleware' => 'auth:api'
], function () {

    # SERVICIOS  TIQUETERA ELECTRÓNICA
    Route::group([
        'prefix'     => 'income',
        'middleware' => 'auth:api'
    ], function () {

        # Sincronización procesos
        Route::apiResource('synchronization', 'income\SynchronizationController');

        Route::get('token-validation', 'income\SynchronizationController@tokenValidation');

        Route::post('affiliate-family-group', 'income\SisafiConsultationController@getAffiliateCategory');

    });

    Route::post('/src1/v1/pack_funciones_cobertura/f_afiliado_ws_recrea', 'income\SisafiConsultationController@getAffiliateCategory');


});


Route::group([
    'prefix' => 'src1',
    'middleware' => 'auth:api'
], function () {

    Route::post('v1/pack_funciones_cobertura/f_afiliado_ws_recrea', 'income\SisafiConsultationController@getAffiliateCategory');

});
