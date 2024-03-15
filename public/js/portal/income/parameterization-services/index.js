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

                $('td', row).eq(6).html(getLableState(data[6])).addClass('dt-center');
                $('td', row).eq(7).html(btnedit).addClass('dt-center');
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
        document.getElementById('form-environment-income-items').reset();
        document.getElementById('form-income-item-rate').reset();

        $('#form-environment-income-items').find("[name=id]").val('');
        $('#icm_environment_icm_menu_item_id').selectpicker('refresh');
        $('#md-icm_environment_income_items').modal()
        $('#form-environment-income-items').find('#name').attr('disabled', false);
        $('#form-environment-income-items').find('#code').attr('disabled', false);
    },

    confirmSavaIncomeItem : function(){

        if(!$('#form-environment-income-items').valid()){
            Biblioteca.notificaciones('Existen datos pendientes de diligenciar.', 'Comercios aliados', 'warning');
            return false;
        }

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
                                Biblioteca.notificaciones('Proceso exitoso.', 'Comercios aliados', 'success');
                                $('#md-icm_environment_income_items').modal('hide')
                                tblenvironmentservices.ajax.reload();
                            }else{
                                Biblioteca.notificaciones(response.message, 'Comercios aliados', 'error');
                            }
                        },
                        timeout: 30000,
                        type: 'POST'
                    });
                },100)
            }
        });
    },

    getIncomeItemrate : function(){
        var income_rate = [];
        $('.rate').each(function(index, element){
            if($(element).val().trim() != ''){
                var rate = {}
                rate.types_of_income_id        = $(element).data('type');
                rate.icm_affiliate_category_id = $(element).data('category_id');
                rate.value                     = $(element).val();
                income_rate.push(rate);
            }
        });
        return income_rate;
    },

    ediEnvironmentService : function(){
        const icm_environment_income_item_id = $(this).data('id');
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

    init : function(){

        $(".monto").on('change click keyup input paste',(function (event) {
            $(this).val(function (index, value) {
                return value.replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            });
        }));

        Biblioteca.validacionGeneral('form-environment-income-items');
        $('body').on('click', '#btn-new-environment-service', this.viewFormNewEnvironmentService);
        $('body').on('click', '.edit-income-item', this.ediEnvironmentService);
        $('body').on('click', '#btn-save-income-item', this.confirmSavaIncomeItem);

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
        $(`input[data-type=${value.types_of_income_id}][data-category_id=${value.icm_affiliate_category_id}]`).val(value.value).trigger('change');
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
