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

        $('#icm_companies_agreement_name').autocomplete({
            serviceUrl:'/income/find-icm-companies-agreement',
            paramName: 'name',
            minChars: 3,
            tabDisabled:true,
            onHint: function (hint) {
                $('#icm_companies_agreement_name-x').val(hint);
                $('#icm_companies_agreement_id').val('');
            },
            onSelect: function(suggestion) {
                $('#icm_companies_agreement_id').val(suggestion.data.userid);
            }
        });

        $('body').on('click', '.environment', this.viewEnviromentItems);
    }

}

services = {

    environment : null,

    initServices : function(environment){
        services.environment = environment;
        this.loadTableServices();

    },

    loadIncomeServices : function(){

        $.ajax({
            url: '/income/environment-income-services/0',
            async: false,
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
                    // const income_services = JSON.stringify(response.data);
                    // sessionStorage.setItem('envei_income' + services.environment.id, income_services);
                    services.constructIncomeServices(response.data);
                }
            },
            timeout: 30000,
            type: 'GET'

        });

    },

    constructIncomeServices : function(incomeservices){

        // const incomeservicesJson = sessionStorage.getItem('envei_income' + this.environment.id);
        // const incomeservices = JSON.parse(incomeservicesJson);

        tr = '';
        $('#tbl-income-items tbody').empty();
        var number = 1;

        // head
        var head = ` <tr>
            <th class="text-left" style="width:5%">#</th>
            <th class="text-left" style="width:10%">Ambiente</th>
            <th class="text-left" style="width:40%">Servicio ingreso</th>`;
        $.each(incomeservices.rate_types, function(index, rate_type){
            head += `<th class="text-center" style="width:15%">${rate_type.name}</th>`;
        });
        head += `</tr>`
        $('#tbl-income-items thead').html(head);



        $.each(incomeservices.incomeservices, function(index, service){
            tr += `
                <tr>
                    <td>${number}</td>
                    <td>${service.icm_environment_name}</td>
                    <td>${service.name}</td>`;
                    $.each(incomeservices.rate_types, function(index, rate_type){
                        tr += `<td class="text-center"><input placeholder="" data-rate_type_id="${rate_type.id}" data-income_item_id="${service.id}" class="form-control form-control-sm monto rate" style="height: 25px;" value=""></td>`;
                    });
            tr += `</tr>`;
            number++;
        });

        $('#tbl-income-items tbody').html(tr);

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
                    const income_services = JSON.stringify(response.data);
                    sessionStorage.setItem('envei_income' + services.environment.id, income_services);
                    services.constructIncomeServices();
                }
            },
            timeout: 30000,
            type: 'GET'

        });

    },

    loadTableServices : function(){

        tblenvironmentservices= $('#tbl-agreements').DataTable();
        tblenvironmentservices.destroy();

        $('#tbl-agreements thead th').each(function () {
            var title = $(this).text();
            if($(this).hasClass('search-disabled')){
                $(this).html(title);
            }else{
                $(this).html(title+' <input type="text" class="col-search-input" placeholder="" />');
            }
        });

        tblenvironmentservices = $('#tbl-agreements').DataTable({
            language: language_es,
            pagingType: "numbers",
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '/income/datatable-parameterization-agreements',
                type: "POST",
                data: {
                    '_token' : $('input[name=_token]').val(),
                    state    : $('#state').val()
                },
                "dataSrc": function (json) {
                    return json.data;
                },
                async: true
            },
            columnDefs: [{
                targets: "_all",
                orderable: false,
            },{ "width": "150px", "targets": 1 },{ "width": "50px", "targets": 8 }],

            initComplete: function () {
            },
            createdRow: function (row, data, index) {

                btnedit = data[7] == 'A' ? '<a href="javaScript:void(0)" data-id="'+data[0]+'" class="mr-2 edit-agreement" title="Editar información del convenio">'
                    + '<i class="fa fa-edit text-success"></i>'
                    + '</a>' : '';

                btnview = `<a href="javaScript:void(0)" data-id="${data[0]}" class="mr-2 view-agreement" title="Ver información del convenio">
                    <i class="fa fa-eye text-primary"></i>
                </a>`;

                $('td', row).eq(7).html(getLableState(data[7])).addClass('dt-center');
                $('td', row).eq(8).html(btnview + btnedit).addClass('dt-center');
                $('td', row).eq(0).html(data[9]).addClass('dt-center');
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

        services.loadIncomeServices();
        document.getElementById('form-agreement').reset();
        document.getElementById('form-income-item-rate').reset();

        $('#form-agreement').find("[name=id]").val('');
        $('#icm_environment_icm_menu_item_id').selectpicker('refresh');
        $('#md-icm_environment_income_items').modal()
        $('#form-agreement').find('#name').attr('disabled', false);
        $('#form-agreement').find('#code').attr('disabled', false);
        $('#example-0').find('input:checkbox:checked').prop('checked', false).change();
        // $('#example-0').find('input:checkbox:eq(2)').click();

    },

    confirmSaveAgreement : function(){

        if(!$('#form-agreement').valid()){
            Biblioteca.notificaciones('Existen datos pendientes de diligenciar.', 'Convenios empresariales', 'warning');
            return false;
        }

        var type_income_check = [];
        $('#example-0').find('input.capture:checkbox:checked').each(function(index, element){
            var check = {};
            check.icm_types_income_id       = $(element).data('type_income_id');
            check.icm_affiliate_category_id = $(element).data('category_id');
            type_income_check.push(check);
        });

        const element = $(this);
        var jsonData=$('#form-agreement').serializeArray()
            .reduce(function(a, z) {
                a[z.name] = z.value;
                return a;
            }, {});

        var income_item_rate       = services.getIncomeItemRate();
        jsonData.income_rates      = income_item_rate;
        jsonData.type_income_check = type_income_check

        swal({
            title: 'Convenios empresariales',
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
                        url: '/income/parameterization-agreements',
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
                                Biblioteca.notificaciones('Proceso exitoso.', 'Convenios empresariales', 'success');
                                $('#md-icm_environment_income_items').modal('hide')
                                tblenvironmentservices.ajax.reload();
                            }else{
                                Biblioteca.notificaciones(response.message, 'Convenios empresariales', 'error');
                            }
                        },
                        timeout: 30000,
                        type: 'POST'
                    });
                },100)
            }
        });
    },

    getIncomeItemRate : function(){
        var income_rate = [];
        $('.rate').each(function(index, element){
            if($(element).val().trim() != ''){
                var rate = {}
                rate.icm_environment_income_item_id = $(element).data('income_item_id');
                rate.icm_rate_type_id               = $(element).data('rate_type_id');
                rate.value                          = $(element).val();
                income_rate.push(rate);
            }
        });
        return income_rate;
    },

    ediAgreement : function(){

        $('#form-agreement').find('#name').attr('disabled', false);
        $('#form-agreement').find('#code').attr('disabled', false);
        $('#form-agreement').find('#icm_companies_agreement_name').attr('disabled', false);
        $('#form-agreement').find('#date_from').attr('disabled', false);
        $('#form-agreement').find('#date_to').attr('disabled', false);
        $('#form-agreement').find('#observations').attr('disabled', false);
        $('#btn-save-agreement').show()

        const icm_agreement_id = $(this).data('id');
        var element = $(this);
        btn.loading(element);
        setTimeout(function(){
            $.ajax({
                url: '/income/parameterization-agreements/' + icm_agreement_id,
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
                        loadDataForm('form-agreement', response.data);
                        $('#value').trigger('change');
                        loadIncomeRates('body', response.income_item_detail);
                        checkIncomeTypes('#example-0', response.agreement_typeincomes);

                        $('#icm_environment_icm_menu_item_id').selectpicker('refresh');
                        $('#form-agreement').find('#name').attr('disabled', true);
                        $('#form-agreement').find('#code').attr('disabled', true);
                    }else{
                    }
                },
                timeout: 30000,
                type: 'GET'
            });
        },100)
    },

    viewAgreement : function(){

        const icm_agreement_id = $(this).data('id');

        var element = $(this);
        btn.loading(element);
        setTimeout(function(){
            $.ajax({
                url: '/income/parameterization-agreements/' + icm_agreement_id,
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
                        loadDataForm('form-agreement', response.data);
                        $('#value').trigger('change');
                        loadIncomeRates('body', response.income_item_detail);
                        checkIncomeTypes('#example-0', response.agreement_typeincomes);

                        $('#icm_environment_icm_menu_item_id').selectpicker('refresh');

                        $('#form-agreement').find('#name').attr('disabled', true);
                        $('#form-agreement').find('#code').attr('disabled', true);
                        $('#form-agreement').find('#icm_companies_agreement_name').attr('disabled', true);
                        $('#form-agreement').find('#date_from').attr('disabled', true);
                        $('#form-agreement').find('#date_to').attr('disabled', true);
                        $('#form-agreement').find('#observations').attr('disabled', true);
                        $('#btn-save-agreement').hide()


                    }else{
                    }
                },
                timeout: 30000,
                type: 'GET'
            });
        },100)
    },

    init : function(){

        $("body").on('change click keyup input paste', '.monto' ,(function (event) {
            $(this).val(function (index, value) {
                return value.replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            });
        }));

        Biblioteca.validacionGeneral('form-agreement');
        $('body').on('click', '#btn-new-environment-service', this.viewFormNewEnvironmentService);
        $('body').on('click', '.edit-agreement', this.ediAgreement);
        $('body').on('click', '#btn-save-agreement', this.confirmSaveAgreement);
        $('body').on('change', '#state', this.loadTableServices);
        $('body').on('click', '.view-agreement', this.viewAgreement);

        this.loadTableServices();

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
        $(`input[data-income_item_id=${value.icm_environment_income_item_id}][data-rate_type_id=${value.icm_rate_type_id}]`).val(value.value).trigger('change');
    });
}

checkIncomeTypes = function(element_id, data){
    $.each(data, function(index, value){
        $(`input[data-type_income_id=${value.icm_types_income_id}][data-category_id=${value.icm_affiliate_category_id}]`).click();
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
