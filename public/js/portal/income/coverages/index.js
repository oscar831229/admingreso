

var tablcoverages = null;


coverages = {

    data : [],

    viewcoverages : function(){

        tablcoverages= $('#tbl-coverages').DataTable();
        tablcoverages.destroy();

        $('#tbl-coverages thead th').each(function () {
            var title = $(this).text();
            if($(this).hasClass('search-disabled')){
                $(this).html(title);
            }else{
                $(this).html(title+' <input type="text" class="col-search-input" placeholder="" />');
            }
        });

        tablcoverages = $('#tbl-coverages').DataTable({
            language: language_es,
            pagingType: "numbers",
            processing: true,
            serverSide: true,
            ajax: {
                url: '/income/datatable-coverages',
                type: "POST",
                data: {
                    '_token'    : $('input[name=_token]').val(),
                    'state'     : $('#state_liquidation').val(),
                    'date_from' : $('#date_from').val(),
                    'date_to'   : $('#date_to').val(),
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
                var errors = data[7];
                var btnaction = data[5] == 'D' ? '<a href="javascript:void(0)" data-id="'+data[0]+'" data-environment="'+data[4]+'" class="tooltipsC" title="'+errors+'"><i class="fa fa-exclamation-triangle text-warning" aria-hidden="true"></i></a>' : '';
                var btnstart  = '<a href="javascript:void(0)" data-id="'+data[0]+'" data-environment="'+data[4]+'" class="tooltipsC btn-reprocess-work ml-2" title="Reprocesar día"><i class="fa fa-tasks" aria-hidden="true"></i></a>';
                $('td', row).eq(7).html(btnaction + btnstart).addClass('dt-center');
                $('td', row).eq(0).html(data[8]).addClass('dt-center');
                $('td', row).eq(5).html(setLabelState(data[5])).addClass('dt-center');
            }
        });

        tablcoverages.columns().every(function () {
            var table = this;
            $('input', this.header()).on('keyup change', function () {
                if (table.search() !== this.value && (this.value.length > 2  || this.value.length == 0)) {
                    table.search(this.value).draw();
                }
            });
        });

    },


    reprocessWork : function(){

        var icm_coverage_id = $(this).data('id');
        var element         = $(this);

        swal({
            title: 'Reprocesar cobertura',
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
                        url: `/income/coverages/${icm_coverage_id}`,
                        async: false,
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
                                Biblioteca.notificaciones('Proceso realizado de forma exitosa', 'Ingreso a sedes', 'success');
                                this.viewcoverages();
                                $('#md-process-coverage').modal('hide');
                            }else{
                                Biblioteca.notificaciones(response.message, 'Ingreso a sedes', 'error');
                            }
                        },
                        timeout: 30000,
                        type: 'GET'
                    });
                },60)
            }
        });
    },

    processCoverage : function(){
        $('#form-billing-customer').find(':input').not('[name="_token"]').val('');
        $('#md-process-coverage').modal();
    },

    executeCoverage : function(){

        if(!$('#form-coverages').valid()){
            Biblioteca.notificaciones('Existe información sin diligenciar', 'Coberturas ingreso sedes', 'warning');
            return false;
        }

        var element = $(this);

        request = $('#form-coverages').serialize();

        swal({
            title: 'Reprocesar cobertura',
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
                        url: `/income/coverages`,
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
                                Biblioteca.notificaciones('Proceso realizado de forma exitosa', 'Ingreso a sedes', 'success');
                                this.viewcoverages();
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

        Biblioteca.validacionGeneral('form-coverages');

        $('body').on('click', '#btn-refresh-liquidation', coverages.viewcoverages);
        $('body').on('click', '.btn-reprocess-work', coverages.reprocessWork);
        $('body').on('click', '#btn-new-coverage', coverages.processCoverage);
        $('body').on('click', '#btn-execute-coverage', coverages.executeCoverage);


        // Obtener la fecha actual
        var fechaActual = new Date();

        // Formatear las fechas según tus necesidades (opcional)
        var primerDiaFormateado = fechaActual.toISOString().split('T')[0];
        var ultimoDiaFormateado = fechaActual.toISOString().split('T')[0];

        $('#date_from').val(primerDiaFormateado);
        $('#date_to').val(ultimoDiaFormateado);

        this.viewcoverages();

    }
}



setLabelState = function(state){
    switch (state) {
        case 'E':
            return '<label class="badge badge-warning">En ejecución</label>';
            break;
        case 'T':
            return '<label class="badge badge-success">Finalizo exitosamente</label>';
            break;
        case 'D':
            return '<label class="badge badge-danger">Finalizo con error</label>';
            break;
        case 'D':
            return '<label class="badge badge-primary">Pendiente de ejecutar</label>';
            break;

        default:
            return '';
            break;
    }
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
    coverages.init();
});
