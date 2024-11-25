

var tablconfiguration = null;


configuration = {

    data : [],

    viewconfiguration : function(){

        tablconfiguration= $('#tbl-configuration').DataTable();
        tablconfiguration.destroy();

        $('#tbl-configuration thead th').each(function () {
            var title = $(this).text();
            if($(this).hasClass('search-disabled')){
                $(this).html(title);
            }else{
                $(this).html(title+' <input type="text" class="col-search-input" placeholder="" />');
            }
        });

        tablconfiguration = $('#tbl-configuration').DataTable({
            language: language_es,
            pagingType: "numbers",
            processing: true,
            serverSide: true,
            ajax: {
                url: '/Admin/datatable-configuration',
                type: "POST",
                data: {
                    '_token' : $('input[name=_token]').val()
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
                var btnaction = '<a href="javascript:void(0)" data-id="'+data[0]+'" class="tooltipsC btn-edit-form-configuration" title="Editar entidad"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>'
                $('td', row).eq(6).html(btnaction).addClass('dt-center');
                $('td', row).eq(2).html(setLabelEnabled(data[2])).addClass('dt-center');
                $('td', row).eq(0).html(data[7]).addClass('dt-center');
            }
        });

        tablconfiguration.columns().every(function () {
            var table = this;
            $('input', this.header()).on('keyup change', function () {
                if (table.search() !== this.value && (this.value.length > 2  || this.value.length == 0)) {
                    table.search(this.value).draw();
                }
            });
        });
    },

    confirmSaveconfiguration : function(){

        if(!$('#form-configuration').valid()){
            Biblioteca.notificaciones('Faltan datos obligatorios por diligenciar.', 'Equipos', 'warning');
            return false;
        }

        swal({
            title: 'Tipos de tarifa',
            text: "¿Esta seguro de continuar con el proceso?",
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
                configuration.saveconfiguration();
            }
        });


    },

    saveconfiguration : function(){

        var element = $('#btn-save');
        var formadata = new FormData($('#form-configuration')[0]);

        btn.loading(element);

        setTimeout(function(){

            $(".tooltip").tooltip("hide");

            $.ajax({
                url: '/Admin/system-configuration',
                async: true,
                data: formadata,
                beforeSend: function(objeto){

                },
                complete: function(objeto, exito){
                    btn.reset(element);
                    if(exito != "success"){
                        alert("No se completo el proceso!");
                    }
                },
                /*contentType: "application/x-www-form-urlencoded",*/
                dataType: "json",
                error: function(objeto, quepaso, otroobj){
                    alert("Ocurrio el siguiente error: "+quepaso);
                    btn.reset(element);
                },
                global: true,
                ifModified: false,
                processData: false, // No procesar los datos (formato de archivo)
                contentType: false, // No establecer el tipo de contenido
                success: function(response){
                    btn.reset(element);
                    if(response.success){
                        tablconfiguration.ajax.reload()
                        document.getElementById('form-configuration').reset();
                        $("[name=id]").val('');
                        $('#md-configuration').modal('hide');
                    }else{
                        Biblioteca.notificaciones(response.message, 'Equipos', 'error');
                    }
                },
                timeout: 30000,
                type: 'POST'

            });
        },100)

    },


    editconfiguration : function(){

        let configuration_id = $(this).data('id');
        var btnedit =$(this);
        var element = $(this);
        btn.loading(element);

        setTimeout(function(){

            $(".tooltip").tooltip("hide");

            $.ajax({
                url: '/Admin/system-configuration/' + configuration_id,
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
                        document.getElementById('form-configuration').reset();
                        $('#form-configuration').find("[name=id]").val('');
                        btnedit.closest('tr').remove()
                        loadDataForm('form-configuration', response.ratetype);
                        $('#md-configuration').modal()
                    }else{
                        Biblioteca.notificaciones(response.message, 'Equipos', 'error');
                    }
                },
                timeout: 30000,
                type: 'GET'

            });
        },100)

    },

    viewModalConfiguration : function(){
        document.getElementById('form-configuration').reset();
        $('#form-configuration').find("[name=id]").val('');
        $('#md-configuration').modal()
    },

    init : function(){
        Biblioteca.validacionGeneral('form-configuration');
        $('body').on('click', '#btn-save', configuration.confirmSaveconfiguration);
        $('body').on('click', '.btn-edit-form-configuration', configuration.editconfiguration);
        $('body').on('click', '#btn-new-configuration', configuration.viewModalConfiguration);
        this.viewconfiguration();
    }
}

loadDataForm = function(idform, data){
    Object.keys(data).forEach(key => {
        if($(`#${idform}`).find(`[name=${key}]`).length > 0){
            $(`#${idform}`).find(`[name=${key}]`).val(data[key]);
        }
    });
}

setLabelState = function(state){
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

setLabelEnabled = function(state){
    switch (state) {
        case 1:
            return '<label class="badge badge-success">Activo</label>';
            break;
        case 0:
            return '<label class="badge badge-warning">Inactivo</label>';
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
    configuration.init();
});
