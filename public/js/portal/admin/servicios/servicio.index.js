$(function(){
    
    
    servicios = function(){
        return {
            elemento : '',
            delete:function(elemento, evento = false){

                if(evento)
                    return evento;

                    
                this.elemento = elemento;
                $('#confirm').modal();
                return evento;
            },
            anular:function(){
                this.elemento.submit(this.elemento, true);
            }
        }
    }()

   
    $('#tbl-servicios').DataTable({
        responsive: true,
        language: {
        searchPlaceholder: 'Search...',
        sSearch: '',
        lengthMenu: '_MENU_ items/page',
        }
    });

    $('.btn-sincronizar').on('click', function(){
        swal({
            title: 'Sincronizar unidades INDIGO',
            text: "¿Esta seguro de sincronizar las unidades.?",
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

                btn.loading($('.btn-sincronizar'));

                setTimeout(function(){

                    var retorno;

                    $.ajax({
                        url: '/Admin/sincronizar/units',
                        async: true,
                        data: {},		
                        beforeSend: function(objeto){
                            
                        },        
                        complete: function(objeto, exito){
                            btn.reset($('.btn-sincronizar'));
                            if(exito != "success"){
                                alert("No se completo el proceso!");
                            }            
                        },
                        contentType: "application/x-www-form-urlencoded",
                        dataType: "json",
                        error: function(objeto, quepaso, otroobj){
                            alert("Ocurrio el siguiente error: "+quepaso);
                            btn.reset('#btn-create');
                        },
                        global: true,
                        ifModified: false,
                        processData:true,
                        success: function(response){
                            btn.reset('#btn-create');
                            Biblioteca.notificaciones('Sincronización unidades', 'Proceso finalizado con exito', 'success');       
                        },
                        timeout: 30000,
                        type: 'GET'
                    });

                },100)    

            }
        });
    });

});


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