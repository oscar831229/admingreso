<?php

Route::group([
    'prefix' => 'cli_cotizante/v4/info/grupo_familiar',
    'middleware' => 'auth:api'
], function () {

    Route::post(
        'conFidelidad',
        'income\SisafiConsultationController@getAffiliateCategory'
    );

});
