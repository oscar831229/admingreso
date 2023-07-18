$(function(){
    
  
    // $('#emails').DataTable({
    //     responsive: true,
    //     language: {
    //     searchPlaceholder: 'Search...',
    //     sSearch: '',
    //     lengthMenu: '_MENU_ items/page',
    //     }
    // });


    emails = function(){
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
   
  

});