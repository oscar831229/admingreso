

var tablcustomers = null;


customers = {

    data : [],

    viewcustomers : function(){

        tablcustomers= $('#tbl-customers').DataTable();
        tablcustomers.destroy();

        $('#tbl-customers thead th').each(function () {
            var title = $(this).text();
            if($(this).hasClass('search-disabled')){
                $(this).html(title);
            }else{
                $(this).html(title+' <input type="text" class="col-search-input" placeholder="" />');
            }
        });

        tablcustomers = $('#tbl-customers').DataTable({
            language: language_es,
            pagingType: "numbers",
            processing: true,
            serverSide: true,
            ajax: {
                url: '/income/datatable-customers',
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
                var btnaction = '<a href="javascript:void(0)" data-id="'+data[0]+'" class="tooltipsC btn-edit-form-customers" title="Editar entidad"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>'
                $('td', row).eq(5).html(btnaction).addClass('dt-center');
                $('td', row).eq(0).html(data[6]).addClass('dt-center');
            }
        });

        tablcustomers.columns().every(function () {
            var table = this;
            $('input', this.header()).on('keyup change', function () {
                if (table.search() !== this.value && (this.value.length > 2  || this.value.length == 0)) {
                    table.search(this.value).draw();
                }
            });
        });
    },

    confirmSavecustomers : function(){

        if(!$('#form-customers').valid()){
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
                customers.savecustomers();
            }
        });


    },

    savecustomers : function(){

        var element = $('#btn-save');
        var formadata = $('#form-customers').serialize();
        btn.loading(element);

        setTimeout(function(){

            $(".tooltip").tooltip("hide");

            $.ajax({
                url: '/income/customers',
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
                        tablcustomers.ajax.reload()
                        document.getElementById('form-customers').reset();
                        $("[name=id]").val('');
                        $('#md-customer').modal('hide')
                    }else{
                        Biblioteca.notificaciones(response.message, 'Equipos', 'error');
                    }
                },
                timeout: 30000,
                type: 'POST'

            });
        },100)

    },


    editcustomers : function(){

        let customers_id = $(this).data('id');
        var btnedit = $(this);
        var element = $(this);
        btn.loading(element);

        setTimeout(function(){

            $(".tooltip").tooltip("hide");

            $.ajax({
                url: '/income/customers/' + customers_id,
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
                        document.getElementById('form-customers').reset();
                        $('#form-customers').find("[name=id]").val('');
                        btnedit.closest('tr').remove()
                        loadDataForm('form-customers', response.ratetype);
                        $('#md-customer').modal()
                    }else{
                        Biblioteca.notificaciones(response.message, 'Equipos', 'error');
                    }
                },
                timeout: 30000,
                type: 'GET'

            });
        },100)

    },

    viewModalcustomers : function(){
        document.getElementById('form-customers').reset();
        $('#form-customers').find("[name=id]").val('');
        $('#md-customer').modal()
    },

    init : function(){
        Biblioteca.validacionGeneral('form-customers');
        $('body').on('click', '#btn-save', customers.confirmSavecustomers);
        $('body').on('click', '.btn-edit-form-customers', customers.editcustomers);
        $('body').on('click', '#btn-new-customers', customers.viewModalcustomers);
        this.viewcustomers();
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
    customers.init();
});
