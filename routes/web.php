<?php

    use Illuminate\Support\Facades\Route;

    Auth::routes(['register' => false]);

    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/', function(){
        return redirect('login');
    });
    //Route::get('/Cotizador', 'CotizadorController@index')->name('cotizador');

    Route::group(['middleware' => ['auth'],'prefix' => 'Admin', 'namespace' => 'Admin'], function() {
        
        # MENU
        Route::get('menu', 'MenuController@index')->name('menu');
        Route::get('menu/crear', 'MenuController@crear')->name('crear_menu');
        Route::post('menu', 'MenuController@guardar')->name('guardar_menu');
        Route::get('menu/{id}/editar', 'MenuController@editar')->name('editar_menu');
        Route::put('menu/{id}', 'MenuController@actualizar')->name('actualizar_menu');
        Route::get('menu/{id}/eliminar', 'MenuController@eliminar')->name('eliminar_menu');
        Route::post('menu/guardar-orden', 'MenuController@guardarOrden')->name('guardar_orden');

        /*RUTAS MENU_ROL*/
        Route::get('menu-rol', 'MenuRolController@index')->name('menu_rol');
        Route::post('menu-rol', 'MenuRolController@guardar')->name('guardar_menu_rol');
        
        # ROLES
        Route::resource('roles','RoleController');

        # USUARIOS
        Route::resource('users','UserController');
        Route::get('users/sucursales/{id_unidad}','UserController@getSucursales');
        Route::get('users/negocio/{id_unidad}','UserController@getUsuarios');
        Route::get('users/sucursal/{sucursal_id}','UserController@usuariosSucursal');

        #EMAILS
        Route::resource('emails','EmailController');
        Route::post('emails/testMail','EmailController@testMail');

        # PLANTILLAS 
        Route::resource('plantillas','PlantillasController');

        

        
        
        

    });

    Route::group(['middleware' => ['auth'],'prefix' => 'wallet', 'namespace' => 'wallet'], function() {

        # COMERCIOS
        Route::resource('business','BusinessController');
        Route::get('details-business', 'BusinessController@getDetailBusiness');

        # PERMISOS COMERCIOS 
        Route::resource('business-users','BusinessUsersController');
        Route::get('finduser/business-users', 'BusinessUsersController@findUser')->name('find.user');
        Route::get('list-stores/{userid}', 'BusinessUsersController@loadUserPermissions')->name('load.user');

        # TIPO DE TRANSACCION 
        Route::resource('movement-types','MovementTypesController');
        Route::get('details-movement-types', 'MovementTypesController@getMovementTypes');


        # USUARIOS BILLETERA ELECTRONICA
        Route::resource('wallet-users','WalletUsersController');
        Route::post('detail-wallet-users','WalletUsersController@detailsWalletUsers');

        # TRANSACCIONES
        Route::resource('transactions','TransactionsController');
        Route::post('detail-transactions','TransactionsController@getTransactions');

        # REPORTES
        Route::resource('wallet-reports','WalletReportController');
        
        # FUNCION BUSCAR USUARIOS BILLETERA
        Route::get('find-wallet-user', 'WalletUsersController@findWalletUser');
        Route::post('wallet-user-transactions', 'WalletUsersController@getTransactions');

        # BOLSILLO DISPONIBLES
        Route::resource('electrical-pockets','ElectricalPocketController');
        Route::get('details-electrical-pockets', 'ElectricalPocketController@getDetailElectricalPockets');
        Route::get('wallet-user-tickets/{electrical_pocket_wallet_user_id}', 'ElectricalPocketController@getDetailElectricalPocketTickets');
        
        

        # CONSECUTIVOS TIQUETERA
        Route::resource('consecutive-tickets','ConsecutiveTicketController');
        Route::get('details-consecutive-tickets', 'ConsecutiveTicketController@getDetailConsecutiveTickets');

        
        
        
                


    }); 