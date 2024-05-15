$(document).ready(function () {
    environment.init();
    invoice.init();
});

environment = {

    id : null,

    viewEnviromentItems : function(){

        environment.id = $(this).data('environment_id');
        $('#title-application').hide();
        $('#name-environtment').html($(this).html());

        $("#div-environments").hide(250, function() {
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
        }1
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

        const element = $(this);
        service = {};
        service.icm_income_item_id = $('#icm_income_item_id').val();
        service.icm_liquidation_id = invoice.icm_liquidation_id;
        service._token = $('input[name=_token]').val();
        service.clients = [];

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
                                Biblioteca.notificaciones('Proceso exitoso.', 'Comercios aliados', 'success');
                                invoice.icm_liquidation_id = response.data.id;
                                let numeroCompleto = completarConCeros(invoice.icm_liquidation_id, 10);
                                $('#number-liquidation').html(numeroCompleto);
                                invoice.loadLiquidationDetail();
                                document.getElementById('form-billing-incomes').reset();
                                $('#document_number').focus();
                            }else{
                                Biblioteca.notificaciones(response.message, 'Comercios aliados', 'error');
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

                        tr += `<tr>
                            <td><h6 class="offset-md-3 let collapsed" data-toggle="collapse" href="#${value.id}" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-chevron-right mr-2" aria-hidden="true"></i>${number}<div></h6></td>
                            <td>${value.icm_environment_income_item_name}</td>
                            <td>${value.applied_rate_code}</td>
                            <td>${value.base}</td>
                            <td>${value.iva}</td>
                            <td>${value.impoconsumo}</td>
                            <td>${value.total}</td>
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

                    const base = response.data.base;
                    const iva = response.data.iva;
                    const impoconsumo = response.data.impoconsumo;
                    const total = response.data.total;

                    // Formatear la cantidad a pesos colombianos
                    const baseFormateada = new Intl.NumberFormat('es-CO', {
                        style: 'currency',
                        currency: 'COP'
                    }).format(base);

                    const ivaFormateada = new Intl.NumberFormat('es-CO', {
                        style: 'currency',
                        currency: 'COP'
                    }).format(iva);

                    const impoconsumoFormateada = new Intl.NumberFormat('es-CO', {
                        style: 'currency',
                        currency: 'COP'
                    }).format(impoconsumo);

                    const totalFormateada = new Intl.NumberFormat('es-CO', {
                        style: 'currency',
                        currency: 'COP'
                    }).format(total);

                    $('#subtotal').html(baseFormateada);
                    $('#iva').html(ivaFormateada);
                    $('#impoconsumo').html(impoconsumoFormateada);
                    $('#total').html(totalFormateada);

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

                btnedit = '';
                btnupload = '';

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
                                Biblioteca.notificaciones('Proceso exitoso.', 'Comercios aliados', 'success');
                                invoice.icm_liquidation_id = response.data.id;
                                document.getElementById('form-billing-incomes').reset();
                                $('#document_number').focus();
                                $('#md-grupo-afiliado').modal('hide');
                                invoice.loadLiquidationDetail();
                            }else{
                                Biblioteca.notificaciones(response.message, 'Comercios aliados', 'error');
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
        alert('Ejecutando pago');
    },

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

services = {

    environment : null,

    initServices : function(environment){
        services.environment = environment;
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
                    sessionStorage.setItem('envei_income' + services.environment.id, income_services);
                    services.constructIncomeServices();
                }
            },
            timeout: 30000,
            type: 'GET'

        });

    },

    constructIncomeServices : function(){

        const incomeservicesJson = sessionStorage.getItem('envei_income' + this.environment.id);
        const incomeservices = JSON.parse(incomeservicesJson);

        tr = '';
        $('#tbl-income-items tbody').empty();
        var number = 1;

        // head
        var head = ` <tr>
                        <th class="text-left">#</th>
                        <th class="text-left" style="width:70%">SERVICIO INGRESO</th>
                        <th class="text-center">VENTA</th>`;
        $.each(incomeservices.rate_types, function(index, rate_type){
            head += `<th class="text-center" style="width:10%">${rate_type.name}</th>`;
        });
        head += `</tr>`
        $('#tbl-income-items thead').html(head);



        $.each(incomeservices.incomeservices, function(index, service){
            tr += `
                <tr>
                    <td>${number}</td>
                    <td>${service.name}</td>
                    <td class="text-center">${service.income_type}</td>`;
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
        $(`input[data-income_item_id=${value.icm_income_item_id}][data-rate_type_id=${value.icm_rate_type_id}]`).val(value.value).trigger('change');
    });
}

loadDataForm = function(idform, data){
    Object.keys(data).forEach(key => {
        if($(`#${idform}`).find(`[name=${key}]`).length > 0){
            $(`#${idform}`).find(`[name=${key}]`).val(data[key]);
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


function completarConCeros(numero, longitud) {
    let numeroString = numero.toString(); // Convertir el número a cadena de texto
    while (numeroString.length < longitud) {
        numeroString = '0' + numeroString; // Agregar ceros a la izquierda
    }
    return numeroString;
}
