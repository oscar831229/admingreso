var _this_parent = null;

company = {

    data : [],

    setData: function(data){
        this.data = data;
    },

    findData : function(){

        if($('#userid').val() == ''){
            Biblioteca.notificaciones('Debe indicar nombre del usuario', 'Usuarios - Entidad.', 'error');
            return false;
        }

        _this = this;
        btn.loading(_this);

        setTimeout(function(){

            $.ajax({
                url: '/income/users-environments/'+$('#userid').val(),
                async: false,
                data: {},
                beforeSend: function(objeto){

                },
                complete: function(objeto, exito){
                    btn.reset(_this);
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

                    if(!response.success){
                        Biblioteca.notificaciones(response.message, 'Cargar ambientes', 'error');
                        return false;
                    }

                    _this_parent.setData(response.data);
                    _this_parent.loadUser();
                    _this_parent.loadEntities();

                    Biblioteca.notificaciones('Carga de datos exitosa.', 'Carga entidades.', 'success');

                },
                timeout: 30000,
                type: 'GET'
            });
        },80 )
    },

    loadUser : function(){
        this.eraseUser();
        $('#id').html(this.data.user.id);
        $('#login').html(this.data.user.login);
        $('#estado').html(this.data.user.active);
        $('#name').html(this.data.user.name);
    },

    eraseUser : function(){
        $('#id').html('');
        $('#login').html('');
        $('#estado').html('');
        $('#name').html('');
    },

    loadEntities : function(){
        this.eraseEntities();
        $.each(this.data.environments, function(index, entity){
            _this_parent.createEntity(entity);
        });
    },

    createEntity : function(entity){
        var tr = '<tr><td>'+entity.icm_entities_code+'</td><td>'+entity.icm_entities_name+'</td><td class="text-center"><input type="checkbox" class="entity" data-entityid="'+entity.icm_entities_id+'" data-userentityid="0"></td></tr>';
        $('#tblentities tbody').append(tr);

        if(entity.user_icm_entities_id != null){
            $('.entity[data-entityid="'+entity.icm_entities_id+'"').data('userentityid', entity.user_icm_entities_id);
            $('.entity[data-entityid="'+entity.icm_entities_id+'"').prop('checked',true);
        }

    },

    eraseEntities : function(){
        $('#tblentities tbody').empty();
    },

    changeEntity : function(){

        var icm_environment_id = $(this).data('entityid');
        var user_icm_environment_id = $(this).data('userentityid');
        var user_id = _this_parent.data.user.id;

        $.ajax({
            url: '/income/users-environments',
            async: false,
            data: {
                user_id : user_id,
                icm_environment_id: icm_environment_id,
                user_icm_environment_id: user_icm_environment_id,
                _token: $('input[name=_token]').val()
            },
            beforeSend: function(objeto){

            },
            complete: function(objeto, exito){
                btn.reset(_this);
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

                if(!response.success){
                    Biblioteca.notificaciones(response.message, 'Asignación empresa.', 'error');
                    return false;
                }

                Biblioteca.notificaciones('Proceso exitoso.', 'Asignación empresa.', 'success');

            },
            timeout: 30000,
            type: 'POST'
        });

    },

    init : function(){
        _this_parent = company;
        $('body').on('click', '#load', this.findData);
        $('body').on('click', '.entity',this.changeEntity);
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

    $('#username').autocomplete({
        serviceUrl:'/income/find-users-environments',
        paramName: 'username',
        minChars: 3,
        tabDisabled:true,
        onHint: function (hint) {
            $('#username-x').val(hint);
            $('#userid').val('');
        },
        onSelect: function(suggestion) {
            $('#userid').val(suggestion.data.userid);
        }
    });

    company.init();

});


function entities(){
    this.data = [],
    this.show = function(){

    },
    this.getCompany = function(){

    },
    this.findData = function(){

    }
}



