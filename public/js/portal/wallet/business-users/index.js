$(function(){
    
    $('#username').autocomplete({
        serviceUrl:'/wallet/finduser/business-users/',
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


function companies(){
    this.data = [],
    this.show = function(){

    },
    this.getCompany = function(){

    },
    this.findData = function(){

    }
}

var _this_parent = null;

company = {
    
    data : [],

    setData: function(data){
        this.data = data;
    },

    findData : function(){

        if($('#userid').val() == ''){
            Biblioteca.notificaciones('Debe indicar nombre del usuario', 'Usuarios - Empresa.', 'error');
            return false;
        }
        
        _this = this;
        btn.loading(_this);

        setTimeout(function(){

            $.ajax({
                url: 'list-stores/'+$('#userid').val(),
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
                        Biblioteca.notificaciones(response.message, 'Carga empresas.', 'error');
                        return false;
                    }

                    _this_parent.setData(response.data);
                    _this_parent.loadUser();
                    _this_parent.loadCompanies();

                    Biblioteca.notificaciones('Carga de datos exitosa.', 'Carga empresas.', 'success');
                },
                timeout: 30000,
                type: 'GET'
            });
        },80 )
    },

    getLableState : function(state){

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
    
    },

    loadUser : function(){
        this.eraseUser();
        $('#id').html(this.data.user.id);
        $('#login').html(this.data.user.login);
        $('#estado').html(this.getLableState(this.data.user.active));
        $('#document_number').html(this.data.user.document_number);
        $('#name').html(this.data.user.name);
    },

    eraseUser : function(){
        $('#id').html('');
        $('#login').html('');
        $('#estado').html('');
        $('#name').html('');
    },

    loadCompanies : function(){
        this.eraseCompanies();
        $.each(this.data.companies, function(index, company){
            _this_parent.createCompany(company);
        });
    },

    createCompany : function(company){

        var tr = '<tr>'
            +    '<td>'+company.code+'</td>'
            +    '<td>'+company.name+'</td>'
            +    '<td>'+company.address+'</td>'
            +    '<td>'+company.phone+'</td>'
            +    '<td class="text-center"><input type="checkbox" class="company" data-storeid="'+company.id+'" data-storeusersid="0"></td>'
            +    '</tr>';
        $('#tblpermissionuser tbody').append(tr);

        if(company.store_user_id != null){
            $('.company[data-storeid="'+company.id+'"').data('storeusersid', company.store_user_id);
            $('.company[data-storeid="'+company.id+'"').prop('checked',true);
        }

    },
    eraseCompanies : function(){
        $('#tblpermissionuser tbody').empty();
    },

    changecompany : function(){

        var store_id = $(this).data('storeid');
        var store_users_id = $(this).data('storeusersid');
        var user_id = _this_parent.data.user.id;

        $.ajax({
            url: 'business-users',
            async: false,
            data: {
                user_id : user_id,  
                store_id: store_id,
                store_users_id: store_users_id,
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

        $('#load').click(this.findData);

        $('body').on('click', '.company',this.changecompany);
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


