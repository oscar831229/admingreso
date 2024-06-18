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

    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user'  , 'AuthController@user');
    });
});



# API V1
Route::group([
    'prefix' => 'v1'
], function () {

    # SERVICIOS  TIQUETERA ELECTRÓNICA
    Route::group([
        'prefix'     => 'income',
        'middleware' => 'auth:api'
    ], function () {

        # Sincronización procesos
        Route::apiResource('synchronization', 'income\SynchronizationController');

        Route::get('token-validation', 'income\SynchronizationController@tokenValidation');



    });




});
