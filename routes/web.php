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

    Route::group(['middleware' => ['auth'],'prefix' => 'income', 'namespace' => 'income'], function() {

        Route::resource('parameterization-services','ParameterizationServiceController');
        Route::get('consecutive-codes/{code}','ParameterizationServiceController@consecutiveCodes');

        Route::post('datatable-parameterization-services', 'ParameterizationServiceController@datatableParameterizationServices');
        Route::get('environment-menus-items/{environment_id}','ParameterizationServiceController@getEnvironmentMenusItems');
        Route::get('environment-income-services/{environment_id}','ParameterizationServiceController@getEnvironmentIncomeServices');

        Route::resource('parameterization-companies','ParameterizationCompanyController');
        Route::get('find-icm-companies-agreement','ParameterizationCompanyController@findCompaniesAgreement')->name('findCompaniesAgreement');
        Route::post('datatable-parameterization-companies','ParameterizationCompanyController@datatableParameterizationCompanies');

        Route::resource('parameterization-agreements','ParameterizationAgreementController');
        Route::post('datatable-parameterization-agreements', 'ParameterizationAgreementController@datatableParameterizationAgreements');

        Route::resource('billing-incomes','BillingIncomeController');
        Route::get('search-client-document/{document_number}','BillingIncomeController@searchClientDocument');
        Route::get('billing-incomes-category/{icm_types_income_id}','BillingIncomeController@billingIncomesCategory');
        Route::get('billing-company-agreement/{icm_companies_agreement_id}','BillingIncomeController@billingCompanyAgreement');
        Route::get('billing-incomes-details/{icm_liquidation_id}','BillingIncomeController@billingIncomesDetails');
        Route::get('billing-people-services/{icm_liquidation_id}', 'BillingIncomeController@getBillingPeopleServices');
        Route::get('view-liquidation-totals/{icm_liquidation_id}', 'BillingIncomeController@viewLiquidationTotals');
        Route::get('view-liquidation-payment/{icm_liquidation_id}', 'BillingIncomeController@viewLiquidationPayment');
        Route::post('pay-billing-incomes', 'BillingIncomeController@payBillingIncomes');
        Route::get('billing-incomes-print/{icm_liquidation_id}', 'BillingIncomeController@billingIncomesPrint');





        Route::resource('incomes','IncomeController');
        Route::get('incomes-dowload/{directory}','IncomeController@dowload')->name('downloadFiles');

        Route::resource('income-reports','IncomeReportController');

        Route::resource('users-environments','UsersEnvironmentController');
        Route::get('find-users-environments','UsersEnvironmentController@findUsersEnvironment')->name('findUsersEnvironment');

        Route::resource('environments','EnvironmentController');

        Route::resource('rate-types','RateTypeController');
        Route::post('datatable-type-rates', 'RateTypeController@datatableTypeRates');

        Route::resource('affiliate-categories','AffiliateCategoryController');
        Route::post('datatable-affiliate-categories', 'AffiliateCategoryController@datatableAffiliateCategories');

        Route::resource('special-rates','SpecialRateController');

    });




