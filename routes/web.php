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

        # UNIDADES MEDICAS
        Route::resource('units','UnitController');
        Route::get('sincronizar/units','UnitController@sincronizar');

        #EMAILS
        Route::resource('emails','EmailController');
        Route::post('emails/testMail','EmailController@testMail');

        # PLANTILLAS 
        Route::resource('plantillas','PlantillasController');

    });

    Route::group(['middleware' => ['auth'],'prefix' => 'Certificates', 'namespace' => 'Certificates'], function() {
        
        # CERTIFICATES DEATHS
        Route::resource('deaths','DeathController');
        Route::get('import/deaths','DeathController@import')->name('death.import');
        Route::post('upload/deaths','DeathController@upload')->name('death.upload');
        Route::post('save/deaths','DeathController@save')->name('death.save');
        Route::get('empty/deaths','DeathController@empty')->name('death.empty');
        Route::get('print/{certificates_id}/deaths','DeathController@print')->name('death.print');

        # CERTIFICATES LIVES
        Route::resource('lives','LiveController');
        Route::get('import/lives','LiveController@import')->name('lives.import');
        Route::post('upload/lives','LiveController@upload')->name('lives.upload');
        Route::post('save/lives','LiveController@save')->name('lives.save');
        Route::get('empty/lives','LiveController@empty')->name('lives.empty');
        Route::get('print/{certificates_id}/lives','LiveController@print')->name('live.print');

        # REPORTE CERTIFICADOS
        Route::resource('reportCertificates','ReportCertificatesController');

        # REPORTE CERTIFICADOS USADOS
        Route::resource('reportCertificatesUseds','reportCertificatesUsedsController');

        # CERTIFICATES
        Route::resource('certificates','CertificatesController');

        # REGISTRO MEDICO
        Route::resource('medical-records','MedicalRecordsController');

        Route::get('batches','CertificatesController@batches')->name('certificate.batches');
        Route::get('batches/{batch_id}/showbatches','CertificatesController@showbatches')->name('certificate.showbatches');
        Route::get('batch/{batch_id}/showbatch','CertificatesController@showbatch')->name('certificate.showbatch');
        Route::post('batch/delete','CertificatesController@deletebatch')->name('certificate.deletebatch');
        
    });

    # CONTROL DE INDICADORES
    Route::group(['middleware' => ['auth'],'prefix' => 'Indicator', 'namespace' => 'Indicator'], function() {
        
        Route::resource('indicators','IndicatorController');
        Route::get('import/{cicle}/indicators','IndicatorController@import')->name('indicator.import');
        Route::get('sendnotify/{cicle}/indicators','IndicatorController@sendnotify')->name('indicator.sendnotify');
        Route::post('upload/indicators','IndicatorController@upload')->name('indicator.upload');
        Route::post('save/indicators','IndicatorController@save')->name('indicators.save');
        Route::post('startcicle/indicators','IndicatorController@startcicle')->name('indicator.startcicle');
        Route::post('update-programmings/indicators','IndicatorController@updateProgramming')->name('indicator.update-programmin');

    });

    Route::post('Indicator/approved','Indicator\IndicatorController@approved')->name('indicators.approved');

    Route::get('Indicator/viewnotify/{token}','Indicator\IndicatorController@viewnotify')->name('indicator.viewnotify');
    Route::get('Indicator/shownotify/{cicle}/indicators','Indicator\IndicatorController@shownotify')->name('indicator.shownotify');

    # CONTROL DE INDICADORES
    Route::group(['middleware' => ['auth'],'prefix' => 'Entity', 'namespace' => 'Entity'], function() {
        
        Route::resource('EpidemiologicalClassifications','EpidemiologicalClassificationController');
        Route::get('findData/EpidemiologicalClassifications','EpidemiologicalClassificationController@findData')->name('EpidemiologicalClassifications.findData');
        
        Route::resource('InvoiceClassifications','InvoiceClassificationController');
        Route::get('findData/invoiceClassifications','InvoiceClassificationController@findData')->name('InvoiceClassifications.findData');
        Route::get('export/InvoiceOpen/{year}','InvoiceClassificationController@exportInvoiceOpen')->name('InvoiceClassifications.exportinvoiceopen');


        Route::resource('ExpensePatiens','ExpensePatientController');
        Route::resource('Surgeries','SurgeryController');

        Route::get('findEntity/ExpensePatiens','ExpensePatientController@findEntity')->name('expense.findEntity');
        Route::get('findUnity/ExpensePatiens','ExpensePatientController@findUnity')->name('expense.findUnity');
        Route::resource('EntryPatiens','EntryPatientController');

        # CARTERA Y GLOSAS
        Route::resource('AccountReceivables','AccountReceivableController');
        Route::post('findData/AccountReceivables','AccountReceivableController@findData')->name('AccountReceivables.findData');;
    
    });

    Route::group(['middleware' => ['auth'],'prefix' => 'Informes', 'namespace' => 'Informes'], function() {
        
        # INFORME GERENCIAL
        Route::resource('gerencial','GerencialController');

        # CONSULTAR SEGUIMIENTOS
        Route::resource('seguimiento','SeguimientoController');

    });

    # CONTROL DE INDICADORES
    Route::group(['middleware' => ['auth'],'prefix' => 'Vaccination', 'namespace' => 'Vaccination'], function() {
        Route::resource('funcionarios','FuncionariosController');
        Route::get('import/funcionarios','FuncionariosController@import')->name('funcionarios.import');
        Route::post('upload/funcionarios','FuncionariosController@upload')->name('funcionarios.upload');
        Route::post('save/funcionarios','FuncionariosController@save')->name('funcionarios.save');
        Route::post('savecontrol/funcionarios','FuncionariosController@savecontrol')->name('funcionarios.savecontrol');
        Route::post('deletecontrol/funcionarios','FuncionariosController@deletecontrol')->name('funcionarios.deletecontrol');
        Route::post('storecontrol/funcionarios','FuncionariosController@storecontrol')->name('funcionarios.storecontrol');
        Route::get('sinprogramar/funcionarios','FuncionariosController@sinprogramar')->name('funcionarios.sinprogramar');
        Route::get('general/funcionarios','FuncionariosController@general')->name('funcionarios.general');
        Route::get('reporte/funcionarios','FuncionariosController@reporte')->name('funcionarios.reporte');
        Route::get('reporteeliminados/funcionarios','FuncionariosController@reporteeliminados')->name('funcionarios.reporteeliminados');
        Route::post('genreporte/funcionarios','FuncionariosController@genreporte')->name('funcionarios.genreporte');
        Route::post('genreporteeliminados/funcionarios','FuncionariosController@genreporteeliminados')->name('funcionarios.genreporteeliminados');
        Route::post('executevaccination/funcionarios','FuncionariosController@executevaccination')->name('funcionarios.executevaccination');
        Route::get('details/funcionarios','FuncionariosController@details')->name('funcionarios.details');
        Route::get('find/funcionarios','FuncionariosController@find')->name('funcionarios.find');
        Route::get('find/municipio','FuncionariosController@findmunicipio')->name('funcionarios.findmunicipio');
        Route::get('find/nationality','FuncionariosController@findnationality')->name('funcionarios.findnationality');
        Route::get('find/entity','FuncionariosController@findentidad')->name('funcionarios.findentidad');
        Route::get('find/medicalUnity','FuncionariosController@findmedicalUnity')->name('funcionarios.medicalunity');
        Route::get('sendemail/funcionarios','FuncionariosController@sendemail')->name('funcionarios.sendemail');
        Route::get('gestionjornada/funcionarios','FuncionariosController@gestionjornada')->name('funcionarios.gestionjornada');

        # GRAFICAS
        Route::get('gestiongeneral/grafica','FuncionariosController@gestiongeneral')->name('funcionarios.gestiongeneral');
        Route::post('exportpendientes/funcionarios','FuncionariosController@exportpendientes')->name('funcionarios.exportpendientes');

        # DOWNLOAD DOCUMENT
        Route::get('donwload/document','FuncionariosController@donwloaddocument')->name('funcionarios.donwloaddocument');
        Route::get('executedocument/document','FuncionariosController@executedocument')->name('funcionarios.executedocument');
        Route::get('exportdocumentlogs/document','FuncionariosController@exportdocumentlogs')->name('document.exportdocumentlogs');

        # DOCUMENT LOGS 
        Route::get('formdocumentlogs/documents','FuncionariosController@formdocumentlogs')->name('funcionarios.formdocumentlogs');

    });


    Route::group(['middleware' => ['auth'],'prefix' => 'Officials', 'namespace' => 'Officials'], function() {
        
        # INFORME GERENCIAL
        Route::resource('Company','companyController');

        # CONSULTAR SEGUIMIENTOS
        Route::resource('health-promoting-entities', 'HealthPromotingEntitiesController');

        Route::resource('pension-fund-managers', 'PensionFundManagersController');

        Route::resource('user-companies', 'UserCompaniesController');
        Route::get('finduser/user-companies', 'UserCompaniesController@findUser')->name('find.user');
        Route::get('loaddata/user-companies/{userid}', 'UserCompaniesController@loadData')->name('load.user');

        Route::post('selectedCompany/user-companies', 'UserCompaniesController@selectedCompany')->name('company.selected');

        Route::resource('admon-officials', 'AdmonOfficialsController'); 
        Route::get('admon-officials/create/{company_id}/{contract_id?}', 'AdmonOfficialsController@create')->name('admon-officials.createuser'); 
        Route::get('/admon-officials/findperson/{document_type}/{document_number}', 'AdmonOfficialsController@findPerson')->name('admon-officials.findperson'); 
        Route::post('admon-officials/peoplecompany', 'AdmonOfficialsController@peoplecompany')->name('peoplecompany');
        Route::post('save-group-family', 'officialManagementController@storeGroupFamily')->name('save.family');
        Route::get('all-group-family/{person_id}', 'officialManagementController@allGroupFamily');
        Route::get('find-group-family/{family_group_id}', 'officialManagementController@findGroupFamily');
        

        # ADMINISTRADORA RIESGOS LABORALES
        Route::resource('occupational-risk-managers', 'OccupationalRiskManagersController');

        # FORMULARIOS EXTRA 
        Route::resource('extra-data', 'extraDataController');
        Route::get('extra-data-complete/{survey_form_id}/{person_id}', 'extraDataController@showExtraForm')->name('extradata.showextraform');
        Route::get('extra-data/create/{form_id?}', 'extraDataController@create')->name('extradata.create');
        Route::post('extra-data/forms', 'extraDataController@getForms')->name('extradata.forms');
        Route::post('extra-data/storefield', 'extraDataController@storefield')->name('extra-data.storefield');
        Route::post('extra-data/survey-form-details', 'extraDataController@getFromFields')->name('extra-data.getformfields');
        Route::post('extra-data/form-publish', 'extraDataController@formPublish')->name('extra-data.formpublish');
        Route::post('store-extra-data', 'extraDataController@storeExtra')->name('extra-data.storeextra');
        
        # BUSCAR ARL Y AFP
        Route::get('/find/occupational_risk_management/', 'AdmonOfficialsController@findOccupationalRiskManagement')->name('admon-officials.occupational_risk_management'); 
        Route::get('/find/pension_fund_managers/', 'AdmonOfficialsController@findPensionFundManagers')->name('admon-officials.pension_fund_managers'); 
        Route::get('/find/municipality/', 'AdmonOfficialsController@findMunicipality')->name('admon-officials.municipality'); 
        Route::get('/find/process-leader/', 'AdmonOfficialsController@findProcessleader')->name('admon-officials.processleader'); 
        Route::get('/find/costCenter/', 'AdmonOfficialsController@findCostCenter')->name('admon-officials.findcostcenter'); 

        # GESTION FUNCIONARIOS
        Route::resource('official-management', 'officialManagementController');
        Route::post('official-management/people', 'officialManagementController@getPeople')->name('official-management.people');
        Route::get('find-people', 'officialManagementController@findPeople')->name('official-management.findpeople');

        # REPORTES GESTION RECURSO HUMANO
        Route::resource('report-management-official', 'reportsManagementOfficialController');
        Route::get('/carvajal', 'AdmonOfficialsController@carvajal')->name('admon-officials.carvajal'); 

        # DESPRENDIBLES DE PAGO
        Route::resource('send-removable-payments', 'SendRemovablePaymentController');
        Route::resource('proccess-send-payments', 'ProccessSendPaymentController');
        Route::get('view-proccess-send-payments/{proccess_id}', 'ProccessSendPaymentController@viewDetailProccess');
        
        
        Route::post('send-removable-payments-all', 'SendRemovablePaymentController@getAllPayments');
        Route::get('find-official-vie', 'SendRemovablePaymentController@findOfficialVie');
        Route::get('print-removable-payments/{liquidation_id}', 'SendRemovablePaymentController@printRemovablePayments');
        Route::post('schedule-removable', 'SendRemovablePaymentController@scheduleRemovable');

        

        
    });


    Route::group(['middleware' => ['auth'],'prefix' => 'ControlDocuments', 'namespace' => 'ControlDocuments'], function() {
        
        # INFORME GERENCIAL
        Route::resource('electronic-documents','ElectronicDocumentsController');
        Route::post('get-documents/electronic-documents','ElectronicDocumentsController@getStateDocument');
        Route::get('findDocument/{id_document}/electronic-documents/','ElectronicDocumentsController@getDocumentById');
        Route::get('downDocument/{id_document}/electronic-documents/','ElectronicDocumentsController@downDocumentById');
        Route::get('viewDocument/{id_document}/electronic-documents/','ElectronicDocumentsController@viewDocument');
        Route::get('downDocumentXMLById/{id_document}/electronic-documents/','ElectronicDocumentsController@downDocumentXMLById');
        Route::get('StateValidDocument/{id_document}/electronic-documents/','ElectronicDocumentsController@StateValidDocument');
        Route::get('StateValidDocumentAll','ElectronicDocumentsController@StateValidDocumentAll');
        
        # TABLERO CONTROL DE MANDO
        Route::resource('dashboards','DashboardsController');
        Route::get('find/statistics','DashboardsController@statistics');

        # REPORTEADOR DOCUMENTOS ELECTRONICOS
        Route::resource('report-electronic-document', 'reportsManagementDocumentController');

        # DEPURAR NOTAS CONTINGENCIA
        Route::resource('debug-notes', 'DebugNotesController');
        Route::post('upload/debug-notes', 'DebugNotesController@uploadDebugNotes');
        Route::post('process/debug-notes', 'DebugNotesController@processDebugNotes');

        # VERIFICACIONES DIAN upload-files-dian
        Route::get('upload-files-dian','ElectronicDocumentsController@indexUploadFileDian')->name('upload.indexuploadfiledian');
        Route::post('upload/upload-file-dian', 'ElectronicDocumentsController@uploadFileDian');
        Route::post('process/upload-file-dian', 'ElectronicDocumentsController@storeFileDian');
        Route::get('join-files-dian','ElectronicDocumentsController@indexJoinFileDian')->name('upload.indexjoinfiledian');
        Route::post('get-join-documents/electronic-documents','ElectronicDocumentsController@getDocumentJoinDian');

    });


    Route::group(['middleware' => ['auth'],'prefix' => 'Contracts', 'namespace' => 'Contracts'], function() {
        
        # ADMINISTRACION DE CONTRATOS
        Route::resource('administrative-contracts','AdministrativeContractsController');
        Route::post('add-commitments','AdministrativeContractsController@addCommitmentNew');
        Route::post('add-commitments-masive','AdministrativeContractsController@addCommitmentNewMasive');
        Route::post('add-supervisors','AdministrativeContractsController@addSupervisorNew');
        
        Route::post('delete-commitments','AdministrativeContractsController@deleteCommitmentNew');
        Route::post('delete-supervisors','AdministrativeContractsController@deleteSupervisorNew');
        Route::get('show-commitments/{administrative_contract_id}','AdministrativeContractsController@getCommitments');
        Route::get('show-supervisors/{administrative_contract_id}','AdministrativeContractsController@getSupervisors');

        # ADMINISTRACION PROVEEDORES
        Route::resource('suppliers','SuppliersController');
        Route::post('suppliers/people','SuppliersController@people');
        Route::get('find-suppliers/{document_number}', 'SuppliersController@findSuppliers');

        # ADMINISTRACIÓN SUPERVISOR 
        Route::resource('supervisors','SupervisorsController');
        Route::post('supervisors/people','SupervisorsController@people');
        Route::get('/supervisors/find/{document_type}/{document_number}', 'SupervisorsController@find')->name('supervisor.find'); 
        Route::get('find-supervisors/{document_number}', 'SupervisorsController@findSupervisor');
        Route::get('find-supervisor', 'SupervisorsController@findSupervisorName');

        # CONSULTA DE RP
        Route::get('find-commitment/{rp_number}/{rp_year}', 'AdministrativeContractsController@findCommitment');
        
        # ASOCIAR CONTRATOS
        Route::post('show/administrative-contracts','AdministrativeContractsController@showContracts')->name('show.contracts');

        # PRESUPUESTO
        Route::get('administrative-contracts-budget/{administrative_contract_id}', 'AdministrativeContractsController@showAdminitrativeBudget');

        # TABLERO SUPERVISOR
        Route::resource('table-supervisors', 'TableSupervisorController');
        Route::post('show/table-supervisors','TableSupervisorController@showContracts')->name('show.contracts.supervi');
        Route::post('export-excel/table-supervisors','TableSupervisorController@exportExcelContracts')->name('export.contracts');
        Route::post('export-excel-dane/table-supervisors','TableSupervisorController@exportExcelContractsDane')->name('export.contractsdane');
        Route::get('supervisors-budget/{year}', 'TableSupervisorController@viewBudget');

        # IMPORTAR CONTRATOS DESDE ARCHIVO EXCEL
        Route::resource('upload-contracts', 'UploadContractController');
        Route::post('upload-contracts/massive-contracts', 'UploadContractController@uploadContracts');
        

    });

    # GESTIÓN PRESUPUESTO
    Route::group(['middleware' => ['auth'],'prefix' => 'BudgetManagement', 'namespace' => 'BudgetManagement'], function() {
        
        # PARAMETRIZACION CODIGOS DANE
        Route::resource('dane-code-programmings','DaneCodeProgrammingController');
        Route::get('show-dane-code-programmings','DaneCodeProgrammingController@showDaneCodes');
        Route::get('findcode/dane-code-programmings', 'DaneCodeProgrammingController@findCodes');

        # PARAMETRIZACIÓN PRESUPUESTO
        Route::resource('category-parameterizations','CategoryParameterizationController');
        Route::post('show/categories','CategoryParameterizationController@showCategories');
        Route::post('store/categories','CategoryParameterizationController@storeCategory')->name('category.store');
        Route::get('category-dane-codes/{category_id}','CategoryParameterizationController@categoryDaneCodes');
        Route::post('category-parameterizations/store-dene-details','CategoryParameterizationController@storeDaneDetails');
        Route::get('desaggregation-dane-codes/{category_id}','DaneCodeProgrammingController@desaggregationDaneCodes');
        
        # PARAMETRIZACION PRESUPUESTO VS DANE
        Route::resource('category-danes','BudgetDaneController');

        # PARAMETRIZACION PRODUCTOS
        Route::resource('product-parameterizations','ProductParameterizationController');
        Route::post('show/products','ProductParameterizationController@showProducts');
        Route::get('product-detail-show/{type_product}/{product_id}/','ProductParameterizationController@showProductDetail');
        Route::get('find-dane-code','ProductParameterizationController@findDaneCode');
        Route::get('find-dane-name','ProductParameterizationController@findDaneName');

        # ACTUALIZACION MASIVA DE PRODUCTOS
        Route::resource('massive-product-updates','MassiveProductUpdateController');
        Route::post('uploads/massive-product-update', 'MassiveProductUpdateController@productUploads');

        # CARGA MASIVO CODIGO DANE
        Route::resource('massive-dane-codes','MassiveDaneCodeController');
        Route::post('uploads/massive-dane-codes', 'MassiveDaneCodeController@daneCodesUploads');
        
        # COMPROMISOS
        Route::resource('commitments','CommitmentController');
        Route::get('export-commitment-trazability/{commitment_id}','CommitmentController@exporExcelTrazability');
        Route::post('get-commitments/commitments','CommitmentController@getCommitments');
        Route::post('commitment-end','CommitmentController@endCommitment');
        Route::post('delete-codedane-register','CommitmentController@deleteCodeDaneRegister');
        Route::post('save-validity-commitments','CommitmentController@saveValidityCommitment');
        Route::get('export/commitments/{commitment_id}','CommitmentController@exportDaneCommitment');
        

        # OBLIGACIONES
        Route::resource('obligations','ObligationController');
        Route::post('get-obligations/obligations','ObligationController@getObligations');
        Route::post('obligation-end','ObligationController@endObligation');
        Route::post('delete-codedane-obligation','ObligationController@deleteCodeDaneRegister');
        Route::get('obligations-desaggregation/{obligation_detail_id}','ObligationController@desaggregationDane');
        Route::get('export-obligation-trazability/{obligation_id}','ObligationController@exporExcelTrazability');
        

        # ORDENES DE PAGO
        Route::resource('paymentorders','PaymentorderController');
        Route::post('get-paymentorders/paymentorders','PaymentorderController@getPaymentorders');
        Route::post('paymentorder-end','PaymentorderController@endPaymentorder');
        Route::post('delete-codedane-paymentorder','PaymentorderController@deleteCodeDaneRegister');
        Route::get('paymentorders-desaggregation/{paymentorder_detail_id}','PaymentorderController@desaggregationDane');
        Route::get('export-paymentorder-trazability/{paymentorder_id}','PaymentorderController@exporExcelTrazability');
        


        # CARGA MASIVA DE GRUPOS RUBRO
        Route::resource('massive-category-groups','MassiveCategoryGroupsController');
        Route::post('show/category-groups','MassiveCategoryGroupsController@showCategoryGroups');
        Route::get('group-category/{product_group_code}/{budgetary_validity_year}','MassiveCategoryGroupsController@groupCategoryFind');
        Route::get('find-category-code','MassiveCategoryGroupsController@findCategoryCode');
        Route::get('find-category-name','MassiveCategoryGroupsController@findCategoryName');
        
        Route::post('store-category-group','MassiveCategoryGroupsController@storeCategoryGroup')->name('store.category.group');

        # ACUTALIZACION DE COMPROBANTES DESAGREGADOS
        Route::resource('adjustment-vouchers','AdjustmentVouchersController');
        Route::post('show-vauchers', 'AdjustmentVouchersController@showVauchers');
        Route::post('show-vauchers-not-reconciled', 'AdjustmentVouchersController@showVauchersNotReconciled');

        # REPORTE DESAGREGADO PRESUPUESTALES
        Route::resource('report-budget-management','ReportBudgetManagementController');

        # COMPROBANTES MODIFICADOS
        Route::resource('modification-vouchers','ModificationVouchersController');
        Route::post('show-modification-vouchers','ModificationVouchersController@showVauchers');
        Route::get('show-modification/commitments/{modification_id}', 'ModificationVouchersController@showModificationCommitment');
        Route::get('show-modification/obligations/{modification_id}', 'ModificationVouchersController@showModificationObligation');
        Route::get('show-modification/paymentorders/{modification_id}', 'ModificationVouchersController@showModificationPaymentOrder');

        # VISUALIZA LOS COMPROBANTES DE AJUSTE GENENERADOS POR MOVIMIENTOS CODIGOS DANE O POR DESAGREGACION DE COMPROBANTES MODIFICADOS INDIGO
        Route::resource('adjusting-vouchers','AdjustingVoucherController');
        Route::post('show-adjusting-vouchers','AdjustingVoucherController@showAdjustingVouchers');
        Route::get('view-adjusting-vouchers/{adjusting_vouchers_id}','AdjustingVoucherController@viewAdjustingVouchers');

        # ENTIDADES PUBLICAS CHIP
        Route::resource('chip-public-entities','ChipPublicEntityController');
        Route::get('detail-chip-public-entities','ChipPublicEntityController@detailChipPublicEntities');
        
        

    });

    # MODULO DE SUMINISTROS
    Route::group(['middleware' => ['auth'],'prefix' => 'SupplyManagement', 'namespace' => 'SupplyManagement'], function() {
        
        # Dashboard
        Route::resource('dashboards','DashboardController');
        
        # Remisiones
        Route::resource('remissions','RemissionController');
        Route::get('legalized-remissions','RemissionController@showLegalizedRemission');
        Route::post('show/remissions','RemissionController@showRemissions');
        Route::get('load/remissions/{remission_id}','RemissionController@getRemissionById');
        Route::get('view-purchaseorder/remissions/{purchaseid}','RemissionController@getPurchaseOrderById');
        Route::post('delete-purchaseorder/remissions','RemissionController@deletePurchaseOrderRemission');
        Route::get('view-purchaseorder/purchaseorder/{purchaseid}','RemissionController@getPurchaseOrderById');
        Route::get('details-product/remissions/{remission_id}/{purchaseorderid}','RemissionController@getDetailRemissionById');
        Route::get('details-product/entrance-vaucher/{vaucher_id}','RemissionController@getDetailEntranceVaucherById');
        
        # Reportes
        Route::resource('reports','ReportManagementController');

        # ORDENES DE COMPRA
        Route::resource('purchase-order','PurchaseOrderController');
        Route::post('show/purchaseorder','PurchaseOrderController@showPurchaseOrder');
        
        Route::get('view-vouchers/purchaseorderdetail/{purchaseorderdetailid}','PurchaseOrderController@viewVoucherPurchaseOrderDetail');
        
        # CONTROL DE PRODUCTOS
        Route::resource('control-products','ControlProductController');
        Route::get('show/control-products/{year}/{type}','ControlProductController@showControlProducts');
        
 
    });

    # MODULO SEGUIMIENTO Y CONTROL
    Route::group(['middleware' => ['auth'],'prefix' => 'Tracing', 'namespace' => 'Tracing'], function() {
        
        Route::resource('meetings','MeetingController');
        Route::post('show/meetings', 'MeetingController@showMeetings')->name('show.meeting');


        Route::resource('new-commitments','NewCommitmentController');
        Route::post('get-commitments','NewCommitmentController@getCommitments');
        Route::post('responsives-store','NewCommitmentController@storeResponsible')->name('responsible.store');
        Route::get('commitment-responsibles/{commitment_id}','NewCommitmentController@commitmentResponsibles');
        Route::post('delete-reponsible','NewCommitmentController@deleteResponsible');
        Route::post('store-tracing','NewCommitmentController@storeTracing')->name('store.tracing');
        Route::get('email-responsible/{mdc_meeting_id}','NewCommitmentController@emailResponsibles');
        Route::get('send-email-meeting/{mdc_meeting_id}/{person_id}','NewCommitmentController@sendEmailResponsibles');


        
        #Route::post('upload-documents','NewCommitmentController@storeCommitmentDocumente')->name('upload.document');
        #Route::get('download-document/{mdc_commitment_document_id}','NewCommitmentController@getDownload')->name('download.document');
        #Route::get('view-tracing/{mdc_commitment_detail_id}','NewCommitmentController@getTracingDetails');

        //Route::get('commitment-tracing-documents/{commitment_detail_id}','NewCommitmentController@commitmentDetailDocuments');

        Route::resource('reports','ReportController');

    });

    # MODULO SEGUIMIENTO Y CONTROL
    Route::group(['middleware' => ['auth'],'prefix' => 'PossibleDonor', 'namespace' => 'PossibleDonor'], function() {

        Route::resource('dashboards','DashboardController');
        Route::get('alert-donor/dashboards/{validity}', 'DashboardController@getAlertDonor' );

        Route::resource('donor-cycle-steps','DonorCycleStepController');
        Route::get('detail-donor-cycle-steps','DonorCycleStepController@detailsDonorSteps');

        # Causales de no donacion
        Route::resource('cause-non-donations','CauseNonDonationController');
        Route::get('detail-cause-non-donations','CauseNonDonationController@detailsCauseNonDonations');
        
        # Entidades prestadoras de servicios de salud.
        Route::resource('health-provider-units','HealthProviderUnitController');
        Route::get('detail-health-provider-units','HealthProviderUnitController@detailsHealthProviderUnits');
        
        Route::resource('permissions-on-alerts','PermissionsOnAlertController');
        Route::get('list-permissions-on-alerts/{userid}', 'PermissionsOnAlertController@loadUserPermissions')->name('load.user');
        
        Route::resource('donor-alerts','DonorAlertController');
        Route::post('detail-donor-alerts','DonorAlertController@detailsDonorAlerts');
        Route::post('tracking-donor-alerts', 'DonorAlertController@storeTrackingDonor');
        Route::post('tracking-upload-documents','DonorAlertController@storeTrackingDocument');
        Route::get('download-document/{documentation_id}','DonorAlertController@getDownload')->name('download.document');
        
        # Reportes módulo posibles donantes
        Route::resource('donor-reports','DonorReportController');

        # Funcionalidad facturación
        Route::resource('pending-billings','PendingBillingController');


        

    });

    Route::get('Tracing/viewCommitment/{token}/{meeting_id}','Tracing\MeetingController@viewCommitmentResponsible')->name('commitment.view');
    Route::get('Tracing/shownotify/{meeting_id}/{token}','Tracing\MeetingController@shownotify')->name('tracing.shownotify');
    Route::post('Tracing/store-tracing-responsible/{token}/{meeting_id}','Tracing\NewCommitmentController@storeTracingResponseive')->name('store.tracingresponsible');
    Route::get('Tracing/commitment-tracing-documents/{commitment_detail_id}','Tracing\NewCommitmentController@commitmentDetailDocuments');
    Route::get('Tracing/view-tracing/{mdc_commitment_detail_id}','Tracing\NewCommitmentController@getTracingDetails');
    Route::get('Tracing/download-document/{mdc_commitment_document_id}','Tracing\NewCommitmentController@getDownload')->name('download.document');
    Route::post('Tracing/upload-documents','Tracing\NewCommitmentController@storeCommitmentDocumente')->name('upload.document');
    

    # URL publicas
    Route::get('/Public/Solicitud', 'Publico\SolicitudController@index')->name('public.solicitud');