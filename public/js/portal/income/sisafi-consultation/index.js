

var tablconsultations = null;


consultations = {

    data_sycn : [],

    data : [],

    viewconsultations : function(){

        consultations.data_sycn = [];

        tablconsultations= $('#tbl-consultations').DataTable();
        tablconsultations.destroy();

        $('#tbl-consultations thead th').each(function () {
            var title = $(this).text();
            if($(this).hasClass('search-disabled')){
                $(this).html(title);
            }else{
                $(this).html(title+' <input type="text" class="col-search-input" placeholder="" />');
            }
        });

        tablconsultations = $('#tbl-consultations').DataTable({
            language: language_es,
            pagingType: "numbers",
            processing: true,
            serverSide: true,
            ajax: {
                url: '/income/datatable-consultations',
                type: "POST",
                data: {
                    '_token'    : $('input[name=_token]').val(),
                    'identificacion': $('#identificacion').val(),
                    'id_principal'  : $('#id_principal').val()
                },
                "dataSrc": function (json) {
                    return json.data;
                },
                async: true
            },
            columnDefs: [{
                targets: "_all",
                orderable: false
            }],

            initComplete : function(settings, json){
            },
            createdRow: function (row, data, index) {
                consultations.data_sycn.push({
                    'tipo_id'        : data[1],
                    'identificacion' : data[2],
                });
                $('td', row).eq(0).html(data[10]).addClass('dt-center');
            }
        });

        tablconsultations.columns().every(function () {
            var table = this;
            $('input', this.header()).on('keyup change', function () {
                if (table.search() !== this.value && (this.value.length > 2  || this.value.length == 0)) {
                    table.search(this.value).draw();
                }
            });
        });

    },

    printInvoice : function(){
        var icm_liquidation_id = $(this).data('id');
        window.open(`/income/billing-incomes-print/${icm_liquidation_id}`, null, 'width=300, height=700, toolbar=no, statusbar=no');
    },

    reloadLiquidation : function(){
        var icm_liquidation_id = $(this).data('id');
        var environment = $(this).data('environment');
        var nuevaUrl = '/income/billing-incomes' +
               '?environment=' + encodeURIComponent(environment) +
               '&icm_liquidation_id=' + encodeURIComponent(icm_liquidation_id);
        window.location.href = nuevaUrl;
    },

    viewLiquidationInvoice : function(){
        var icm_liquidation_id = $(this).data('id');
        invoice.icm_liquidation_id = icm_liquidation_id;
        invoice.loadLiquidationInfo();
    },

    syncAffiliates : function(){

        if(consultations.data_sycn.length <= 0 ){
            Biblioteca.notificaciones('No existe informacion para sincronizar', 'Sincronización', 'warning');
            return false;
        }

        element = $(this);

        swal({
            title: 'Sincroniar informacion Afiliados SISAFI',
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
                        url: '/income/sisafi-consultation',
                        async: true,
                        data: {
                            affiliates : consultations.data_sycn,
                            _token     : $('[name=_token]').val()
                        },
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
                            btn.reset(element);
                        },
                        global: true,
                        ifModified: false,
                        processData:true,
                        success: function(response){
                            if(response.success){
                                setTimeout(() => {
                                    Biblioteca.notificaciones('Proceso exitoso.', 'Sincronización', 'success');
                                    btn.reset(element);
                                    $('#btn-refresh-liquidation').trigger('click');
                                }, 4000);
                            }else{
                                btn.reset(element);
                                Biblioteca.notificaciones(response.message, 'Sincronización', 'error');
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

        $('body').on('click', '#btn-refresh-liquidation', consultations.viewconsultations);
        $('body').on('click', '.btn-print-invoice', consultations.printInvoice);
        $('body').on('click', '.btn-reload-liquidation', consultations.reloadLiquidation);
        $('body').on('click', '.btn-view-liquidation'  , consultations.viewLiquidationInvoice);
        $('body').on('click', '#btn-procesar-sync'  , consultations.syncAffiliates);

        // Obtener la fecha actual
        var fechaActual = new Date();

        // Formatear las fechas según tus necesidades (opcional)
        var primerDiaFormateado = fechaActual.toISOString().split('T')[0];
        var ultimoDiaFormateado = fechaActual.toISOString().split('T')[0];

        $('#date_from').val(primerDiaFormateado);
        $('#date_to').val(ultimoDiaFormateado);

        this.viewconsultations();
    }
}

invoice = {

    icm_liquidation_id : null,

    loadLiquidationInfo : function(){
        $('#md-liquidation-invoice').modal();
        invoice.loadLiquidationDetail();
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
                        var subsidio    = formatearNumero(value.subsidy);

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
                                                <th>Fidelidad</th>
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
                    // invoice.loadTablePeople();
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

                    let invoice_number = response.data.billing_prefix + response.data.consecutive_billing
                    $('#number-invoice').html(invoice_number);

                }
            },
            timeout: 30000,
            type: 'GET'
        });

    },

    loadTablePeople : function(services){

        var tr = '';


        $.each(services, function(index_service, service){

            $(`#tbl-${service.id} tbody`).empty();

            $.each(service.people, function(index, detail){

                // detail = funcionarios.depuraNulls(detail);
                var number = parseInt(index) + 1;

                btndeleteperson  = '';

                tr  ='<tr>'
                    +'    <td>'+ number +'</td>'
                    +'    <td>'+ detail.document_number +'</td>'
                    +'    <td>'+ detail.person_name +'</td>'
                    +'    <td>'+ detail.fidelidad +'</td>'
                    +'    <td>'+ detail.icm_types_income_name +'</td>'
                    +'    <td>'+ detail.icm_affiliate_category_name +'</td>'
                    +'    <td>'+ detail.icm_family_compensation_fund_name +'</td>'
                    +'    <td></td>'
                    +'    <td class="text-center">' + btndeleteperson +'    </td>'
                    +'</tr>';

                $(`#tbl-${service.id} tbody`).append(tr);

            })
        });

    },
}



setLabelState = function(state){
    switch (state) {
        case 'P':
            return '<label class="badge badge-warning" style="width: 75px;">En proceso</label>';
            break;
        case 'F':
            return '<label class="badge badge-success" style="width: 75px;">Facturada</label>';
            break;
        case 'X':
            return '<label class="badge badge-primary" style="width: 75px;">Coberturas</label>';
            break;

        default:
            return '';
            break;
    }
}

function completarConCeros(numero, longitud) {
    let numeroString = numero.toString();
    while (numeroString.length < longitud) {
        numeroString = '0' + numeroString;
    }
    return numeroString;
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


$(function(){
    consultations.init();
});
