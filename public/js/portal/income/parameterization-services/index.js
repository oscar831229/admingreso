$(document).ready(function () {
    environment.init();
    services.init();
});

var tblenvironmentservices = null;

environment = {

    id : null,

    viewEnviromentItems : function(){

        environment.id = $(this).data('environment_id');
        $("#div-environments").hide(250, function() {
            $("#div-created-services").show();
        });

        $('#name-environtment').html($(this).html());
        services.initServices(environment);

    },

    init : function(){
        $('body').on('click', '.environment', this.viewEnviromentItems);
    }

}

services = {

    environment : null,

    initServices : function(environment){
        services.environment = environment;
        this.loadEnvironmentMenusItems();
        this.loadTableServices();
    },

    loadEnvironmentMenusItems : function(){
        const menusitems = sessionStorage.getItem('enviroment_' + this.environment.id);
        if(menusitems == null){
            this.getEnvironmentMenusItems(this.environment.id);
        }else{
            this.constructMenusItems();
        }
    },

    constructMenusItems : function(){

        const menusitemsJson = sessionStorage.getItem('enviroment_' + this.environment.id);
        const menusitems = JSON.parse(menusitemsJson);

        // Obtener referencia al elemento select
        var selectElement = document.getElementById("icm_environment_icm_menu_item_id");

        // Recorrer el array y crear opciones
        for (var i = 0; i < menusitems.length; i++) {
            // Crear un elemento de opción
            var optionElement = document.createElement("option");

            // Establecer el valor y texto de la opción
            optionElement.value = menusitems[i].id;
            optionElement.text = menusitems[i].name;

            // Agregar la opción al elemento select
            selectElement.add(optionElement);
        }

        $('#icm_environment_icm_menu_item_id').selectpicker('refresh');


    },

    getEnvironmentMenusItems : function(environment_id){

        $.ajax({
            url: '/income/environment-menus-items/' + environment_id,
            async: true,
            data: {},
            beforeSend: function(objeto){

            },
            complete: function(objeto, exito){
                if(exito != "success"){
                    alert("No se completo el proceso!");
                }
            },
            contentType: "application/x-www-form-urlencoded",
            dataType: "json",
            error: function(objeto, quepaso, otroobj){
                alert("Ocurrio el siguiente error: "+quepaso);
            },
            global: true,
            ifModified: false,
            processData:true,
            success: function(response){


                if(response.success){
                    const menus_items = JSON.stringify(response.data);
                    sessionStorage.setItem('enviroment_' + services.environment.id, menus_items);
                    services.constructMenusItems();
                }

            },
            timeout: 30000,
            type: 'GET'

        });

    },

    loadTableServices : function(){

        let icm_environment_id = this.environment.id;

        tblenvironmentservices= $('#tbl-environment-services').DataTable();
        tblenvironmentservices.destroy();

        $('#tbl-environment-services thead th').each(function () {
            var title = $(this).text();
            if($(this).hasClass('search-disabled')){
                $(this).html(title);
            }else{
                $(this).html(title+' <input type="text" class="col-search-input" placeholder="" />');
            }
        });

        tblenvironmentservices = $('#tbl-environment-services').DataTable({
            language: language_es,
            pagingType: "numbers",
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '/income/datatable-parameterization-services',
                type: "POST",
                data: {
                    '_token' : $('input[name=_token]').val(),
                    'icm_environment_id' : icm_environment_id
                },
                "dataSrc": function (json) {
                    return json.data;
                },
                async: true
            },
            columnDefs: [{
                targets: "_all",
                orderable: false,
            },{ "width": "200px", "targets": 2 }],

            initComplete: function () {
            },
            createdRow: function (row, data, index) {

                btnedit = '<a href="javaScript:void(0)" data-id="'+data[0]+'" class="mr-2 edit-income-item" title="Editar servicio de ingreso">'
                    + '<i class="fa fa-edit text-success"></i>'
                    + '</a>';

                btnassign = '<a href="javaScript:void(0)" data-id="'+data[0]+'" class="mr-2 assign-environment" title="Asignar servicio a ambiente">'
                    + '<i class="fa fa-assistive-listening-systems text-primary"></i>'
                    + '</a>';

                $('td', row).eq(6).html(getLableState(data[6])).addClass('dt-center');
                $('td', row).eq(7).html(btnedit + btnassign).addClass('dt-center');
                $('td', row).eq(0).html(data[8]).addClass('dt-center');
            }
        });

        tblenvironmentservices.columns().every(function () {
            var table = this;
            $('input', this.header()).on('keyup change', function () {
                if (table.search() !== this.value && (this.value.length > 2  || this.value.length == 0)) {
                    table.search(this.value).draw();
                }
            });
        });

    },

    viewFormNewEnvironmentService : function(){
        var element = $(this);

        if (element.is("button")) {
            $("#code").prop("disabled", false);
            btn.loading(element);
            setTimeout(function(){
                $.ajax({
                    url: '/income/consecutive-codes/services',
                    async: true,
                    data: {},
                    beforeSend: function(objeto){

                    },
                    complete: function(objeto, exito){
                        btn.reset(element);
                        if(exito != "success"){
                            alert("No se completo el proceso!");
                        }
                    },
                    contentType: "application/x-www-form-urlencoded",
                    dataType: "json",
                    error: function(objeto, quepaso, otroobj){
                        alert("Ocurrio el siguiente error: "+quepaso);
                        btn.reset(element);
                    },
                    global: true,
                    ifModified: false,
                    processData:true,
                    success: function(response){
                        if(response.success && response.data.trim() != ''){
                            $('#code').val(response.data);
                            $("#code").prop("disabled", true);
                        }
                    },
                    timeout: 30000,
                    type: 'GET'
                });
            },100)
        }

        services.resetFormsRates();
        $('#form-environment-income-items :input').prop('disabled', false);
        $('#form-environment-income-items').find("[name=id]").val('');
        $('#icm_environment_icm_menu_item_id').selectpicker('refresh');
        $('#md-icm_environment_income_items').modal()
        $('#form-environment-income-items').find('#name').attr('disabled', false);
        $('#form-environment-income-items').find('#code').attr('disabled', false);
    },

    resetFormsRates : function(){
        document.getElementById('form-environment-income-items').reset();
        $('.form-tarifas-available').each(function(index, element){
            element.reset();
        });
    },

    confirmSaveIncomeItem : function(){

        if(!$('#form-environment-income-items').valid()){
            Biblioteca.notificaciones('Existen datos pendientes de diligenciar.', 'Ingresos a sedes', 'warning');
            return false;
        }

        var decimalValue = $('#code_seac').val();

        // Validación con expresión regular para verificar el formato decimal (12,0)
        var decimalRegex = /^\d{1,12}$/; // Acepta de 1 a 12 dígitos numéricos

        if (!decimalRegex.test(decimalValue)) {
            Biblioteca.notificaciones('Código seac incorrecto, debe ser númerico maximo de 12 números', 'Ingresos a sedes', 'warning');
            return false; // Detiene el envío del formulario si la validación falla
        }

        $("#code").prop("disabled", false);

        const element = $(this);
        var jsonData=$('#form-environment-income-items').serializeArray()
            .reduce(function(a, z) {
                a[z.name] = z.value;
                return a;
            }, {});

        jsonData.icm_environment_id = services.environment.id;
        var income_item_rate = services.getIncomeItemrate();
        jsonData.income_rates = income_item_rate;

        swal({
            title: 'Servicios ingresos a sedes',
            text: "¿Esta seguro de continuar con el proceso.?",
            icon: 'warning',
            showConfirmButton:false,
            buttons: {
                Aceptar: {
                    text: "Aceptar",
                    value: 'Aceptar',
                    visible: true
                },
                cancel: true
            },
        }).then((value) => {
            if (value) {
                btn.loading(element);
                setTimeout(function(){
                    $.ajax({
                        url: '/income/parameterization-services',
                        async: true,
                        data: jsonData,
                        beforeSend: function(objeto){

                        },
                        complete: function(objeto, exito){
                            btn.reset(element);
                            if(exito != "success"){
                                alert("No se completo el proceso!");
                            }
                        },
                        contentType: "application/x-www-form-urlencoded",
                        dataType: "json",
                        error: function(objeto, quepaso, otroobj){
                            alert("Ocurrio el siguiente error: "+quepaso);
                            btn.reset(element);
                        },
                        global: true,
                        ifModified: false,
                        processData:true,
                        success: function(response){
                            btn.reset(element);
                            if(response.success){
                                Biblioteca.notificaciones('Proceso exitoso.', 'Ingresos a sedes', 'success');
                                $('#md-icm_environment_income_items').modal('hide')
                                tblenvironmentservices.ajax.reload();
                            }else{
                                Biblioteca.notificaciones(response.message, 'Ingresos a sedes', 'error');
                            }
                        },
                        timeout: 30000,
                        type: 'POST'
                    });
                },100)
            }
        });
    },

    confirmUpdateIncomeItem : function(){

        const element = $(this);

        var environment_enabled = [];

        // Cargar los ambientes habilitados para venta
        $('#tabla-data tbody tr').each(function() {

            var checkbox                         = $(this).find('[type=checkbox]');
            var environment_id                    = checkbox.data('environment_id');
            var icm_environment_icm_menu_item_id = $(this).find('select').val();

            environment_enabled.push({
                environment_id                   : environment_id,
                enabled                          : checkbox.prop('checked'),
                icm_environment_icm_menu_item_id : icm_environment_icm_menu_item_id
            });

        });

        var icm_environment_income_item_id = $('#btn-update-income-item').data('icm_environment_income_item_id');

        swal({
            title: 'Actualizar permisos ventas sedes',
            text: "¿Esta seguro de continuar con el proceso.?",
            icon: 'warning',
            showConfirmButton:false,
            buttons: {
                Aceptar: {
                    text: "Aceptar",
                    value: 'Aceptar',
                    visible: true
                },
                cancel: true
            },
        }).then((value) => {
            if (value) {
                btn.loading(element);
                setTimeout(function(){
                    $.ajax({
                        url: '/income/parameterization-services/' + icm_environment_income_item_id,
                        async: true,
                        data: {
                            environment_enabled : environment_enabled,
                            _token : $('[name=_token]').val()
                        },
                        beforeSend: function(objeto){

                        },
                        complete: function(objeto, exito){
                            btn.reset(element);
                            if(exito != "success"){
                                alert("No se completo el proceso!");
                            }
                        },
                        contentType: "application/x-www-form-urlencoded",
                        dataType: "json",
                        error: function(objeto, quepaso, otroobj){
                            alert("Ocurrio el siguiente error: "+quepaso);
                            btn.reset(element);
                        },
                        global: true,
                        ifModified: false,
                        processData:true,
                        success: function(response){
                            btn.reset(element);
                            if(response.success){
                                Biblioteca.notificaciones('Proceso exitoso.', 'Ingresos a sedes', 'success');
                                $('#md-icm_environment_income_items').modal('hide')
                                tblenvironmentservices.ajax.reload();
                            }else{
                                Biblioteca.notificaciones(response.message, 'Ingresos a sedes', 'error');
                            }
                        },
                        timeout: 30000,
                        type: 'PUT'
                    });
                },100)
            }
        });
    },

    getIncomeItemrate : function(){

        var income_rate = [];
        $('.rate').each(function(index, element){
            if($(element).val().trim() != ''){
                var subsidy = 0;
                var rate = {}
                rate.icm_types_income_id       = $(element).data('type');
                rate.icm_affiliate_category_id = $(element).data('category_id');
                rate.value                     = $(element).val();
                rate.icm_rate_type_id          = $(element).data('icm_rate_type_id');
                rate.subsidy                   = subsidy;

                if($(`input.subsidio_venta[data-type=${rate.icm_types_income_id}][data-category_id=${rate.icm_affiliate_category_id}][data-icm_rate_type_id=${rate.icm_rate_type_id}]`).length > 0){
                    rate.subsidy = $(`input.subsidio_venta[data-type=${rate.icm_types_income_id}][data-category_id=${rate.icm_affiliate_category_id}][data-icm_rate_type_id=${rate.icm_rate_type_id}]`).val();
                }

                income_rate.push(rate);

            }
        });

        return income_rate;
    },

    ediEnvironmentService : function(){

        $('#form-environment-income-items :input').prop('disabled', false);
        const icm_environment_income_item_id = $(this).data('id');
        $('#content-parameterization-services').show();
        $('#content-environments').hide();

        var element = $(this);
        btn.loading(element);
        setTimeout(function(){
            $.ajax({
                url: '/income/parameterization-services/' + icm_environment_income_item_id,
                async: true,
                data: {},
                beforeSend: function(objeto){

                },
                complete: function(objeto, exito){
                    btn.reset(element);
                    if(exito != "success"){
                        alert("No se completo el proceso!");
                    }
                },
                contentType: "application/x-www-form-urlencoded",
                dataType: "json",
                error: function(objeto, quepaso, otroobj){
                    alert("Ocurrio el siguiente error: "+quepaso);
                    btn.reset(element);
                },
                global: true,
                ifModified: false,
                processData:true,
                success: function(response){
                    btn.reset(element);
                    if(response.success){
                        services.viewFormNewEnvironmentService();
                        loadDataForm('form-environment-income-items', response.data);
                        $('#value').trigger('change');
                        $('#value_high').trigger('change');
                        loadIncomeRates('body', response.income_item_detail);
                        $('#icm_environment_icm_menu_item_id').selectpicker('refresh');
                        $('#form-environment-income-items').find('#name').attr('disabled', true);
                        $('#form-environment-income-items').find('#code').attr('disabled', true);
                    }else{
                    }
                },
                timeout: 30000,
                type: 'GET'
            });
        },100)
    },

    assignEnvironment : function(){

        // Inicializar formulario
        $('#tabla-data tbody tr').each(function() {
            $(this).find('select').val('');
            $(this).find('[type=checkbox]').prop('checked', false);
        });

        $('#content-parameterization-services').hide();
        $('#content-environments').show();

        const icm_environment_income_item_id = $(this).data('id');
        $('#btn-update-income-item').data('icm_environment_income_item_id', icm_environment_income_item_id);
        var element = $(this);
        btn.loading(element);
        setTimeout(function(){
            $.ajax({
                url: '/income/parameterization-services/' + icm_environment_income_item_id,
                async: true,
                data: {
                    enable_sale : true
                },
                beforeSend: function(objeto){

                },
                complete: function(objeto, exito){
                    btn.reset(element);
                    if(exito != "success"){
                        alert("No se completo el proceso!");
                    }
                },
                contentType: "application/x-www-form-urlencoded",
                dataType: "json",
                error: function(objeto, quepaso, otroobj){
                    alert("Ocurrio el siguiente error: "+quepaso);
                    btn.reset(element);
                },
                global: true,
                ifModified: false,
                processData:true,
                success: function(response){
                    btn.reset(element);
                    if(response.success){
                        services.viewFormNewEnvironmentService();
                        $('#form-environment-income-items :input').prop('disabled', true);
                        loadDataForm('form-environment-income-items', response.data);
                        $('#value').trigger('change');
                        $.each(response.environments_enabled, function(index, value){
                            var enabled = value.state == 'A' ? true : false;
                            $(`[type=checkbox][data-environment_id=${value.icm_environment_id}]`).prop('checked', enabled).prop('disabled', value.maestro);
                            $(`select[data-environment_id=${value.icm_environment_id}]`).val(value.icm_environment_icm_menu_item_id).prop('disabled', value.maestro);
                        })
                        $('#icm_environment_icm_menu_item_id').selectpicker('refresh');
                    }
                },
                timeout: 30000,
                type: 'GET'
            });
        },100)
    },

    init : function(){

        $(".monto").on('change click keyup input paste',(function (event) {
            $(this).val(function (index, value) {
                return value.replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            });
        }));

        Biblioteca.validacionGeneral('form-environment-income-items');
        $('body').on('click', '#btn-new-environment-service', this.viewFormNewEnvironmentService);
        $('body').on('click', '.edit-income-item', this.ediEnvironmentService);
        $('body').on('click', '.assign-environment', this.assignEnvironment);
        $('body').on('click', '#btn-save-income-item', this.confirmSaveIncomeItem);
        $('body').on('click', '#btn-update-income-item', this.confirmUpdateIncomeItem);

    }

}

function getLableState(state){

    switch (state) {
        case 'A':
            return '<label class="badge badge-success">Activo</label>';
            break;
        case 'I':
            return '<label class="badge badge-warning">Inactivo</label>';
            break;

        default:
            return '';
            break;
    }

}

loadDataForm = function(idform, data){
    Object.keys(data).forEach(key => {
        if($(`#${idform}`).find(`[name=${key}]`).length > 0){
            $(`#${idform}`).find(`[name=${key}]`).val(data[key]);
        }
    });
}

loadIncomeRates = function(idform, data){

    $.each(data, function(index, value){
        $(`input.valor_venta[data-type=${value.icm_types_income_id}][data-category_id=${value.icm_affiliate_category_id}][data-icm_rate_type_id=${value.icm_rate_type_id}]`).val(value.value).trigger('change');
        if($(`input.subsidio_venta[data-type=${value.icm_types_income_id}][data-category_id=${value.icm_affiliate_category_id}][data-icm_rate_type_id=${value.icm_rate_type_id}]`).length > 0){
            $(`input.subsidio_venta[data-type=${value.icm_types_income_id}][data-category_id=${value.icm_affiliate_category_id}][data-icm_rate_type_id=${value.icm_rate_type_id}]`).val(value.subsidy).trigger('change');
        }
    });
}

btn = {

  loading : function(element){
      var loadingText = '<i class="fa fa-spinner fa-spin"></i> Procesando...';
      if ($(element).html() !== loadingText) {
          $(element).data('original-text', $(element).html());
          $(element).html(loadingText);
          $(element).prop( "disabled", true );
      }
  },

  reset : function(element){
      $(element).html($(element).data('original-text'));
      $(element).prop( "disabled", false );
  }

}
