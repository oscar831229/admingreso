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

    # VIE - MODULO NOMINA
    Route::group([
        'prefix' => 'vie/payroll',
        'middleware' => 'auth:api'
    ], function () {
        Route::apiResource('schedule-shifts', 'Payroll\ScheduleShiftController');
    });


    # CRISTAL
    Route::group([
        'prefix' => 'his/scheduling',
        'middleware' => 'auth:api'
    ], function () {
        
        # Consulta de agendas medicas parametrizadas en indigo
        Route::apiResource('medical-appointments', 'His\Scheduling\MedicalAppointmentsController');
        
        # Consulta de especialidades con agendas disponibles indigo
        Route::apiResource('listings', 'His\Scheduling\ListingController');

        # Consulta de especialistas que tiene agendas disponibles con indigo



    });


    

});