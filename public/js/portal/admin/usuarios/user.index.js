$(function(){
    
    
    users = function(){
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

   
    $('#datatable1').DataTable({
        responsive: true,
        language: {
        searchPlaceholder: 'Buscar...',
        sSearch: '',
        lengthMenu: '_MENU_ items/page',
        }
    });

    $('#datatable2').DataTable({
        bLengthChange: false,
        searching: false,
        responsive: true
    });

});