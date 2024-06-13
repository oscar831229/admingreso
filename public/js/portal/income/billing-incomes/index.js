var wizard = null;
$(document).ready(function () {
    environment.init();
    invoice.init();
    payment.init();
});

environment = {

    id : null,

    viewEnviromentItems : function(){

        environment.id = $(this).data('environment_id');
        $('#title-application').hide();
        $('#name-environtment').html($(this).html());

        $("#div-environments").hide(350, function() {
            $("#div-content-enveiroment").show();
        });

        invoice.initServices(environment);
        services.initServices(environment);

        // Inactivar opción de ingreso afiliado - solo se activa si hay problemas con el servicio de consulta.
        $('#icm_types_income_id option:contains("AFILIADO")').hide()

    },

    init : function(){
        $('body').on('click', '.environment', this.viewEnviromentItems);
    }

}

invoice = {

    icm_liquidation_id : null,

    environment : null,

    family_group : [],

    initServices : function(environment){
        this.environment = environment;
        this.loadIncomeServices();
    },

    loadIncomeServices : function(){

        $.ajax({
            url: '/income/environment-income-services/' + this.environment.id,
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
                    sessionStorage.setItem('envei_income' + invoice.environment.id, income_services);
                    invoice.constructIncomeServices();
                    invoice.constructResolutionPayment();
                }
            },
            timeout: 30000,
            type: 'GET'

        });

    },

    constructIncomeServices : function(){
        const incomeservicesJson = sessionStorage.getItem('envei_income' + invoice.environment.id);
        const incomeservices = JSON.parse(incomeservicesJson);
        // options
        var option = `<option value="">Seleccione..</option>`;
        $.each(incomeservices.incomeservices, function(index, service){
            option += `<option value="${service.id}">${service.name}</option>`;
        });
        $('#icm_income_item_id').html(option);
    },

    constructResolutionPayment : function(){
        const incomeservicesJson = sessionStorage.getItem('envei_income' + invoice.environment.id);
        const incomeservices = JSON.parse(incomeservicesJson);
        // options

        var invoice_type = '';
        var name         = '';
        var option       = `<option value="">Seleccione..</option>`;
        $.each(incomeservices.resolutions_environtment, function(index, resolution){
            invoice_type = resolution.invoice_type == 'P' ? 'FACTURA POS' : 'FACTURA ELECTRÓNICA';
            name = resolution.prefix + ' - ' + invoice_type;
            option += `<option value="${resolution.id}">${name}</option>`;
        });
        $('#icm_resolution_id').html(option);
    },

    setIncomeServices : function(){

        $(this).prop('disabled', false);
        var icm_income_item_id = $(this).find('option:selected').val();
        const incomeservicesJson = sessionStorage.getItem('envei_income' + invoice.environment.id);
        const incomeservices = JSON.parse(incomeservicesJson);
        var number_places = '';
        $.each(incomeservices.incomeservices, function(index, income_item){
            if(income_item.id == icm_income_item_id){
                number_places = income_item.number_places;
                return false;
            }
        });

        $('#number_places').val(number_places);

        if(icm_income_item_id.trim() != ''){
            $(this).prop('disabled', true);
        }

        $('#document_number').focus();

    },

    changeIncomeServices : function(){
        $('#icm_income_item_id').val('').trigger('change');
    },

    searchForClient : function(){
        invoice.disableFields(false);
        let document_number = $('#document_number').val();
        if(document_number.trim() != ''){
            $("#loading").css("display", "block");
            $.ajax({
                url: '/income/search-client-document/' + document_number,
                async: true,
                data: {},
                beforeSend: function(objeto){

                },
                complete: function(objeto, exito){
                    $("#loading").css("display", "none");
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

                    $('#icm_types_income_id option:contains("AFILIADO")').hide()

                    if(response.success){

                        if(response.grupo_afilaido.length > 0){
                            invoice.family_group = response.grupo_afilaido;
                            invoice.seeFamilyGroup(response.grupo_afilaido, document_number);
                        }else if(response.client){
                            loadDataForm('form-billing-incomes', response.client);
                            $('#form-billing-incomes').validate().resetForm();
                            invoice.disableFields();
                        }

                        if(response.grupo_afilaido.length == 0){
                            $("#icm_types_income_id option:contains('PARTICULAR')").prop('selected', true).trigger('change');
                        }

                        if(!response.control_service){
                            Biblioteca.notificaciones('Problema con servicio de categorias', 'Ingresos a sede', 'warning');
                            Biblioteca.notificaciones(response.error_service, 'Ingresos a sede', 'warning');
                            $('#icm_types_income_id option:contains("AFILIADO")').show()
                        }

                    }
                },
                timeout: 30000,
                type: 'GET'
            });
        }
    },

    disableFields : function(disabled = true, parent = 'body'){
        $(parent).find('#document_type').attr('disabled', disabled);
        $(parent).find('#first_name').attr('disabled', disabled);
        $(parent).find('#second_name').attr('disabled', disabled);
        $(parent).find('#first_surname').attr('disabled', disabled);
        $(parent).find('#second_surname').attr('disabled', disabled);
    },

    seeFamilyGroup : function(family_group, document_number){
        var tr = '';
        $('#tbl-grupo-afiliado tbody').empty();
        $.each(family_group, function(index, value){

            let check = document_number == value.document_number ? 'checked' : '';

            tr += `<tr class="even pointer">
                <td class="a-center ">
                    <input type="checkbox" class="flat check-affiliate" data-index="${index}" ${check}>
                </td>
                <td class=" ">${value.document_number}</td>
                <td class=" ">${value.first_name} ${value.second_name} ${value.first_surname} ${value.second_surname}</td>
                <td class=" ">${value.icm_affiliate_category_code}</td>
                <td class=" ">${value.gender_code}</td>
                <td class=" ">${value.birth_date}</td>
                <td class="a-right a-right ">${value.number_years}</td>
                <td class="a-right a-right ">${value.name_company_affiliates}</td>
                </td>
            </tr>`;
        });

        $('#tbl-grupo-afiliado tbody').html(tr);
        setTimeout(() => {
            invoice.initIcheck();
            $('#md-grupo-afiliado').modal({backdrop: 'static', keyboard: false})
        }, 100);

    },

    initIcheck : function(){
        $("input.flat").iCheck('destroy');
        $("input.flat").iCheck({
            checkboxClass: "icheckbox_flat-green",
            radioClass: "iradio_flat-green"
        })
    },

    displayProcessColumns : function(){
        var text = $(this).find('option:selected').text();
        $('#icm_family_compensation_fund_id').val('');
        $('.div-family-compensation-fund').hide();
        $('#icm_companies_agreement_name').val('');
        $('#icm_companies_agreement_id').val('').trigger('change');


        switch (text) {
            case 'CAJAS SIN FRONTERAS':
                $('.div-family-compensation-fund').show();
                $('#icm_family_compensation_fund_id').val('');
                break;
            default:
                break;
        }
    },

    ediAgreement : function(){

        const icm_agreement_id = $('#icm_agreement_id').val();

        if(icm_agreement_id.trim() == '')
            return true;

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
                        $('#icm_environment_icm_menu_item_id').selectpicker('refresh');
                        $('#md-icm_environment_income_items').find('input').prop('disabled', true);
                    }else{
                    }
                },
                timeout: 30000,
                type: 'GET'
            });
        },100)
    },

    confirmbBillingIncomes : function(){

        if(!$('#form-billing-incomes').valid()){
            Biblioteca.notificaciones('Existe información sin diligenciar.', 'Ingreso sedes', 'warning');
            return false;
        }

        if($('#icm_income_item_id').val() == ''){
            Biblioteca.notificaciones('No ha seleccionado el servicio a utilizar', 'Ingreso sedes', 'warning');
            return false;
        }

        const element = $(this);
        service = {};
        service.icm_income_item_id = $('#icm_income_item_id').val();
        service.icm_liquidation_id = invoice.icm_liquidation_id;
        service._token = $('input[name=_token]').val();
        service.clients = [];

        invoice.disableFields(false);
        var client = $('#form-billing-incomes').serializeArray()
            .reduce(function(a, z) {
                a[z.name] = z.value;
                return a;
            }, {});
        service.clients.push(client);

        swal({
            title: 'Ingreso a sedes',
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
                        url: '/income/billing-incomes',
                        async: true,
                        data: service,
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
                                Biblioteca.notificaciones('Proceso exitoso.', 'Ingreso a sedes', 'success');
                                invoice.icm_liquidation_id = response.data.id;
                                let numeroCompleto = completarConCeros(invoice.icm_liquidation_id, 10);
                                $('#number-liquidation').html(numeroCompleto);
                                invoice.loadLiquidationDetail();
                                document.getElementById('form-billing-incomes').reset();
                                $('#icm_types_income_id').trigger('change');
                                $('#document_number').focus();
                                invoice.disableFields(false);
                            }else{
                                Biblioteca.notificaciones(response.message, 'Ingreso a sedes', 'error');
                            }
                        },
                        timeout: 30000,
                        type: 'POST'
                    });
                },60)
            }
        });
    },

    loadLiquidationDetail(){

        var icm_liquidation_id = invoice.icm_liquidation_id
        let numeroCompleto = completarConCeros(invoice.icm_liquidation_id, 10);
        $('#number-liquidation').html(numeroCompleto);

        $.ajax({
            url: '/income/billing-incomes-details/' + icm_liquidation_id,
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
                    var tr = '';
                    $('#tbl-details tbody').empty();
                    var number = 1;
                    $.each(response.data, function(key, value){

                        var base        = formatearNumero(value.base);
                        var iva         = formatearNumero(value.iva);
                        var impoconsumo = formatearNumero(value.impoconsumo);
                        var total       = formatearNumero(value.total);
                        var subsidio    = formatearNumero(value.icm_type_subsidy_id == 1 ? value.discount : 0);

                        tr += `<tr>
                            <td><h6 class="offset-md-3 let collapsed" data-toggle="collapse" href="#${value.id}" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-chevron-right mr-2" aria-hidden="true"></i>${number}<div></h6></td>
                            <td>${value.icm_environment_income_item_name}</td>
                            <td>${value.applied_rate_code}</td>
                            <td>${subsidio}</td>
                            <td>${base}</td>
                            <td>${iva}</td>
                            <td>${impoconsumo}</td>
                            <td>${total}</td>
                        </tr>`;

                        // Personas vinculadas a liquidación del servicio de entrada
                        tr += `<tr>
                            <td colspan="9">
                                <div class="card-body table-responsive p-0 col-sm-12 collapse" id="${value.id}" style="">
                                    <table class="table table-hover width60" id='tbl-${value.id}'>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th style="width: 20%">Número identificación</th>
                                                <th style="width: 30%">Nombre usuario</th>
                                                <th>Tipo ingreso</th>
                                                <th>Categoria</th>
                                                <th>Caja sin fronteras</th>
                                                <th class="text-center"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>`;

                        number++;

                    });
                    $('#tbl-details tbody').html(tr);
                    invoice.loadTablePeople();
                    invoice.getPeopleService();
                }
            },
            timeout: 30000,
            type: 'GET'

        });


        invoice.viewLiquidationTotals();


    },

    getPeopleService : function(){

        var icm_liquidation_id = invoice.icm_liquidation_id

        $.ajax({
            url: '/income/billing-people-services/' + icm_liquidation_id,
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
                    invoice.loadTablePeople(response.data);
                }
            },
            timeout: 30000,
            type: 'GET'
        });

    },

    viewLiquidationTotals : function(){

        var icm_liquidation_id = invoice.icm_liquidation_id

        $.ajax({
            url: '/income/view-liquidation-totals/' + icm_liquidation_id,
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

                    var subtotal      = formatearNumero(response.data.base);
                    var iva           = formatearNumero(response.data.iva);
                    var impoconsumo   = formatearNumero(response.data.impoconsumo);
                    var total         = formatearNumero(response.data.total);
                    var total_subsidy = formatearNumero(response.data.total_subsidy)

                    $('#subtotal').html(subtotal);
                    $('#iva').html(iva);
                    $('#impoconsumo').html(impoconsumo);
                    $('#total').html(total);
                    $('#total_subsidy').html(total_subsidy);

                }
            },
            timeout: 30000,
            type: 'GET'
        });

    },

    loadTablePeople : function(services){

        var tr = '';
        var btnedit = '';
        var btnupload = '';
        var btnview = '';
        var btndowload = '';
        var profiles = [];


        $.each(services, function(index_service, service){

            $(`#tbl-${service.id} tbody`).empty();

            $.each(service.people, function(index, detail){

                // detail = funcionarios.depuraNulls(detail);
                var number = parseInt(index) + 1;

                btnview = `<a href="javascript:void(0);" class="btn-accion-tabla view-support tooltipsC" title="Ver datos" data-index_contract="${index_service}" data-index="${index}" data-hrm_academic_training_people_id="${detail.id}">
                    <i class="fa fa-eye"></i>
                </a>`;

                btnedit   = '';
                btnupload = '';
                btnview   = '';

                // if(contract.state != 'I'){
                //     btnedit = `<a href="javascript:void(0);" class="btn-accion-tabla edit-support tooltipsC" title="Editar información documentos soporte" data-index_contract="${index_contract}" data-index="${index}" data-hrm_academic_training_people_id="${detail.hrm_academic_training_people_id}">
                //         <i class="fa fa-edit text-success"></i>
                //     </a>`;

                //     btnupload = detail.hrm_academic_training_people_id == 0 ? '' : `<a href="javascript:void(0);" class="btn-accion-tabla upload-support tooltipsC" title="Cargar documento soporte" data-index_contract="${index_contract}" data-index="${index}" data-hrm_academic_training_people_id="${detail.hrm_academic_training_people_id}" data-hrm_academic_training_id="${detail.hrm_academic_training_id}" style="color: #FF5722;">
                //         <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                //     </a>`;
                // }


                // btndowload = detail.common_general_document_id == 0 ? '' : `<a href="javascript:void(0);" class="btn-accion-tabla donwload-support tooltipsC" title="Descargar documento soporte" data-index_contract="${index_contract}" data-index="${index}" data-hrm_academic_training_people_id="${detail.hrm_academic_training_people_id}" data-hrm_academic_training_id="${detail.hrm_academic_training_id}" style="color: #0f6eb9;">
                //     <i class="fa fa-cloud-download" aria-hidden="true"></i>
                // </a>`;

                tr  ='<tr>'
                    +'    <td>'+ number +'</td>'
                    +'    <td>'+ detail.document_number +'</td>'
                    +'    <td>'+ detail.person_name +'</td>'
                    +'    <td>'+ detail.icm_types_income_name +'</td>'
                    +'    <td>'+ detail.icm_affiliate_category_name +'</td>'
                    +'    <td>'+ detail.icm_family_compensation_fund_name +'</td>'
                    +'    <td></td>'
                    +'    <td class="text-center">' +  btnview + btnedit + btnupload + btndowload
                    +'    </td>'
                    +'</tr>';

                $(`#tbl-${service.id} tbody`).append(tr);

            })
        });

    },

    conAffiliateRegistration : function(){

        if($('input.check-affiliate[type="checkbox"]:checked').length == 0){
            Biblioteca.notificaciones('No ha seleccionado afiliado a registra', 'Ingreso sedes', 'warning');
            return false;
        }

        const element = $(this);
        service = {};
        service.icm_income_item_id = $('#icm_income_item_id').val();
        service.icm_liquidation_id = invoice.icm_liquidation_id;
        service._token = $('input[name=_token]').val();
        service.clients = [];
        $('input.check-affiliate[type="checkbox"]:checked').each(function(key, element){
            var index = $(element).data('index');
            affiliate = invoice.family_group[index];
            affiliate.icm_companies_agreement_id = $('#icm_companies_agreement_affiliate_id').val();
            affiliate.icm_companies_agreement_name = $('#icm_companies_agreement_affiliate_name').val();
            affiliate.icm_agreement_id = $('#icm_agreement_affiliate_id').val();
            service.clients.push(affiliate);
        });

        swal({
            title: 'Ingreso a sedes',
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
                        url: '/income/billing-incomes',
                        async: true,
                        data: service,
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
                                Biblioteca.notificaciones('Proceso exitoso.', 'Ingreso a sedes', 'success');
                                invoice.icm_liquidation_id = response.data.id;
                                document.getElementById('form-billing-incomes').reset();
                                $('#document_number').focus();
                                $('#md-grupo-afiliado').modal('hide');
                                invoice.loadLiquidationDetail();
                            }else{
                                Biblioteca.notificaciones(response.message, 'Ingreso a sedes', 'error');
                            }
                        },
                        timeout: 30000,
                        type: 'POST'
                    });
                },60)
            }
        });

    },

    confirmExecutePay : function(){

        var element = $(this);

        // Asignar número de liquidación a pagar en la componente
        var icm_liquidation_id     = invoice.icm_liquidation_id;
        payment.icm_liquidation_id = invoice.icm_liquidation_id;
        payment.startPaymentProcess(element);

    },

    control_init_step : false,



    init : function(){

        Biblioteca.validacionGeneral('form-billing-incomes');

        $('body').on('change', '#icm_income_item_id', this.setIncomeServices);
        $('body').on('click', '#btn-change-income-service', this.changeIncomeServices);
        $('body').on('blur', '#document_number', this.searchForClient);
        $('body').on('change', '#icm_types_income_id', this.displayProcessColumns);
        $('body').on('click', '#edit-agreement', this.ediAgreement);
        $('body').on('click', '#btn-save', this.confirmbBillingIncomes);
        $('body').on('click', '#btn-mass-affiliate', this.conAffiliateRegistration);
        $('body').on('click', '#pay-settlement', this.confirmExecutePay);

        $('#icm_companies_agreement_name').autocomplete({
            serviceUrl:'/income/find-icm-companies-agreement',
            paramName: 'name',
            minChars: 3,
            tabDisabled:true,
            onHint: function (hint) {
                $('#icm_companies_agreement_name-x').val(hint);
                $('#icm_companies_agreement_id').val('').trigger('change');
            },
            onSelect: function(suggestion) {
                $('#icm_companies_agreement_id').val(suggestion.data.userid).trigger('change');
            }
        });

        $('#icm_companies_agreement_name_affiliate').autocomplete({
            serviceUrl:'/income/find-icm-companies-agreement',
            paramName: 'name',
            minChars: 3,
            tabDisabled:true,
            onHint: function (hint) {
                $('#icm_companies_agreement_name_affiliate-x').val(hint);
                $('#icm_companies_agreement_affiliate_id').val('').trigger('change');
            },
            onSelect: function(suggestion) {
                $('#icm_companies_agreement_affiliate_id').val(suggestion.data.userid).trigger('change');
            }
        });

        SelectAnidado.autocargado('icm_types_income_id','icm_affiliate_category_id', '/income/billing-incomes-category')
        SelectAnidado.autocargado('icm_companies_agreement_id','icm_agreement_id', '/income/billing-company-agreement')
        SelectAnidado.autocargado('icm_companies_agreement_affiliate_id','icm_agreement_affiliate_id', '/income/billing-company-agreement')

    }

}

payment = {

    document_number : null,

    icm_liquidation_id : 0,

    data : null,

    total_payment_recorded : 0,

    total_balance : 0,

    resetForm : function(){
        $('#form-billing-customer').find(':input').not('[name="document_number"], [name="_token"]').val('');
    },

    viewInfoLiquidation : function(){
        $('#step-2').find('#subtotal').html(formatearNumero(payment.data.base));
        $('#step-2').find('#iva').html(formatearNumero(payment.data.iva));
        $('#step-2').find('#impoconsumo').html(formatearNumero(payment.data.impoconsumo));
        $('#step-2').find('#total_subsidy').html(formatearNumero(payment.data.total_subsidy));
        $('#step-2').find('#total').html(formatearNumero(payment.data.total));
    },

    startPaymentProcess : function(element){

        btn.loading(element);

        setTimeout(function(){

            $.ajax({
                url: '/income/view-liquidation-payment/' + payment.icm_liquidation_id,
                async: true,
                data: {},
                beforeSend: function(objeto){

                },
                complete: function(objeto, exito){
                    if(exito != "success"){
                        alert("No se completo el proceso!");
                    }
                    btn.reset(element);
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
                        payment.data = response.icm_liquidation;
                        payment.reapplyPayments();
                        payment.viewInfoLiquidation();
                        loadDataForm('form-billing-customer', response.customer);
                        invoice.disableFields(true, '#form-billing-customer');
                        payment.document_number = response.customer.document_number;
                        $('#md-payment').modal({
                            backdrop: 'static',
                            keyboard: false
                        })
                    }
                },
                timeout: 30000,
                type: 'GET'
            });

        },100)

    },

    reapplyPayments : function(){

        var payment_methods = sessionStorage.getItem(payment.icm_liquidation_id);
        payment_methods     = payment_methods ? JSON.parse(payment_methods) : [];

        var balance             = payment.data.total;
        var new_payment_methods = [];

        $.each(payment_methods, function(key, payment_method){


            var value_received = payment_method.value_received;
            var value          = value_received > balance ? balance : value_received;
            var leftover_value = value_received > balance ? value_received - balance : 0;
            balance            = balance - value;

            new_payment_methods.push({
                payment_method      : payment_method.payment_method,
                payment_method_text : payment_method.payment_method_text,
                approval_date       : payment_method.approval_date,
                approval_number     : payment_method.approval_number,
                value_received      : value_received,
                value               : value,
                leftover_value      : leftover_value
            })

        });


        var payment_methodsJSON = JSON.stringify(new_payment_methods);

        // Guardar la cadena JSON en sessionStorage
        sessionStorage.setItem(payment.icm_liquidation_id, payment_methodsJSON);

    },

    searchForClient : function(){

        invoice.disableFields(false);
        let document_number = $(this).val();

        if(document_number.trim() != '' && payment.document_number != document_number){

            $.ajax({
                url: `/income/search-client-document/${document_number}?notcategory=true`,
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
                        payment.document_number = document_number;
                        invoice.disableFields(false, '#form-billing-customer');
                        payment.resetForm();
                        if(response.client){
                            loadDataForm('form-billing-customer', response.client);
                            $('#form-billing-customer').validate().resetForm();
                            invoice.disableFields(true, '#form-billing-customer');
                            $('#form-billing-customer').find('#document_type').trigger('change');
                        }else{
                            $('#form-billing-customer [name=document_type]').focus();
                        }
                    }
                },
                timeout: 30000,
                type: 'GET'
            });

        }

    },

    init_SmartWizard : function () {

        wizard = $('#wizard').smartWizard({
            selected: 0,
            keyNavigation: false,
            labelNext :  'Siguiente',
            labelPrevious : 'Anterior',
            labelFinish : 'Generar factura',
            buttonOrder: ['prev', 'next'],
            hideButtonsOnDisabled: true,
            showFinishButtonAlways: false,
            onFinish: function(){
                // Mostrar el botón de finalización solo en el último paso
                // $('#smartwizard').smartWizard('showFinish');
                alert('Oprimio el botono finsh');
            },
            onLeaveStep: function(obj, context){

                var stepIndex     = context.fromStep; // Índice del paso actual
                var nextStepIndex = context.toStep; // Índice del siguiente paso


                if(nextStepIndex > stepIndex && stepIndex == 1 ){

                    if(!$('#form-billing-customer').valid()){
                        Biblioteca.notificaciones('Existe información requerida sin diligenciar.', 'Pago ingreso a sedes', 'warning');
                        return false;
                    }

                    // Actualizar datos clientes
                    request = $('#form-billing-customer').serialize();
                    invoice.disableFields(true, '#form-billing-customer');
                    var element = $('.buttonNext');
                    btn.loading(element);
                    setTimeout(function(){
                        $.ajax({
                            url: `/income/billing-incomes/${payment.icm_liquidation_id}`,
                            async: false,
                            data: request,
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
                                }else{
                                    Biblioteca.notificaciones(response.message, 'Ingreso a sedes', 'error');
                                }
                            },
                            timeout: 30000,
                            type: 'PUT'
                        });
                    },60)

                }

                // Cargar pagos realizados
                payment.uploadInvoicePayments();

                return true;

            }
        });

        $('.buttonNext').addClass('btn btn-primary');
        $('.buttonPrevious').addClass('btn btn-default');
        $('.buttonFinish').addClass('btn btn-default');

    },

    changeDocumentType : function(){

        var selected_option = $(this).children("option:selected").text().toUpperCase();

        // Emepresa
        if(selected_option == 'NIT'){
            $('#label-type-person').html('Razon social');
            $('.div-only-for-person').hide();
            $('.div-first-name').removeClass('col-lg-3');
            $('.div-first-name').addClass('col-lg-12');
            $('#form-billing-customer').find('#first_surname').attr('required', false);
            $('#form-billing-customer').find('#first_name').attr('placeholder', 'Razon social');
        }else{
            // Persona
            $('#label-type-person').html('Primer nombre');
            $('.div-only-for-person').show();
            $('.div-first-name').addClass('col-lg-3');
            $('.div-first-name').removeClass('col-lg-12');
            $('#form-billing-customer').find('#first_surname').attr('required', true);
            $('#form-billing-customer').find('#first_name').attr('placeholder', 'Primer nombre');
        }
        $('#form-billing-customer').find('#first_name').focus();
    },

    changePaymentMethod : function(){

        var type_payment = $(this).find("option:selected").data('type-payment');
        $("#form-payments input:not([name='input3'])").val('');
        $("#form-payments input[name='approval_date'], #form-payments input[name='approval_number']").prop('disabled', true);
        $('.required-info-payment').hide();
        $('#approval_date, #approval_number').attr('required', false);
        if(type_payment == 'T'){
            $("#form-payments input[name='approval_date'], #form-payments input[name='approval_number']").prop('disabled', false);
            $('.required-info-payment').hide();
            $('#approval_date, #approval_number').attr('required', true);
            $('.required-info-payment').show();
        }else{
            $("#form-payments input[name='value']").focus()
            $('.required-info-payment').hide();
        }

        var balance = payment.data.total - payment.total_payment_recorded;
        if($("#form-payments input[name='value']").val() == ''){
            $("#form-payments input[name='value']").val(balance).trigger('change');
        }

    },

    registerPaymentMethod : function(){

        // Validar formulario de pago
        if(!$('#form-payments').valid()){
            Biblioteca.notificaciones('Existe información sin diligenciar.', 'Metodos de pago', 'warning');
            return false;
        }

        var payment_methods = sessionStorage.getItem(payment.icm_liquidation_id);
        payment_methods     = payment_methods ? JSON.parse(payment_methods) : [];

        var payment_method       = $('#form-payments').find('#payment-method').val();
        var payment_method_text  = $('#form-payments').find('#payment-method option:selected').text();
        var approval_date        = $('#form-payments').find('#approval_date').val();
        var approval_number      = $('#form-payments').find('#approval_number').val();
        var value                = $('#form-payments').find('#value').val();

        // Eliminar comas de la cadena
        var valuesincomas = value.replace(',', '');

        // Convertir la cadena a un número flotante
        var valuefloat      = parseFloat(valuesincomas);
        var value           = valuefloat > payment.total_balance ? payment.total_balance : valuefloat;
        var leftover_value  = valuefloat > payment.total_balance ? valuefloat - payment.total_balance : 0;

        payment_methods.push({
            payment_method      : payment_method,
            payment_method_text : payment_method_text,
            approval_date       : approval_date,
            approval_number     : approval_number,
            value_received      : valuefloat,
            value               : value,
            leftover_value      : leftover_value
        })

        var payment_methodsJSON = JSON.stringify(payment_methods);

        // Guardar la cadena JSON en sessionStorage
        sessionStorage.setItem(payment.icm_liquidation_id, payment_methodsJSON);

        // Guardar información del metodo en session
        payment.uploadInvoicePayments();

        // Genera saldos nuevos
        Biblioteca.notificaciones('registrado de forma exitosa.', 'Metodos de pago', 'success');
        document.getElementById('form-payments').reset();

    },

    uploadInvoicePayments : function(){

        var payment_methods = sessionStorage.getItem(payment.icm_liquidation_id);
        payment_methods     = payment_methods ? JSON.parse(payment_methods) : [];
        var tr  = '';
        var total_payments       = 0;
        var total_value_returned = 0;
        $('#tbl-income-payments tbody').empty();
        var number = 1;


        $.each(payment_methods, function(key, value){

            var btndelete = `<a href="javascript:void(0)" data-index="${key}" class="tooltipsC btn-delete-method-payment" title="Borrar forma de pago"><i class="fa fa-trash-o text-danger" aria-hidden="true"></i></a>`;

            tr += `<tr>
                <td>${number} ${btndelete}</td>
                <td>${value.payment_method_text}</td>
                <td>${value.approval_date}</td>
                <td>${value.approval_number}</td>
                <td>${formatearNumero(value.value_received)}</td>
                <td>${formatearNumero(value.value)}</td>
                <td>${formatearNumero(value.leftover_value)}</td>
            </tr>`

            total_payments       = total_payments + value.value;
            total_value_returned = total_value_returned + value.leftover_value
            number++;

        });

        $('#tbl-income-payments tbody').html(tr);
        $('#total_payment').html(formatearNumero(total_payments));
        $('#total_value_returned').html(formatearNumero(total_value_returned));

        payment.total_balance = payment.data.total - total_payments;
        $('#total_balance').html(formatearNumero(payment.total_balance));

        $('#btn-execute-payment').attr('disabled', true);
        if(payment.total_balance == 0){
            $('#btn-execute-payment').attr('disabled', false);
        }

        payment.total_payment_recorded = total_payments;

    },

    deleteMethodPayment : function(){

        var index = $(this).data('index');

        swal({
            title: 'Eliminar forma de pago',
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

                var payment_methods = sessionStorage.getItem(payment.icm_liquidation_id);
                payment_methods     = payment_methods ? JSON.parse(payment_methods) : [];
                payment_methods.splice(index, 1);

                var payment_methodsJSON = JSON.stringify(payment_methods);
                sessionStorage.setItem(payment.icm_liquidation_id, payment_methodsJSON);
                payment.reapplyPayments();
                payment.uploadInvoicePayments();

            }
        });

    },

    executePayment : function(){
        $('#md-resolutions').modal();
    },

    acceptPayment : function(){

        var icm_resolution_id = $('#icm_resolution_id').val();
        element               = $(this);

        swal({
            title: 'Genera comprobante',
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

                var payment_methods = sessionStorage.getItem(payment.icm_liquidation_id);
                payment_methods     = payment_methods ? JSON.parse(payment_methods) : [];
                btn.loading(element);

                setTimeout(function(){
                    $.ajax({
                        url: '/income/pay-billing-incomes',
                        async: true,
                        data: {
                            icm_liquidation_id : payment.icm_liquidation_id,
                            icm_resolution_id  : icm_resolution_id,
                            payment_methods    : payment_methods,
                            _token             : $('[name=_token]').val()
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
                                Biblioteca.notificaciones('Proceso exitoso.', 'Ingreso a sedes', 'success');
                                invoice.icm_liquidation_id = response.data.id;
                                let numeroCompleto = completarConCeros(invoice.icm_liquidation_id, 10);
                                $('#number-liquidation').html(numeroCompleto);
                                invoice.loadLiquidationDetail();
                                document.getElementById('form-billing-incomes').reset();
                                $('#icm_types_income_id').trigger('change');
                                $('#document_number').focus();
                                invoice.disableFields(false);
                            }else{
                                Biblioteca.notificaciones(response.message, 'Ingreso a sedes', 'error');
                            }
                        },
                        timeout: 30000,
                        type: 'POST'
                    });
                },60)

            }
        });
    },

    init : function(){

        Biblioteca.validacionGeneral('form-billing-customer');
        Biblioteca.validacionGeneral('form-payments');

        $('#form-billing-customer').on('blur', '#document_number', this.searchForClient);
        $('#form-billing-customer').on('change', '#document_type', this.changeDocumentType);

        $('#form-payments').on('change', '#payment-method', this.changePaymentMethod);
        $('#form-payments').on('click', '.btn-save-method-payment', this.registerPaymentMethod);
        $('#tbl-income-payments').on('click', '.btn-delete-method-payment', this.deleteMethodPayment);

        $('body').on('click', '#btn-execute-payment', this.executePayment);
        $('#md-resolutions').on('click', '#btn-accept-payment', this.acceptPayment);


        $("#form-payments").on('keypress',"input[name='value']", function(event){
            if(event.which === 13){
                event.preventDefault();
                payment.registerPaymentMethod();
            }
        });

        $(".monto").on('change click keyup input paste',(function (event) {
            $(this).val(function (index, value) {
                return value.replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            });
        }));

        $('#md-payment').on('shown.bs.modal', function () {

            if(!invoice.control_init_step){
                payment.init_SmartWizard();
                invoice.control_init_step = true;
            }else{
                wizard.smartWizard('goToStep', 1)
                setTimeout(() => {
                    wizard.smartWizard('disableStep', 2)
                    wizard.smartWizard('disableStep', 3)
                }, 360);
            }

        });

    }
}

services = {

    environment : null,

    initServices : function(environment){
        services.environment = environment;
        this.loadIncomeServices();
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
            <th class="text-left" style="width:15%">SUCURSAL O SEDE</th>
            <th class="text-left" style="width:50%">SERVICIO INGRESO</th>`;
        $.each(incomeservices.rate_types, function(index, rate_type){
            head += `<th class="text-center" style="width:10%">${rate_type.name}</th>`;
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

    viewFormNewEnvironmentService : function(){
        document.getElementById('form-agreement').reset();
        document.getElementById('form-income-item-rate').reset();

        $('#form-agreement').find("[name=id]").val('');
        $('#icm_environment_icm_menu_item_id').selectpicker('refresh');
        $('#md-icm_environment_income_items').modal()
        $('#form-agreement').find('#name').attr('disabled', false);
        $('#form-agreement').find('#code').attr('disabled', false);
    },
}

loadIncomeRates = function(idform, data){
    $.each(data, function(index, value){
        $(`input[data-income_item_id=${value.icm_environment_income_item_id}][data-rate_type_id=${value.icm_rate_type_id}]`).val(value.value).trigger('change');
    });
}

loadDataForm = function(idform, data){
    Object.keys(data).forEach(key => {
        if($(`#${idform}`).find(`[name=${key}]`).length > 0){
            $(`#${idform}`).find(`[name=${key}]`).val(data[key]);
        }
    });
}


formatearNumero = function(numero){

    const totalFormateada = new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP'
    }).format(numero);

    return totalFormateada;

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


function completarConCeros(numero, longitud) {
    let numeroString = numero.toString();
    while (numeroString.length < longitud) {
        numeroString = '0' + numeroString;
    }
    return numeroString;
}


