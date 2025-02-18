

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

        $.validator.addMethod("phoneCustom", function (value, element) {
            return this.optional(element) || /^[+]?[0-9]{1,4}?[-.●]?[0-9]{1,4}?[-.●]?[0-9]{1,4}?[-.●]?[0-9]{1,4}$/.test(value) && value.replace(/[^\d]/g, '').length <= 10;
        }, "Por favor, ingresa un número de teléfono válido con un máximo de 10 dígitos.");

        // Método de validación personalizada para verificar que el dominio es válido
        $.validator.addMethod("validEmailDomain", function(value, element) {
            // Expresión regular para verificar el dominio
            // Este patrón revisa que el dominio tenga al menos un '.' y un dominio válido de nivel superior
            var domainPattern = /^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            return this.optional(element) || domainPattern.test(value.split('@')[1]);
        }, "Por favor ingresa un dominio válido.");

        var rules= {
            document_type: {
                required: true
            },
            document_number: {
                required: true
            },
            first_name: {
                required: true
            },
            first_surname: {
                required: true
            },
            birthday_date: {
                required: true
            },
            gender: {
                required: true
            },
            icm_municipality_id: {
                required: true
            },
            address: {
                required: true
            },
            type_regime_id: {
                required: true
            },
            phone: {
                required: true,
                phoneCustom: true // Para validar números de teléfono en formato US (puedes cambiar esto si tu país tiene un formato distinto)
            },
            email: {
                required: true,
                email: true, // Validación de correo electrónico
                validEmailDomain: true // Verifica que el dominio sea válido
            }
        };

        var messages = {
            document_type: {
                required: "Tipo de documento de identificación es obligatorio.",
            },
            document_number: {
                required: "Número de documento de identificación es obligatorio.",
            },
            first_name: {
                required: "Primer nombre es obligatorio.",
            },
            first_surname: {
                required: "Primer apellido es obligatorio.",
            },
            birthday_date: {
                required: "Fecha de nacimiento es obligatorio.",
            },
            gender: {
                required: "Genero es obligatorio.",
            },
            icm_municipality_id: {
                required: "Municipio es obligatorio.",
            },
            address: {
                required: "Dirección es obligatorio.",
            },
            type_regime_id: {
                required: "Regimen es obligatorio.",
            },
            phone: {
                required: "El teléfono es obligatorio.",
                phoneCustom: "Por favor, ingresa un número de teléfono válido." // Mensaje de error personalizado para teléfono
            },
            email: {
                required: "El correo electrónico es obligatorio.",
                email: "Por favor, ingresa un correo electrónico válido.",
                validEmailDomain: "El dominio del correo no es válido" // Mensaje si el dominio no es correcto
            }
        };

        Biblioteca.validacionGeneral('form-customers', rules, messages);

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
