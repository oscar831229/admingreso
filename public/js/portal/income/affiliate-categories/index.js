

var tablcategories = null;


categories = {

    data : [],

    viewcategories : function(){

        tablcategories= $('#tbl-categories').DataTable();
        tablcategories.destroy();

        $('#tbl-categories thead th').each(function () {
            var title = $(this).text();
            if($(this).hasClass('search-disabled')){
                $(this).html(title);
            }else{
                $(this).html(title+' <input type="text" class="col-search-input" placeholder="" />');
            }
        });

        tablcategories = $('#tbl-categories').DataTable({
            language: language_es,
            pagingType: "numbers",
            processing: true,
            serverSide: true,
            ajax: {
                url: '/income/datatable-affiliate-categories',
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
                var btnaction = '<a href="javascript:void(0)" data-id="'+data[0]+'" class="tooltipsC btn-edit-form-categories" title="Editar entidad"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>'
                btnaction = '';
                $('td', row).eq(5).html(btnaction).addClass('dt-center');
                $('td', row).eq(0).html(data[6]).addClass('dt-center');
                $('td', row).eq(4).html(setLabelState(data[4])).addClass('dt-center');
            }
        });

        tablcategories.columns().every(function () {
            var table = this;
            $('input', this.header()).on('keyup change', function () {
                if (table.search() !== this.value && (this.value.length > 2  || this.value.length == 0)) {
                    table.search(this.value).draw();
                }
            });
        });
    },

    confirmSavecategories : function(){

        if(!$('#form-categories').valid()){
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
                categories.savecategories();
            }
        });


    },

    savecategories : function(){

        var element = $('#btn-save');
        var formadata = $('#form-categories').serialize();
        btn.loading(element);

        setTimeout(function(){

            $(".tooltip").tooltip("hide");

            $.ajax({
                url: '/income/affiliate-categories',
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
                        tablcategories.ajax.reload()
                        document.getElementById('form-categories').reset();
                        $("[name=id]").val('');
                        $('#md-new-categories').modal('hide');
                    }else{
                        Biblioteca.notificaciones(response.message, 'Equipos', 'error');
                    }
                },
                timeout: 30000,
                type: 'POST'

            });
        },100)

    },


    editcategories : function(){

        let categories_id = $(this).data('id');
        var btnedit =$(this);
        var element = $(this);
        btn.loading(element);

        setTimeout(function(){

            $(".tooltip").tooltip("hide");

            $.ajax({
                url: '/income/affiliate-categories/' + categories_id,
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
                        document.getElementById('form-categories').reset();
                        $('#form-categories').find("[name=id]").val('');
                        btnedit.closest('tr').remove()
                        loadDataForm('form-categories', response.ratetype);
                        $('#md-rate-type').modal()
                    }else{
                        Biblioteca.notificaciones(response.message, 'Equipos', 'error');
                    }
                },
                timeout: 30000,
                type: 'GET'

            });
        },100)

    },

    viewModalcategories : function(){
        document.getElementById('form-categories').reset();
        $('#form-categories').find("[name=id]").val('');
        $('#md-rate-type').modal()
    },

    init : function(){
        Biblioteca.validacionGeneral('form-categories');
        $('body').on('click', '#btn-save', categories.confirmSavecategories);
        $('body').on('click', '.btn-edit-form-categories', categories.editcategories);
        $('body').on('click', '#btn-new-categories', categories.viewModalcategories);
        this.viewcategories();
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
    categories.init();
});
